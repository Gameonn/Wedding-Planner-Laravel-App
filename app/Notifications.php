<?php namespace App;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Davibennun\LaravelPushNotification\Facades\PushNotification;

class Notifications extends Model {

	// Validation Rules

	public static $accessTokenRequired = array(
        'access_token' => 'required|exists:users,access_token',
    );

    public static $markReadNotificationsRules = array(
        'access_token' => 'required|exists:users,access_token',
        'notification_id' => 'required',
    );    

    public static $markReadBadgeNotificationsRules = array(
        'access_token' => 'required|exists:users,access_token',
        'clear_type' => 'required',
    );        

//************************************************************************************************
//                                      Common Functions
//************************************************************************************************ 

    public static function sendUserPush($message, $user_id) {

        $user_detail = DB::table('users')->select('device_token', 'reg_id')->where('id', $user_id)->first();

        if(!empty($user_detail->device_token)) {

            $send = PushNotification::app('appNameIOS')
                ->to($user_detail->device_token)
                ->send($message);        

            // dd($send);
        }        
        
        if(!empty($user_detail->reg_id)) {

            $send = PushNotification::app('appNameAndroid')
                ->to($user_detail->reg_id)
                ->send($message);        

            // dd($send);  
        }        

    }

    public static function sendVendorPush($message, $user_id) {

        $user_detail = DB::table('users')->select('device_token', 'reg_id')->where('id', $user_id)->first();

        if(!empty($user_detail->device_token)) {

            $send = PushNotification::app('appNameIOS2')
                ->to($user_detail->device_token)
                ->send($message);        
 
            // dd($send);
        }        
        
        if(!empty($user_detail->reg_id)) {

            $send = PushNotification::app('appNameAndroid2')
                ->to($user_detail->reg_id)
                ->send($message);        

            // dd($send);
        }        

    }    

    public static function sendGroupPush($badge_count, $notif_msg_2, $notif_type, $user_id, $user_id_2, $user_who_made_name, $group_id, $unread_notification_count, $unread_chat_msgs_count, $unread_conceirge_msgs_count) {

        $collaborator_members_data = DB::select(
            "SELECT `user_id_2`            
            FROM `collaborator_members`
            WHERE `collaborator_id` = '$group_id' AND `user_id_2` != '$user_id'
        ");

        foreach ($collaborator_members_data as $key => $value) {
            
            // Setting Up Push
            $message = PushNotification::Message($notif_msg_2, array(
                'badge' => $badge_count,
                'sound' => 'example.aiff',

                'locArgs' => array(
                    't' => $notif_type,
                    'u1' => $user_id,
                    'u2' => $value->user_id_2,                
                    'u2n' => $user_who_made_name,
                    'g' => $group_id,
                    'unc' => $unread_notification_count,
                    'umc' => $unread_chat_msgs_count,
                    'ucmc' => $unread_conceirge_msgs_count,
                ),
            )); 

            // Send Push
            Notifications::sendUserPush($message, $value->user_id_2);

        }         

    }

    public static function getUserName($user_id) {

        $user_detail = DB::table('users')->select('name')->where('id', $user_id)->first();
        return $user_detail->name;

    }  

    public static function viewNotifications($user_id) {

        $notification_listing = DB::select(  
            "SELECT `id`,
            `user_who_made_id`,
            (SELECT `name` FROM `users` WHERE `id` = `user_who_made_id` LIMIT 1) AS `user_who_made_name`,
            `user_who_received_id`,
            (SELECT `name` FROM `users` WHERE `id` = `user_who_received_id` LIMIT 1) AS `user_who_received_name`,
            `user_id`,            
            (SELECT `id` FROM `wedding` WHERE `user_id` = `notifications`.`user_who_received_id` LIMIT 1) AS `wedding_id`,
            (SELECT `name` FROM `wedding` WHERE `user_id` = `notifications`.`user_who_received_id` LIMIT 1) AS `wedding_name`,
            (SELECT `id` FROM `wedding` WHERE `user_id` = `notifications`.`user_id` LIMIT 1) AS `wedding_id_2`,
            (SELECT `name` FROM `wedding` WHERE `user_id` = `notifications`.`user_id` LIMIT 1) AS `wedding_name_2`,
            `vendor_id`,
            (SELECT `business_name` FROM `vendor_details` WHERE `user_id` = `notifications`.`vendor_id` LIMIT 1) AS `vendor_business_name`,
            (SELECT `business_type` FROM `vendor_details` WHERE `user_id` = `notifications`.`vendor_id` LIMIT 1) AS `vendor_business_type`,
            `image`,
            `notif_type`,
            `review_id`,
            `favorite_id`,
            `contract_id`,
            `collaborator_id`,
            `notif_msg`,
            `is_read`,                
            `created_at`,
            CASE
                WHEN DATEDIFF(UTC_TIMESTAMP, created_at) != 0 THEN CONCAT(DATEDIFF(UTC_TIMESTAMP, created_at) ,' d ago')
                WHEN HOUR(TIMEDIFF(UTC_TIMESTAMP, created_at)) != 0 THEN CONCAT(HOUR(TIMEDIFF(UTC_TIMESTAMP, created_at)) ,' h ago')
                WHEN MINUTE(TIMEDIFF(UTC_TIMESTAMP, created_at)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(UTC_TIMESTAMP, created_at)) ,' m ago')
                ELSE
                CONCAT(SECOND(TIMEDIFF(UTC_TIMESTAMP, created_at)) ,' s ago')
            END as time_since                
            FROM `notifications`
            WHERE `user_who_received_id` = '$user_id'
        ");            

        foreach ($notification_listing as $key => $value) {
            
            if($notification_listing[$key]->wedding_id == null || $notification_listing[$key]->wedding_id == "")
                $notification_listing[$key]->wedding_id = "";

            if($notification_listing[$key]->wedding_name == null || $notification_listing[$key]->wedding_name == "")
                $notification_listing[$key]->wedding_name = "";                

            if($notification_listing[$key]->vendor_business_name == null || $notification_listing[$key]->vendor_business_name == "")
                $notification_listing[$key]->vendor_business_name = "";                                                

            if($notification_listing[$key]->vendor_business_type == null || $notification_listing[$key]->vendor_business_type == "")
                $notification_listing[$key]->vendor_business_type = "";                                                

            if($notification_listing[$key]->image == null || $notification_listing[$key]->image == "")
                $notification_listing[$key]->image = "";
            else 
                $notification_listing[$key]->image = Users::getFormattedImage($notification_listing[$key]->image);

            // Manage Notification Response
            if($notification_listing[$key]->notif_type == 'request_feedback')
                $notification_listing[$key]->notif_type = "1";
            else if($notification_listing[$key]->notif_type == 'write_review')
                $notification_listing[$key]->notif_type = "2";
            else if($notification_listing[$key]->notif_type == 'made_favorite_wedding')
                $notification_listing[$key]->notif_type = "3";
            else if($notification_listing[$key]->notif_type == 'made_favorite_vendor')
                $notification_listing[$key]->notif_type = "4";
            else if($notification_listing[$key]->notif_type == 'create_contract')
                $notification_listing[$key]->notif_type = "5";
            else if($notification_listing[$key]->notif_type == 'change_contract_status_y')
                $notification_listing[$key]->notif_type = "6";
            else if($notification_listing[$key]->notif_type == 'change_contract_status_n')
                $notification_listing[$key]->notif_type = "7";
            else if($notification_listing[$key]->notif_type == 'add_member')
                $notification_listing[$key]->notif_type = "8";
            else if($notification_listing[$key]->notif_type == 'event_ended')
                $notification_listing[$key]->notif_type = "12";
            else 
                $notification_listing[$key]->notif_type = "0";  

        }

        return $notification_listing;

    }

    public static function countUnreadNotification($user_id) {

        $unread_notification_data = DB::select("SELECT count(`id`) AS `unread_count` FROM `notifications` WHERE `user_who_received_id` = $user_id AND `is_read_2` = '0'");        
        $unread_notification_count = $unread_notification_data[0]->unread_count; 

        $unread_notification_count = strval($unread_notification_count);
        // dd($unread_notification_count);
        return $unread_notification_count;

    }

    public static function countUnreadChatMessages($user_id) {

        $val1 = DB::select("SELECT count(`id`) AS `count` FROM `chat` WHERE `sent_to` = '$user_id' AND `is_read` = '0'");   

        $val2 = DB::select(
            "SELECT `collaborator_id`,
            (SELECT count(`id`) AS `count` 
                FROM `chat` 
                WHERE `group_id` = `collaborator_members`.`collaborator_id` AND 
                `id` > (SELECT `chat_id` FROM `chat_group_read` WHERE `user_id` = '$user_id' AND `group_id` = `collaborator_members`.`collaborator_id`)) AS `count`
            FROM `collaborator_members` 
            WHERE `user_id_2` = '$user_id'
        ");
        $tot_count = 0;
        foreach ($val2 as $key => $value) {
            $tot_count = $tot_count + $value->count;
        }

        $unread_1_1_count = $val1[0]->count; // 1 to 1 unread msgs        

        $total_unread_msgs = $unread_1_1_count + $tot_count;

        if($total_unread_msgs == null) 
            return $total_unread_msgs = "";
        else
            return $total_unread_msgs = strval($total_unread_msgs);

    }

    // public static function countUnreadChatMessages2($user_id) {

    //     $val1 = DB::select("SELECT count(`id`) AS `count` FROM `chat` WHERE `sent_to` = '$user_id' AND `is_read_2` = '0'");   

    //     $val2 = DB::select(
    //         "SELECT count(`id`) AS `count`
    //         FROM `chat` 
    //         WHERE `id` > (SELECT `chat_id_2` FROM `chat_group_read` WHERE `user_id` = '$user_id' ORDER BY `id` DESC LIMIT 1) AND `sent_to` = '$user_id' AND `group_id` != '0'
    //     ");
        
    //     $unread_1_1_count = $val1[0]->count; // 1 to 1 unread msgs        
    //     $unread_group_count = $val2[0]->count;

    //     var_dump($unread_1_1_count);
    //     var_dump($unread_group_count);

    //     $total_unread_msgs = $unread_1_1_count + $unread_group_count;

    //     if($total_unread_msgs == null) 
    //         return $total_unread_msgs = "0";
    //     else
    //         return $total_unread_msgs = strval($total_unread_msgs);

    // }

    public static function countUnreadChatMessages2($user_id) {

        $val1 = DB::select("SELECT count(`id`) AS `count` FROM `chat` WHERE `sent_to` = '$user_id' AND `is_read_2` = '0'");   

        $val2 = DB::select(
            "SELECT `collaborator_id`,
            (SELECT count(`id`) AS `count` 
                FROM `chat` 
                WHERE `group_id` = `collaborator_members`.`collaborator_id` AND 
                `id` > (SELECT `chat_id_2` FROM `chat_group_read` WHERE `user_id` = '$user_id' AND `group_id` = `collaborator_members`.`collaborator_id`)) AS `count`
            FROM `collaborator_members` 
            WHERE `user_id_2` = '$user_id'
        ");
        $tot_count = 0;
        foreach ($val2 as $key => $value) {
            $tot_count = $tot_count + $value->count;
        }

        $unread_1_1_count = $val1[0]->count; // 1 to 1 unread msgs        
        $total_unread_msgs = $unread_1_1_count + $tot_count;  

        // var_dump($unread_1_1_count);
        // var_dump($tot_count);              
        

        if($total_unread_msgs == null) 
            return $total_unread_msgs = "0";
        else
            return $total_unread_msgs = strval($total_unread_msgs);

    }

    public static function countUnreadConceirgeMessages($user_id) {

        $val2 = DB::select("SELECT count(`id`) AS `count` FROM `conceirge` WHERE `sent_to` = '$user_id' AND `is_read` = '0'");  
        $unread_conceirge_count = $val2[0]->count;  // Conceirge unread msgs

        if($unread_conceirge_count == null)
            $unread_conceirge_count = "";

        return $unread_conceirge_count;

    }

    public static function countUnreadConceirgeMessages2($user_id) {

        $val2 = DB::select("SELECT count(`id`) AS `count` FROM `conceirge` WHERE `sent_to` = '$user_id' AND `is_read_2` = '0'");  
        $unread_conceirge_count = $val2[0]->count;  // Conceirge unread msgs

        if($unread_conceirge_count == null)
            $unread_conceirge_count = "";

        return $unread_conceirge_count;

    }

    public static function updateChatGroupReadBadge($user_id) {

        $chat_data = DB::table('chat')->select('id')->where('sent_to', $user_id)->orderBy('id', 'desc')->first();

        if(!empty($chat_data))
            DB::table('chat_group_read')->where('user_id', $user_id)->update(['chat_id_2' => $chat_data->id]);
        
    }

    

//************************************************************************************************
//                                      Notification Functions
//************************************************************************************************    

    // Request Feedback Notification -- 1
    public static function requestFeedbackNotification($vendor_id, $user_id) {

        $current_time = Carbon::now();

        $user_data = DB::select(
            "SELECT `id`, `name`,
            (SELECT `image` FROM `vendor_portfolio_images` WHERE `user_id` = '$vendor_id' LIMIT 1) AS `vendor_portfolio_image`
            FROM `users`
            WHERE `id` = '$vendor_id'
        ");        

        $notif_msg = "Requested you to provide a feedback.";

        $notification_id = DB::table('notifications')->insertGetId(array(
            'user_who_made_id' => $vendor_id,
            'user_who_received_id' => $user_id,
            'user_id' => $user_id,
            'vendor_id' => $vendor_id,
            'image' => $user_data[0]->vendor_portfolio_image,
            'notif_type' => 'request_feedback',
            'notif_msg' => $notif_msg,
            'created_at' => $current_time,
            'updated_at' => $current_time,
        ));

        $notification_id = strval($notification_id);

        $user_who_made_name = Notifications::getUserName($vendor_id);  

        $notif_msg_2 = $user_who_made_name." requested you to provide a feedback.";   

        // Count Unread Notification
        $unread_notification_count = Notifications::countUnreadNotification($user_id);          
        $unread_chat_msgs_count = Notifications::countUnreadChatMessages2($user_id);  
        $unread_conceirge_msgs_count = Notifications::countUnreadConceirgeMessages2($user_id);  
        $badge_count = $unread_notification_count + $unread_chat_msgs_count + $unread_conceirge_msgs_count;      
        $badge_count = strval($badge_count);

        // Setting Up Push
        $message = PushNotification::Message($notif_msg_2, array(
            'badge' => $badge_count,
            'sound' => 'example.aiff',

            'locArgs' => array(
                't' => "1",
                'u1' => $vendor_id,
                'u2' => $user_id,
                'u2n' => $user_who_made_name,
                'nid' => $notification_id,
                'unc' => $unread_notification_count,
                'umc' => $unread_chat_msgs_count,
                'ucmc' => $unread_conceirge_msgs_count,
            ),
        ));        

        // Send Push
        Notifications::sendUserPush($message, $user_id);                

    }

    // Write Review Notification -- 2
    public static function writeReviewNotification($user_id, $vendor_id, $rating, $vendor_reviews_id) {            

        $current_time = Carbon::now();

        $user_data = DB::select(
            "SELECT `id`, `name`, `image`            
            FROM `users`
            WHERE `id` = '$user_id'
        "); 

        $notif_msg = 'Rated you '.$rating;

        $notification_id = DB::table('notifications')->insertGetId(array(
            'user_who_made_id' => $user_id,
            'user_who_received_id' => $vendor_id,
            'user_id' => $user_id,
            'vendor_id' => $vendor_id,
            'image' => $user_data[0]->image,
            'notif_type' => 'write_review',
            'review_id' => $vendor_reviews_id,
            'notif_msg' => $notif_msg,
            'created_at' => $current_time,
            'updated_at' => $current_time,
        ));    

        $notification_id = strval($notification_id);

        $user_who_made_name = Notifications::getUserName($user_id);

        // Count Unread Notification
        $unread_notification_count = Notifications::countUnreadNotification($vendor_id);  
        $unread_chat_msgs_count = Notifications::countUnreadChatMessages2($vendor_id);  
        $unread_conceirge_msgs_count = Notifications::countUnreadConceirgeMessages2($vendor_id);  
        $badge_count = $unread_notification_count + $unread_chat_msgs_count + $unread_conceirge_msgs_count;
        $badge_count = strval($badge_count);

        $notif_msg_2 = $user_who_made_name.' rated you '.$rating;

        // Setting Up Push
        $message = PushNotification::Message($notif_msg_2, array(
            'badge' => $badge_count,
            'sound' => 'example.aiff',

            'locArgs' => array(
                't' => "2",
                'u1' => $user_id,
                'u2' => $vendor_id,
                'u2n' => $user_who_made_name,
                'nid' => $notification_id,
                'unc' => $unread_notification_count,
                'umc' => $unread_chat_msgs_count,
                'ucmc' => $unread_conceirge_msgs_count,
            ),
        ));        

        // Send Push
        Notifications::sendVendorPush($message, $vendor_id); 

    }

    // Make Favorite Wedding Notification -- 3
    public static function makeFavoriteWeddingNotification($user_id, $wedding_id, $favorite_id) {

        $current_time = Carbon::now();

        $user_data = DB::select(
            "SELECT `id`, `name`, `image`,
            (SELECT `user_id` FROM `wedding` WHERE `id` = '$wedding_id') AS `user_id_2`
            FROM `users`
            WHERE `id` = '$user_id'
        "); 

        $notif_msg = 'Favouritised your wedding.';

        $notification_id = DB::table('notifications')->insertGetId(array(
            'user_who_made_id' => $user_id,
            'user_who_received_id' => $user_data[0]->user_id_2,
            'user_id' => $user_id,            
            'image' => $user_data[0]->image,
            'notif_type' => 'made_favorite_wedding',
            'favorite_id' => $favorite_id,
            'notif_msg' => $notif_msg,
            'created_at' => $current_time,
            'updated_at' => $current_time,
        ));  

        $notification_id = strval($notification_id);

        $user_who_made_name = Notifications::getUserName($user_id);

        // Count Unread Notification
        $unread_notification_count = Notifications::countUnreadNotification($user_data[0]->user_id_2);  
        $unread_chat_msgs_count = Notifications::countUnreadChatMessages2($user_data[0]->user_id_2);  
        $unread_conceirge_msgs_count = Notifications::countUnreadConceirgeMessages2($user_data[0]->user_id_2);  
        $badge_count = $unread_notification_count + $unread_chat_msgs_count + $unread_conceirge_msgs_count;
        $badge_count = strval($badge_count);

        $notif_msg_2 = $user_who_made_name.' favouritised your wedding.';

        // Setting Up Push
        $message = PushNotification::Message($notif_msg_2, array(
            'badge' => $badge_count,
            'sound' => 'example.aiff',

            'locArgs' => array(
                't' => "3",
                'u1' => $user_id,
                'u2' => $user_data[0]->user_id_2,
                'u2n' => $user_who_made_name,
                'w' => $wedding_id,
                'nid' => $notification_id,
                'unc' => $unread_notification_count,
                'umc' => $unread_chat_msgs_count,
                'ucmc' => $unread_conceirge_msgs_count,
            ),
        ));                

        // Send Push
        Notifications::sendUserPush($message, $user_data[0]->user_id_2);  

    }

    // Make Favorite Vendor Notification -- 4
    public static function makeFavoriteVendorNotification($user_id, $vendor_id, $favorite_id) {

        $current_time = Carbon::now();

        $user_data = DB::select(
            "SELECT `id`, `name`, `image`
            FROM `users`
            WHERE `id` = '$user_id'
        "); 

        $notif_msg = 'Favouritised you.';

        $notification_id = DB::table('notifications')->insertGetId(array(
            'user_who_made_id' => $user_id,
            'user_who_received_id' => $vendor_id,
            'user_id' => $user_id,
            'vendor_id' => $vendor_id,
            'image' => $user_data[0]->image,
            'notif_type' => 'made_favorite_vendor',
            'favorite_id' => $favorite_id,
            'notif_msg' => $notif_msg,
            'created_at' => $current_time,
            'updated_at' => $current_time,
        ));   

        $notification_id = strval($notification_id);

        $user_who_made_name = Notifications::getUserName($user_id);

        // Count Unread Notification
        $unread_notification_count = Notifications::countUnreadNotification($vendor_id);           
        $unread_chat_msgs_count = Notifications::countUnreadChatMessages2($vendor_id);  
        $unread_conceirge_msgs_count = Notifications::countUnreadConceirgeMessages2($vendor_id);  
        $badge_count = $unread_notification_count + $unread_chat_msgs_count + $unread_conceirge_msgs_count;                   
        $badge_count = strval($badge_count);

        $notif_msg_2 = $user_who_made_name.' favouritised you.';

        // Setting Up Push
        $message = PushNotification::Message($notif_msg_2, array(
            'badge' => $badge_count,
            'sound' => 'example.aiff',

            'locArgs' => array(
                't' => "4",
                'u1' => $user_id,
                'u2' => $vendor_id,
                'u2n' => $user_who_made_name,
                'v' => $vendor_id,
                'nid' => $notification_id,
                'unc' => $unread_notification_count,
                'umc' => $unread_chat_msgs_count,
                'ucmc' => $unread_conceirge_msgs_count,
            ),
        ));        

        // Send Push
        Notifications::sendVendorPush($message, $vendor_id);

    }

    // Create Contract Notification -- 5
    public static function createContractNotification($user_id, $vendor_id, $contract_id) {

        $current_time = Carbon::now();

        $user_data = DB::select(
            "SELECT `id`, `name`, `image`
            FROM `users`
            WHERE `id` = '$user_id'
        "); 

        $notif_msg = 'Requested you to confirm the deal.';

        $notification_id = DB::table('notifications')->insertGetId(array(
            'user_who_made_id' => $user_id,
            'user_who_received_id' => $vendor_id,
            'user_id' => $user_id,
            'vendor_id' => $vendor_id,
            'image' => $user_data[0]->image,
            'notif_type' => 'create_contract',
            'contract_id' => $contract_id,
            'notif_msg' => $notif_msg,
            'created_at' => $current_time,
            'updated_at' => $current_time,
        ));

        $notification_id = strval($notification_id);

        $user_who_made_name = Notifications::getUserName($user_id);

        // Count Unread Notification
        $unread_notification_count = Notifications::countUnreadNotification($vendor_id);                
        $unread_chat_msgs_count = Notifications::countUnreadChatMessages2($vendor_id);  
        $unread_conceirge_msgs_count = Notifications::countUnreadConceirgeMessages2($vendor_id);  
        $badge_count = $unread_notification_count + $unread_chat_msgs_count + $unread_conceirge_msgs_count;                   
        $badge_count = strval($badge_count);

        $notif_msg_2 = $user_who_made_name.' requested you to confirm the deal.';

        // Setting Up Push
        $message = PushNotification::Message($notif_msg_2, array(
            'badge' => $badge_count,
            'sound' => 'example.aiff',

            'locArgs' => array(
                't' => "5",
                'u1' => $user_id,
                'u2' => $vendor_id,
                'u2n' => $user_who_made_name,
                'c' => $contract_id,
                'nid' => $notification_id,
                'unc' => $unread_notification_count,
                'umc' => $unread_chat_msgs_count,
                'ucmc' => $unread_conceirge_msgs_count,
            ),
        ));                

        // Send Push
        Notifications::sendVendorPush($message, $vendor_id);   

    }

    // Change Contract Status Notification -- 6 & 7
    public static function changeContractStatusNotification($contract_id, $vendor_id, $status) {

        $current_time = Carbon::now();

        $user_data = DB::select(
            "SELECT `id`, `name`, `image`,
            (SELECT `user_id` FROM `wedding_vendor` WHERE `id` = '$contract_id') AS `user_id`
            FROM `users`
            WHERE `id` = '$vendor_id'
        "); 

        if($status==1) {
            $notif_type = "change_contract_status_y";
            $notif_msg = "Accepted your deal";
            $notif_type = "6";
        }
        else {
            $notif_type = "change_contract_status_n";
            $notif_msg = "Declined your deal";
            $notif_type = "7";
        }

        $notification_id = DB::table('notifications')->insertGetId(array(
            'user_who_made_id' => $vendor_id,
            'user_who_received_id' => $user_data[0]->user_id,
            'user_id' => $user_data[0]->user_id,
            'vendor_id' => $vendor_id,
            'image' => $user_data[0]->image,
            'notif_type' => $notif_type,
            'contract_id' => $contract_id,
            'notif_msg' => $notif_msg,
            'created_at' => $current_time,
            'updated_at' => $current_time,
        ));   

        $notification_id = strval($notification_id);
        $notif_type = strval($notif_type);

        $user_who_made_name = Notifications::getUserName($vendor_id);

        // Count Unread Notification
        $unread_notification_count = Notifications::countUnreadNotification($user_data[0]->user_id);             
        $unread_chat_msgs_count = Notifications::countUnreadChatMessages2($user_data[0]->user_id);  
        $unread_conceirge_msgs_count = Notifications::countUnreadConceirgeMessages2($user_data[0]->user_id);  
        $badge_count = $unread_notification_count + $unread_chat_msgs_count + $unread_conceirge_msgs_count;
        $badge_count = strval($badge_count);

        $notif_msg_2 = $user_who_made_name.' '.$notif_msg;

        // Setting Up Push
        $message = PushNotification::Message($notif_msg_2, array(
            'badge' => $badge_count,
            'sound' => 'example.aiff',

            'locArgs' => array(
                't' => $notif_type,
                'u1' => $vendor_id,
                'u2' => $user_data[0]->user_id,
                'u2n' => $user_who_made_name,
                'c' => $contract_id,
                'nid' => $notification_id,
                'unc' => $unread_notification_count,
                'umc' => $unread_chat_msgs_count,
                'ucmc' => $unread_conceirge_msgs_count,
            ),
        ));        

        // Send Push
        Notifications::sendUserPush($message, $user_data[0]->user_id);   

    }

    // Collaborator Add Member Notification -- 8
    public static function collaboratorAddMemberNotification($user_id, $user_id_2, $collaborator_id) {

        $current_time = Carbon::now();

        $user_data = DB::select(
            "SELECT `id`, `name`, `image`, 
            (SELECT `name` FROM `collaborators` WHERE `id` = '$collaborator_id') AS `collaborator_name`           
            FROM `users`
            WHERE `id` = '$user_id'
        ");      

        $notif_msg = 'Added you in '.$user_data[0]->collaborator_name.' group';  

        $notification_id = DB::table('notifications')->insertGetId(array(
            'user_who_made_id' => $user_id,
            'user_who_received_id' => $user_id_2,
            'image' => $user_data[0]->image,
            'notif_type' => 'add_member',
            'collaborator_id' => $collaborator_id,
            'notif_msg' => $notif_msg,
            'created_at' => $current_time,
            'updated_at' => $current_time,
        ));

        $notification_id = strval($notification_id);

        $user_who_made_name = Notifications::getUserName($user_id);

        // Count Unread Notification
        $unread_notification_count = Notifications::countUnreadNotification($user_id_2);               
        $unread_chat_msgs_count = Notifications::countUnreadChatMessages2($user_id_2);  
        $unread_conceirge_msgs_count = Notifications::countUnreadConceirgeMessages2($user_id_2);  
        $badge_count = $unread_notification_count + $unread_chat_msgs_count + $unread_conceirge_msgs_count;     
        $badge_count = strval($badge_count);

        $notif_msg_2 = $user_who_made_name.' added you in '.$user_data[0]->collaborator_name.' group';

        // Setting Up Push
        $message = PushNotification::Message($notif_msg_2, array(
            'badge' => $badge_count,
            'sound' => 'example.aiff',

            'locArgs' => array(
                't' => "8",
                'u1' => $user_id,
                'u2' => $user_id_2,
                'u2n' => $user_who_made_name,
                'nid' => $notification_id,
                'unc' => $unread_notification_count,
                'umc' => $unread_chat_msgs_count,
                'ucmc' => $unread_conceirge_msgs_count,
            ),
        ));        

        // Send Push
        Notifications::sendUserPush($message, $user_id_2);

    }

    // Chat Notification -- 9 and 10
    public static function chatNotifications($user_id, $user_id_2, $group_id) {

        $notif_msg = "Sent you a message";

        if($group_id == 0)
            $notif_type = 9;
        else 
            $notif_type = 10;
        
        $notif_type = strval($notif_type); 

        $user_who_made_name = Notifications::getUserName($user_id_2);

        // Count Unread Notification
        $unread_notification_count = Notifications::countUnreadNotification($user_id_2);           
        $unread_chat_msgs_count = Notifications::countUnreadChatMessages2($user_id_2);  
        $unread_conceirge_msgs_count = Notifications::countUnreadConceirgeMessages2($user_id_2);  
        $badge_count = $unread_notification_count + $unread_chat_msgs_count + $unread_conceirge_msgs_count;
        $badge_count = strval($badge_count);

        $notif_msg_2 = $user_who_made_name.' sent you a message';

        if($group_id != 0) {
            Notifications::sendGroupPush($badge_count, $notif_msg_2, $notif_type, $user_id, $user_id_2, $user_who_made_name, $group_id, $unread_notification_count, $unread_chat_msgs_count, $unread_conceirge_msgs_count);
            // return Notifications::sendGroupPush($user_id, $group_id, $notif_msg_2);
        }
        else {

            // Setting Up Push
            $message = PushNotification::Message($notif_msg_2, array(
                'badge' => $badge_count,
                'sound' => 'example.aiff',

                'locArgs' => array(
                    't' => $notif_type,
                    'u1' => $user_id,
                    'u2' => $user_id_2,                
                    'u2n' => $user_who_made_name,
                    'g' => $group_id,
                    'unc' => $unread_notification_count,
                    'umc' => $unread_chat_msgs_count,
                    'ucmc' => $unread_conceirge_msgs_count,
                ),
            )); 

            $user_detail = DB::table('users')->select('user_role')->where('id', $user_id_2)->first();
        
            // Send Push
            if($user_detail->user_role == '0' || $user_detail->user_role == '2')        
                Notifications::sendUserPush($message, $user_id_2);
            else 
                Notifications::sendVendorPush($message, $user_id_2);
        }        

    }

    // Chat Notification -- 11
    public static function conceirgeChatNotifications($user_id, $user_id_2) {        

        // Count Unread Notification
        $unread_notification_count = Notifications::countUnreadNotification($user_id_2);         
        $unread_chat_msgs_count = Notifications::countUnreadChatMessages2($user_id_2);  
        $unread_conceirge_msgs_count = Notifications::countUnreadConceirgeMessages2($user_id_2);  
        $badge_count = $unread_notification_count + $unread_chat_msgs_count + $unread_conceirge_msgs_count;
        $badge_count = strval($badge_count);

        $user_who_made_name = 'Admin';
        $notif_msg_2 = $user_who_made_name.' sent you a message';

        // Setting Up Push
        $message = PushNotification::Message($notif_msg_2, array(
            'badge' => $badge_count,
            'sound' => 'example.aiff',

            'locArgs' => array(
                't' => "11",
                'u1' => $user_id,
                'u2' => $user_id_2,
                'u2n' => 'Admin',
                'unc' => $unread_notification_count,
                'umc' => $unread_chat_msgs_count,
                'ucmc' => $unread_conceirge_msgs_count,
            ),
        )); 

        $user_detail = DB::table('users')->select('user_role')->where('id', $user_id_2)->first();

        // Send Push
        if($user_detail->user_role == '0' || $user_detail->user_role == '2')        
            Notifications::sendUserPush($message, $user_id_2);
        else 
            Notifications::sendVendorPush($message, $user_id_2);

    }

    // Event Ended Notification -- 12
    public static function cronEventEnded() {

        $current_time = Carbon::now();

        $wedding_data = DB::select(
            "SELECT `wedding`.`id` AS `wedding_id`, `wedding`.`user_id`, `wedding`.`name` AS `wedding_name`,
            `wedding_vendor`.`id` AS `wedding_vendor_id`,
            (SELECT `image` FROM `wedding_photos` WHERE `wedding_id` = `wedding`.`id` LIMIT 1) AS `wedding_image`,
            `wedding_vendor`.`vendor_id` 
            FROM `wedding` 
            JOIN `wedding_vendor` ON `wedding`.`id` = `wedding_vendor`.`wedding_id`
            WHERE `wedding`.`date` < '$current_time' AND `wedding`.`is_notified` = '0'
        ");

        //***********************Send Push***********************
        
        foreach ($wedding_data as $key => $value) {

            $notif_msg = "EVENT ENDED TODAY";

            $notification_id = DB::table('notifications')->insertGetId(array(
                'user_who_made_id' => 0,
                'user_who_received_id' => $value->vendor_id,
                'user_id' => $value->user_id,
                'vendor_id' => $value->vendor_id,
                'image' => $value->wedding_image,
                'notif_type' => 'event_ende	d',                
                'notif_msg' => $notif_msg,
                'created_at' => $current_time,
                'updated_at' => $current_time,
            ));    

            $notification_id = strval($notification_id);
            
            // message to be send in push
            $message = PushNotification::Message($notif_msg, array(
                'badge' => 1,
                'sound' => 'example.aiff',


                'locArgs' => array(
                    't' => "12",
                    'wid' => $value->wedding_id,
                    'u1' => $value->wedding_name,
                    'uid' => $value->user_id,
                    'vid' => $value->vendor_id,
                    'nid' => $notification_id,
                ),
            ));

            Notifications::sendVendorPush($message, $value->vendor_id);

            DB::table('wedding')->where('id', $value->wedding_id)->update(['is_notified' => '1']);

        }

        return $wedding_data;        

    }

    // Get Notifications
    public static function getNotifications($input) {

        $validation = Validator::make($input, Notifications::$accessTokenRequired);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {

            $access_token = $input['access_token'];            
            $user_id = Users::getUserIdByToken($access_token);         

            $notification_listing = Notifications::viewNotifications($user_id);                       

            return Response::json(array('status'=>1, 'msg'=>'Notification Listing', 'notification_listing'=>$notification_listing), 200);
        }

    }

    // Mark Read Notifications
    public static function markReadNotifications($input) {

        $validation = Validator::make($input, Notifications::$markReadNotificationsRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }

        $access_token = $input['access_token'];
        $notification_id = $input['notification_id'];
        $user_id = Users::getUserIdByToken($access_token);

        DB::table('notifications')->where('id', $notification_id)->update(['is_read' => '1']);

        return Response::json(array('status'=>1, 'msg'=>'Sucessfully Marked Read'), 200);

    }    

    // Remove Notification
    public static function removeNotification($input) {

        $validation = Validator::make($input, Notifications::$markReadNotificationsRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }

        $access_token = $input['access_token'];
        $notification_id = $input['notification_id'];
        $user_id = Users::getUserIdByToken($access_token);

        DB::table('notifications')->where('id', $notification_id)->delete();

        return Response::json(array('status'=>1, 'msg'=>'Sucessfully Removed'), 200);

    }   

    // Clear Notifications
    public static function clearNotifications($input) {

        $validation = Validator::make($input, Notifications::$accessTokenRequired);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }

        $access_token = $input['access_token'];        
        $user_id = Users::getUserIdByToken($access_token);

        DB::table('notifications')->where('user_who_received_id', $user_id)->delete();

        return Response::json(array('status'=>1, 'msg'=>'Sucessfully Cleared'), 200);

    }   

    // Mark Read Badge Notifications
    public static function markReadBadgeNotifications($input) {

        $validation = Validator::make($input, Notifications::$markReadBadgeNotificationsRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }

        $access_token = $input['access_token'];
        $clear_type = $input['clear_type']; // 1 - clear notification count, 2 - clear chat message count, 3 - clear conceirge message count
        $user_id = Users::getUserIdByToken($access_token);

        // $user_type = Users::getUserType($user_id);

        if($clear_type == '1') {
            DB::table('notifications')->where('user_who_received_id', $user_id)->update(['is_read_2' => '1']);
            return Response::json(array('status'=>1, 'msg'=>'Sucessfully Marked Notifications Badges'), 200);
        }
        elseif($clear_type == '2') {
            DB::table('chat')->where('sent_to', $user_id)->update(['is_read_2' => '1']);

            // Getting the most recent id from table and setting it no matter of who is the user
            $chat_data = DB::table('chat')->select('id')->orderBy('id', 'desc')->first();
            // return $chat_data->id;

            if(!empty($chat_data))
                DB::table('chat_group_read')->where('user_id', $user_id)->update(['chat_id_2' => $chat_data->id]);

            return Response::json(array('status'=>1, 'msg'=>'Sucessfully Marked Chat Messages'), 200);
        }
        elseif($clear_type == '3') {
            DB::table('conceirge')->where('sent_to', $user_id)->update(['is_read_2' => '1']);            
            return Response::json(array('status'=>1, 'msg'=>'Sucessfully Conceirge Messages'), 200);
        }
        else {
            return Response::json(array('status'=>1, 'msg'=>'Invalid Clear Type'), 200);
        }        

    }

    // Get Badge Notification Count
    public static function getBadgeNotificationCount($input) {

        $validation = Validator::make($input, Notifications::$accessTokenRequired);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }

        $access_token = $input['access_token'];        
        $user_id = Users::getUserIdByToken($access_token);

        // Count Unread Notification
        $unread_notification_count = Notifications::countUnreadNotification($user_id);         
        $unread_chat_msgs_count = Notifications::countUnreadChatMessages2($user_id);  
        $unread_conceirge_msgs_count = Notifications::countUnreadConceirgeMessages2($user_id);  
        $badge_count = $unread_notification_count + $unread_chat_msgs_count + $unread_conceirge_msgs_count;
        $badge_count = strval($badge_count);

        return Response::json(array('status'=>1, 'msg'=>'Counts', 'unread_notification_count'=>$unread_notification_count, 'unread_chat_msgs_count'=>$unread_chat_msgs_count, 'unread_conceirge_msgs_count'=>$unread_conceirge_msgs_count, 'badge_count'=>$badge_count), 200);

    } 

}
