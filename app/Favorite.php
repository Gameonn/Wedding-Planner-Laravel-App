<?php namespace App;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class Favorite extends Model {

	// Validation Rules

	public static $accessTokenRequired = array(
        'access_token' => 'required|exists:users,access_token',
    );

    public static $makeFavoriteWeddingRules = array(
        'access_token' => 'required|exists:users,access_token',
        'wedding_id' => 'required',
    );

    public static $makeFavoriteVendorRules = array(
        'access_token' => 'required|exists:users,access_token',
        'vendor_id' => 'required',
    );

    // Common Functions

    public static function viewWeddingFavoriteListing($user_id) {

    	$wedding_favorite_listing = DB::select(
    		"SELECT `id`, `user_id`, `vendor_id`, `wedding_id`,
    		(SELECT `name` FROM `wedding` WHERE `id` = `favorite`.`wedding_id`) AS `wedding_name`,
    		(SELECT `location` FROM `wedding` WHERE `id` = `favorite`.`wedding_id`) AS `wedding_location`,
    		(SELECT `image` FROM `wedding_photos` WHERE `wedding_id` = `favorite`.`wedding_id` LIMIT 1) AS `wedding_image`
    		FROM `favorite`
    		WHERE `user_id` = '$user_id' AND `vendor_id` = '0'
		");

		foreach ($wedding_favorite_listing as $key => $value) {            

            if($wedding_favorite_listing[$key]->wedding_image==null) 
            	$wedding_favorite_listing[$key]->wedding_image="";
            else 
            	$wedding_favorite_listing[$key]->wedding_image = Users::getFormattedImage($wedding_favorite_listing[$key]->wedding_image);
        }

		return $wedding_favorite_listing;

    }

    public static function viewVendorFavoriteListing($user_id) {

        $vendor_favorite_listing = DB::select(
            "SELECT `id`, `user_id`, `vendor_id`, `wedding_id`,
            (SELECT `business_name` FROM `vendor_details` WHERE `user_id` = `favorite`.`vendor_id`) AS `business_name`,
            (SELECT `business_type` FROM `vendor_details` WHERE `user_id` = `favorite`.`vendor_id`) AS `business_type`,
            (SELECT `image` FROM `vendor_portfolio_images` WHERE `user_id` = `favorite`.`vendor_id` LIMIT 1) AS `vendor_portfolio_image`
            FROM `favorite`
            WHERE `user_id` = '$user_id' AND `wedding_id` = '0'
        ");

        foreach ($vendor_favorite_listing as $key => $value) {            

            if($vendor_favorite_listing[$key]->vendor_portfolio_image==null) 
                $vendor_favorite_listing[$key]->vendor_portfolio_image="";
            else 
                $vendor_favorite_listing[$key]->vendor_portfolio_image = Users::getFormattedImage($vendor_favorite_listing[$key]->vendor_portfolio_image);
        }

        return $vendor_favorite_listing;

    }

    public static function viewFavoriteListing($user_id) {

        $favorite_listing = DB::select(
            "SELECT `id`, `user_id`, `vendor_id`, `wedding_id`, `created_at`,
            (SELECT `business_name` FROM `vendor_details` WHERE `user_id` = `favorite`.`vendor_id`) AS `business_name`,
            (SELECT `business_type` FROM `vendor_details` WHERE `user_id` = `favorite`.`vendor_id`) AS `business_type`,
            (SELECT `image` FROM `vendor_portfolio_images` WHERE `user_id` = `favorite`.`vendor_id` LIMIT 1) AS `vendor_portfolio_image`,
            (SELECT `name` FROM `wedding` WHERE `id` = `favorite`.`wedding_id`) AS `wedding_name`,
            (SELECT `location` FROM `wedding` WHERE `id` = `favorite`.`wedding_id`) AS `wedding_location`,
            (SELECT `image` FROM `wedding_photos` WHERE `wedding_id` = `favorite`.`wedding_id` LIMIT 1) AS `wedding_image`
            FROM `favorite`
            WHERE `user_id` = '$user_id'
            ORDER BY `id` DESC
        ");

        foreach ($favorite_listing as $key => $value) { 

            if($favorite_listing[$key]->business_name==null)
               $favorite_listing[$key]->business_name = "";
               
           if($favorite_listing[$key]->business_type==null)
               $favorite_listing[$key]->business_type = "";           

            if($favorite_listing[$key]->vendor_portfolio_image==null) 
                $favorite_listing[$key]->vendor_portfolio_image="";
            else 
                $favorite_listing[$key]->vendor_portfolio_image = Users::getFormattedImage($favorite_listing[$key]->vendor_portfolio_image);

            if($favorite_listing[$key]->wedding_name==null)
               $favorite_listing[$key]->wedding_name = ""; 

           if($favorite_listing[$key]->wedding_location==null)
               $favorite_listing[$key]->wedding_location = ""; 

           if($favorite_listing[$key]->wedding_image==null) 
                $favorite_listing[$key]->wedding_image="";
            else 
                $favorite_listing[$key]->wedding_image = Users::getFormattedImage($favorite_listing[$key]->wedding_image);            

        }

        return $favorite_listing;

    }

    // Make Favorite Wedding

    public static function makeFavoriteWedding($input) { 

        $validation = Validator::make($input, Favorite::$makeFavoriteWeddingRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {
            $access_token = $input['access_token'];
            $wedding_id = $input['wedding_id'];
            $user_id = Users::getUserIdByToken($access_token);   

            $current_time = Carbon::now();         

            $check_favorite = DB::table('favorite')->select('id')->where('user_id', $user_id)->where('wedding_id', $wedding_id)->first();

            if(empty($check_favorite)) {

            	$favorite_id = DB::table('favorite')->insertGetId(array(
	            	'user_id' => $user_id,
	            	'wedding_id' => $wedding_id,
	            	'created_at' => $current_time,
	            	'updated_at' => $current_time,
	        	));

                Notifications::makeFavoriteWeddingNotification($user_id, $wedding_id, $favorite_id);

	        	return Response::json(array('status' => 1, 'msg' => 'Successfully Favoritized'), 200);
            }                        
            else {
            	return Response::json(array('status' => 0, 'msg' => 'Already Favoritized'), 200);
            }

        }

    }

    // Remove Favorite Wedding

    public static function removeFavoriteWedding($input) { 

        $validation = Validator::make($input, Favorite::$makeFavoriteWeddingRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {
            $access_token = $input['access_token'];
            $wedding_id = $input['wedding_id'];
            $user_id = Users::getUserIdByToken($access_token);   

            $current_time = Carbon::now();         

            $check_favorite = DB::table('favorite')->select('id')->where('user_id', $user_id)->where('wedding_id', $wedding_id)->first();

            if(!empty($check_favorite)) {

            	$favorite_id = DB::table('favorite')->where('user_id', $user_id)->where('wedding_id', $wedding_id)->delete();
	        	return Response::json(array('status' => 1, 'msg' => 'Successfully Removed'), 200);
            }                        
            else {
            	return Response::json(array('status' => 0, 'msg' => 'Invalid Parameters'), 200);
            }

        }

    }

    // Favorite Listing Wedding

    public static function viewFavoriteWeddingListing($input) { 

        $validation = Validator::make($input, Favorite::$accessTokenRequired);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {
            $access_token = $input['access_token'];            
            $user_id = Users::getUserIdByToken($access_token);   

            $wedding_favorite_listing = Favorite::viewWeddingFavoriteListing($user_id);

            return Response::json(array('status' => 1, 'msg' => 'Wedding Favorite Listing', 'wedding_favorite_listing' => $wedding_favorite_listing), 200);

        }

    }

    // Make Favorite Vendor

    public static function makeFavoriteVendor($input) { 

        $validation = Validator::make($input, Favorite::$makeFavoriteVendorRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {
            $access_token = $input['access_token'];
            $vendor_id = $input['vendor_id'];
            $user_id = Users::getUserIdByToken($access_token);   

            $current_time = Carbon::now();         

            $check_favorite = DB::table('favorite')->select('id')->where('user_id', $user_id)->where('vendor_id', $vendor_id)->first();

            if(empty($check_favorite)) {

                $favorite_id = DB::table('favorite')->insertGetId(array(
                    'user_id' => $user_id,
                    'vendor_id' => $vendor_id,
                    'created_at' => $current_time,
                    'updated_at' => $current_time,
                ));

                Notifications::makeFavoriteVendorNotification($user_id, $vendor_id, $favorite_id);

                return Response::json(array('status' => 1, 'msg' => 'Successfully Favoritized'), 200);
            }                        
            else {
                return Response::json(array('status' => 0, 'msg' => 'Already Favoritized'), 200);
            }

        }

    }

    // Remove Favorite Vendor

    public static function removeFavoriteVendor($input) { 

        $validation = Validator::make($input, Favorite::$makeFavoriteVendorRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {
            $access_token = $input['access_token'];
            $vendor_id = $input['vendor_id'];
            $user_id = Users::getUserIdByToken($access_token);               

            $check_favorite = DB::table('favorite')->select('id')->where('user_id', $user_id)->where('vendor_id', $vendor_id)->first();

            if(!empty($check_favorite)) {

                $favorite_id = DB::table('favorite')->where('user_id', $user_id)->where('vendor_id', $vendor_id)->delete();
                return Response::json(array('status' => 1, 'msg' => 'Successfully Removed'), 200);
            }                        
            else {
                return Response::json(array('status' => 0, 'msg' => 'Invalid Parameters'), 200);
            }

        }

    }

    // Favorite Listing Vendor

    public static function viewFavoriteVendorListing($input) { 

        $validation = Validator::make($input, Favorite::$accessTokenRequired);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {
            $access_token = $input['access_token'];            
            $user_id = Users::getUserIdByToken($access_token);   

            $vendor_favorite_listing = Favorite::viewVendorFavoriteListing($user_id);

            return Response::json(array('status' => 1, 'msg' => 'Vendor Favorite Listing', 'vendor_favorite_listing' => $vendor_favorite_listing), 200);

        }

    }

}
