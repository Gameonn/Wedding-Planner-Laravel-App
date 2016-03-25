<?php namespace App;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use \DateTime;

class Chat extends Model {

	// Validation Rules

	public static $accessTokenRequired = array(
        'access_token' => 'required|exists:users,access_token',
    );

    public static $insertMessageRules = array(
        'access_token' => 'required|exists:users,access_token',
        'user_id_2' => 'required',  
        'message_type' => 'required',               
    );

    public static $viewmessagesRules = array(
        'access_token' => 'required|exists:users,access_token',
        'user_id_2' => 'required|numeric',
        'last_message_id' => 'required',        
    );    

    public static $getCurrentMessagesRules = array(
        'access_token' => 'required|exists:users,access_token',
        'user_id_2' => 'required|numeric',      
    );        

    // Common Functions

    public static function markChatGroupRead($user_id, $group_id, $chat_id) {
        DB::table('chat_group_read')->where('user_id', $user_id)->where('group_id', $group_id)->update(['chat_id_2' => $chat_id]);        
    }

    public static function getMessages($user_id, $user_id_2, $group_id, $last_message_id, $zone) {

        if($group_id==0) {
            $where_query = "(`sent_by`='$user_id_2' AND `sent_to`='$user_id') AND `group_id` = '0'";

            // $where_query = "(`sent_by`='$user_id' OR `sent_to`='$user_id') AND (`sent_by`='$user_id_2' OR `sent_to`='$user_id_2') AND `group_id` = '0'";
        } 
        else {
            $where_query = "group_id = '$group_id' AND `sent_by` != '$user_id'";    
        }

        DB::update("UPDATE `chat` SET `is_read`='1' WHERE `sent_to`='$user_id' AND `sent_by`='$user_id_2'");

        $messages = DB::select(
            "SELECT 
                `id`, 
                `sent_by`, 
                `sent_to`, 
                `message`, 
                `group_id`, 
                (SELECT `name` FROM `collaborators` WHERE `id` = `chat`.`group_id`) AS `group_name`, 
                `message_type`, 
                `file_name` AS `chat_image`, 
                `file_original_name` AS `chat_image_original`, 
                `wedding_id`,
                (SELECT `image` FROM `wedding_photos` WHERE `wedding_id` = `chat`.`wedding_id` LIMIT 1) AS `wedding_image`,
                (SELECT `name` FROM `wedding` WHERE `id` = `chat`.`wedding_id`) AS `wedding_name`,
                (SELECT `wedding_type` FROM `wedding` WHERE `id` = `chat`.`wedding_id`) AS `wedding_type`,
                `vendor_id`,
                (SELECT `image` FROM `vendor_portfolio_images` WHERE `user_id` = `chat`.`vendor_id` LIMIT 1) AS `vendor_portfolio_image`,
                (SELECT `business_name` FROM `vendor_details` WHERE `user_id` = `chat`.`vendor_id`) AS `business_name`,
                (SELECT `business_type` FROM `vendor_details` WHERE `user_id` = `chat`.`vendor_id`) AS `business_type`,
                (SELECT AVG(`rating`) FROM `vendor_reviews` WHERE `vendor_id` = `chat`.`vendor_id`) AS `vendor_rating`,     
                `chat`.`sent_by` AS `user_id`,           
                (SELECT `users`.`name` FROM `users` WHERE `users`.`id` = `chat`.`sent_by`) AS `user_name`,
                (SELECT `business_name` FROM `vendor_details` WHERE `user_id` = `chat`.`sent_by` LIMIT 1) AS `user_business_name`,
                (SELECT `users`.`image` FROM `users` WHERE `users`.`id` = `chat`.`sent_by`) AS `image`,
                `created_at`,
                CASE
                    WHEN DATEDIFF(UTC_TIMESTAMP, created_at) != 0 THEN CONCAT(DATEDIFF(UTC_TIMESTAMP, created_at) ,' d ago')
                    WHEN HOUR(TIMEDIFF(UTC_TIMESTAMP, created_at)) != 0 THEN CONCAT(HOUR(TIMEDIFF(UTC_TIMESTAMP, created_at)) ,' h ago')
                    WHEN MINUTE(TIMEDIFF(UTC_TIMESTAMP, created_at)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(UTC_TIMESTAMP, created_at)) ,' m ago')
                    ELSE
                    CONCAT(SECOND(TIMEDIFF(UTC_TIMESTAMP, created_at)) ,' s ago')
                END as time_since
            FROM `chat`
            WHERE $where_query AND `id`>'$last_message_id'
        ");

        foreach($messages as $key => $value) {
            $messages[$key]->image = Users::getFormattedImage($messages[$key]->image);            

            if($messages[$key]->group_name == null) 
                $messages[$key]->group_name = "";

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

    public static function getPreviousMessages($user_id, $user_id_2, $group_id, $last_message_id, $zone) {

        if($group_id==0) {
            $where_query = "(`sent_by`='$user_id' OR `sent_to`='$user_id') AND (`sent_by`='$user_id_2' OR `sent_to`='$user_id_2') AND `group_id` = '0'";
        } 
        else {
            $where_query = "group_id = '$group_id'";    
        }

        DB::update("UPDATE `chat` SET `is_read`='1' WHERE `sent_to`='$user_id' AND `sent_by`='$user_id_2'");

        $messages = DB::select(
            "SELECT 
                `id`, 
                `sent_by`, 
                `sent_to`, 
                `message`, 
                `group_id`, 
                (SELECT `name` FROM `collaborators` WHERE `id` = `chat`.`group_id`) AS `group_name`, 
                `message_type`, 
                `file_name` AS `chat_image`, 
                `file_original_name` AS `chat_image_original`, 
                `wedding_id`,
                (SELECT `image` FROM `wedding_photos` WHERE `wedding_id` = `chat`.`wedding_id` LIMIT 1) AS `wedding_image`,
                (SELECT `name` FROM `wedding` WHERE `id` = `chat`.`wedding_id`) AS `wedding_name`,
                (SELECT `wedding_type` FROM `wedding` WHERE `id` = `chat`.`wedding_id`) AS `wedding_type`,
                `vendor_id`,
                (SELECT `image` FROM `vendor_portfolio_images` WHERE `user_id` = `chat`.`vendor_id` LIMIT 1) AS `vendor_portfolio_image`,
                (SELECT `business_name` FROM `vendor_details` WHERE `user_id` = `chat`.`vendor_id`) AS `business_name`,
                (SELECT `business_type` FROM `vendor_details` WHERE `user_id` = `chat`.`vendor_id`) AS `business_type`,
                (SELECT AVG(`rating`) FROM `vendor_reviews` WHERE `vendor_id` = `chat`.`vendor_id`) AS `vendor_rating`,                
                `chat`.`sent_by` AS `user_id`,
                (SELECT `users`.`name` FROM `users` WHERE `users`.`id` = `chat`.`sent_by`) AS `user_name`,
                (SELECT `business_name` FROM `vendor_details` WHERE `user_id` = `chat`.`sent_by` LIMIT 1) AS `user_business_name`,
                (SELECT `users`.`image` FROM `users` WHERE `users`.`id` = `chat`.`sent_by`) AS `image`,
                `created_at`,
                CASE
                    WHEN DATEDIFF(UTC_TIMESTAMP, created_at) != 0 THEN CONCAT(DATEDIFF(UTC_TIMESTAMP, created_at) ,' d ago')
                    WHEN HOUR(TIMEDIFF(UTC_TIMESTAMP, created_at)) != 0 THEN CONCAT(HOUR(TIMEDIFF(UTC_TIMESTAMP, created_at)) ,' h ago')
                    WHEN MINUTE(TIMEDIFF(UTC_TIMESTAMP, created_at)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(UTC_TIMESTAMP, created_at)) ,' m ago')
                    ELSE
                    CONCAT(SECOND(TIMEDIFF(UTC_TIMESTAMP, created_at)) ,' s ago')
                END as time_since
            FROM `chat`
            WHERE $where_query AND `id`<'$last_message_id'
        ");

        foreach($messages as $key => $value) {
            $messages[$key]->image = Users::getFormattedImage($messages[$key]->image);

            if($messages[$key]->group_name == null) 
                $messages[$key]->group_name = "";

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

    public static function getCurrentMessages($user_id, $user_id_2, $group_id, $zone) {

        if($group_id==0) {
            $where_query = "(`sent_by`='$user_id' OR `sent_to`='$user_id') AND (`sent_by`='$user_id_2' OR `sent_to`='$user_id_2') AND `group_id` = '0'";
        } 
        else {
            $where_query = "group_id = '$group_id'";    
        }        

        $messages = DB::select(
            "SELECT * FROM (SELECT 
                `id`, 
                `sent_by`, 
                `sent_to`, 
                `message`, 
                `group_id`,
                (SELECT `name` FROM `collaborators` WHERE `id` = `chat`.`group_id`) AS `group_name`, 
                `message_type`, 
                `file_name` AS `chat_image`, 
                `file_original_name` AS `chat_image_original`, 
                `wedding_id`,
                (SELECT `image` FROM `wedding_photos` WHERE `wedding_id` = `chat`.`wedding_id` LIMIT 1) AS `wedding_image`,
                (SELECT `name` FROM `wedding` WHERE `id` = `chat`.`wedding_id`) AS `wedding_name`,
                (SELECT `wedding_type` FROM `wedding` WHERE `id` = `chat`.`wedding_id`) AS `wedding_type`, 
                `vendor_id`,
                (SELECT `image` FROM `vendor_portfolio_images` WHERE `user_id` = `chat`.`vendor_id` LIMIT 1) AS `vendor_portfolio_image`,
                (SELECT `business_name` FROM `vendor_details` WHERE `user_id` = `chat`.`vendor_id`) AS `business_name`,
                (SELECT `business_type` FROM `vendor_details` WHERE `user_id` = `chat`.`vendor_id`) AS `business_type`,
                (SELECT AVG(`rating`) FROM `vendor_reviews` WHERE `vendor_id` = `chat`.`vendor_id`) AS `vendor_rating`,                
                `chat`.`sent_by` AS `user_id`,
                (SELECT `users`.`name` FROM `users` WHERE `users`.`id` = `chat`.`sent_by`) AS `user_name`,
                (SELECT `business_name` FROM `vendor_details` WHERE `user_id` = `chat`.`sent_by` LIMIT 1) AS `user_business_name`,
                (SELECT `users`.`image` FROM `users` WHERE `users`.`id` = `chat`.`sent_by`) AS `image`,
                `created_at`,
                CASE
                    WHEN DATEDIFF(UTC_TIMESTAMP, created_at) != 0 THEN CONCAT(DATEDIFF(UTC_TIMESTAMP, created_at) ,' d ago')
                    WHEN HOUR(TIMEDIFF(UTC_TIMESTAMP, created_at)) != 0 THEN CONCAT(HOUR(TIMEDIFF(UTC_TIMESTAMP, created_at)) ,' h ago')
                    WHEN MINUTE(TIMEDIFF(UTC_TIMESTAMP, created_at)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(UTC_TIMESTAMP, created_at)) ,' m ago')
                    ELSE
                    CONCAT(SECOND(TIMEDIFF(UTC_TIMESTAMP, created_at)) ,' s ago')
                END as time_since
            FROM `chat`
            WHERE $where_query 
            ORDER BY `id` DESC
            LIMIT 40) sub
            ORDER BY `id` ASC 
        ");

        foreach($messages as $key => $value) {
            $messages[$key]->image = Users::getFormattedImage($messages[$key]->image);

            if($messages[$key]->group_name == null) 
                $messages[$key]->group_name = "";

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

            $last_message_id = $messages[$key]->id;
        }        

        // Marking Message Read
        DB::update("UPDATE `chat` SET `is_read`='1' WHERE `sent_to`='$user_id' AND `sent_by`='$user_id_2'");                
        
        // Mark Chat Group Id Read
        if(empty($last_message_id) || $last_message_id == null) {
            $last_message_id = 0;
        }
        Chat::markChatGroupRead($user_id, $group_id, $last_message_id);

        return $messages;

    }

    // Message Details
    public static function getMessageDetails($user_id, $user_id_2, $group_id, $zone) {

        if($group_id==0) {
            $where_query = "(`sent_by`='$user_id' OR `sent_to`='$user_id') AND (`sent_by`='$user_id_2' OR `sent_to`='$user_id_2') AND `group_id` = '0'";
        } 
        else {
            $where_query = "group_id = '$group_id'";    
        }        

        $messages = DB::select(
            "SELECT 
                `id`, 
                `sent_by`, 
                `sent_to`,                 
                `group_id`, 
                (SELECT `name` FROM `collaborators` WHERE `id` = `chat`.`group_id`) AS `group_name`, 
                (SELECT `id` FROM `collaborators` WHERE `id` = `chat`.`group_id` AND `user_id` = '$user_id' LIMIT 1) AS `is_owner`,
                `message_type`,                 
                `chat`.`sent_by` AS `user_id`,
                (SELECT `users`.`name` FROM `users` WHERE `users`.`id` = '$user_id_2') AS `user_name`,
                (SELECT `business_name` FROM `vendor_details` WHERE `user_id` = '$user_id_2' LIMIT 1) AS `user_business_name`,
                (SELECT `users`.`image` FROM `users` WHERE `users`.`id` = '$user_id_2') AS `image`,
                (SELECT `users`.`phone_no` FROM `users` WHERE `users`.`id` = '$user_id_2') AS `phone_no`
            FROM `chat`
            WHERE $where_query 
            ORDER BY `id` DESC
            LIMIT 1
        ");

        foreach($messages as $key => $value) {
            
            if($messages[$key]->group_name == null) 
                $messages[$key]->group_name = "";

            if($messages[$key]->is_owner == null) 
                    $messages[$key]->is_owner = "0";
                else 
                    $messages[$key]->is_owner = "1";

            if($messages[$key]->user_business_name == null) 
                $messages[$key]->user_business_name = "";

            if($messages[$key]->phone_no == null) 
                $messages[$key]->phone_no = "";

            if($messages[$key]->image == null) 
                $messages[$key]->image = Users::getFormattedImage($messages[$key]->image);

        }

        if(!empty($messages[0]))
            return $messages[0];
        else {                        

            $messages = DB::select(
                "SELECT 
                    `id`, 
                    '' AS `sent_by`, 
                    '' AS `sent_to`,                 
                    '$group_id' AS `group_id`,
                    (SELECT `name` FROM `collaborators` WHERE `id` = '$group_id') AS `group_name`, 
                    (SELECT `id` FROM `collaborators` WHERE `id` = '$group_id' AND `user_id` = '$user_id' LIMIT 1) AS `is_owner`,
                    '' AS `message_type`,                 
                    '$user_id_2' AS `user_id`,
                    (SELECT `users`.`name` FROM `users` WHERE `users`.`id` = '$user_id_2') AS `user_name`,
                    (SELECT `business_name` FROM `vendor_details` WHERE `user_id` = '$user_id_2' LIMIT 1) AS `user_business_name`,
                    (SELECT `users`.`image` FROM `users` WHERE `users`.`id` = '$user_id_2') AS `image`,
                    (SELECT `users`.`phone_no` FROM `users` WHERE `users`.`id` = '$user_id_2') AS `phone_no`
                FROM `users`                                
                LIMIT 1
            ");

            foreach($messages as $key => $value) {
                
                if($messages[$key]->group_name == null) 
                    $messages[$key]->group_name = "";

                if($messages[$key]->is_owner == null) 
                    $messages[$key]->is_owner = "0";
                else 
                    $messages[$key]->is_owner = "1";

                if($messages[$key]->user_business_name == null) 
                    $messages[$key]->user_business_name = "";

                if($messages[$key]->phone_no == null) 
                    $messages[$key]->phone_no = "";

                if($messages[$key]->image == null) 
                    $messages[$key]->image = Users::getFormattedImage($messages[$key]->image);

            }

            return $messages[0];

        }
    }

    // Chat Functions

    public static function sendmessage($input) {

        $validation = Validator::make($input, Chat::$insertMessageRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {

            $access_token = $input['access_token'];
            $user_id_2 = $input['user_id_2'];            
            $message_type = $input['message_type'];		// 0 : message, 1 : image, 2 : wedding, 3 : vendor
            $group_id = isset($input['group_id']) ? $input['group_id'] : "0";
            $message = isset($input['message']) ? $input['message'] : "";
            $wedding_id = isset($input['wedding_id']) ? $input['wedding_id'] : "";
            $vendor_id = isset($input['vendor_id']) ? $input['vendor_id'] : "";
            $zone = isset($input['zone']) ? $input['zone'] : "Asia/Kolkata";
            $imgfile = Input::file('image');
            $current_time = Carbon::now();            

            if(empty($zone))
                $zone = "Asia/Kolkata";

            $user_id = Users::getUserIdByToken($access_token);

            if($message_type==1) {
            	if(empty($imgfile) || $imgfile=="")
            		return Response::json(array('status'=>0, 'msg'=>'Image Required'), 200);
            }
            else if($message_type==2) {
            	if(empty($wedding_id) || $wedding_id=="")
            		return Response::json(array('status'=>0, 'msg'=>'Wedding Id Required'), 200);
            }
			else if($message_type==3) {
				if(empty($vendor_id) || $vendor_id=="")
            		return Response::json(array('status'=>0, 'msg'=>'vendor Id Required'), 200);
			}            	
			else {
				if(empty($message) || $message=="")
            		return Response::json(array('status'=>0, 'msg'=>'Provide some text'), 200);
			}

            // Handling User Profile Image
            if($imgfile=="") {
                $image = "";
                $image_original = "";
            }
            else {
                $image = Users::uploadImage($user_id, $input);
                $image_original = $imgfile->getClientOriginalName();
            }                   

            $chat_id = DB::table('chat')->insertGetId(array(
            	'sent_by' => $user_id,
            	'sent_to' => $user_id_2,
            	'message' => $message,
            	'group_id' => $group_id,
            	'message_type' => $message_type,
            	'file_name' => $image,
            	'file_original_name' => $image_original,
            	'wedding_id' => $wedding_id,
            	'vendor_id' => $vendor_id,
            	'created_at' => $current_time,
            	'updated_at' => $current_time,
        	));

            if($group_id != 0) {

                $collaborator_members_data = DB::select("SELECT `user_id_2` FROM `collaborator_members` WHERE `collaborator_id` = '$group_id'");
                foreach ($collaborator_members_data as $key2 => $value2) {
                    $chat_group_read_data = DB::table('chat_group_read')->select('id')->where('user_id', $value2->user_id_2)->where('group_id', $group_id)->first();

                    if(empty($chat_group_read_data)) {
                        DB::table('chat_group_read')->insertGetId(array(
                            'user_id' => $value2->user_id_2,
                            'group_id' => $group_id,                            
                            'created_at' => $current_time,
                            'updated_at' => $current_time,
                        ));

                        // Mark Chat Group Id Read
                        Chat::markChatGroupRead($user_id, $group_id, $chat_id);                        
                    }
                    else {
                        // Mark Chat Group Id Read                        
                        Chat::markChatGroupRead($user_id, $group_id, $chat_id);                                               
                    }
                }

            }

            // Send Chat Push
            Notifications::chatNotifications($user_id, $user_id_2, $group_id);

            // Chat Response
            $messages = DB::select(
                "SELECT 
                    `id`, 
                    `sent_by`, 
                    `sent_to`, 
                    `message`, 
                    `group_id`,
                    '' AS `group_name`, 
                    `message_type`, 
                    `file_name` AS `chat_image`, 
                    `file_original_name` AS `chat_image_original`, 
                    `wedding_id`,
                    (SELECT `image` FROM `wedding_photos` WHERE `wedding_id` = `chat`.`wedding_id` LIMIT 1) AS `wedding_image`,
                    (SELECT `name` FROM `wedding` WHERE `id` = `chat`.`wedding_id`) AS `wedding_name`,
                    (SELECT `wedding_type` FROM `wedding` WHERE `id` = `chat`.`wedding_id`) AS `wedding_type`,
                    `vendor_id`,
                    (SELECT `image` FROM `vendor_portfolio_images` WHERE `user_id` = `chat`.`vendor_id` LIMIT 1) AS `vendor_portfolio_image`,
                    (SELECT `business_name` FROM `vendor_details` WHERE `user_id` = `chat`.`vendor_id`) AS `business_name`,
                    (SELECT `business_type` FROM `vendor_details` WHERE `user_id` = `chat`.`vendor_id`) AS `business_type`,
                    (SELECT AVG(`rating`) FROM `vendor_reviews` WHERE `vendor_id` = `chat`.`vendor_id`) AS `vendor_rating`,                
                    `chat`.`sent_by` AS `user_id`,
                    (SELECT `users`.`name` FROM `users` WHERE `users`.`id` = `chat`.`sent_by`) AS `user_name`,
                    (SELECT `business_name` FROM `vendor_details` WHERE `user_id` = `chat`.`sent_by` LIMIT 1) AS `user_business_name`,
                    (SELECT `users`.`image` FROM `users` WHERE `users`.`id` = `chat`.`sent_by`) AS `image`,
                    `created_at`,
                    CASE
                        WHEN DATEDIFF(UTC_TIMESTAMP, created_at) != 0 THEN CONCAT(DATEDIFF(UTC_TIMESTAMP, created_at) ,' d ago')
                        WHEN HOUR(TIMEDIFF(UTC_TIMESTAMP, created_at)) != 0 THEN CONCAT(HOUR(TIMEDIFF(UTC_TIMESTAMP, created_at)) ,' h ago')
                        WHEN MINUTE(TIMEDIFF(UTC_TIMESTAMP, created_at)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(UTC_TIMESTAMP, created_at)) ,' m ago')
                        ELSE
                        CONCAT(SECOND(TIMEDIFF(UTC_TIMESTAMP, created_at)) ,' s ago')
                    END as time_since
                FROM `chat`
                WHERE `id` = '$chat_id'
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
            
            return Response::json(array('status'=>1, 'msg'=>'Message Sent', 'message' => $messages[0]), 200);
        }

    }

    public static function viewmessages($input) {

        $validation = Validator::make($input, Chat::$viewmessagesRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {

            $access_token = $input['access_token'];
            $last_message_id = $input['last_message_id'];
            $user_id_2 = $input['user_id_2'];    
            $group_id = isset($input['group_id']) ? $input['group_id'] : 0;
            $zone = isset($input['zone']) ? $input['zone'] : "Asia/Kolkata";
            $user_id = Users::getUserIdByToken($access_token);

            if(empty($zone))
                $zone = "Asia/Kolkata";

            $messages = Chat::getMessages($user_id, $user_id_2, $group_id, $last_message_id, $zone);

            return Response::json(array('status'=>1, 'msg'=>'Messages', 'messages'=>$messages), 200);

        }

    }

    public static function viewPreviousMessages($input) {

        $validation = Validator::make($input, Chat::$viewmessagesRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {

            $access_token = $input['access_token'];
            $last_message_id = $input['last_message_id'];
            $user_id_2 = $input['user_id_2'];
            $group_id = isset($input['group_id']) ? $input['group_id'] : 0;
            $zone = isset($input['zone']) ? $input['zone'] : "Asia/Kolkata";
            $user_id = Users::getUserIdByToken($access_token);

            if(empty($zone))
                $zone = "Asia/Kolkata";

            $messages = Chat::getPreviousMessages($user_id, $user_id_2, $group_id, $last_message_id, $zone);            

            return Response::json(array('status'=>1, 'msg'=>'Messages', 'messages'=>$messages), 200);

        }

    }

    public static function viewCurrentMessages($input) {

        $validation = Validator::make($input, Chat::$getCurrentMessagesRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {

            $access_token = $input['access_token'];
            $user_id_2 = $input['user_id_2'];
            $group_id = isset($input['group_id']) ? $input['group_id'] : 0;
            $zone = isset($input['zone']) ? $input['zone'] : "Asia/Kolkata";
            $user_id = Users::getUserIdByToken($access_token);

            if(empty($zone))
                $zone = "Asia/Kolkata";

            $messages = Chat::getCurrentMessages($user_id, $user_id_2, $group_id, $zone);
            $message_details = Chat::getMessageDetails($user_id, $user_id_2, $group_id, $zone); 

            return Response::json(array('status'=>1, 'msg'=>'Latest 40 Messages', 'message_details'=>$message_details, 'messages'=>$messages), 200);
        }

    }

    public static function viewmessagelisting($input) {

        $validation = Validator::make($input, Chat::$accessTokenRequired);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {

            $access_token = $input['access_token'];
            $zone = isset($input['zone']) ? $input['zone'] : "Asia/Kolkata";
            $user_id = Users::getUserIdByToken($access_token);

            if(empty($zone))
                $zone = "Asia/Kolkata";

            $message_lisitng = DB::select(
                "SELECT chat.* FROM(

                    SELECT chat.* FROM(
                    
                        SELECT
                        chat.id,
                        chat.sent_to,
                        chat.sent_by,
                        chat.message,
                        chat.group_id,
                        '' AS `group_name`,
                        '' AS `group_images`,
                        '' AS `is_owner`,
                        chat.message_type,
                        chat.file_name AS `chat_image`,
                        chat.file_original_name AS `chat_image_original`,
                        `chat`.`wedding_id`, 
                        (SELECT `image` FROM `wedding_photos` WHERE `wedding_id` = `chat`.`wedding_id` LIMIT 1) AS `wedding_image`,
                        (SELECT `name` FROM `wedding` WHERE `id` = `chat`.`wedding_id`) AS `wedding_name`,
                        (SELECT `wedding_type` FROM `wedding` WHERE `id` = `chat`.`wedding_id`) AS `wedding_type`,
                        `chat`.`vendor_id`,
                        (SELECT `image` FROM `vendor_portfolio_images` WHERE `user_id` = `chat`.`vendor_id` LIMIT 1) AS `vendor_portfolio_image`,
                        (SELECT `business_name` FROM `vendor_details` WHERE `user_id` = `chat`.`vendor_id`) AS `business_name`,
                        (SELECT `business_type` FROM `vendor_details` WHERE `user_id` = `chat`.`vendor_id`) AS `business_type`,
                        (SELECT AVG(`rating`) FROM `vendor_reviews` WHERE `vendor_id` = `chat`.`vendor_id`) AS `vendor_rating`,  
                        `chat`.`sent_to` AS `user_id`,
                        (SELECT `name` FROM `users` WHERE `id` = `user_id`) AS `user_name`,
                        (SELECT `business_name` FROM `vendor_details` WHERE `user_id` = `user_id` LIMIT 1) AS `user_business_name`,
                        (SELECT `image` FROM `users` WHERE `id` = `user_id`) AS `image`,
                        (SELECT `phone_no` FROM `users` WHERE `id` = `user_id`) AS `phone_no`,
                        chat.created_at,
                        CASE
                            WHEN DATEDIFF(UTC_TIMESTAMP,chat.created_at) != 0 THEN DATE_FORMAT(chat.created_at,'%d/%m/%Y')
                            WHEN HOUR(TIMEDIFF(UTC_TIMESTAMP, chat.created_at)) != 0 THEN CONCAT(HOUR(TIMEDIFF(UTC_TIMESTAMP, chat.created_at)) ,' h')
                            WHEN MINUTE(TIMEDIFF(UTC_TIMESTAMP,chat.created_at)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(UTC_TIMESTAMP,chat.created_at)) ,' m')
                            ELSE
                            CONCAT('Now')
                        END as time_since      
                        FROM chat JOIN users ON chat.sent_to = users.id
                        WHERE chat.sent_by='$user_id'  AND `chat`.`group_id` = '0'                        

                        UNION

                        SELECT   
                        chat.id,                     
                        chat.sent_by,
                        chat.sent_to,
                        chat.message,
                        chat.group_id,
                        '' AS `group_name`,
                        '' AS `group_images`,
                        '' AS `is_owner`,
                        chat.message_type,
                        chat.file_name AS `chat_image`,
                        chat.file_original_name AS `chat_image_original`,
                        `chat`.`wedding_id`,
                        (SELECT `image` FROM `wedding_photos` WHERE `wedding_id` = `chat`.`wedding_id` LIMIT 1) AS `wedding_image`,
                        (SELECT `name` FROM `wedding` WHERE `id` = `chat`.`wedding_id`) AS `wedding_name`,
                        (SELECT `wedding_type` FROM `wedding` WHERE `id` = `chat`.`wedding_id`) AS `wedding_type`,
                        `chat`.`vendor_id`,
                        (SELECT `image` FROM `vendor_portfolio_images` WHERE `user_id` = `chat`.`vendor_id` LIMIT 1) AS `vendor_portfolio_image`,
                        (SELECT `business_name` FROM `vendor_details` WHERE `user_id` = `chat`.`vendor_id`) AS `business_name`,
                        (SELECT `business_type` FROM `vendor_details` WHERE `user_id` = `chat`.`vendor_id`) AS `business_type`,
                        (SELECT AVG(`rating`) FROM `vendor_reviews` WHERE `vendor_id` = `chat`.`vendor_id`) AS `vendor_rating`,            
                        `chat`.`sent_by` AS `user_id`,
                        (SELECT `name` FROM `users` WHERE `id` = `user_id`) AS `user_name`,
                        (SELECT `business_name` FROM `vendor_details` WHERE `user_id` = `user_id` LIMIT 1) AS `user_business_name`,
                        (SELECT `image` FROM `users` WHERE `id` = `user_id`) AS `image`,
                        (SELECT `phone_no` FROM `users` WHERE `id` = `user_id`) AS `phone_no`,
                        chat.created_at,
                        CASE
                            WHEN DATEDIFF(UTC_TIMESTAMP,chat.created_at) != 0 THEN DATE_FORMAT(chat.created_at,'%d/%m/%Y')
                            WHEN HOUR(TIMEDIFF(UTC_TIMESTAMP, chat.created_at)) != 0 THEN CONCAT(HOUR(TIMEDIFF(UTC_TIMESTAMP, chat.created_at)) ,' h')
                            WHEN MINUTE(TIMEDIFF(UTC_TIMESTAMP,chat.created_at)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(UTC_TIMESTAMP,chat.created_at)) ,' m')
                            ELSE
                            CONCAT('Now')
                        END as time_since
                        FROM chat JOIN users ON chat.sent_by = users.id
                        WHERE chat.sent_to='$user_id' AND `chat`.`group_id` = '0'
                    ) AS chat

                ORDER BY created_at DESC) AS chat
                GROUP BY chat.sent_to
                ORDER BY created_at DESC
            ");             

            $message_listing_group = DB::select(
                "SELECT `temp`.* FROM (
                    SELECT
                        chat.id,
                        chat.sent_to,
                        chat.sent_by,
                        chat.message,
                        chat.message_type,
                        chat.group_id,
                        (SELECT `name` FROM `collaborators` WHERE `id` = `chat`.`group_id` LIMIT 1) AS `group_name`,
                        (SELECT GROUP_CONCAT( (SELECT `image` FROM `users` WHERE `id` = `collaborator_members`.`user_id_2`) SEPARATOR ',') FROM `collaborator_members` WHERE `collaborator_id` = `chat`.`group_id`) AS group_images,
                        (SELECT `id` FROM `collaborators` WHERE `id` = `chat`.`group_id` AND `user_id` = '$user_id' LIMIT 1) AS `is_owner`,
                        chat.file_name AS `chat_image`,
                        chat.file_original_name AS `chat_image_original`,
                        `chat`.`wedding_id`,
                        (SELECT `image` FROM `wedding_photos` WHERE `wedding_id` = `chat`.`wedding_id` LIMIT 1) AS `wedding_image`,
                        (SELECT `name` FROM `wedding` WHERE `id` = `chat`.`wedding_id`) AS `wedding_name`,
                        (SELECT `wedding_type` FROM `wedding` WHERE `id` = `chat`.`wedding_id`) AS `wedding_type`,
                        `chat`.`vendor_id`,
                        (SELECT `image` FROM `vendor_portfolio_images` WHERE `user_id` = `chat`.`vendor_id` LIMIT 1) AS `vendor_portfolio_image`,
                        (SELECT `business_name` FROM `vendor_details` WHERE `user_id` = `chat`.`vendor_id`) AS `business_name`,
                        (SELECT `business_type` FROM `vendor_details` WHERE `user_id` = `chat`.`vendor_id`) AS `business_type`,
                        (SELECT AVG(`rating`) FROM `vendor_reviews` WHERE `vendor_id` = `chat`.`vendor_id`) AS `vendor_rating`,               
                        `chat`.`sent_by` AS `user_id`,                     
                        (SELECT `name` FROM `users` WHERE `id` = `user_id` LIMIT 1) AS `user_name`,
                        (SELECT `business_name` FROM `vendor_details` WHERE `user_id` = `user_id` LIMIT 1) AS `user_business_name`,
                        (SELECT `image` FROM `users` WHERE `id` = `user_id` LIMIT 1) AS `image`,
                        (SELECT `phone_no` FROM `users` WHERE `id` = `user_id` LIMIT 1) AS `phone_no`,
                        `chat`.`created_at`,                     
                        CASE
                            WHEN DATEDIFF(UTC_TIMESTAMP,chat.created_at) != 0 THEN DATE_FORMAT(chat.created_at,'%d/%m/%Y')
                            WHEN HOUR(TIMEDIFF(UTC_TIMESTAMP, chat.created_at)) != 0 THEN CONCAT(HOUR(TIMEDIFF(UTC_TIMESTAMP, chat.created_at)) ,' h')
                            WHEN MINUTE(TIMEDIFF(UTC_TIMESTAMP,chat.created_at)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(UTC_TIMESTAMP,chat.created_at)) ,' m')
                            ELSE
                            CONCAT('Now')
                        END as time_since                        
                    FROM `chat` 
                    JOIN `collaborator_members` ON `collaborator_members`.`collaborator_id` = `chat`.`group_id`
                    WHERE `collaborator_members`.`user_id_2` = '$user_id'
                    ORDER BY `chat`.`id` DESC) AS `temp`
                    GROUP BY `temp`.`group_id`
            ");

            $mergerd_arr = array_merge($message_lisitng, $message_listing_group);            
            $all_message_listing = array_merge_recursive($message_lisitng, $message_listing_group);

            foreach($all_message_listing as $key => $value) {               

                if($all_message_listing[$key]->image==null)
                    $all_message_listing[$key]->image = "";                
                else                    
                    $all_message_listing[$key]->image = Users::getFormattedImage($all_message_listing[$key]->image);

                // Images Group
                if($all_message_listing[$key]->group_images==null || $all_message_listing[$key]->group_images=="")
                    $all_message_listing[$key]->group_images = "";                
                else {
                    $group_images_arr = explode(',', $all_message_listing[$key]->group_images);
                    foreach ($group_images_arr as $key2 => $value2) {
                        if($key2<3)
                            $group_images_arr_2[] = Users::getFormattedImage($group_images_arr[$key2]);    
                    }                    
                    $all_message_listing[$key]->group_images = $group_images_arr_2;
                    unset($group_images_arr_2);     
                }

                if($all_message_listing[$key]->is_owner == null) 
                    $all_message_listing[$key]->is_owner = "0";
                else 
                    $all_message_listing[$key]->is_owner = "1";

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

                if($all_message_listing[$key]->phone_no == null) 
                    $all_message_listing[$key]->phone_no = "";

                $date = new DateTime($all_message_listing[$key]->created_at);
                $date->setTimezone(new \DateTimeZone($zone)); 
                $all_message_listing[$key]->created_at = $date->format('Y-m-d h:m:s');
                $all_message_listing[$key]->date = $date->format('d M Y');
                $all_message_listing[$key]->time = $date->format('H:i A');

            }

            return Response::json(array('status'=>1, 'msg'=>'Chat Message Listing', 'message_listing'=>$all_message_listing), 200);
        }

    }

}
