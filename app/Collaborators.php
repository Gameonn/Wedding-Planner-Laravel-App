<?php namespace App;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class Collaborators extends Model {

	// Validation Rules

	public static $accessTokenRequired = array(
        'access_token' => 'required|exists:users,access_token',
    );

    public static $createCollaboratorGroupRules = array(
        'access_token' => 'required|exists:users,access_token',
        'name' => 'required',
        'member_ids' => 'required',
    );

    public static $createCollaboratorGroupByInviteRules = array(
        'access_token' => 'required|exists:users,access_token',
        'phone_nos' => 'required', 
        'name' => 'required',        
    );    

    public static $editCollaboratorsRules = array(
        'access_token' => 'required|exists:users,access_token',
        'collaborator_id' => 'required',
        'name' => 'required',
    );

    public static $addMembersRules = array(
        'access_token' => 'required|exists:users,access_token',        
        'phone_nos' => 'required',
        'collaborator_id' => 'required',
    );    

    public static $removeMembersRules = array( 
        'access_token' => 'required|exists:users,access_token',  
        'collaborators_id' => 'required',       
        'collaborator_member_ids' => 'required',
    );      

    public static $viewMembersRules = array( 
        'access_token' => 'required|exists:users,access_token',                
        'collaborator_id' => 'required',
    );          

    public static $removeCollaboratorGroupRules = array(
        'access_token' => 'required|exists:users,access_token',
        'collaborator_id' => 'required',        
    );            

    // Common Funtions

    public static function collaboratorsListing($user_id) {

		$collaborator_listing = DB::select(
    		"SELECT `collaborators`.`id` AS `collaborators_id`, `collaborators`.`user_id`, `collaborators`.`name`,            
            (SELECT COUNT(`id`) FROM `collaborator_members` WHERE `collaborator_id`=`collaborators_id`) AS `collaborator_member_count`
            FROM `collaborators`            
            WHERE `collaborators`.`user_id` = '$user_id' AND (SELECT COUNT(`id`) FROM `collaborator_members` WHERE `collaborator_id`=`collaborators`.`id`) > '0'
		");

		foreach ($collaborator_listing as $key => $value) {

			$collaborators_id = $collaborator_listing[$key]->collaborators_id;

			$collaborators_member_details = DB::select(
				"SELECT `id` AS `collaborator_members_id`, `user_id_2`,
				(SELECT `name` FROM `users` WHERE `id` = `collaborator_members`.`user_id_2`) AS `collaborator_members_name`,
				(SELECT `image` FROM `users` WHERE `id` = `collaborator_members`.`user_id_2`) AS `collaborator_members_image`
				FROM `collaborator_members`
				WHERE `collaborator_members`.`collaborator_id` = '$collaborators_id'
			");      

            foreach ($collaborators_member_details as $key2 => $value2) {
                $collaborators_member_details[$key2]->collaborator_members_image = Users::getFormattedImage($collaborators_member_details[$key2]->collaborator_members_image);
            }      

            $collaborator_listing[$key]->collaborators_member_details = $collaborators_member_details;
        }  

        return $collaborator_listing;         

    }

    public static function getMembers($collaborator_id) {

        $collaborator_members = DB::select(
            "SELECT `id` AS `collaborator_members_id`, `collaborator_id`, `user_id_1`, `user_id_2`, 
            (SELECT `name` FROM `users` WHERE `id` = `collaborator_members`.`user_id_2`) AS `collaborator_members_name`,
            (SELECT `image` FROM `users` WHERE `id` = `collaborator_members`.`user_id_2`) AS `collaborator_members_image`
            FROM `collaborator_members`
            WHERE `collaborator_id` = '$collaborator_id' 
        ");

        foreach ($collaborator_members as $key => $value) {
            $value->collaborator_members_image = Users::getFormattedImage($value->collaborator_members_image);
        }

        return $collaborator_members;

    }

    // Functionality

    public static function createCollaboratorGroup($input) {

        $validation = Validator::make($input, Collaborators::$createCollaboratorGroupRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {

            $access_token = $input['access_token'];
            $name = $input['name'];
            $member_ids = $input['member_ids'];
            $user_id = Users::getUserIdByToken($access_token);

            $member_ids_array = json_decode($member_ids);

            $current_time = Carbon::now();

            $collaborator_id = DB::table('collaborators')->insertGetId(array(
                'user_id' => $user_id,
                'name' => $name,
                'created_at' => $current_time,
                'updated_at' => $current_time,
            ));

            foreach ($member_ids_array as $key => $value) {

                $check_user = DB::table('users')->select('id')->where('user_role', '0')->where('id', $member_ids_array[$key])->first();

                if(!empty($check_user)) {

                    $collaborator_members_id = DB::table('collaborator_members')->insertGetId(array(
                        'collaborator_id' => $collaborator_id,
                        'user_id_1' => $user_id,
                        'user_id_2' => $member_ids_array[$key],
                        'created_at' => $current_time,
                        'updated_at' => $current_time,
                    ));

                }                            

            }                               
            
            return Response::json(array('status'=>1, 'msg'=>'Collaborator Group Created'), 200);
        }

    }

    public static function createCollaboratorGroupByInvite($input) {

        $validation = Validator::make($input, Collaborators::$createCollaboratorGroupByInviteRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {

            $access_token = $input['access_token'];
            $phone_nos = $input['phone_nos'];
            $name = $input['name'];             
            $user_id = Users::getUserIdByToken($access_token);

            //return $phone_no_array = json_decode($phone_nos);

            $phone_no_array = explode(',', $phone_nos);

            $current_time = Carbon::now();

            // Create New Collaborator Group
            $collaborator_id = DB::table('collaborators')->insertGetId(array(
                'user_id' => $user_id,
                'name' => $name,
                'created_at' => $current_time,
                'updated_at' => $current_time,
            ));

            // Add Owner of group as member
            $collaborator_members_id = DB::table('collaborator_members')->insertGetId(array(
                'collaborator_id' => $collaborator_id,
                'user_id_1' => $user_id,
                'user_id_2' => $user_id,
                'created_at' => $current_time,
                'updated_at' => $current_time,
            ));

            $app_users = array();
            $new_users = array();

            $check_collaborator = DB::table('collaborators')->select('id')->where('user_id', $user_id)->where('id', $collaborator_id)->first();            

            if(empty($check_collaborator)) {
                return Response::json(array('status'=>0, 'msg'=>'Invalid User and Collaborator'), 200);
            }

            foreach ($phone_no_array as $key => $value) {

                $check_user = DB::table('users')->select('id', 'phone_no')->where('phone_no', $phone_no_array[$key])->first();

                if(!empty($check_user)) {

                    $app_users[] = $check_user->phone_no;    

                    $collaborator_member_check = DB::table('collaborator_members')->select('id')->where('collaborator_id', $collaborator_id)->where('user_id_2', $check_user->id)->first();

                    if(empty($collaborator_member_check)) {

                        $collaborator_members_id = DB::table('collaborator_members')->insertGetId(array(
                            'collaborator_id' => $collaborator_id,
                            'user_id_1' => $user_id,
                            'user_id_2' => $check_user->id,
                            'created_at' => $current_time,
                            'updated_at' => $current_time,
                        ));

                        Notifications::collaboratorAddMemberNotification($user_id, $check_user->id, $collaborator_id);

                    }                                

                } 
                else {

                    $check_invitation = DB::table('invitations')->select('id')->where('phone_no', $phone_no_array[$key])->first();

                    if(empty($check_invitation)) {
                        $invitation_id = DB::table('invitations')->insertGetId(array(
                            'user_id' => $user_id,
                            'phone_no' => $phone_no_array[$key], 
                            'collaborator_id' => $collaborator_id,                       
                            'created_at' => $current_time,
                            'updated_at' => $current_time,
                        ));
                    }   

                    $new_users[] = $phone_no_array[$key];

                }                           

            }                               
            
            return Response::json(array('status'=>1, 'msg'=>'Sent Invitations', 'app_users'=>$app_users, 'new_users'=>$new_users), 200);
        }

    }

    public static function editCollaborators($input) {

        $validation = Validator::make($input, Collaborators::$editCollaboratorsRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {

            $access_token = $input['access_token'];
            $collaborator_id = $input['collaborator_id'];
            $name = $input['name'];            
            $user_id = Users::getUserIdByToken($access_token);            

            $current_time = Carbon::now();

            DB::table('collaborators')
                ->where('id', $collaborator_id)
                ->update(['name' => $name, 'updated_at' => $current_time]);                                                
            
            return Response::json(array('status'=>1, 'msg'=>'Collaborator Group Edited'), 200);
        }

    } 

    // Add Members
    public static function addMembers($input) {

        $validation = Validator::make($input, Collaborators::$addMembersRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {

            $access_token = $input['access_token'];
            $phone_nos = $input['phone_nos']; 
            $collaborator_id = $input['collaborator_id'];
            $user_id = Users::getUserIdByToken($access_token);
            
            $phone_no_array = explode(',', $phone_nos);

            $current_time = Carbon::now();

            $app_users = array();
            $new_users = array();

            $check_collaborator = DB::table('collaborators')->select('id')->where('user_id', $user_id)->where('id', $collaborator_id)->first();

            if(empty($check_collaborator)) {
                return Response::json(array('status'=>0, 'msg'=>'Invalid User and Collaborator'), 200);
            }            

            foreach ($phone_no_array as $key => $value) {

                $check_user = DB::table('users')->select('id', 'phone_no')->where('phone_no', $phone_no_array[$key])->first(); 

                if(!empty($check_user)) {       

                    $app_users[] = $check_user->phone_no;               

                    $collaborator_member_check = DB::table('collaborator_members')->select('id')->where('collaborator_id', $collaborator_id)->where('user_id_2', $check_user->id)->first();

                    if(empty($collaborator_member_check)) {

                        $collaborator_members_id = DB::table('collaborator_members')->insertGetId(array(
                            'collaborator_id' => $collaborator_id,
                            'user_id_1' => $user_id,
                            'user_id_2' => $check_user->id,
                            'created_at' => $current_time,
                            'updated_at' => $current_time,
                        ));

                        Notifications::collaboratorAddMemberNotification($user_id, $check_user->id, $collaborator_id);

                    }                                

                } 
                else {

                    $check_invitation = DB::table('invitations')->select('id')->where('phone_no', $phone_no_array[$key])->first();

                    if(empty($check_invitation)) {
                        $invitation_id = DB::table('invitations')->insertGetId(array(
                            'user_id' => $user_id,
                            'phone_no' => $phone_no_array[$key], 
                            'collaborator_id' => $collaborator_id,                       
                            'created_at' => $current_time,
                            'updated_at' => $current_time,
                        ));
                    }   

                    $new_users[] = $phone_no_array[$key];

                }                           

            }                               
            
            return Response::json(array('status'=>1, 'msg'=>'Sent Invitations', 'app_users'=>$app_users, 'new_users'=>$new_users), 200);

        }

    }

    // Remove Members
    public static function removeMembers($input) {

        $validation = Validator::make($input, Collaborators::$removeMembersRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {

            $access_token = $input['access_token']; 
            $collaborators_id = $input['collaborators_id']; 
            $collaborator_member_ids = $input['collaborator_member_ids'];            
            $user_id = Users::getUserIdByToken($access_token); 

            $collaborator_member_id_array = explode(',', $collaborator_member_ids);

            foreach ($collaborator_member_id_array as $key => $value) {
                
                DB::table('collaborator_members')
                    ->where('id', $collaborator_member_id_array[$key])
                    ->where('collaborator_id', $collaborators_id)
                    ->delete();

            }

            $collaborator_members = Collaborators::getMembers($collaborators_id);          

            return Response::json(array('status'=>1, 'msg'=>'Collaborator Member Removed', 'collaborator_members'=>$collaborator_members), 200);
        }

    }

    // View Members
    public static function viewMembers($input) {

        $validation = Validator::make($input, Collaborators::$viewMembersRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {

            $access_token = $input['access_token']; 
            $collaborator_id = $input['collaborator_id'];            
            $user_id = Users::getUserIdByToken($access_token); 

            $collaborator_members = Collaborators::getMembers($collaborator_id);

            return Response::json(array('status'=>1, 'msg'=>'Collaborator Member Listing', 'collaborator_members'=>$collaborator_members), 200);
        }

    }

    // Remove Collaborator Group
    public static function removeCollaboratorGroup($input) {

        $validation = Validator::make($input, Collaborators::$removeCollaboratorGroupRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {

            $access_token = $input['access_token'];
            $collaborator_id = $input['collaborator_id'];
            $user_id = Users::getUserIdByToken($access_token);

            // Delete Collaborator Group
            DB::table('collaborators')->where('id', $collaborator_id)->delete();

            // Delete Collaborator Members
            DB::table('collaborator_members')->where('collaborator_id', $collaborator_id)->delete();       
            
            return Response::json(array('status'=>1, 'msg'=>'Collaborator Group Removed'), 200);
        }

    }    

	public static function viewCollaboratorsListing($input) {

        $validation = Validator::make($input, Users::$accessTokenRequired);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {

            $access_token = $input['access_token'];
            $user_id = Users::getUserIdByToken($access_token);

            $collaborator_listing = Collaborators::collaboratorsListing($user_id);            
            
            return Response::json(array('status'=>1, 'msg'=>'Collaborator Listing', 'collaborator_listing' => $collaborator_listing), 200);
        }

    }

}
