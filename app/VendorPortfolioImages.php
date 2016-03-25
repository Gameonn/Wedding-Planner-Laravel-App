<?php namespace App;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class VendorPortfolioImages extends Model {

	// Validation Rules

	public static $accessTokenRequired = array(
        'access_token' => 'required|exists:users,access_token',
    );

    public static $portfolioCreateRules = array(
        'access_token' => 'required|exists:users,access_token',
        'image_count'=>'required|numeric',        
    );

    public static $portfolioDeleteRules = array(
        'access_token' => 'required|exists:users,access_token',
        'image_ids'=>'required',
    );

    // Portfolio Functions 

    public static function portfolioCreate($input) {

        $validation = Validator::make($input, VendorPortfolioImages::$portfolioCreateRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {

            $access_token = $input['access_token'];
            $user_id = Users::getUserIdByToken($access_token);
            $image_count = $input['image_count'];

            $current_time = Carbon::now();

            // Uploading Portfolio Photos
            $multiple_images = Users::uploadMultipleImages($image_count);

            foreach ($multiple_images as $key => $value) {
                
                $vendor_portfolio_image_id = DB::table('vendor_portfolio_images')->insertGetId(
                    array(                    
                        'user_id' => $user_id,
                        'image' => $multiple_images[$key],                        
                        'created_at' => $current_time,
                        'updated_at' => $current_time,
                    )
                );

            }
            
            // Sending Response
            return Users::viewVendorProfileDetails($user_id);
        }

    }

    public static function portfolioDelete($input) {

        $validation = Validator::make($input, VendorPortfolioImages::$portfolioDeleteRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {

            $access_token = $input['access_token'];
            $image_ids = $input['image_ids'];
            $user_id = Users::getUserIdByToken($access_token);                       

            $image_id_array = json_decode($image_ids);

            foreach ($image_id_array as $key => $value) {            
            	DB::table('vendor_portfolio_images')->where('id', $image_id_array[$key])->where('user_id', $user_id)->delete();
            }

            $vendor_details = Users::viewVendorProfileDetails2($user_id);
            $vendor_extra_details = Users::viewVendorExtraDetails($user_id);
            $vendor_review_details = Users::viewVendorReviewDetails($user_id);
            $vendor_portfolio_images = Users::viewVendorPortfolioImages($user_id);

            // Sending Response
            return Response::json(array('status'=>1, 'msg'=>'Images Removed Successfully', 'vendor_details'=>$vendor_details, 'vendor_extra_details'=>$vendor_extra_details, 'vendor_review_details'=>$vendor_review_details, 'vendor_portfolio_images'=>$vendor_portfolio_images), 200);        
        }

    }

}
