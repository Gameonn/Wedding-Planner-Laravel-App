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
use AWS;  

class AdminCollaborators extends Model {

//*******************************************************************************************************
//                                         Validation Rules
//*******************************************************************************************************

    //

//*******************************************************************************************************
//                                          Common Functions
//*******************************************************************************************************

    //

//*******************************************************************************************************
//                                     Admin Collaborators Functions
//*******************************************************************************************************

    public static function collaboratorGroupListing($keyword, $page) {

        $length = 20;
        $offset = $page * $length;
        $sr_no = $page * $length;

        if($keyword)
            $where_query = "AND `name` LIKE '%$keyword%'";
        else
            $where_query = "";
        
        $collaborator_group_listing = DB::select(
            "SELECT `id`, 
            `user_id`,
            (SELECT `name` FROM `users` WHERE `id` = `collaborators`.`user_id`) AS `user_name`,
            (SELECT `image` FROM `users` WHERE `id` = `collaborators`.`user_id`) AS `user_image`,
            `name`, 
            (SELECT `id` FROM `wedding` WHERE `user_id` = `collaborators`.`user_id` LIMIT 1) AS `wedding_id`,
            (SELECT `name` FROM `wedding` WHERE `user_id` = `collaborators`.`user_id` LIMIT 1) AS `wedding_name`,
            `created_at`,
            (SELECT count(`id`) FROM `collaborators` WHERE 1 $where_query) AS `total_collaborator_groups`,
            '$keyword' AS `keyword`,
            '$sr_no' AS `sr_no`,
            '$page' AS `page_no`
            FROM `collaborators`
            WHERE 1
            $where_query
            LIMIT $offset, $length
        ");

        foreach ($collaborator_group_listing as $key => $value) {

            if($value->user_image==null || $value->user_image=="")
                $value->user_image = Users::getFormattedImage('default-profile-pic.png');
            else 
                $value->user_image = Users::getFormattedImage($value->user_image);            

            if($value->keyword == null || $value->keyword == "" || $value->keyword == "0")      
                $value->keyword = "0";  

            $value->sr_no = $sr_no + 1;
            $sr_no++;            

            // Count Total Pages
            $collaborator_group_listing[0]->total_pages = ceil($collaborator_group_listing[0]->total_collaborator_groups / 20);
        }        

        return $collaborator_group_listing;

    }

    // Collaborator Group Members
    public static function collaboratorGroupMembers($collaborator_id) {

        $collaborator_group_members = DB::select(
            "SELECT `id`, 
            `collaborator_id`,
            (SELECT `name` FROM `collaborators` WHERE `id` = `collaborator_members`.`collaborator_id`) AS `group_name`,
            `user_id_1`,            
            `user_id_2`,
            (SELECT `name` FROM `users` WHERE `id` = `collaborator_members`.`user_id_2`) AS `user_name`,
            (SELECT `image` FROM `users` WHERE `id` = `collaborator_members`.`user_id_2`) AS `user_image`,
            (SELECT `phone_no` FROM `users` WHERE `id` = `collaborator_members`.`user_id_2`) AS `user_phone_no`,            
            `created_at`      
            FROM `collaborator_members`            
            WHERE `collaborator_id` = '$collaborator_id'
        ");

        foreach ($collaborator_group_members as $key => $value) {

            if($value->user_image==null || $value->user_image=="")
                $value->user_image = Users::getFormattedImage('default-profile-pic.png');
            else 
                $value->user_image = Users::getFormattedImage($value->user_image);            

        }        

        return $collaborator_group_members;

    }


}
