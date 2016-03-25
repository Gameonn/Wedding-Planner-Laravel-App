<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Input;
use \DateTime;
use Carbon\Carbon;

class Conceirge extends Model {
    

    // Validation Rules

    public static $accessTokenRequired = array(
        'access_token' => 'required|exists:users,access_token',
    );

    public static $sendConceirgeUserMessageRules = array(
        'access_token' => 'required|exists:users,access_token',        
        'message_type' => 'required',               
    );

    public static $sendConceirgeAdminMessageRules = array(
        'user_id' => 'required',
        'user_id_2' => 'required',  
        'message_type' => 'required',               
    );    

    public static $viewConceirgeUserMessagesRules = array(
        'access_token' => 'required|exists:users,access_token',
        'user_id_2' => 'required',
        'last_message_id' => 'required',        
    );    

    public static $viewCurrentConceirgeUserMessagesRules = array(
        'access_token' => 'required|exists:users,access_token',
        'user_id_2' => 'required',      
    );           

    public static $viewCurrentConceirgeAdminMessagesRules = array(
        'user_id' => 'required',
        'user_id_2' => 'required',      
    );     

    public static $viewPreviousConceirgeAdminMessagesRules = array(
        'user_id' => 'required',
        'user_id_2' => 'required',  
        'last_message_id' => 'required|numeric',      
    );                

    // Common Functions

    public static function markReadMessages($user_id, $user_id_2) {

        DB::table('conceirge')->where('sent_to', $user_id)->where('sent_by', $user_id_2)->update(['is_read' => '1']);

    }

    public static function insertConceirgeMessage($user_id, $user_id_2, $message_type, $message, $wedding_id, $vendor_id, $zone, $imgfile) {

        $current_time = Carbon::now();

        // Handling User Profile Image
        if($imgfile=="") {
            $image = "";
            $image_original = "";
        }
        else {
            $image = Users::uploadImage();
            $image_original = $imgfile->getClientOriginalName();
        }                   

        if($message_type==1) {
            if(empty($imgfile) || $imgfile=="")
                return 'Image Required';
        }
        else if($message_type==2) {
            if(empty($wedding_id) || $wedding_id=="")
                return 'Wedding Id Required';
        }
        else if($message_type==3) {
            if(empty($vendor_id) || $vendor_id=="")
                return 'Vendor Id Required';
        }               
        else {
            if(empty($message) || $message=="")
                return 'Provide some text';
        }

        $chat_id = DB::table('conceirge')->insertGetId(array(
            'sent_by' => $user_id,
            'sent_to' => $user_id_2,
            'message' => $message,                
            'message_type' => $message_type,
            'file_name' => $image,
            'file_original_name' => $image_original,
            'wedding_id' => $wedding_id,
            'vendor_id' => $vendor_id,
            'created_at' => $current_time,
            'updated_at' => $current_time,
        ));                       

        return $chat_id = strval($chat_id);

    }

    public static function getConceirgeUserMessages($user_id, $user_id_2, $last_message_id, $zone) {

        //$where_query = "(`sent_by`='$user_id_2' AND `sent_to`='$user_id')";

        $where_query = "(`sent_by`='$user_id_2' AND `sent_to`='$user_id')";        

        // DB::update("UPDATE `conceirge` SET `is_read`='1' WHERE `sent_to`='$user_id' AND `sent_by`='$user_id_2'");

        $messages = DB::select(
            "SELECT 
                `id`, 
                `sent_by`, 
                `sent_to`, 
                `message`,                                 
                `message_type`, 
                `file_name` AS `chat_image`, 
                `file_original_name` AS `chat_image_original`, 
                `wedding_id`,
                (SELECT `image` FROM `wedding_photos` WHERE `wedding_id` = `conceirge`.`wedding_id` LIMIT 1) AS `wedding_image`,
                (SELECT `name` FROM `wedding` WHERE `id` = `conceirge`.`wedding_id`) AS `wedding_name`,
                (SELECT `wedding_type` FROM `wedding` WHERE `id` = `conceirge`.`wedding_id`) AS `wedding_type`,
                `vendor_id`,
                (SELECT `image` FROM `vendor_portfolio_images` WHERE `user_id` = `conceirge`.`vendor_id` LIMIT 1) AS `vendor_portfolio_image`,
                (SELECT `business_name` FROM `vendor_details` WHERE `user_id` = `conceirge`.`vendor_id`) AS `business_name`,
                (SELECT `business_type` FROM `vendor_details` WHERE `user_id` = `conceirge`.`vendor_id`) AS `business_type`,
                (SELECT AVG(`rating`) FROM `vendor_reviews` WHERE `vendor_id` = `conceirge`.`vendor_id`) AS `vendor_rating`,     
                `conceirge`.`sent_by` AS `user_id`,           
                (SELECT `users`.`name` FROM `users` WHERE `users`.`id` = `conceirge`.`sent_by`) AS `user_name`,
                (SELECT `business_name` FROM `vendor_details` WHERE `user_id` = `conceirge`.`sent_by` LIMIT 1) AS `user_business_name`,
                (SELECT `users`.`image` FROM `users` WHERE `users`.`id` = `conceirge`.`sent_by`) AS `image`,
                `created_at`,
                CASE
                    WHEN DATEDIFF(UTC_TIMESTAMP, created_at) != 0 THEN CONCAT(DATEDIFF(UTC_TIMESTAMP, created_at) ,' d ago')
                    WHEN HOUR(TIMEDIFF(UTC_TIMESTAMP, created_at)) != 0 THEN CONCAT(HOUR(TIMEDIFF(UTC_TIMESTAMP, created_at)) ,' h ago')
                    WHEN MINUTE(TIMEDIFF(UTC_TIMESTAMP, created_at)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(UTC_TIMESTAMP, created_at)) ,' m ago')
                    ELSE
                    CONCAT(SECOND(TIMEDIFF(UTC_TIMESTAMP, created_at)) ,' s ago')
                END as time_since
            FROM `conceirge`
            WHERE $where_query AND `id`>'$last_message_id'
        ");

        foreach($messages as $key => $value) {

            if($messages[$key]->user_id == 0)                 
                $messages[$key]->image = Users::getFormattedImage('admin-profile-pic.jpg');
            else
                $messages[$key]->image = Users::getFormattedImage($messages[$key]->image);

            if($messages[$key]->chat_image == null || $messages[$key]->chat_image == "") 
                $messages[$key]->chat_image = "";
            else 
                $messages[$key]->chat_image = Users::getFormattedImage($messages[$key]->chat_image);

            if($messages[$key]->wedding_image == null) 
                $messages[$key]->wedding_image = "";
            else 
                $messages[$key]->wedding_image = Users::getFormattedImage($messages[$key]->wedding_image);

            if($messages[$key]->wedding_name == null) 
                $messages[$key]->wedding_name = "";

            if($messages[$key]->wedding_type == null) 
                $messages[$key]->wedding_type = "";

            if($messages[$key]->vendor_portfolio_image == null) 
                $messages[$key]->vendor_portfolio_image = "";
            else 
                $messages[$key]->vendor_portfolio_image = Users::getFormattedImage($messages[$key]->vendor_portfolio_image);

            if($messages[$key]->business_name == null) 
                $messages[$key]->business_name = "";

            if($messages[$key]->business_type == null) 
                $messages[$key]->business_type = "";

            if($messages[$key]->vendor_rating == null) 
                $messages[$key]->vendor_rating = "";

            if($messages[$key]->user_business_name == null) 
                $messages[$key]->user_business_name = "";

            if($messages[$key]->user_name == null) 
                $messages[$key]->user_name = "";

            $date = new DateTime($messages[$key]->created_at);
            $date->setTimezone(new \DateTimeZone($zone)); 
            $messages[$key]->created_at = $date->format('Y-m-d h:m:s');
            $messages[$key]->date = $date->format('d M Y');
            $messages[$key]->time = $date->format('H:i A');
        }

        return $messages;

    }

    public static function getPreviousConceirgeUserMessages($user_id, $user_id_2, $last_message_id, $zone) {
        
        $where_query = "(`sent_by`='$user_id' OR `sent_to`='$user_id') AND (`sent_by`='$user_id_2' OR `sent_to`='$user_id_2')";

        // DB::update("UPDATE `conceirge` SET `is_read`='1' WHERE `sent_to`='$user_id' AND `sent_by`='$user_id_2'");

        $messages = DB::select(
            "SELECT 
                `id`, 
                `sent_by`, 
                `sent_to`, 
                `message`,                 
                `message_type`, 
                `file_name` AS `chat_image`, 
                `file_original_name` AS `chat_image_original`, 
                `wedding_id`,
                (SELECT `image` FROM `wedding_photos` WHERE `wedding_id` = `conceirge`.`wedding_id` LIMIT 1) AS `wedding_image`,
                (SELECT `name` FROM `wedding` WHERE `id` = `conceirge`.`wedding_id`) AS `wedding_name`,
                (SELECT `wedding_type` FROM `wedding` WHERE `id` = `conceirge`.`wedding_id`) AS `wedding_type`,
                `vendor_id`,
                (SELECT `image` FROM `vendor_portfolio_images` WHERE `user_id` = `conceirge`.`vendor_id` LIMIT 1) AS `vendor_portfolio_image`,
                (SELECT `business_name` FROM `vendor_details` WHERE `user_id` = `conceirge`.`vendor_id`) AS `business_name`,
                (SELECT `business_type` FROM `vendor_details` WHERE `user_id` = `conceirge`.`vendor_id`) AS `business_type`,
                (SELECT AVG(`rating`) FROM `vendor_reviews` WHERE `vendor_id` = `conceirge`.`vendor_id`) AS `vendor_rating`,                
                `conceirge`.`sent_by` AS `user_id`,
                (SELECT `users`.`name` FROM `users` WHERE `users`.`id` = `conceirge`.`sent_by`) AS `user_name`,
                (SELECT `business_name` FROM `vendor_details` WHERE `user_id` = `conceirge`.`sent_by` LIMIT 1) AS `user_business_name`,
                (SELECT `users`.`image` FROM `users` WHERE `users`.`id` = `conceirge`.`sent_by`) AS `image`,
                `created_at`,
                CASE
                    WHEN DATEDIFF(UTC_TIMESTAMP, created_at) != 0 THEN CONCAT(DATEDIFF(UTC_TIMESTAMP, created_at) ,' d ago')
                    WHEN HOUR(TIMEDIFF(UTC_TIMESTAMP, created_at)) != 0 THEN CONCAT(HOUR(TIMEDIFF(UTC_TIMESTAMP, created_at)) ,' h ago')
                    WHEN MINUTE(TIMEDIFF(UTC_TIMESTAMP, created_at)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(UTC_TIMESTAMP, created_at)) ,' m ago')
                    ELSE
                    CONCAT(SECOND(TIMEDIFF(UTC_TIMESTAMP, created_at)) ,' s ago')
                END as time_since
            FROM `conceirge`
            WHERE $where_query AND `id`<'$last_message_id'
        ");

        foreach($messages as $key => $value) {
            $messages[$key]->image = Users::getFormattedImage($messages[$key]->image);

            if($messages[$key]->chat_image == null || $messages[$key]->chat_image == "") 
                $messages[$key]->chat_image = "";
            else 
                $messages[$key]->chat_image = Users::getFormattedImage($messages[$key]->chat_image);

            if($messages[$key]->wedding_image == null) 
                $messages[$key]->wedding_image = "";
            else 
                $messages[$key]->wedding_image = Users::getFormattedImage($messages[$key]->wedding_image);

            if($messages[$key]->wedding_name == null) 
                $messages[$key]->wedding_name = "";

            if($messages[$key]->wedding_type == null) 
                $messages[$key]->wedding_type = "";

            if($messages[$key]->vendor_portfolio_image == null) 
                $messages[$key]->vendor_portfolio_image = "";
            else 
                $messages[$key]->vendor_portfolio_image = Users::getFormattedImage($messages[$key]->vendor_portfolio_image);

            if($messages[$key]->business_name == null) 
                $messages[$key]->business_name = "";

            if($messages[$key]->business_type == null) 
                $messages[$key]->business_type = "";

            if($messages[$key]->vendor_rating == null) 
                $messages[$key]->vendor_rating = "";

            if($messages[$key]->user_business_name == null) 
                $messages[$key]->user_business_name = "";

            $date = new DateTime($messages[$key]->created_at);
            $date->setTimezone(new \DateTimeZone($zone)); 
            $messages[$key]->created_at = $date->format('Y-m-d h:m:s');
            $messages[$key]->date = $date->format('d M Y');
            $messages[$key]->time = $date->format('H:i A');
        }

        return $messages;

    }

    public static function getAllConceirgeUserMessages($user_id, $user_id_2, $zone) {
        
        $where_query = "(`sent_by`='$user_id' OR `sent_to`='$user_id') AND (`sent_by`='$user_id_2' OR `sent_to`='$user_id_2')";

        // DB::update("UPDATE `conceirge` SET `is_read`='1' WHERE `sent_to`='$user_id' AND `sent_by`='$user_id_2'");

        $messages = DB::select(
            "SELECT 
                `id`, 
                `sent_by`, 
                `sent_to`, 
                `message`,                 
                `message_type`, 
                `file_name` AS `chat_image`, 
                `file_original_name` AS `chat_image_original`, 
                `wedding_id`,
                (SELECT `image` FROM `wedding_photos` WHERE `wedding_id` = `conceirge`.`wedding_id` LIMIT 1) AS `wedding_image`,
                (SELECT `name` FROM `wedding` WHERE `id` = `conceirge`.`wedding_id`) AS `wedding_name`,
                (SELECT `wedding_type` FROM `wedding` WHERE `id` = `conceirge`.`wedding_id`) AS `wedding_type`,
                `vendor_id`,
                (SELECT `image` FROM `vendor_portfolio_images` WHERE `user_id` = `conceirge`.`vendor_id` LIMIT 1) AS `vendor_portfolio_image`,
                (SELECT `business_name` FROM `vendor_details` WHERE `user_id` = `conceirge`.`vendor_id`) AS `business_name`,
                (SELECT `business_type` FROM `vendor_details` WHERE `user_id` = `conceirge`.`vendor_id`) AS `business_type`,
                (SELECT AVG(`rating`) FROM `vendor_reviews` WHERE `vendor_id` = `conceirge`.`vendor_id`) AS `vendor_rating`,                
                `conceirge`.`sent_by` AS `user_id`,
                (SELECT `users`.`name` FROM `users` WHERE `users`.`id` = `conceirge`.`sent_by`) AS `user_name`,
                (SELECT `business_name` FROM `vendor_details` WHERE `user_id` = `conceirge`.`sent_by` LIMIT 1) AS `user_business_name`,
                (SELECT `users`.`image` FROM `users` WHERE `users`.`id` = `conceirge`.`sent_by`) AS `image`,
                `created_at`,
                CASE
                    WHEN DATEDIFF(UTC_TIMESTAMP, created_at) != 0 THEN CONCAT(DATEDIFF(UTC_TIMESTAMP, created_at) ,' d ago')
                    WHEN HOUR(TIMEDIFF(UTC_TIMESTAMP, created_at)) != 0 THEN CONCAT(HOUR(TIMEDIFF(UTC_TIMESTAMP, created_at)) ,' h ago')
                    WHEN MINUTE(TIMEDIFF(UTC_TIMESTAMP, created_at)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(UTC_TIMESTAMP, created_at)) ,' m ago')
                    ELSE
                    CONCAT(SECOND(TIMEDIFF(UTC_TIMESTAMP, created_at)) ,' s ago')
                END as time_since
            FROM `conceirge`
            WHERE $where_query
        ");

        foreach($messages as $key => $value) {
            $messages[$key]->image = Users::getFormattedImage($messages[$key]->image);

            if($messages[$key]->chat_image == null || $messages[$key]->chat_image == "") 
                $messages[$key]->chat_image = "";
            else 
                $messages[$key]->chat_image = Users::getFormattedImage($messages[$key]->chat_image);

            if($messages[$key]->wedding_image == null) 
                $messages[$key]->wedding_image = "";
            else 
                $messages[$key]->wedding_image = Users::getFormattedImage($messages[$key]->wedding_image);

            if($messages[$key]->wedding_name == null) 
                $messages[$key]->wedding_name = "";

            if($messages[$key]->wedding_type == null) 
                $messages[$key]->wedding_type = "";

            if($messages[$key]->vendor_portfolio_image == null) 
                $messages[$key]->vendor_portfolio_image = "";
            else 
                $messages[$key]->vendor_portfolio_image = Users::getFormattedImage($messages[$key]->vendor_portfolio_image);

            if($messages[$key]->business_name == null) 
                $messages[$key]->business_name = "";

            if($messages[$key]->business_type == null) 
                $messages[$key]->business_type = "";

            if($messages[$key]->vendor_rating == null) 
                $messages[$key]->vendor_rating = "";

            if($messages[$key]->user_business_name == null) 
                $messages[$key]->user_business_name = "";

            $date = new DateTime($messages[$key]->created_at);
            $date->setTimezone(new \DateTimeZone($zone)); 
            $messages[$key]->created_at = $date->format('Y-m-d h:m:s');
            $messages[$key]->date = $date->format('d M Y');
            $messages[$key]->time = $date->format('H:i A');
        }

        return $messages;

    }

    public static function getCurrentConceirgeUserMessages($user_id, $user_id_2, $zone) {

        Conceirge::markReadMessages($user_id, $user_id_2);
        
        $where_query = "(`sent_by`='$user_id' OR `sent_to`='$user_id') AND (`sent_by`='$user_id_2' OR `sent_to`='$user_id_2')";                

        $messages = DB::select(
            "SELECT * FROM (SELECT 
                `id`,
                `id` AS `current_message_id`,
                `sent_by`, 
                `sent_to`, 
                `message`,                 
                `message_type`, 
                `file_name` AS `chat_image`, 
                `file_original_name` AS `chat_image_original`, 
                `wedding_id`,
                (SELECT `image` FROM `wedding_photos` WHERE `wedding_id` = `conceirge`.`wedding_id` LIMIT 1) AS `wedding_image`,
                (SELECT `name` FROM `wedding` WHERE `id` = `conceirge`.`wedding_id`) AS `wedding_name`,
                (SELECT `wedding_type` FROM `wedding` WHERE `id` = `conceirge`.`wedding_id`) AS `wedding_type`,
                `vendor_id`,
                (SELECT `image` FROM `vendor_portfolio_images` WHERE `user_id` = `conceirge`.`vendor_id` LIMIT 1) AS `vendor_portfolio_image`,
                (SELECT `business_name` FROM `vendor_details` WHERE `user_id` = `conceirge`.`vendor_id`) AS `business_name`,
                (SELECT `business_type` FROM `vendor_details` WHERE `user_id` = `conceirge`.`vendor_id`) AS `business_type`,
                (SELECT AVG(`rating`) FROM `vendor_reviews` WHERE `vendor_id` = `conceirge`.`vendor_id`) AS `vendor_rating`,                
                `conceirge`.`sent_by` AS `user_id`,
                (SELECT `users`.`name` FROM `users` WHERE `users`.`id` = `conceirge`.`sent_by`) AS `user_name`,
                (SELECT `users`.`phone_no` FROM `users` WHERE `users`.`id` = `conceirge`.`sent_by`) AS `user_phone_no`,
                (SELECT `business_name` FROM `vendor_details` WHERE `user_id` = `conceirge`.`sent_by` LIMIT 1) AS `user_business_name`,
                (SELECT `users`.`image` FROM `users` WHERE `users`.`id` = `conceirge`.`sent_by`) AS `image`,                
                (SELECT `id` FROM `conceirge` WHERE ((`sent_by`='$user_id' AND `sent_to`='$user_id_2') OR (`sent_by`='$user_id_2' AND `sent_to`='$user_id')) AND `id`<`current_message_id` ORDER BY `id` DESC LIMIT 1) AS `is_more_chat`,
                `created_at`,
                CASE
                    WHEN DATEDIFF(UTC_TIMESTAMP, created_at) != 0 THEN CONCAT(DATEDIFF(UTC_TIMESTAMP, created_at) ,' d ago')
                    WHEN HOUR(TIMEDIFF(UTC_TIMESTAMP, created_at)) != 0 THEN CONCAT(HOUR(TIMEDIFF(UTC_TIMESTAMP, created_at)) ,' h ago')
                    WHEN MINUTE(TIMEDIFF(UTC_TIMESTAMP, created_at)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(UTC_TIMESTAMP, created_at)) ,' m ago')
                    ELSE
                    CONCAT(SECOND(TIMEDIFF(UTC_TIMESTAMP, created_at)) ,' s ago')
                END as time_since
            FROM `conceirge`
            WHERE $where_query 
            ORDER BY `id` DESC
            LIMIT 40) sub
            ORDER BY `id` ASC
        ");

        foreach($messages as $key => $value) {
            
            if($messages[$key]->user_id == 0) {
                $messages[$key]->user_name = 'Admin';
                $messages[$key]->image = Users::getFormattedImage('admin-profile-pic.jpg');
            }
            else
                $messages[$key]->image = Users::getFormattedImage($messages[$key]->image);

            if($messages[$key]->user_phone_no == null) 
                $messages[$key]->user_phone_no = "";

            if($messages[$key]->chat_image == null || $messages[$key]->chat_image == "") 
                $messages[$key]->chat_image = "";
            else 
                $messages[$key]->chat_image = Users::getFormattedImage($messages[$key]->chat_image);

            if($messages[$key]->wedding_image == null) 
                $messages[$key]->wedding_image = "";
            else 
                $messages[$key]->wedding_image = Users::getFormattedImage($messages[$key]->wedding_image);

            if($messages[$key]->wedding_name == null) 
                $messages[$key]->wedding_name = "";

            if($messages[$key]->wedding_type == null) 
                $messages[$key]->wedding_type = "";

            if($messages[$key]->vendor_portfolio_image == null) 
                $messages[$key]->vendor_portfolio_image = "";
            else 
                $messages[$key]->vendor_portfolio_image = Users::getFormattedImage($messages[$key]->vendor_portfolio_image);

            if($messages[$key]->business_name == null) 
                $messages[$key]->business_name = "";

            if($messages[$key]->business_type == null) 
                $messages[$key]->business_type = "";

            if($messages[$key]->vendor_rating == null) 
                $messages[$key]->vendor_rating = "";
            else 
                $messages[$key]->vendor_rating = round($messages[$key]->vendor_rating, 1);

            if($messages[$key]->user_business_name == null) 
                $messages[$key]->user_business_name = "";

            $date = new DateTime($messages[$key]->created_at);
            $date->setTimezone(new \DateTimeZone($zone)); 
            $messages[$key]->created_at = $date->format('Y-m-d h:m:s');
            $messages[$key]->date = $date->format('d M Y');
            $messages[$key]->time = $date->format('H:i A');
        }

        // Marking Message Read
        DB::update("UPDATE `conceirge` SET `is_read`='1' WHERE `sent_to`='$user_id' AND `sent_by`='$user_id_2'");

        return $messages;

    }

    public static function getConceirgeMessageListing($user_id, $zone) {
        
        if(empty($zone))
            $zone = "Asia/Kolkata";

        $message_lisitng = DB::select(
            "SELECT conceirge.* FROM(

                SELECT conceirge.* FROM(
                
                    SELECT
                    conceirge.id,
                    conceirge.sent_to,
                    conceirge.sent_by,
                    conceirge.message,                                            
                    conceirge.message_type,
                    conceirge.file_name AS `chat_image`,
                    conceirge.file_original_name AS `chat_image_original`,
                    `conceirge`.`wedding_id`, 
                    (SELECT `image` FROM `wedding_photos` WHERE `wedding_id` = `conceirge`.`wedding_id` LIMIT 1) AS `wedding_image`,
                    (SELECT `name` FROM `wedding` WHERE `id` = `conceirge`.`wedding_id`) AS `wedding_name`,
                    (SELECT `wedding_type` FROM `wedding` WHERE `id` = `conceirge`.`wedding_id`) AS `wedding_type`,
                    `conceirge`.`vendor_id`,
                    (SELECT `image` FROM `vendor_portfolio_images` WHERE `user_id` = `conceirge`.`vendor_id` LIMIT 1) AS `vendor_portfolio_image`,
                    (SELECT `business_name` FROM `vendor_details` WHERE `user_id` = `conceirge`.`vendor_id`) AS `business_name`,
                    (SELECT `business_type` FROM `vendor_details` WHERE `user_id` = `conceirge`.`vendor_id`) AS `business_type`,
                    (SELECT AVG(`rating`) FROM `vendor_reviews` WHERE `vendor_id` = `conceirge`.`vendor_id`) AS `vendor_rating`,  
                    `conceirge`.`sent_to` AS `user_id`,
                    (SELECT `name` FROM `users` WHERE `id` = `user_id`) AS `user_name`,
                    (SELECT `phone_no` FROM `users` WHERE `id` = `user_id`) AS `user_phone_no`,
                    (SELECT `business_name` FROM `vendor_details` WHERE `user_id` = `user_id` LIMIT 1) AS `user_business_name`,
                    (SELECT `image` FROM `users` WHERE `id` = `user_id`) AS `image`,
                    (SELECT count(*) FROM `conceirge` WHERE `conceirge`.`is_read`='0' AND `conceirge`.`sent_to`= '$user_id' AND `conceirge`.`sent_by`= `user_id`) AS unread_count,
                    (SELECT count(*) FROM `conceirge` WHERE `conceirge`.`is_read`='0' AND `conceirge`.`sent_to`= '$user_id') AS total_unread_count,
                    conceirge.created_at,
                    CASE
                        WHEN DATEDIFF(UTC_TIMESTAMP,`conceirge`.created_at) != 0 THEN DATE_FORMAT(`conceirge`.created_at,'%d/%m/%Y')
                        WHEN HOUR(TIMEDIFF(UTC_TIMESTAMP, `conceirge`.created_at)) != 0 THEN CONCAT(HOUR(TIMEDIFF(UTC_TIMESTAMP, `conceirge`.created_at)) ,' h')
                        WHEN MINUTE(TIMEDIFF(UTC_TIMESTAMP,`conceirge`.created_at)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(UTC_TIMESTAMP,`conceirge`.created_at)) ,' m')
                        ELSE
                        CONCAT('Now')
                    END as time_since            
                    FROM conceirge
                    WHERE conceirge.sent_by='$user_id'

                    UNION

                    SELECT   
                    conceirge.id,                     
                    conceirge.sent_by,
                    conceirge.sent_to,
                    conceirge.message,                                            
                    conceirge.message_type,
                    conceirge.file_name AS `chat_image`,
                    conceirge.file_original_name AS `chat_image_original`,
                    `conceirge`.`wedding_id`,
                    (SELECT `image` FROM `wedding_photos` WHERE `wedding_id` = `conceirge`.`wedding_id` LIMIT 1) AS `wedding_image`,
                    (SELECT `name` FROM `wedding` WHERE `id` = `conceirge`.`wedding_id`) AS `wedding_name`,
                    (SELECT `wedding_type` FROM `wedding` WHERE `id` = `conceirge`.`wedding_id`) AS `wedding_type`,
                    `conceirge`.`vendor_id`,
                    (SELECT `image` FROM `vendor_portfolio_images` WHERE `user_id` = `conceirge`.`vendor_id` LIMIT 1) AS `vendor_portfolio_image`,
                    (SELECT `business_name` FROM `vendor_details` WHERE `user_id` = `conceirge`.`vendor_id`) AS `business_name`,
                    (SELECT `business_type` FROM `vendor_details` WHERE `user_id` = `conceirge`.`vendor_id`) AS `business_type`,
                    (SELECT AVG(`rating`) FROM `vendor_reviews` WHERE `vendor_id` = `conceirge`.`vendor_id`) AS `vendor_rating`,            
                    `conceirge`.`sent_by` AS `user_id`,
                    (SELECT `name` FROM `users` WHERE `id` = `user_id`) AS `user_name`,
                    (SELECT `phone_no` FROM `users` WHERE `id` = `user_id`) AS `user_phone_no`,
                    (SELECT `business_name` FROM `vendor_details` WHERE `user_id` = `user_id` LIMIT 1) AS `user_business_name`,
                    (SELECT `image` FROM `users` WHERE `id` = `user_id`) AS `image`,
                    (SELECT count(*) FROM `conceirge` WHERE `conceirge`.`is_read`='0' AND `conceirge`.`sent_to`= '$user_id' AND `conceirge`.`sent_by`= `user_id`) AS unread_count,
                    (SELECT count(*) FROM `conceirge` WHERE `conceirge`.`is_read`='0' AND `conceirge`.`sent_to`= '$user_id') AS total_unread_count,
                    conceirge.created_at,
                    CASE
                        WHEN DATEDIFF(UTC_TIMESTAMP,`conceirge`.created_at) != 0 THEN DATE_FORMAT(`conceirge`.created_at,'%d/%m/%Y')
                        WHEN HOUR(TIMEDIFF(UTC_TIMESTAMP, `conceirge`.created_at)) != 0 THEN CONCAT(HOUR(TIMEDIFF(UTC_TIMESTAMP, `conceirge`.created_at)) ,' h')
                        WHEN MINUTE(TIMEDIFF(UTC_TIMESTAMP,`conceirge`.created_at)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(UTC_TIMESTAMP,`conceirge`.created_at)) ,' m')
                        ELSE
                        CONCAT('Now')
                    END as time_since    
                    FROM conceirge
                    WHERE conceirge.sent_to='$user_id'
                ) AS conceirge

            ORDER BY created_at DESC) AS conceirge
            GROUP BY conceirge.sent_to
            ORDER BY created_at DESC
        ");                         
        
        $all_message_listing = $message_lisitng;

        foreach($all_message_listing as $key => $value) {               

            if($all_message_listing[$key]->image==null)
                $all_message_listing[$key]->image = "";                
            else                    
                $all_message_listing[$key]->image = Users::getFormattedImage($all_message_listing[$key]->image);

            if($all_message_listing[$key]->user_phone_no == null) 
                $all_message_listing[$key]->user_phone_no = "";

            if($all_message_listing[$key]->chat_image == null || $all_message_listing[$key]->chat_image == "") 
                $all_message_listing[$key]->chat_image = "";
            else 
                $all_message_listing[$key]->chat_image = Users::getFormattedImage($all_message_listing[$key]->chat_image);

            if($all_message_listing[$key]->wedding_image == null) 
                $all_message_listing[$key]->wedding_image = "";
            else 
                $all_message_listing[$key]->wedding_image = Users::getFormattedImage($all_message_listing[$key]->wedding_image);

            if($all_message_listing[$key]->wedding_name == null) 
                $all_message_listing[$key]->wedding_name = "";

            if($all_message_listing[$key]->wedding_type == null) 
                $all_message_listing[$key]->wedding_type = "";

            if($all_message_listing[$key]->vendor_portfolio_image == null) 
                $all_message_listing[$key]->vendor_portfolio_image = "";
            else 
                $all_message_listing[$key]->vendor_portfolio_image = Users::getFormattedImage($all_message_listing[$key]->vendor_portfolio_image);

            if($all_message_listing[$key]->business_name == null) 
                $all_message_listing[$key]->business_name = "";

            if($all_message_listing[$key]->business_type == null) 
                $all_message_listing[$key]->business_type = "";

            if($all_message_listing[$key]->vendor_rating == null) 
                $all_message_listing[$key]->vendor_rating = "";

            if($all_message_listing[$key]->user_business_name == null) 
                $all_message_listing[$key]->user_business_name = "";

            $date = new DateTime($all_message_listing[$key]->created_at);
            $date->setTimezone(new \DateTimeZone($zone)); 
            $all_message_listing[$key]->created_at = $date->format('Y-m-d h:m:s');
            $all_message_listing[$key]->date = $date->format('d M Y');
            $all_message_listing[$key]->time = $date->format('H:i A');

        }

        return $all_message_listing;        

    }

    public static function getConceirgeMessageListing2($zone) {
        
        if(empty($zone))
            $zone = "Asia/Kolkata";

        $message_lisitng = DB::select(
            "SELECT temp2.* FROM 
                (SELECT temp.* FROM (
                    SELECT
                        `C`.`id`,
                        `C`.`sent_to`,
                        `C`.`sent_by`,
                        `C`.`message`,
                        's' AS `mode`, 
                        `sent_by` as `other_user`,                                            
                        `C`.`message_type`,
                        `C`.`file_name` AS `chat_image`,
                        `C`.`file_original_name` AS `chat_image_original`,
                        `C`.`wedding_id`, 
                        (SELECT `image` FROM `wedding_photos` WHERE `wedding_id` = `C`.`wedding_id` LIMIT 1) AS `wedding_image`,
                        (SELECT `name` FROM `wedding` WHERE `id` = `C`.`wedding_id`) AS `wedding_name`,
                        (SELECT `wedding_type` FROM `wedding` WHERE `id` = `C`.`wedding_id`) AS `wedding_type`,
                        `C`.`vendor_id`,
                        (SELECT `image` FROM `vendor_portfolio_images` WHERE `user_id` = `C`.`vendor_id` LIMIT 1) AS `vendor_portfolio_image`,
                        (SELECT `business_name` FROM `vendor_details` WHERE `user_id` = `C`.`vendor_id`) AS `business_name`,
                        (SELECT `business_type` FROM `vendor_details` WHERE `user_id` = `C`.`vendor_id`) AS `business_type`,
                        (SELECT AVG(`rating`) FROM `vendor_reviews` WHERE `vendor_id` = `C`.`vendor_id`) AS `vendor_rating`,  
                        `C`.`sent_to` AS `user_id`,
                        (SELECT `name` FROM `users` WHERE `id` = `user_id`) AS `user_name`,
                        (SELECT `phone_no` FROM `users` WHERE `id` = `user_id`) AS `user_phone_no`,
                        (SELECT `business_name` FROM `vendor_details` WHERE `user_id` = `user_id` LIMIT 1) AS `user_business_name`,
                        (SELECT `image` FROM `users` WHERE `id` = `user_id`) AS `image`,
                        '10' AS `message_count`,
                        `C`.`created_at`,
                        CASE
                            WHEN DATEDIFF(UTC_TIMESTAMP,`C`.`created_at`) != 0 THEN DATE_FORMAT(`C`.`created_at`,'%d/%m/%Y')
                            WHEN HOUR(TIMEDIFF(UTC_TIMESTAMP,`C`.`created_at`)) != 0 THEN TIME_FORMAT(`C`.`created_at`,' h')
                            WHEN MINUTE(TIMEDIFF(UTC_TIMESTAMP,`C`.`created_at`)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(UTC_TIMESTAMP,`C`.`created_at`)) ,' m')
                            ELSE
                            CONCAT('Now')
                        END as `time_since`
                    FROM `conceirge` as C
                    WHERE LENGTH(C.sent_to)>=5
                    UNION
                    SELECT                         
                        `C`.`id`,
                        `C`.`sent_to`,
                        `C`.`sent_by`,
                        `C`.`message`,
                        'r' AS `mode`, 
                        `sent_to` as `other_user`,                                            
                        `C`.`message_type`,
                        `C`.`file_name` AS `chat_image`,
                        `C`.`file_original_name` AS `chat_image_original`,
                        `C`.`wedding_id`, 
                        (SELECT `image` FROM `wedding_photos` WHERE `wedding_id` = `C`.`wedding_id` LIMIT 1) AS `wedding_image`,
                        (SELECT `name` FROM `wedding` WHERE `id` = `C`.`wedding_id`) AS `wedding_name`,
                        (SELECT `wedding_type` FROM `wedding` WHERE `id` = `C`.`wedding_id`) AS `wedding_type`,
                        `C`.`vendor_id`,
                        (SELECT `image` FROM `vendor_portfolio_images` WHERE `user_id` = `C`.`vendor_id` LIMIT 1) AS `vendor_portfolio_image`,
                        (SELECT `business_name` FROM `vendor_details` WHERE `user_id` = `C`.`vendor_id`) AS `business_name`,
                        (SELECT `business_type` FROM `vendor_details` WHERE `user_id` = `C`.`vendor_id`) AS `business_type`,
                        (SELECT AVG(`rating`) FROM `vendor_reviews` WHERE `vendor_id` = `C`.`vendor_id`) AS `vendor_rating`,  
                        `C`.`sent_to` AS `user_id`,
                        (SELECT `name` FROM `users` WHERE `id` = `user_id`) AS `user_name`,
                        (SELECT `phone_no` FROM `users` WHERE `id` = `user_id`) AS `user_phone_no`,
                        (SELECT `business_name` FROM `vendor_details` WHERE `user_id` = `user_id` LIMIT 1) AS `user_business_name`,
                        (SELECT `image` FROM `users` WHERE `id` = `user_id`) AS `image`,
                        '10' AS `message_count`,
                        `C`.`created_at`,
                        CASE
                            WHEN DATEDIFF(UTC_TIMESTAMP,`C`.`created_at`) != 0 THEN DATE_FORMAT(`C`.`created_at`,'%d/%m/%Y')
                            WHEN HOUR(TIMEDIFF(UTC_TIMESTAMP,`C`.`created_at`)) != 0 THEN TIME_FORMAT(`C`.`created_at`,' h')
                            WHEN MINUTE(TIMEDIFF(UTC_TIMESTAMP,`C`.`created_at`)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(UTC_TIMESTAMP,`C`.`created_at`)) ,' m')
                            ELSE
                            CONCAT('Now')
                        END as `time_since`
                    FROM `conceirge` as C                        
                    WHERE LENGTH(C.sent_by)>=5
                ) as temp 
                ORDER BY temp.created_at DESC
            ) as temp2 
            GROUP BY other_user 
            ORDER BY temp2.created_at DESC
        ");  
        
        $all_message_listing = $message_lisitng;

        foreach($all_message_listing as $key => $value) {               

            if($all_message_listing[$key]->image==null)
                $all_message_listing[$key]->image = "";                
            else                    
                $all_message_listing[$key]->image = Users::getFormattedImage($all_message_listing[$key]->image);

            if($all_message_listing[$key]->user_phone_no == null) 
                $all_message_listing[$key]->user_phone_no = "";

            if($all_message_listing[$key]->chat_image == null || $all_message_listing[$key]->chat_image == "") 
                $all_message_listing[$key]->chat_image = "";
            else 
                $all_message_listing[$key]->chat_image = Users::getFormattedImage($all_message_listing[$key]->chat_image);

            if($all_message_listing[$key]->wedding_image == null) 
                $all_message_listing[$key]->wedding_image = "";
            else 
                $all_message_listing[$key]->wedding_image = Users::getFormattedImage($all_message_listing[$key]->wedding_image);

            if($all_message_listing[$key]->wedding_name == null) 
                $all_message_listing[$key]->wedding_name = "";

            if($all_message_listing[$key]->wedding_type == null) 
                $all_message_listing[$key]->wedding_type = "";

            if($all_message_listing[$key]->vendor_portfolio_image == null) 
                $all_message_listing[$key]->vendor_portfolio_image = "";
            else 
                $all_message_listing[$key]->vendor_portfolio_image = Users::getFormattedImage($all_message_listing[$key]->vendor_portfolio_image);

            if($all_message_listing[$key]->business_name == null) 
                $all_message_listing[$key]->business_name = "";

            if($all_message_listing[$key]->business_type == null) 
                $all_message_listing[$key]->business_type = "";

            if($all_message_listing[$key]->vendor_rating == null) 
                $all_message_listing[$key]->vendor_rating = "";

            if($all_message_listing[$key]->user_business_name == null) 
                $all_message_listing[$key]->user_business_name = "";

            $date = new DateTime($all_message_listing[$key]->created_at);
            $date->setTimezone(new \DateTimeZone($zone)); 
            $all_message_listing[$key]->created_at = $date->format('Y-m-d h:m:s');
            $all_message_listing[$key]->date = $date->format('d M Y');
            $all_message_listing[$key]->time = $date->format('H:i A');

        }

        return $all_message_listing;        

    }

    public static function getConceirgeAdminId($user_id) {

        $conceirge_data = DB::select(
            "SELECT `id`, `sent_by`, `sent_to`
            FROM `conceirge`
            WHERE `sent_by` = '$user_id' || `sent_to` = '$user_id'
        ");

        // var_dump($conceirge_data[0]->sent_by);
        // var_dump($conceirge_data[0]->sent_to);

        if($conceirge_data[0]->sent_by == $user_id)
            $admin_id = $conceirge_data[0]->sent_to;
        else
            $admin_id = $conceirge_data[0]->sent_by;

        return $admin_id;

    }

    // Conceirge User Functions

    public static function sendConceirgeUserMessage($input) {

        $validation = Validator::make($input, Conceirge::$sendConceirgeUserMessageRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {

            $access_token = $input['access_token'];
            $user_id_2 = '0';            
            $message_type = $input['message_type'];     // 0 : message, 1 : image, 2 : wedding, 3 : vendor            
            $message = isset($input['message']) ? $input['message'] : "";
            $wedding_id = isset($input['wedding_id']) ? $input['wedding_id'] : "";
            $vendor_id = isset($input['vendor_id']) ? $input['vendor_id'] : "";
            $zone = isset($input['zone']) ? $input['zone'] : "Asia/Kolkata";
            $imgfile = Input::file('image');                        

            if(empty($zone))
                $zone = "Asia/Kolkata";

            $user_id = Users::getUserIdByToken($access_token); 

            $conceirge_data = DB::select(
                "SELECT `id`, `sent_by`, `sent_to` 
                FROM `conceirge`
                WHERE `sent_by` = '$user_id' OR `sent_to` = '$user_id'
            ");

            if(empty($conceirge_data)) {

                $admin_data_1 = DB::table('admin')->select('id')->where('last_active', '1')->where('user_role', 'chat_operator')->first();
                $last_active_admin_id = $admin_data_1->id;
                $admin_data_2 = DB::table('admin')->select('id')->where('id', '>', $last_active_admin_id)->where('user_role', 'chat_operator')->first();
                if(empty($admin_data_2)) {
                    $admin_data_2 = DB::table('admin')->select('id')->where('user_role', 'chat_operator')->first();
                }
                $active_admin_id = $admin_data_2->id;

                // Updating Active chat operator
                DB::table('admin')->update(['last_active' => '0']);
                DB::table('admin')->where('id', $active_admin_id)->update(['last_active' => '1']);

                $active_admin_id = 'admin'.$admin_data_2->id;

            }
            else {

                if($user_id == $conceirge_data[0]->sent_by)
                    $active_admin_id = $conceirge_data[0]->sent_to;
                else
                    $active_admin_id = $conceirge_data[0]->sent_by;

            }
        
            $response = Conceirge::insertConceirgeMessage($user_id, $active_admin_id, $message_type, $message, $wedding_id, $vendor_id, $zone, $imgfile);    

            if($response == 'Image Required') 
                return Response::json(array('status'=>0, 'msg'=>$response), 200);
            else if ($response == 'Wedding Id Required')                 
                return Response::json(array('status'=>0, 'msg'=>$response), 200);
            else if ($response == 'Vendor Id Required')                 
                return Response::json(array('status'=>0, 'msg'=>$response), 200);
            else if ($response == 'Provide some text')                 
                return Response::json(array('status'=>0, 'msg'=>$response), 200);
            else
                return Response::json(array('status'=>1, 'msg'=>'Message Sent', 'message_id' => $response), 200);            
        }

    }    

    public static function viewConceirgeUserMessages($input) {

        $validation = Validator::make($input, Conceirge::$viewConceirgeUserMessagesRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {

            $access_token = $input['access_token'];
            $last_message_id = $input['last_message_id'];
            // $user_id_2 = $input['user_id_2'];                
            $zone = isset($input['zone']) ? $input['zone'] : "Asia/Kolkata";
            $user_id = Users::getUserIdByToken($access_token);

            $user_id_2 = Conceirge::getConceirgeAdminId($user_id);

            if(empty($zone))
                $zone = "Asia/Kolkata";

            $messages = Conceirge::getConceirgeUserMessages($user_id, $user_id_2, $last_message_id, $zone);

            return Response::json(array('status'=>1, 'msg'=>'Messages', 'messages'=>$messages), 200);

        }

    }

    public static function viewPreviousConceirgeUserMessages($input) {

        $validation = Validator::make($input, Conceirge::$viewConceirgeUserMessagesRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {

            $access_token = $input['access_token'];
            $last_message_id = $input['last_message_id'];
            // $user_id_2 = $input['user_id_2'];            
            $zone = isset($input['zone']) ? $input['zone'] : "Asia/Kolkata";
            $user_id = Users::getUserIdByToken($access_token);

            $user_id_2 = Conceirge::getConceirgeAdminId($user_id);

            if(empty($zone))
                $zone = "Asia/Kolkata";

            $messages = Conceirge::getPreviousConceirgeUserMessages($user_id, $user_id_2, $last_message_id, $zone);            

            return Response::json(array('status'=>1, 'msg'=>'Messages', 'messages'=>$messages), 200);
        }

    }

    public static function viewCurrentConceirgeUserMessages($input) {

        $validation = Validator::make($input, Conceirge::$viewCurrentConceirgeUserMessagesRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {

            $access_token = $input['access_token'];
            $user_id_2 = $input['user_id_2'];            
            $zone = isset($input['zone']) ? $input['zone'] : "Asia/Kolkata";
            $user_id = Users::getUserIdByToken($access_token);

            $conceirge_data = DB::select("SELECT `id` FROM `conceirge` WHERE `sent_by` = '$user_id' OR `sent_to` = '$user_id'");

            if(!empty($conceirge_data)) {
                $user_id_2 = Conceirge::getConceirgeAdminId($user_id);

                if(empty($zone))
                    $zone = "Asia/Kolkata";

                $messages = Conceirge::getCurrentConceirgeUserMessages($user_id, $user_id_2, $zone);
            }            
            else {
                $messages = array();
            }

            return Response::json(array('status'=>1, 'msg'=>'Latest 40 Messages', 'messages'=>$messages), 200);
        }

    }    

    public static function viewConceirgeUserMessageListing($input) {

        $validation = Validator::make($input, Conceirge::$accessTokenRequired);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }

        $access_token = $input['access_token'];
        $zone = isset($input['zone']) ? $input['zone'] : "Asia/Kolkata";
        $user_id = Users::getUserIdByToken($access_token);         

        if(empty($zone))
            $zone = "Asia/Kolkata";

        $message_lisitng = Conceirge::getConceirgeMessageListing($user_id, $zone);   
        
        return Response::json(array('status'=>1, 'msg'=>'Chat Message Listing', 'message_listing'=>$message_lisitng), 200);
        
    }

    // Conceirge User Functions

    public static function sendConceirgeAdminMessage($input) {

        $validation = Validator::make($input, Conceirge::$sendConceirgeAdminMessageRules);
        if($validation->fails()) {
            return $validation->getMessageBag()->first();
        }
        else {

            $user_id = $input['user_id'];
            $user_id_2 = $input['user_id_2'];            
            $message_type = $input['message_type'];     // 0 : message, 1 : image, 2 : wedding, 3 : vendor            
            $message = isset($input['message']) ? $input['message'] : "";
            $wedding_id = isset($input['wedding_id']) ? $input['wedding_id'] : "";
            $vendor_id = isset($input['vendor_id']) ? $input['vendor_id'] : "";
            $zone = isset($input['zone']) ? $input['zone'] : "Asia/Kolkata";
            $imgfile = Input::file('image');                        

            if(empty($zone))
                $zone = "Asia/Kolkata";            

            $response = Conceirge::insertConceirgeMessage($user_id, $user_id_2, $message_type, $message, $wedding_id, $vendor_id, $zone, $imgfile);    

            Notifications::conceirgeChatNotifications($user_id, $user_id_2);

            if($response == 'Image Required') 
                return $response;
            else if ($response == 'Wedding Id Required')                 
                return $response;
            else if ($response == 'Vendor Id Required')                 
                return $response;
            else if ($response == 'Provide some text')                 
                return $response;
            else 
                return $response;
        }

    }

    public static function viewCurrentConceirgeAdminMessages($input) {

        $validation = Validator::make($input, Conceirge::$viewCurrentConceirgeAdminMessagesRules);
        if($validation->fails()) {
            return $validation->getMessageBag()->first();
        }
        else {

            $user_id = $input['user_id'];
            $user_id_2 = $input['user_id_2'];            
            $zone = isset($input['zone']) ? $input['zone'] : "Asia/Kolkata";            

            if(empty($zone))
                $zone = "Asia/Kolkata";

            $messages = Conceirge::getCurrentConceirgeUserMessages($user_id, $user_id_2, $zone);

            // Formating Image For Admin View
            foreach ($messages as $key => $value) {             
                if($messages[$key]->image == "") 
                    $messages[$key]->image = Users::getFormattedImage('default-profile-pic.png'); 
            }          

            $response_html = "";
            $response_html_2 = "";

            foreach($messages as $key => $value)  {                

                if($value->sent_by == 0) {

                    if($value->chat_image != "") {                  
                        $chat_image_html = '<div class="chat-image-box"> <img src="'.$value->chat_image.'/300"> </div>';
                    }                
                    else 
                        $chat_image_html = "";

                    if(strpos($value->image,'graph.facebook.com') !== false)
                        $profile_image_html = '<img class="message-avatar" src="'.$value->image.'" alt="">';
                    else
                        $profile_image_html = '<img class="message-avatar" src="'.$value->image.'/50" alt="">';

                    if($value->user_name == "")                                    
                      $user_name_html = $value->user_phone_no;
                    else                       
                      $user_name_html = $value->user_name;

                    if($value->message_type == 0) {
                        $message_html = $value->message;
                    }
                    elseif($value->message_type == 1) {
                        $message_html = '<div class="chat-image-box">
                                          <img src="'.$value->chat_image.'/300">
                                        </div>';
                    }
                    elseif($value->message_type == 2) {
                        $message_html = '<div class="row">
                                          <div class="col-lg-offset-8 col-lg-4">
                                              <div class="widget-head-color-box navy-bg p-lg text-center my-chat-widget">
                                                  <div class="m-b-md">
                                                  <h2 class="font-bold no-margins">
                                                      '.$value->wedding_name.'
                                                  </h2>
                                                      <small>Wedding</small>
                                                  </div>
                                                  <img src="'.$value->wedding_image.'/300" class="m-b-md" alt="profile">
                                                  <div>
                                                      <span>'.$value->wedding_type.'</span>
                                                  </div>                                                  
                                              </div>                                              
                                          </div>                                          
                                        </div>';
                    }
                    elseif($value->message_type == 3) {
                        $message_html = '<div class="row">
                                          <div class="col-lg-offset-8 col-lg-4">
                                              <div class="widget-head-color-box lazur-bg p-lg text-center my-chat-widget">
                                                  <div class="m-b-md">
                                                  <h2 class="font-bold no-margins">
                                                      '.$value->business_name.'
                                                  </h2>
                                                      <small>Vendor</small>
                                                  </div>
                                                  <img src="'.$value->vendor_portfolio_image.'/300" class="m-b-md" alt="profile">
                                                  <div>
                                                      <span>'.$value->business_type.'</span>      
                                                  </div>
                                                  <div>
                                                      <span> <strong>Rating</strong> '.$value->vendor_rating.'</span>
                                                  </div>
                                              </div>                                              
                                          </div>                                          
                                        </div>';
                    }
                    else {}

                    $response_html = '<div class="chat-message right">
                        '.$profile_image_html.'
                        <div class="message">                                  
                            <strong>
                            '.$user_name_html.'
                            </strong>
                            <span class="message-date"> '.$value->time_since.' </span>
                            <span class="message-content">
                              '.$message_html.'
                            </span>
                        </div>
                    </div>';
                }
                else {

                    if($value->chat_image != "") {                  
                        $chat_image_html = '<div class="chat-image-box"> <img src="'.$value->chat_image.'/300"> </div>';
                    }                
                    else 
                        $chat_image_html = "";

                    if(strpos($value->image,'graph.facebook.com') !== false)
                        $profile_image_html = '<img class="message-avatar" src="'.$value->image.'" alt="">';
                    else
                        $profile_image_html = '<img class="message-avatar" src="'.$value->image.'/50" alt="">';

                    if($value->user_name == "")                                    
                      $user_name_html = $value->user_phone_no;
                    else                       
                      $user_name_html = $value->user_name;

                    if($value->message_type == 0) {
                        $message_html = $value->message;
                    }
                    elseif($value->message_type == 1) {
                        $message_html = '<div class="chat-image-box">
                                          <img src="'.$value->chat_image.'/300">
                                        </div>';
                    }
                    elseif($value->message_type == 2) {
                        $message_html = '<div class="row">
                                          <div class="col-lg-4">
                                              <div class="widget-head-color-box navy-bg p-lg text-center my-chat-widget">
                                                  <div class="m-b-md">
                                                  <h2 class="font-bold no-margins">
                                                      '.$value->wedding_name.'
                                                  </h2>
                                                      <small>Wedding</small>
                                                  </div>
                                                  <img src="'.$value->wedding_image.'/300" class="m-b-md" alt="profile">
                                                  <div>
                                                      <span>'.$value->wedding_type.'</span>
                                                  </div>                                                  
                                              </div>                                              
                                          </div>                                          
                                        </div>';
                    }
                    elseif($value->message_type == 3) {
                        $message_html = '<div class="row">
                                          <div class="col-lg-4">
                                              <div class="widget-head-color-box lazur-bg p-lg text-center my-chat-widget">
                                                  <div class="m-b-md">
                                                  <h2 class="font-bold no-margins">
                                                      '.$value->business_name.'
                                                  </h2>
                                                      <small>Vendor</small>
                                                  </div>
                                                  <img src="'.$value->vendor_portfolio_image.'/300" class="m-b-md" alt="profile">
                                                  <div>
                                                      <span>'.$value->business_type.'</span>      
                                                  </div>
                                                  <div>
                                                      <span> <strong>Rating</strong> '.$value->vendor_rating.'</span>
                                                  </div>
                                              </div>                                              
                                          </div>                                          
                                        </div>';
                    }
                    else {}

                    $response_html = '<div class="chat-message left">
                        '.$profile_image_html.'
                        <div class="message">                                  
                            <strong>
                            '.$user_name_html.'
                            </strong>
                            <span class="message-date"> '.$value->time_since.' </span>
                            <span class="message-content">
                              '.$message_html.'
                            </span>
                        </div>
                    </div>';
                }

                $response_html_2 = $response_html_2.$response_html;
            }

            //return $messages;            

            return $response_html_2;           

        }

    }

    public static function viewPreviousConceirgeAdminMessages($input) {        

        $validation = Validator::make($input, Conceirge::$viewPreviousConceirgeAdminMessagesRules);
        if($validation->fails()) {
            return $validation->getMessageBag()->first();
        }
        else {

            $user_id = $input['user_id'];
            $user_id_2 = $input['user_id_2'];  
            $last_message_id = $input['last_message_id'];            
            $zone = isset($input['zone']) ? $input['zone'] : "Asia/Kolkata";            

            if(empty($zone))
                $zone = "Asia/Kolkata";

            $messages = Conceirge::getAllConceirgeUserMessages($user_id, $user_id_2, $zone);

            // Formating Image For Admin View
            foreach ($messages as $key => $value) {             
                if($messages[$key]->image == "") 
                    $messages[$key]->image = Users::getFormattedImage('default-profile-pic.png'); 
            }                                    

            $response_html = "";
            $response_html_2 = "";            

            foreach($messages as $key => $value)  {                

              if($value->sent_by == 0) {

                if($value->chat_image != "") {                  
                    $chat_image_html = '<div class="chat-image-box"> <img src="'.$value->chat_image.'/300"> </div>';
                }                
                else 
                    $chat_image_html = "";                

                $response_html = '<div class="chat-message right">
                      <img class="message-avatar" src="'.$value->image.'/50" alt="">
                      <div class="message">                                  
                          <strong>'.$value->user_name.'</strong>
                          <span class="message-date">'.$value->time_since.'</span>
                          <span class="message-content">
                            '.$chat_image_html.$value->message.'
                          </span>
                      </div>
                  </div>';
              }
              else {

                if($value->chat_image != "") {                  
                    $chat_image_html = '<div class="chat-image-box"> <img src="'.$value->chat_image.'/300"> </div>';
                }                
                else 
                    $chat_image_html = "";

                $response_html = '<div class="chat-message left">
                      <img class="message-avatar" src="'.$value->image.'/50" alt="">
                      <div class="message">
                          <strong>'.$value->user_name.'</strong>
                          <span class="message-date">'.$value->time_since.'</span>
                          <span class="message-content">
                            '.$chat_image_html.$value->message.'
                          </span>
                      </div>
                  </div>';
              }

              $response_html_2 = $response_html_2.$response_html;
            }

            //return $messages;

            return $response_html_2;           

        }

    }

    public static function viewConceirgeAdminMessageListing($user_id) {

        $zone = "Asia/Kolkata";                 
        $message_lisitng = Conceirge::getConceirgeMessageListing($user_id, $zone);  

        $response_html = "";
        $response_html_2 = "";    

        if($message_lisitng[0]->total_unread_count == 0)
            $response_html_unread_count = "";
        else     
            $response_html_unread_count = '<span class="label label-warning my-message-count">'.$message_lisitng[0]->total_unread_count.'</span>';

        foreach ($message_lisitng as $key => $value) {
        
            $response_html = '
                <li class="my-header-dropdown-li">
                    <div class="dropdown-messages-box">
                        <a href="'.URL::to('/').'/admin/conceirge/admin'.Session::get('admin_id').'/'.$value->user_id.'" class="pull-left">
                            <img alt="image" class="img-circle" src="'.$value->image.'">
                        </a>
                        <div class="media-body">                                            
                            <small class="pull-right">'.$value->time_since.'</small>
                            <strong>'.$value->user_name.'</strong> messaged you. <br> 
                            '.$value->message.'
                        </div>
                    </div>
                </li>
                <li class="divider"></li>
            ';

            $response_html_2 = $response_html_2.$response_html;

        }                      

        $response_html_3 = '<li>
            <div class="text-center link-block">
                <a href="'.URL::to('/').'/admin/conceirge">
                    <i class="fa fa-envelope"></i> <strong>Read All Messages</strong>                    
                </a>
            </div>
        </li>';

        $response_html_2 = $response_html_2.$response_html_3;

        $response_html_4 = '
            <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                <i class="fa fa-envelope"></i>  
                '.$response_html_unread_count.'
            </a>
            <ul class="dropdown-menu dropdown-messages">
                '.$response_html_2.'
            </ul>                
        ';

        return $response_html_4;

    }

    public static function viewConceirgeAdminMessageListing2($user_id) {

        $zone = "Asia/Kolkata";                 
        $message_lisitng = Conceirge::getConceirgeMessageListing($user_id, $zone);      

        // Formating Image For Admin View
        foreach ($message_lisitng as $key => $value) {             
            if($message_lisitng[$key]->image == "") 
                $message_lisitng[$key]->image = Users::getFormattedImage('default-profile-pic.png'); 
        }                                        

        return $message_lisitng;

    }

    public static function viewConceirgeAdminMessageListing3($user_id) {

        $zone = "Asia/Kolkata";                 
        return $message_lisitng = Conceirge::getConceirgeMessageListing2($zone);      

        // Formating Image For Admin View
        foreach ($message_lisitng as $key => $value) {             
            if($message_lisitng[$key]->image == "") 
                $message_lisitng[$key]->image = Users::getFormattedImage('default-profile-pic.png'); 
        }                                        

        return $message_lisitng;

    }

    public static function uploadChatImage($input) {

        $validation = Validator::make($input, Conceirge::$sendConceirgeAdminMessageRules);
        if($validation->fails()) {
            return $validation->getMessageBag()->first();
        }
        else {

            $user_id = $input['user_id'];
            $user_id_2 = $input['user_id_2'];            
            $message_type = $input['message_type'];     // 0 : message, 1 : image, 2 : wedding, 3 : vendor            
            $message = isset($input['message']) ? $input['message'] : "";
            $wedding_id = isset($input['wedding_id']) ? $input['wedding_id'] : "";
            $vendor_id = isset($input['vendor_id']) ? $input['vendor_id'] : "";
            $zone = isset($input['zone']) ? $input['zone'] : "Asia/Kolkata";
            $imgfile = Input::file('image');                        

            if(empty($zone))
                $zone = "Asia/Kolkata";                

            return $response = Conceirge::insertConceirgeMessage($user_id, $user_id_2, $message_type, $message, $wedding_id, $vendor_id, $zone, $imgfile);    

            if($response == 'Image Required') 
                return Response::json(array('status'=>0, 'msg'=>$response), 200);
            else if ($response == 'Wedding Id Required')                 
                return Response::json(array('status'=>0, 'msg'=>$response), 200);
            else if ($response == 'Vendor Id Required')                 
                return Response::json(array('status'=>0, 'msg'=>$response), 200);
            else if ($response == 'Provide some text')                 
                return Response::json(array('status'=>0, 'msg'=>$response), 200);
            else
                return Response::json(array('status'=>1, 'msg'=>'Message Sent', 'message_id' => $response), 200);
        }        

    }

    public static function vendorListing() {

        $vendor_listing = DB::select(
            "SELECT `users`.`id` as `vendor_id`, 
            `vendor_details`.`id` as `vendor_detail_id`, `vendor_details`.`business_name`, 
            (SELECT `image` FROM `vendor_portfolio_images` WHERE `user_id` = `vendor_id` LIMIT 1) as `vendor_portfolio_image`
            FROM `users`
            JOIN `vendor_details` ON `users`.`id` = `vendor_details`.`user_id`
        ");

        foreach ($vendor_listing as $key => $value) {
            $value->vendor_portfolio_image = Users::getFormattedImage($value->vendor_portfolio_image);
        }

        return $vendor_listing;

    }

    public static function weddingListing() {

        $wedding_listing = DB::select(
            "SELECT `id` AS `wedding_id`, `user_id`, `name`,
            (SELECT `image` FROM `wedding_photos` WHERE `wedding_id` = `wedding`.`id` LIMIT 1) as `wedding_image`
            FROM `wedding`            
        ");

        foreach ($wedding_listing as $key => $value) {
            $value->wedding_image = Users::getFormattedImage($value->wedding_image);
        }

        return $wedding_listing;

    }

 
}