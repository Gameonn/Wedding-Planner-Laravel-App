<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use \DateTime;

class AdminChat extends Model {

    // Validation Rules
    public static $conceirge2Rules = array(
        'user_id' => 'required',
    );

    // Common Function
    public static function getCurrentMessages($user_id, $user_id_2) {

        //DB::update("UPDATE `chat` SET `is_read`='1' WHERE `sent_to`='$user_id' AND `sent_by`='$user_id_2'");

        $messages = DB::select(
            "SELECT * FROM (SELECT 
                `id`, 
                `sent_by`, 
                `sent_to`, 
                `message`,                 
                `message_type`, 
                `file_name` AS `chat_image`, 
                `file_original_name` AS `chat_image_original`,                 
                `is_read`,
                (SELECT `users`.`name` FROM `users` WHERE `users`.`id` = `admin_chat`.`sent_by`) AS `user_name`,
                (SELECT `users`.`image` FROM `users` WHERE `users`.`id` = `admin_chat`.`sent_by`) AS `image`,
                CASE
                    WHEN DATEDIFF(UTC_TIMESTAMP, created_at) != 0 THEN CONCAT(DATEDIFF(UTC_TIMESTAMP, created_at) ,' d ago')
                    WHEN HOUR(TIMEDIFF(UTC_TIMESTAMP, created_at)) != 0 THEN CONCAT(HOUR(TIMEDIFF(UTC_TIMESTAMP, created_at)) ,' h ago')
                    WHEN MINUTE(TIMEDIFF(UTC_TIMESTAMP, created_at)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(UTC_TIMESTAMP, created_at)) ,' m ago')
                    ELSE
                    CONCAT(SECOND(TIMEDIFF(UTC_TIMESTAMP, created_at)) ,' s ago')
                END as time_since
            FROM `admin_chat`
            WHERE (`sent_by`='$user_id' OR `sent_to`='$user_id') AND (`sent_by`='$user_id_2' OR `sent_to`='$user_id_2')
            ORDER BY `id` DESC
            LIMIT 40) sub
            ORDER BY `id` ASC
        ");

        foreach($messages as $key => $value) {
            $messages[$key]->image = Users::getFormattedImage($messages[$key]->image);

            if($messages[$key]->chat_image == null || $messages[$key]->chat_image == "") 
                $messages[$key]->chat_image = "";
            else 
                $messages[$key]->chat_image = Users::getFormattedImage($messages[$key]->chat_image);            
            
        }

        return $messages;

    }

    // Conceirge Chat Function
    public static function conceirge2($user_id, $user_id_2) {
       
        return $messages = AdminChat::getCurrentMessages($user_id, $user_id_2);                    
        
    }


}
