<?php namespace App;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class VendorReviews extends Model {

	// Validation Rules

	public static $accessTokenRequired = array(
        'access_token' => 'required|exists:users,access_token',
    );

    public static $requestFeedbackRules = array(
        'access_token' => 'required|exists:users,access_token',
        'user_id_2' => 'required',
    );

    public static $writeReviewRules = array(
        'access_token' => 'required|exists:users,access_token',
        'vendor_id' => 'required',        
    );

    public static $deleteReviewRules = array(
        'access_token' => 'required|exists:users,access_token',
        'vendor_reviews_id' => 'required',          
    );    

    public static $reviewListingByVendorIdRules = array(
        'access_token' => 'required|exists:users,access_token',
        'vendor_id' => 'required',          
    );        

    // Common Functions

    public static function getReviewsByUserId($vendor_id) {        

        // User Review Listing
        $user_review_listing = DB::select( 
            "SELECT `id`, `user_id`, `vendor_id`, `created_at`,
            (SELECT `name` FROM `users` WHERE `id` = `vendor_reviews`.`user_id`) AS `user_name`,
            (SELECT `image` FROM `users` WHERE `id` = `vendor_reviews`.`user_id`) AS `user_image`,
            `rating`, `feedback`,
            CASE
                WHEN DATEDIFF(UTC_TIMESTAMP, `created_at`) != 0 THEN CONCAT(DATEDIFF(UTC_TIMESTAMP, `created_at`) ,' d ago')
                WHEN HOUR(TIMEDIFF(UTC_TIMESTAMP, `created_at`)) != 0 THEN CONCAT(HOUR(TIMEDIFF(UTC_TIMESTAMP, `created_at`)) ,' h ago')
                WHEN MINUTE(TIMEDIFF(UTC_TIMESTAMP, `created_at`)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(UTC_TIMESTAMP, `created_at`)) ,' m ago')
                ELSE
                CONCAT(SECOND(TIMEDIFF(UTC_TIMESTAMP, `created_at`)) ,' s ago')
            END as time_since                
            FROM `vendor_reviews`
            WHERE `vendor_id` = '$vendor_id'
            ORDER BY `id` DESC
        ");

        foreach ($user_review_listing as $key => $value) {
            $user_review_listing[$key]->user_image = Users::getFormattedImage($user_review_listing[$key]->user_image);
        }

        return $user_review_listing;

    }    

    // Request For Feedback

    public static function requestFeedback($input) {    

        $validation = Validator::make($input, VendorReviews::$requestFeedbackRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {

            $access_token = $input['access_token'];
            $user_id_2 = $input['user_id_2'];
            $user_id = Users::getUserIdByToken($access_token);   

            Notifications::requestFeedbackNotification($user_id, $user_id_2);         
            
            return Response::json(array('status'=>1, 'msg'=>'Successfully Requested For Review'), 200);
        }

    }

    // Write Review 

    public static function writeReview($input) {

        $validation = Validator::make($input, VendorReviews::$writeReviewRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {

            $access_token = $input['access_token']; 
            $vendor_id = $input['vendor_id'];
            $rating = isset($input['rating']) ? $input['rating'] : "";
            $feedback = isset($input['feedback']) ? $input['feedback'] : "";

            $user_id = Users::getUserIdByToken($access_token);            

            $current_time = Carbon::now();

            $check_customer = DB::table('users')->select('id')->where('id', $user_id)->where('user_role', '0')->first();
            $check_vendor = DB::table('users')->select('id')->where('id', $vendor_id)->where('user_role', '1')->first();
            $check_review = DB::table('vendor_reviews')->select('id')->where('user_id', $user_id)->where('vendor_id', $vendor_id)->first();

            if(empty($check_customer)) {
                return Response::json(array('status'=>0, 'msg'=>'Invalid Customer'), 200);
            }
            else if(empty($check_vendor)) {
                return Response::json(array('status'=>0, 'msg'=>'Invalid Vendor'), 200);    
            }
            else if(!empty($check_review)) {
                return Response::json(array('status'=>0, 'msg'=>'Already Reviewed'), 200);
            }
            else {
                $vendor_reviews_id = DB::table('vendor_reviews')->insertGetId(array(
                        'user_id' => $user_id, 
                        'vendor_id' => $vendor_id,
                        'rating' => $rating,
                        'feedback' => $feedback, 
                        'created_at' => $current_time, 
                        'updated_at' => $current_time, 
                    )
                ); 
            }             

            Notifications::writeReviewNotification($user_id, $vendor_id, $rating, $vendor_reviews_id);

            $review_listing = VendorReviews::getReviewsByUserId($vendor_id);
            
            return Response::json(array('status'=>1, 'msg'=>'Successfully Reviewed', 'review_listing'=>$review_listing), 200);
        }

    }

    public static function deleteReview($input) {

        $validation = Validator::make($input, VendorReviews::$deleteReviewRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {

            $access_token = $input['access_token']; 
            $vendor_reviews_id = $input['vendor_reviews_id'];

            $user_id = Users::getUserIdByToken($access_token);            

            $check_review = DB::table('vendor_reviews')->select('id')->where('id', $vendor_reviews_id)->where('user_id', $user_id)->first();

            if(empty($check_review)) {
                return Response::json(array('status'=>0, 'msg'=>'Invalid Parameters'), 200);
            }
            else {
                $vendor_reviews = DB::table('vendor_reviews')->where('id', $vendor_reviews_id)->delete(); 
            }            

            //$review_listing = VendorReviews::getReviewsByUserId($user_id);
            
            return Response::json(array('status'=>1, 'msg'=>'Successfully Removed'), 200);
        }

    }

    public static function reviewListing($input) {

        $validation = Validator::make($input, VendorReviews::$accessTokenRequired);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {

            $access_token = $input['access_token']; 
            $user_id = Users::getUserIdByToken($access_token);        

            $check_user = DB::table('users')->select('id')->where('id', $user_id)->where('user_role', '1')->first();    

            if(!empty($check_user)) {

                $check_review = DB::table('vendor_reviews')->select('id')->where('vendor_id', $user_id)->first();

                if(empty($check_review)) {
                    return Response::json(array('status'=>0, 'msg'=>'Invalid Parameters'), 200);
                }

                $review_listing = VendorReviews::getReviewsByUserId($user_id);
                
                return Response::json(array('status'=>1, 'msg'=>'Review Listing', 'review_listing'=>$review_listing), 200);
            }
            else {
                return Response::json(array('status'=>1, 'msg'=>'Only Vendor id is allowed'), 200);    
            }
        }

    }

    public static function reviewListingByVendorId($input) {

        $validation = Validator::make($input, VendorReviews::$reviewListingByVendorIdRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {

            $access_token = $input['access_token']; 
            $vendor_id = $input['vendor_id']; 
            $user_id = Users::getUserIdByToken($access_token);        

            $check_user = DB::table('users')->select('id')->where('id', $vendor_id)->where('user_role', '1')->first();    

            if(!empty($check_user)) {                

                $review_listing = VendorReviews::getReviewsByUserId($vendor_id);
                
                return Response::json(array('status'=>1, 'msg'=>'Review Listing', 'review_listing'=>$review_listing), 200);
            }
            else {
                return Response::json(array('status'=>1, 'msg'=>'Only Vendor id is allowed'), 200);    
            }
        }

    }    



}
