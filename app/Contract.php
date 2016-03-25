<?php namespace App;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class Contract extends Model {

	// Validation Rules

	public static $accessTokenRequired = array(
        'access_token' => 'required|exists:users,access_token',
    );

    public static $createContractRules = array(
        'access_token' => 'required|exists:users,access_token',
        'wedding_id' => 'required',
        'vendor_id' => 'required',
    );

    public static $changeContractStatusRules = array(
        'access_token' => 'required|exists:users,access_token',                
        'contract_id' => 'required',
        'status' => 'required',
    );

    // Functionality

    public static function createContract($input) {

        $validation = Validator::make($input, Contract::$createContractRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {

            $access_token = $input['access_token'];
            $wedding_id = $input['wedding_id'];
            $vendor_id = $input['vendor_id'];
            $user_id = Users::getUserIdByToken($access_token);

            $current_time = Carbon::now();

            $check_user = DB::table('wedding')->select('id')->where('user_id', $user_id)->where('id', $wedding_id)->first();

            if(!empty($check_user)) {

            	$check_wedding_vendor = DB::table('wedding_vendor')->select('id')->where('user_id', $user_id)->where('wedding_id', $wedding_id)->where('vendor_id', $vendor_id)->first();

	            if(empty($check_wedding_vendor)) {

	            	$wedding_vendor_id = DB::table('wedding_vendor')->insertGetId(array(
		            	'user_id' => $user_id,
		            	'wedding_id' => $wedding_id,
		            	'vendor_id' => $vendor_id,
		            	'created_at' => $current_time,
		            	'updated_at' => $current_time,
		        	));

                    Notifications::createContractNotification($user_id, $vendor_id, $wedding_vendor_id);

		        	return Response::json(array('status'=>1, 'msg'=>'Contract Created'), 200);

	            }    
	            else {
	            	return Response::json(array('status'=>0, 'msg'=>'Already Contracted'), 200);
	            }

            }
            else {
            	return Response::json(array('status'=>0, 'msg'=>'Invalid Parameters'), 200);
            }	                    
                        
        }

    }

    public static function changeContractStatus($input) {

        $validation = Validator::make($input, Contract::$changeContractStatusRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {

            $access_token = $input['access_token'];                        
            $wedding_vendor_id = $input['contract_id'];
            $status = $input['status'];
            $user_id = Users::getUserIdByToken($access_token);

            $current_time = Carbon::now();

            $check_wedding_vendor = DB::table('wedding_vendor')->select('id', 'status')->where('id', $wedding_vendor_id)->where('vendor_id', $user_id)->first();

            if(!empty($check_wedding_vendor)) {            	

                if($check_wedding_vendor->status != 1) {

                    DB::table('wedding_vendor')->where('id', $wedding_vendor_id)->update([
                        'status' => $status,
                        'updated_at' => $current_time,
                    ]);

                    // Clear Previous Notification
                    DB::table('notifications')
                        ->where('user_who_received_id', $user_id)
                        ->where('notif_type', 'create_contract')
                        ->where('contract_id', $wedding_vendor_id)
                        ->delete();

                    // Notification
                    Notifications::changeContractStatusNotification($wedding_vendor_id, $user_id, $status);

                    return Response::json(array('status'=>1, 'msg'=>'Contract Status Updated'), 200);               
                }   
                else {
                    return Response::json(array('status'=>0, 'msg'=>'Already Contracted'), 200);    
                }         	

            }
            else {
            	return Response::json(array('status'=>0, 'msg'=>'Invalid Parameters'), 200);
            }	                    
                        
        }

    }

    public static function deleteContract($input) {

        $validation = Validator::make($input, Contract::$createContractRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {

            $access_token = $input['access_token'];
            $wedding_id = $input['wedding_id'];
            $vendor_id = $input['vendor_id'];            
            $user_id = Users::getUserIdByToken($access_token);

            $current_time = Carbon::now();

            $check_user = DB::table('wedding')->select('id')->where('user_id', $user_id)->where('id', $wedding_id)->first();

            if(!empty($check_user)) {            		            

            	$check_wedding_vendor = DB::table('wedding_vendor')->select('id')->where('user_id', $user_id)->where('wedding_id', $wedding_id)->where('vendor_id', $vendor_id)->first();

            	if(!empty($check_wedding_vendor)) {
            		DB::table('wedding_vendor')->where('user_id', $user_id)->where('wedding_id', $wedding_id)->where('vendor_id', $vendor_id)->delete();

	        		return Response::json(array('status'=>1, 'msg'=>'Contract Removed'), 200);	            
            	}   
            	else {
            		return Response::json(array('status'=>1, 'msg'=>'Does not exist'), 200);	            
            	}         	

            }
            else {
            	return Response::json(array('status'=>0, 'msg'=>'Invalid Parameters'), 200);
            }	                    
                        
        }

    }

}
