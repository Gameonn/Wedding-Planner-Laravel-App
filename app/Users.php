<?php namespace App;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use AWS;  
use \DateTime;

class Users extends Model {

	// Validation Rules

	public static $accessTokenRequired = array(
        'access_token' => 'required|exists:users,access_token',
    );

    public static $vendorSignUpRules = array(
            
    );

    // public static $vendorSignUpRules = array(
    //     'email' => 'required|email|Unique:users',
    //     'phone_no' => 'required|Unique:users',
    //     'password' => 'required',
    //     'business_name' => 'required',
    //    	'business_type' => 'required|numeric',
    //    	'location' => 'required',
    //     'image_count'=>'required|numeric',        
    // );

    public static $vendorLoginRules = array(
        'id' => 'required',
        'password' => 'required',
    );    

    public static $vendorLoginFbCheckRules = array(
        'fb_id' => 'required',        
    );

    public static $vendorLoginFbRules = array(
        'fb_id' => 'required',
        'image_count' => 'required',
    );    

    public static $vendorEditProfileRules = array(
        'access_token' => 'required|exists:users,access_token',        
        'phone_no' => 'required',        
        'business_name' => 'required',
        'business_type' => 'required',
        'location' => 'required',        
    );

    public static $vendorChangePasswordRules = array(
        'access_token' => 'required|exists:users,access_token',
        'old_password' => 'required',
        'new_password' => 'required',
    );

    public static $vendorViewProfileByIdRules = array(
        'access_token' => 'exists:users,access_token',
        'user_id_2' => 'required|numeric|exists:users,id',         
    );

    public static $forgotPasswordRules = array(
        'email' => 'required',
    );

    public static $forgotPassword3Rules = array(        
        'password' => 'required',
        'confirm_password' => 'required',       
        'token' => 'required|exists:password_resets,token',             
        'user_id' => 'required|exists:users,id', 
    );

    public static $vendorListingByTypeRules = array(        
        'access_token' => 'required|exists:users,access_token',
        'business_type' => 'required|exists:businesses,business',       
    );

    public static $vendorListingRules = array(        
        'access_token' => 'exists:users,access_token',        
    );    

    // User Rules

    public static $userSignUpRules = array(        
        'phone_no' => 'unique:users'
    );

    public static $userLoginRules = array(
        'id' => 'required',
        'password' => 'required',
    );

    public static $userEditProfileRules = array(
        'access_token' => 'required|exists:users,access_token',
        'gender' => 'in:0,1',
    );    

    public static $userChangePasswordRules = array(
        'access_token' => 'required|exists:users,access_token',
        'old_password' => 'required',
        'new_password' => 'required',
    );    

    public static $userViewProfileByIdRules = array(
        'access_token' => 'required|exists:users,access_token',
        'user_id_2' => 'required|numeric|exists:users,id',
    );

    public static $userLoginFbRules = array(
        'fb_id' => 'required',
    );

    public static $setDeviceTokenRules = array(
        'access_token' => 'required|exists:users,access_token',
        'device_token' => 'required',
    );        

    public static $setRegIdRules = array(
        'access_token' => 'required|exists:users,access_token',
        'reg_id' => 'required',
    );   

    public static $userHomeRules = array(
        'access_token' => 'exists:users,access_token',        
    );               

    // Common Functons

    public static function generateToken() {
        return $access_token = str_random(30);
    }

    public static function updateUserToken($user_id) {
        $access_token = Users::generateToken();
        DB::table('users')->where('id', $user_id)->update(['access_token' => $access_token]);
        return $access_token;
    }

    public static function getLatLng($address) {
        $Address = $address;
        $Address = urlencode($Address);

        //Google Api Key : AIzaSyAdkueIs-Tm1J7MybH2cGa1oGe880As0s0

        $request_url = "https://maps.googleapis.com/maps/api/geocode/xml?address=".$Address."&key=AIzaSyAdkueIs-Tm1J7MybH2cGa1oGe880As0s0";

        //$request_url = "http://maps.googleapis.com/maps/api/geocode/xml?address=".$Address."&sensor=true";
        $xml = simplexml_load_file($request_url) or die("url not loading");
        $status = $xml->status;
        if ($status=="OK") {
            $Lat = $xml->result->geometry->location->lat;
            $Lon = $xml->result->geometry->location->lng;
            return $LatLng = "$Lat,$Lon";
        }
        else
            return 0;
    }

    public static function updateLatLng($user_id, $lat, $lng, $country) {
        if($lat=='0' || $lng=='0') {
            $latlng = Users::getLatLng($country);
            $latlngarr = explode(',', $latlng);
            $lat = $latlngarr[0];
            $lng = $latlngarr[1];
        }
        DB::table('users')->where('id', $user_id)->update(['lat' => $lat, 'lng' => $lng]);
    }

    public static function getFormattedImage($image) {
        if($image=='') {
            return $image;
        }
        else if(strpos($image,'graph.facebook.com') !== false) {
            return $image;
        }
        else {
            $img_base_path = URL::to('/').'/';
            $image_name_divide_arr = explode('.', $image);
            $img_name = $image_name_divide_arr[0];
            $img_ext = end($image_name_divide_arr);
            return $img_name2 = $img_base_path.'photos/thumb/'.$img_name.'/'.$img_ext;
        }
    }

    public static function getFormattedImage2($image) {
        if($image=='') {
            return $image;
        }
        else if(strpos($image,'graph.facebook.com') !== false) {
            return $image;
        }
        else {
            $img_base_path = URL::to('/').'/';
            $image_name_divide_arr = explode('.', $image);
            $img_name = $image_name_divide_arr[0];
            $img_ext = end($image_name_divide_arr);
            return $img_name2 = $img_base_path.'cover/thumb/'.$img_name.'/'.$img_ext;
        }
    }

    public static function getUserIdByToken($access_token) {
        $user_data = DB::table('users')->select('id')->where('access_token', $access_token)->first();
        return $user_data->id;
    }

    // Image Upload Methods
    // public static function uploadImage() {
    //     if(Input::file('image')->isValid()){
    //         // store file input in a variable
    //         $file = Input::file('image');
    //         //get extension of file
    //         $ext = $file->getClientOriginalExtension();
    //         //directory to store images
    //         $dir = 'uploads';
    //         // change filename to random name
    //         $filename = substr(time(), 0, 15).str_random(30) . ".{$ext}";
    //         // move uploaded file to temp. directory
    //         $upload_success = Input::file('image')->move($dir, $filename);
    //         $img = $upload_success ? $filename : '';
    //     }
    //     return $img;
    // }

    public static function uploadImage() {
        if(Input::file('image')->isValid()) {

            // store file input in a variable
            $image = Input::file('image');
            //get extension of file
            $ext = $image->getClientOriginalExtension();
            // change filename to random name
            $filename = substr(time(), 0, 15).str_random(30) . ".{$ext}";            

            $s3 = AWS::get('s3');
            $s3->putObject(array(
                'Bucket'     => 'whatashaadi',
                'Key'        => 'uploads/'.$filename,
                'SourceFile' => $image->getPathname(),
                'ContentType' => 'images/jpeg',
                'ACL' => 'public-read'
            ));

            return $filename;            
        }
    }

    public static function uploadCoverImage() {
        if(Input::file('cover_image')->isValid()) {

            // store file input in a variable
            $image = Input::file('cover_image');
            //get extension of file
            $ext = $image->getClientOriginalExtension();
            // change filename to random name
            $filename = substr(time(), 0, 15).str_random(30) . ".{$ext}";            

            $s3 = AWS::get('s3');
            $s3->putObject(array(
                'Bucket'     => 'whatashaadi',
                'Key'        => 'uploads/'.$filename,
                'SourceFile' => $image->getPathname(),
                'ContentType' => 'images/jpeg',
                'ACL' => 'public-read'
            ));

            return $filename;            
        }
    }    

    // Upload Multiple Images
    // public static function uploadMultipleImages($image_count){
    //     for($i=0; $i<$image_count; $i++) {
    //         $img_name = "images".$i;
    //         $imgfile = Input::file($img_name);
    //         if($imgfile->isValid()) {
    //             $destinationPath = 'uploads'; // upload path
    //             $extension = $imgfile->getClientOriginalExtension(); // getting image extension
    //             $fileName = substr(time(), 0, 15).str_random(30) . '.' . $extension; // renaming image
    //             $img_name_array[] = $fileName;
    //             $imgfile->move($destinationPath, $fileName); // uploading file to given path
    //         }
    //     }
    //     return $img_name_array;
    //     //return $img_name_array2 = json_encode($img_name_array);
    // }

    public static function uploadMultipleImages($image_count){
        for($i=0; $i<$image_count; $i++) {

            $img_name = "images".$i;
            $imgfile = Input::file($img_name);
            if($imgfile->isValid()) {                
                //get extension of file
                $ext = $imgfile->getClientOriginalExtension();
                // change filename to random name
                $filename = substr(time(), 0, 15).str_random(30) . ".{$ext}";
                $img_name_array[] = $filename;

                $s3 = AWS::get('s3');
                $s3->putObject(array(
                    'Bucket'     => 'whatashaadi',
                    'Key'        => 'uploads/'.$filename,
                    'SourceFile' => $imgfile->getPathname(),
                    'ContentType' => 'images/jpeg',
                    'ACL' => 'public-read'
                ));                

            }
        }
        //return $img_name_array2 = json_encode($img_name_array);
        return $img_name_array;
    }

    // Handle Multiple Images Upload And Replace If Editted
    public static function handleMultipleImageEdit($image_count, $input, $user_id) {

        $img_base_path = URL::to('/').'/uploads/';

        for($i=0; $i<$image_count; $i++) {
            $img_name = "images".$i;

            $imgfile = Input::file($img_name);

            if($imgfile=="") {
                $img_url = $input[$img_name];
                $img_url_name_arr = explode("/",$img_url);
                $url_name_last = end($img_url_name_arr);
                $url_name_last_array[] = $url_name_last;
                $img_name_array[] = $url_name_last;
            }
            else {
                if($imgfile->isValid()) {
                    $destinationPath = 'uploads'; // upload path
                    $extension = $imgfile->getClientOriginalExtension(); // getting image extension
                    $fileName = str_random(40) . '.' . $extension; // renaming image
                    $img_name_array[] = $fileName;
                    $imgfile->move($destinationPath, $fileName); // uploading file to given path
                }
            }
        }            

        $img_name_db = DB::select("SELECT `image` FROM `vendor_portfolio_images` WHERE `user_id`='$user_id'");
        foreach ($img_name_db as $key => $value) {
            $img_name_db_array[] = $img_name_db[$key]->image;
        }

        $img_del_array = array_diff($img_name_db_array, $img_name_array);        

        foreach ($img_del_array as $key2 => $value2) {            

            if(file_exists($img_base_path.$img_del_array[$key2])) {
                unlink($img_base_path.$img_del_array[$key2]);
            }
        }

        return $img_name_array;

    }

    public static function viewVendorProfileDetails($user_id) {

        // Vendor Details
        $vendor_details = DB::select(
            "SELECT `users`.`id` AS `user_id`, `users`.`id` AS `vendor_id`, `users`.`name`, `users`.`email`, `users`.`password`, `users`.`access_token`, `users`.`fb_id`, `users`.`gender`, `users`.`image`, `users`.`phone_no`, `users`.`phone_no_2`, `users`.`phone_no_3`, `users`.`user_role`, `users`.`approved`,
            `vendor_details`.`id` AS `vendor_details_id`, `vendor_details`.`business_name`, `vendor_details`.`business_type`, `vendor_details`.`description`, `vendor_details`.`location`, `vendor_details`.`lat`, `vendor_details`.`lng`, `vendor_details`.`average_cost`,
            (SELECT `image` FROM `vendor_portfolio_images` WHERE `user_id` = `users`.`id` LIMIT 1) AS `vendor_portfolio_image`,
            (SELECT AVG(`rating`) FROM `vendor_reviews` WHERE `vendor_id` = `users`.`id`) AS `vendor_rating`
            FROM `users`
            JOIN `vendor_details` ON `vendor_details`.`user_id` = `users`.`id`
            WHERE `users`.`id` = '$user_id'
        ");            

        $vendor_details[0]->image = Users::getFormattedImage($vendor_details[0]->image);

        // Manage Null

        if($vendor_details[0]->email==null) {
            $vendor_details[0]->email="";
        }

        if($vendor_details[0]->access_token==null) {
            $vendor_details[0]->access_token="";
        }            

        if($vendor_details[0]->fb_id==null) {
            $vendor_details[0]->fb_id="";
        }

        if($vendor_details[0]->phone_no==null || $vendor_details[0]->phone_no==0) {
            $vendor_details[0]->phone_no="";
        }

        if($vendor_details[0]->phone_no==null || $vendor_details[0]->phone_no_2==0) {
            $vendor_details[0]->phone_no_2="";
        }

        if($vendor_details[0]->phone_no==null || $vendor_details[0]->phone_no_3==0) {
            $vendor_details[0]->phone_no_3="";
        }

        // Manage Portfolio Response
        if($vendor_details[0]->vendor_portfolio_image == null)
            $vendor_details[0]->vendor_portfolio_image  = "";
        else 
            $vendor_details[0]->vendor_portfolio_image = Users::getFormattedImage($vendor_details[0]->vendor_portfolio_image);

        $vendor_details[0]->vendor_rating = round($vendor_details[0]->vendor_rating, 1);
        $vendor_details[0]->vendor_rating = strval($vendor_details[0]->vendor_rating);

        // Vendor Extra Details
        $vendor_extra_details = DB::table('vendor_extra_details')->select('user_id', 'detail_name', 'detail_desc')->where('user_id', $user_id)->get();

        // Vendor Review Details
        $vendor_review_details = DB::select(
            "SELECT `id`, `user_id`, `vendor_id`, `rating`, `feedback`, `created_at`,
            (SELECT `image` FROM `users` WHERE `id` = `vendor_reviews`.`user_id`) AS `user_image`,
            (SELECT `name` FROM `users` WHERE `id` = `vendor_reviews`.`user_id`) AS `user_name`,
            CASE
                WHEN DATEDIFF(UTC_TIMESTAMP, created_at) != 0 THEN CONCAT(DATEDIFF(UTC_TIMESTAMP, created_at) ,' d ago')
                WHEN HOUR(TIMEDIFF(UTC_TIMESTAMP, created_at)) != 0 THEN CONCAT(HOUR(TIMEDIFF(UTC_TIMESTAMP, created_at)) ,' h ago')
                WHEN MINUTE(TIMEDIFF(UTC_TIMESTAMP, created_at)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(UTC_TIMESTAMP, created_at)) ,' m ago')
                ELSE
                CONCAT(SECOND(TIMEDIFF(UTC_TIMESTAMP, created_at)) ,' s ago')
            END as time_since
            FROM `vendor_reviews`
            WHERE `vendor_id` = '$user_id'
        ");

        foreach ($vendor_review_details as $key3 => $value3) {
            $vendor_review_details[$key3]->user_image = Users::getFormattedImage($vendor_review_details[$key3]->user_image);
        }

        // Portfolio Images
        $vendor_portfolio_images = DB::table('vendor_portfolio_images')->select('id', 'user_id', 'image')->where('user_id', $user_id)->get();

        foreach ($vendor_portfolio_images as $key2 => $value2) {
            $vendor_portfolio_images[$key2]->image = Users::getFormattedImage($vendor_portfolio_images[$key2]->image);
        }

        return Response::json(array('status'=>1, 'msg'=>'Vendor Details', 'vendor_details'=>$vendor_details[0], 'vendor_extra_details'=>$vendor_extra_details, 'vendor_review_details'=>$vendor_review_details, 'vendor_portfolio_images'=>$vendor_portfolio_images), 200);

    }

    public static function viewVendorProfileDetails2($user_id) {

        // Vendor Details
        $vendor_details = DB::select(
            "SELECT `users`.`id` AS `user_id`, `users`.`id` AS `vendor_id`, `users`.`name`, `users`.`email`, `users`.`password`, `users`.`access_token`, `users`.`fb_id`, `users`.`gender`, `users`.`image`, `users`.`phone_no`, `users`.`phone_no_2`, `users`.`phone_no_3`, `users`.`user_role`, `users`.`approved`,
            `vendor_details`.`id` AS `vendor_details_id`, `vendor_details`.`business_name`, `vendor_details`.`business_type`, `vendor_details`.`description`, `vendor_details`.`location`, `vendor_details`.`lat`, `vendor_details`.`lng`, `vendor_details`.`average_cost`,
            (SELECT `image` FROM `vendor_portfolio_images` WHERE `user_id` = `users`.`id` LIMIT 1) AS `vendor_portfolio_image`,
            (SELECT AVG(`rating`) FROM `vendor_reviews` WHERE `vendor_id` = `users`.`id`) AS `vendor_rating`            
            FROM `users`
            JOIN `vendor_details` ON `vendor_details`.`user_id` = `users`.`id`
            WHERE `users`.`id` = '$user_id'
        ");            

        $vendor_details[0]->image = Users::getFormattedImage($vendor_details[0]->image);

        // Manage Null

        if($vendor_details[0]->email==null) {
            $vendor_details[0]->email="";
        }

        if($vendor_details[0]->access_token==null) {
            $vendor_details[0]->access_token="";
        }            

        if($vendor_details[0]->fb_id==null) {
            $vendor_details[0]->fb_id="";
        }

        if($vendor_details[0]->phone_no==null || $vendor_details[0]->phone_no==0) {
            $vendor_details[0]->phone_no="";
        }

        if($vendor_details[0]->phone_no==null || $vendor_details[0]->phone_no_2==0) {
            $vendor_details[0]->phone_no_2="";
        }

        if($vendor_details[0]->phone_no==null || $vendor_details[0]->phone_no_3==0) {
            $vendor_details[0]->phone_no_3="";
        }

        // Manage Portfolio Response
        if($vendor_details[0]->vendor_portfolio_image == null)
            $vendor_details[0]->vendor_portfolio_image  = "";
        else 
            $vendor_details[0]->vendor_portfolio_image = Users::getFormattedImage($vendor_details[0]->vendor_portfolio_image);

        $vendor_details[0]->vendor_rating = round($vendor_details[0]->vendor_rating, 1);
        $vendor_details[0]->vendor_rating = strval($vendor_details[0]->vendor_rating);
        
        return $vendor_details[0];

    }

    public static function viewVendorProfileDetails3($user_id, $user_id_2) {

        // Vendor Details
        $vendor_details = DB::select(
            "SELECT `users`.`id` AS `user_id`, `users`.`id` AS `vendor_id`, `users`.`name`, `users`.`email`, `users`.`password`, `users`.`access_token`, `users`.`fb_id`, `users`.`gender`, `users`.`image`, `users`.`image` AS `cover_image`, `users`.`phone_no`, `users`.`phone_no_2`, `users`.`phone_no_3`, `users`.`user_role`, `users`.`approved`,
            `vendor_details`.`id` AS `vendor_details_id`, `vendor_details`.`business_name`, `vendor_details`.`business_type`, `vendor_details`.`description`, `vendor_details`.`location`, `vendor_details`.`lat`, `vendor_details`.`lng`, `vendor_details`.`average_cost`,
            (SELECT `image` FROM `vendor_portfolio_images` WHERE `user_id` = `users`.`id` LIMIT 1) AS `vendor_portfolio_image`,
            (SELECT AVG(`rating`) FROM `vendor_reviews` WHERE `vendor_id` = `users`.`id`) AS `vendor_rating`,
            (SELECT `id` FROM `favorite` WHERE `user_id` = '$user_id_2' AND `vendor_id` = `users`.`id`) AS `is_fav`
            FROM `users`
            JOIN `vendor_details` ON `vendor_details`.`user_id` = `users`.`id`
            WHERE `users`.`id` = '$user_id'
        ");            

        $vendor_details[0]->image = Users::getFormattedImage($vendor_details[0]->image);

        // Manage Null        

        if($vendor_details[0]->email==null) {
            $vendor_details[0]->email="";
        }

        if($vendor_details[0]->access_token==null) {
            $vendor_details[0]->access_token="";
        }            

        // Manage Cover Image Response
        if($vendor_details[0]->cover_image == null)
            $vendor_details[0]->cover_image  = "";
        else 
            $vendor_details[0]->cover_image = Users::getFormattedImage2($vendor_details[0]->cover_image);

        if($vendor_details[0]->fb_id==null) {
            $vendor_details[0]->fb_id="";
        }

        if($vendor_details[0]->is_fav==null) 
            $vendor_details[0]->is_fav="0";        
        else 
            $vendor_details[0]->is_fav="1";   

        if($vendor_details[0]->phone_no==null || $vendor_details[0]->phone_no==0) {
            $vendor_details[0]->phone_no="";
        }

        if($vendor_details[0]->phone_no==null || $vendor_details[0]->phone_no_2==0) {
            $vendor_details[0]->phone_no_2="";
        }

        if($vendor_details[0]->phone_no==null || $vendor_details[0]->phone_no_3==0) {
            $vendor_details[0]->phone_no_3="";
        }     

        // Manage Portfolio Response
        if($vendor_details[0]->vendor_portfolio_image == null)
            $vendor_details[0]->vendor_portfolio_image  = "";
        else 
            $vendor_details[0]->vendor_portfolio_image = Users::getFormattedImage($vendor_details[0]->vendor_portfolio_image);

        $vendor_details[0]->vendor_rating = round($vendor_details[0]->vendor_rating, 1);
        $vendor_details[0]->vendor_rating = strval($vendor_details[0]->vendor_rating);
        
        return $vendor_details[0];

    }

    public static function viewVendorExtraDetails($user_id) {        

        // Vendor Extra Details
        $vendor_extra_details = DB::table('vendor_extra_details')->select('user_id', 'detail_name', 'detail_desc')->where('user_id', $user_id)->get();
        return $vendor_extra_details;

    }

    public static function viewVendorReviewDetails($user_id) {        

        // Vendor Review Details
        $vendor_review_details = DB::select(
            "SELECT `id`, `user_id`, `vendor_id`, `rating`, `feedback`, `created_at`,
            (SELECT `image` FROM `users` WHERE `id` = `vendor_reviews`.`user_id`) AS `user_image`,
            (SELECT `name` FROM `users` WHERE `id` = `vendor_reviews`.`user_id`) AS `user_name`,
            CASE
                WHEN DATEDIFF(UTC_TIMESTAMP, created_at) != 0 THEN CONCAT(DATEDIFF(UTC_TIMESTAMP, created_at) ,' d ago')
                WHEN HOUR(TIMEDIFF(UTC_TIMESTAMP, created_at)) != 0 THEN CONCAT(HOUR(TIMEDIFF(UTC_TIMESTAMP, created_at)) ,' h ago')
                WHEN MINUTE(TIMEDIFF(UTC_TIMESTAMP, created_at)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(UTC_TIMESTAMP, created_at)) ,' m ago')
                ELSE
                CONCAT(SECOND(TIMEDIFF(UTC_TIMESTAMP, created_at)) ,' s ago')
            END as time_since
            FROM `vendor_reviews`
            WHERE `vendor_id` = '$user_id'
            ORDER BY `id` DESC
        ");

        foreach ($vendor_review_details as $key3 => $value3) {
            $vendor_review_details[$key3]->user_image = Users::getFormattedImage($vendor_review_details[$key3]->user_image);
        }

        // Portfolio Images
        $vendor_portfolio_images = DB::table('vendor_portfolio_images')->select('id', 'user_id', 'image')->where('user_id', $user_id)->get();

        foreach ($vendor_portfolio_images as $key2 => $value2) {
            $vendor_portfolio_images[$key2]->image = Users::getFormattedImage($vendor_portfolio_images[$key2]->image);
        }

        return $vendor_review_details;

    }

    public static function viewVendorPortfolioImages($user_id) {                

        // Portfolio Images
        $vendor_portfolio_images = DB::table('vendor_portfolio_images')->select('id', 'user_id', 'image')->where('user_id', $user_id)->get();

        foreach ($vendor_portfolio_images as $key2 => $value2) {
            $vendor_portfolio_images[$key2]->image = Users::getFormattedImage($vendor_portfolio_images[$key2]->image);
        }

        return $vendor_portfolio_images;

    }

    public static function viewVendorSimilar($user_id_2, $user_id, $lat, $lng) { 

        if($lat!="" && $lng!="") {
            $distance_query = "( 6373 * acos( cos( radians($lat) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin(radians(lat)) ) ) AS `distance`";            
        }   
        else {
            $distance_query = "'-' AS `distance`";            
        }    

        // Vendor Similar
        $vendor_similar = DB::select(
            "SELECT `users`.`id` AS `user_id`, `users`.`id` AS `vendor_id`, `users`.`name`,
            `vendor_details`.`id` AS `vendor_details_id`, `vendor_details`.`business_name`, `vendor_details`.`business_type`, `vendor_details`.`average_cost`,
            (SELECT `image` FROM `vendor_portfolio_images` WHERE `user_id` = `users`.`id` LIMIT 1) AS `vendor_portfolio_image`,
            (SELECT AVG(`rating`) FROM `vendor_reviews` WHERE `vendor_id` = `users`.`id`) AS `vendor_rating`,
            (SELECT `id` FROM `favorite` WHERE `user_id` = '$user_id' AND `vendor_id` = `users`.`id`) AS `is_fav`,
            (SELECT count(`id`) FROM `vendor_reviews` WHERE `vendor_id` = `users`.`id`) AS `review_count`,
            $distance_query
            FROM `users`
            JOIN `vendor_details` ON `vendor_details`.`user_id` = `users`.`id`
            WHERE `vendor_details`.`business_type` = (SELECT `business_type` FROM `vendor_details` WHERE `user_id` = '$user_id_2') AND `users`.`user_role` = '1' AND `users`.`id` != '$user_id_2' AND `users`.`approved` = '1'
        ");        

        foreach ($vendor_similar as $key => $value) {

            // Manage Portfolio Response
            if($vendor_similar[$key]->vendor_portfolio_image == null)
                $vendor_similar[$key]->vendor_portfolio_image  = "";
            else 
                $vendor_similar[$key]->vendor_portfolio_image = Users::getFormattedImage($vendor_similar[$key]->vendor_portfolio_image);            

            // Manage Favorite Response
            if($vendor_similar[$key]->is_fav == null)
                $vendor_similar[$key]->is_fav  = "0";
            else 
                $vendor_similar[$key]->is_fav  = "1";

            // Manage Favorite Response
            if($vendor_similar[$key]->vendor_rating == null)
                $vendor_similar[$key]->vendor_rating  = "";
            else
                $vendor_similar[$key]->vendor_rating = round($vendor_similar[$key]->vendor_rating, 1);

            if($vendor_similar[$key]->distance != "-") {
                $vendor_similar[$key]->distance = round($vendor_similar[$key]->distance, 2);
                $vendor_similar[$key]->distance = strval($vendor_similar[$key]->distance);
            }         

            $vendor_similar[$key]->sub_string = $vendor_similar[$key]->business_type.' • '.$vendor_similar[$key]->review_count.' Review • '.$vendor_similar[$key]->distance.' km away';   

        }

        return $vendor_similar;

    }

    public static function viewUserProfileDetails($user_id) {        

        // User Details
        $user_details = DB::select(
            "SELECT `users`.`id` AS `user_id`, `users`.`name`, `users`.`email`, `users`.`access_token`, `users`.`fb_id`, `users`.`gender`, `users`.`image`, `users`.`image` AS `cover_image`, `users`.`phone_no`, `users`.`user_role`, `users`.`approved`, 
                `wedding`.`id` AS `wedding_id`, `wedding`.`name` AS `wedding_name`, `wedding`.`description`, `wedding`.`date`, `wedding`.`wedding_type`, `wedding`.`location`, `wedding`.`lat`, `wedding`.`lng`
            FROM `users` 
            LEFT JOIN `wedding` ON `wedding`.`user_id` = `users`.`id`           
            WHERE `users`.`id` = '$user_id'
        ");            

        foreach ($user_details as $key => $value) {
            
            $user_details[$key]->image = Users::getFormattedImage($user_details[$key]->image);
            $user_details[$key]->cover_image = Users::getFormattedImage2($user_details[$key]->cover_image);
            $user_details[$key]->date = date("d F Y", strtotime($user_details[$key]->date));        

            // Manage Null

            if($user_details[$key]->name==null) 
                $user_details[$key]->name="";            

            if($user_details[$key]->email==null) {
                $user_details[$key]->email="";
            }

            if($user_details[$key]->access_token==null) {
                $user_details[$key]->access_token="";
            }            

            if($user_details[$key]->fb_id==null) {
                $user_details[$key]->fb_id="";
            }

            if($user_details[$key]->wedding_id==null) 
                $user_details[$key]->wedding_id="";   

            if($user_details[$key]->description==null) 
                $user_details[$key]->description="";   

            if($user_details[$key]->wedding_type==null) 
                $user_details[$key]->wedding_type="";   

            if($user_details[$key]->location==null) 
                $user_details[$key]->location="";   

            if($user_details[$key]->lat==null) 
                $user_details[$key]->lat="";

            if($user_details[$key]->lng==null) 
                $user_details[$key]->lng="";   

        }        

        return $user_details[0];

        //return Response::json(array('status'=>1, 'msg'=>'User Details', 'user_details'=>$user_details), 200);

    }

    // View Profile Hit
    public static function viewProfileHit($user_id) {

        $current_time = Carbon::now();

        $current_time_start = $current_time->year.'-'.$current_time->month.'-'.$current_time->day.' 00:00:00';
        $current_time_end = $current_time->year.'-'.$current_time->month.'-'.$current_time->day.' 23:59:59';        

        $profile_hit_count = DB::table('profile_view_count')->select('profile_view_count')->where('user_id', $user_id)->whereBetween('created_at', [$current_time_start, $current_time_end])->first();

        if(!empty($profile_hit_count)) {  

            $profile_hit_new = $profile_hit_count->profile_view_count + 1;
            DB::table('profile_view_count')->where('user_id', $user_id)->update(['profile_view_count' => $profile_hit_new, 'updated_at' => $current_time]);

        }
        else {
            DB::table('profile_view_count')->insertGetId(array(
                'user_id' => $user_id,
                'profile_view_count' => 1,
                'created_at' => $current_time,
                'updated_at' => $current_time,
            ));
        }

    }

    // View Profile Hit
    public static function searchCategoryHit($business_type_id) {

        $current_time = Carbon::now();

        $current_time_start = $current_time->year.'-'.$current_time->month.'-'.$current_time->day.' 00:00:00';
        $current_time_end = $current_time->year.'-'.$current_time->month.'-'.$current_time->day.' 23:59:59';        

        $search_category_hit_count = DB::table('category_search_hits')->select('search_category_count')->where('business_type_id', $business_type_id)->whereBetween('created_at', [$current_time_start, $current_time_end])->first();

        if(!empty($search_category_hit_count)) {  

            $search_category_hit_new = $search_category_hit_count->search_category_count + 1;
            DB::table('category_search_hits')->where('business_type_id', $business_type_id)->update(['search_category_count' => $search_category_hit_new, 'updated_at' => $current_time]);

        }
        else {
            DB::table('category_search_hits')->insertGetId(array(
                'business_type_id' => $business_type_id,
                'search_category_count' => 1,
                'created_at' => $current_time,
                'updated_at' => $current_time,
            ));
        }        

    }

    public static function markRecentlyViewedVendor($vendor_id, $user_id) { 

        $current_time = Carbon::now();

        $check_recently_viewed_vendors = DB::table('recently_viewed_vendors')->select('id')->where('user_id', $user_id)->where('vendor_id', $vendor_id)->first();

        if(empty($check_recently_viewed_vendors)) {
            DB::table('recently_viewed_vendors')->insertGetId(array(
                'user_id' => $user_id,
                'vendor_id' => $vendor_id,
                'created_at' => $current_time,
                'updated_at' => $current_time,
            ));
        }      
        else {
            DB::table('recently_viewed_vendors')->where('id', $check_recently_viewed_vendors->id)->delete();

            DB::table('recently_viewed_vendors')->insertGetId(array(
                'user_id' => $user_id,
                'vendor_id' => $vendor_id,
                'created_at' => $current_time,
                'updated_at' => $current_time,
            ));
        }  

        $count_recently_viewed_vendors = DB::table('recently_viewed_vendors')->select('id')->where('user_id', $user_id)->orderBy('id', 'desc')->get();        

        if(count($count_recently_viewed_vendors)>5) {
            $last_id = $count_recently_viewed_vendors[4]->id;

            DB::table('recently_viewed_vendors')->where('id', '<', $last_id)->delete(); 
        }

    }

    public static function checkUserInvitation($user_id, $phone_no) {

        if($phone_no != "") {

            $current_time = Carbon::now();            

            $check_invitation = DB::table('invitations')->select('id', 'user_id', 'collaborator_id')->where('phone_no', $phone_no)->first();
            if(!empty($check_invitation)) {    

                $collaborator_member_check = DB::table('collaborator_members')->select('id')->where('collaborator_id', $check_invitation->collaborator_id)->where('user_id_2', $user_id)->first();                

                if(empty($collaborator_member_check)) {             

                    $collaborator_members_id = DB::table('collaborator_members')->insertGetId(array(
                        'collaborator_id' => $check_invitation->collaborator_id,
                        'user_id_1' => $check_invitation->user_id,
                        'user_id_2' => $user_id,
                        'created_at' => $current_time,
                        'updated_at' => $current_time,
                    ));                

                }
            }

            DB::table('users')->where('id', $user_id)->update(['user_role' => '2']);     

            return 1;   

        }     
    }

    public static function checkUserRole($user_id) {
        $user_detail = DB::table('users')->select('user_role')->where('id', $user_id)->first();
        return $user_detail->user_role;
    }

    // Handling Device Token
    public static function updateDeviceToken($user_id, $device_token) {
        if(isset($device_token)) { 

            $chk_device_id = DB::select("SELECT `id` FROM `users` WHERE `device_token`='$device_token'");
            if(count($chk_device_id)!=0) {                
                DB::update("UPDATE `users` SET `device_token`='' WHERE `device_token`='$device_token'");
                DB::update("UPDATE `users` SET `device_token`='$device_token' WHERE `id`='$user_id'");
            }
            else {
                DB::update("UPDATE `users` SET `device_token`='$device_token' WHERE `id`='$user_id'");
            }
        }
    }

    // Handling Reg Id
    public static function updateRegId($user_id, $reg_id) {
        if(isset($reg_id)) { 

            $chk_reg_id = DB::select("SELECT `id` FROM `users` WHERE `reg_id`='$reg_id'");
            if(count($chk_reg_id)!=0) {                
                DB::update("UPDATE `users` SET `reg_id`='' WHERE `reg_id`='$reg_id'");
                DB::update("UPDATE `users` SET `reg_id`='$reg_id' WHERE `id`='$user_id'");
            }
            else {
                DB::update("UPDATE `users` SET `reg_id`='$reg_id' WHERE `id`='$user_id'");
            }
        }
    }

    public static function getVendorBusiness() {

        $businesses = DB::select(
            "SELECT `businesses`.`id` AS `business_id`, `businesses`.`business`, `businesses`.`image`, 
            `sub_businesses`.`id` AS `sub_business_id`, `sub_businesses`.`sub_business`
            FROM `businesses`            
            LEFT JOIN `sub_businesses` ON `businesses`.`id` = `sub_businesses`.`business_id`
        ");        

        foreach ($businesses as $key => $value) {
                
            if(!isset($final[$value->business_id])){

                $final[$value->business_id]=array(
                    "business_id"=>$value->business_id,
                    "business_name"=>$value->business,
                    "business_image"=>$value->image,
                    "sub_business"=>array()
                );
            }

            if(!isset($final[$value->business_id]['sub_business'][$value->sub_business_id])){

                $final[$value->business_id]['sub_business'][$value->sub_business_id]=array(
                    "sub_business_id"=>$value->sub_business_id,
                    "sub_business_name"=>$value->sub_business
                    );

            }
        }
                                    
        foreach($final as $value){
            $sub=array();
            foreach($value['sub_business'] as $value2){
                $sub[]=$value2;
            }
            $value['sub_business']=$sub;
                                                 
            $data[]=$value;
        }

        foreach ($data as $key => $value) {
            $data[$key]['business_image'] = Users::getFormattedImage($value['business_image']);

            foreach ($data[$key]['sub_business'] as $key2 => $value2) {
                if($data[$key]['sub_business'][$key2]['sub_business_id'] == null)                
                    $data[$key]['sub_business'][$key2]['sub_business_id'] = "";

                if($data[$key]['sub_business'][$key2]['sub_business_name'] == null)                
                    $data[$key]['sub_business'][$key2]['sub_business_name'] = "";
            }
            
        }

        return $data;

    }  

    public static function cityListing() {

        $city_list = DB::table('tblcitylist')->select('city_id', 'city_name', 'state')->get();
        return $city_list;

    }

    public static function updateSubCategorySearchCount($keyword) {

        $sub_business_data = DB::table('sub_businesses')->select('id', 'search_count')->where('sub_business', $keyword)->first();

        if(!empty($sub_business_data)) {
            $value = $sub_business_data->search_count + 1;
            DB::table('sub_businesses')->where('id', $sub_business_data->id)->update(['search_count' => $value]);        
        }

    }    

    public static function searchVendorListing($user_id, $keyword, $lat, $lng) {

        $distance_query = "'-' AS `distance`";
        if($lat!="" && $lng!="") {
            $distance_query = "( 6373 * acos( cos( radians($lat) ) * cos( radians( `vendor_details`.`lat` ) ) * cos( radians( `vendor_details`.`lng` ) - radians($lng) ) + sin( radians($lat) ) * sin(radians(`vendor_details`.`lat`)) ) ) AS `distance`";              
        }   
        else {
            $distance_query = "'-' AS `distance`";                
        }

        $vendor_listing = DB::select(
            "SELECT `vendor_details`.`user_id`, `vendor_details`.`user_id` AS `vendor_id`, 
            `users`.`approved`,
            (SELECT `name` FROM `users` WHERE `id` = `vendor_details`.`user_id`) AS `name`,
            `vendor_details`.`id` AS `vendor_details_id`, `vendor_details`.`business_name`, `vendor_details`.`business_type`, `vendor_details`.`average_cost`,            
            (SELECT `image` FROM `vendor_portfolio_images` WHERE `user_id` = `vendor_details`.`user_id` LIMIT 1) AS `vendor_portfolio_image`,
            '' AS `vendor_portfolio_images`,
            (SELECT AVG(`rating`) FROM `vendor_reviews` WHERE `vendor_id` = `vendor_details`.`user_id`) AS `vendor_rating`,
            (SELECT `id` FROM `favorite` WHERE `user_id` = '$user_id' AND `vendor_id` = `vendor_details`.`user_id` LIMIT 1) AS `is_fav`,
            (SELECT count(`id`) FROM `favorite` WHERE `vendor_id` = `vendor_details`.`user_id` LIMIT 1) AS `fav_count`,
            (SELECT count(`id`) FROM `vendor_reviews` WHERE `vendor_id` = `vendor_details`.`user_id`) AS `review_count`,
            `vendor_details`.`created_at` AS `created_at`,
            $distance_query        
            FROM `vendor_details` 
            JOIN `users` ON `users`.`id` = `vendor_details`.`user_id`
            WHERE `users`.`approved` = '1' AND `business_name` LIKE '%$keyword%' OR `business_type` LIKE '%$keyword%'

            UNION

            SELECT `vendor_details`.`user_id`, `vendor_details`.`user_id` AS `vendor_id`, 
            `users`.`approved`,
            (SELECT `name` FROM `users` WHERE `id` = `vendor_details`.`user_id`) AS `name`,
            `vendor_details`.`id` AS `vendor_details_id`, `vendor_details`.`business_name`, `vendor_details`.`business_type`, `vendor_details`.`average_cost`,            
            (SELECT `image` FROM `vendor_portfolio_images` WHERE `user_id` = `vendor_details`.`user_id` LIMIT 1) AS `vendor_portfolio_image`,
            '' AS `vendor_portfolio_images`,
            (SELECT AVG(`rating`) FROM `vendor_reviews` WHERE `vendor_id` = `vendor_details`.`user_id`) AS `vendor_rating`,
            (SELECT `id` FROM `favorite` WHERE `user_id` = '$user_id' AND `vendor_id` = `vendor_details`.`user_id` LIMIT 1) AS `is_fav`,
            (SELECT count(`id`) FROM `favorite` WHERE `vendor_id` = `vendor_details`.`user_id` LIMIT 1) AS `fav_count`,
            (SELECT count(`id`) FROM `vendor_reviews` WHERE `vendor_id` = `vendor_details`.`user_id`) AS `review_count`,
            `vendor_details`.`created_at` AS `created_at`,
            $distance_query        
            FROM `sub_businesses`             
            JOIN `businesses` ON `sub_businesses`.`business_id` = `businesses`.`id`
            JOIN `vendor_details` ON `sub_businesses`.`sub_business` = `vendor_details`.`business_type`
            JOIN `users` ON `users`.`id` = `vendor_details`.`user_id`
            WHERE `users`.`approved` = '1' AND `businesses`.`business` LIKE '%$keyword%'
        ");

        foreach ($vendor_listing as $key => $value) {                

            // Manage Portfolio Response
            if($vendor_listing[$key]->vendor_portfolio_image == null)
                $vendor_listing[$key]->vendor_portfolio_image  = "";
            else 
                $vendor_listing[$key]->vendor_portfolio_image = Users::getFormattedImage($vendor_listing[$key]->vendor_portfolio_image);

            // Manage Multiple Portfolio Response
            $curr_user_id = $vendor_listing[$key]->user_id;
            $vendor_portfolio_images_data = DB::select("SELECT `id`, `user_id`, `image` FROM `vendor_portfolio_images` WHERE `user_id` = '$curr_user_id' LIMIT 4");

            if(!empty($vendor_portfolio_images_data)) {

                foreach ($vendor_portfolio_images_data as $key2 => $value2) {
                    $value2->image = Users::getFormattedImage($value2->image);
                }

                 $vendor_listing[$key]->vendor_portfolio_images = $vendor_portfolio_images_data;

            }
            else {
                $vendor_listing[$key]->vendor_portfolio_images = array();
            }                            

            // Manage Favorite Response
            if($vendor_listing[$key]->is_fav == null)
                $vendor_listing[$key]->is_fav  = "0";
            else 
                $vendor_listing[$key]->is_fav  = "1";

            // Manage Favorite Response
            if($vendor_listing[$key]->vendor_rating == null)
                $vendor_listing[$key]->vendor_rating  = "";
            else {
                $vendor_listing[$key]->vendor_rating = round($vendor_listing[$key]->vendor_rating, 1);
                $vendor_listing[$key]->vendor_rating = strval($vendor_listing[$key]->vendor_rating);
            }                

            if($vendor_listing[$key]->distance != '-') {
                $vendor_listing[$key]->distance = round($vendor_listing[$key]->distance, 2);
                $vendor_listing[$key]->distance = strval($vendor_listing[$key]->distance);
            }               

            $vendor_listing[$key]->sub_string = $vendor_listing[$key]->business_type.' • '.$vendor_listing[$key]->review_count.' Review • '.$vendor_listing[$key]->distance.' km away';                        

        }

        return $vendor_listing;

    }

    public static function searchWeddingListing($user_id, $keyword) {

        $current_time = Carbon::now();
        $zone = "Asia/Kolkata";

        $date = new DateTime($current_time);
        $date->setTimezone(new \DateTimeZone($zone)); 
        $current_date_time = $date->format('Y-m-d h:m:s');

        $wedding_listing = DB::select(            
            "SELECT `id`, `user_id`, `name` AS `wedding_name`, `description`, `date`, `wedding_type`, `location`, `lat`, `lng`,
            (SELECT `name` FROM `users` WHERE `id` = `wedding`.`user_id` LIMIT 1) AS `name`,
            (SELECT `image` FROM `wedding_photos` WHERE `wedding_id` = `wedding`.`id` LIMIT 1) AS `wedding_image`,
            '' AS `wedding_images`,
            (SELECT `id` FROM `favorite` WHERE `user_id` = '$user_id' AND `wedding_id` = `wedding`.`id`) AS `is_fav`,
            (SELECT count(`id`) FROM `favorite` WHERE `wedding_id` = `wedding`.`id`) AS `fav_count`,
            '-' AS `distance`
            FROM `wedding`             
            WHERE `name` LIKE '%$keyword%' AND `date` < '$current_date_time'

            UNION

            SELECT * FROM (SELECT `wedding_vendor`.`wedding_id` AS `id`,
            `wedding_vendor`.`user_id`,
            (SELECT `name` FROM `wedding` WHERE `id` = `wedding_vendor`.`wedding_id`) AS `wedding_name`,
            (SELECT `description` FROM `wedding` WHERE `id` = `wedding_vendor`.`wedding_id`) AS `description`,
            (SELECT `date` FROM `wedding` WHERE `id` = `wedding_vendor`.`wedding_id`) AS `date`,
            (SELECT `wedding_type` FROM `wedding` WHERE `id` = `wedding_vendor`.`wedding_id`) AS `wedding_type`,
            (SELECT `location` FROM `wedding` WHERE `id` = `wedding_vendor`.`wedding_id`) AS `location`,
            (SELECT `lat` FROM `wedding` WHERE `id` = `wedding_vendor`.`wedding_id`) AS `lat`,
            (SELECT `lng` FROM `wedding` WHERE `id` = `wedding_vendor`.`wedding_id`) AS `lng`,

            (SELECT `name` FROM `users` WHERE `id` = `wedding_vendor`.`user_id` LIMIT 1) AS `name`,
            (SELECT `image` FROM `wedding_photos` WHERE `wedding_id` = `wedding_vendor`.`wedding_id` LIMIT 1) AS `wedding_image`,
            '' AS `wedding_images`,
            (SELECT `id` FROM `favorite` WHERE `user_id` = '$user_id' AND `wedding_id` = `wedding_vendor`.`wedding_id`) AS `is_fav`,
            (SELECT count(`id`) FROM `favorite` WHERE `wedding_id` = `wedding_vendor`.`wedding_id`) AS `fav_count`,
            '-' AS `distance`

            FROM `vendor_details`
            JOIN `wedding_vendor` ON `wedding_vendor`.`vendor_id` = `vendor_details`.`user_id`            
            WHERE `business_name` LIKE '%$keyword%') as `temp` 
            WHERE `temp`.`date` < '$current_date_time'
        ");

        foreach ($wedding_listing as $key => $value) {

            if($value->wedding_name == null)
                $value->wedding_name = "";

            if($value->description == null)
                $value->description = "";

            if($value->date == null)
                $value->date = "";

            if($value->wedding_type == null)
                $value->wedding_type = "";

            if($value->location == null)
                $value->location = "";

            if($value->lat == null)
                $value->lat = "";

            if($value->lng == null)
                $value->lng = "";

            // Manage Wedding Image
            if($wedding_listing[$key]->wedding_image != NULL)
                $wedding_listing[$key]->wedding_image = Users::getFormattedImage($wedding_listing[$key]->wedding_image);
            else 
                $wedding_listing[$key]->wedding_image = "";

            // Manage Multiple Portfolio Response
            $wedding_id_2 = $wedding_listing[$key]->id;            
            $wedding_listing[$key]->wedding_images = Wedding::weddingPhotosByWeddingId($wedding_id_2);            

            // Manage Favorite
            if($wedding_listing[$key]->is_fav != NULL)
                $wedding_listing[$key]->is_fav = "1";
            else 
                $wedding_listing[$key]->is_fav = "0";
        }

        return $wedding_listing;

        // Query for reference
        // SELECT `vendor_details`.`id` AS `vendor_details_id`, `vendor_details`.`business_name`, `wedding_vendor`.`id` AS `wedding_vendor_id`
        // FROM `vendor_details`
        // JOIN `wedding_vendor` ON `wedding_vendor`.`vendor_id` = `vendor_details`.`user_id`
        // WHERE `business_name` LIKE '%Boom%'    

    }

    public static function getContractedVendors($user_id, $lat, $lng) {

        $distance_query = "'-' AS `distance`";
        if($lat!="" && $lng!="") {
            $distance_query = "( 6373 * acos( cos( radians($lat) ) * cos( radians( (SELECT `lat` FROM `vendor_details` WHERE `user_id` = `wedding_vendor`.`vendor_id`) ) ) * cos( radians( (SELECT `lng` FROM `vendor_details` WHERE `user_id` = `wedding_vendor`.`vendor_id`) ) - radians($lng) ) + sin( radians($lat) ) * sin(radians((SELECT `lat` FROM `vendor_details` WHERE `user_id` = `wedding_vendor`.`vendor_id`))) ) ) AS `distance`";              
        }   
        else {
            $distance_query = "'-' AS `distance`";                
        }

        $vendor_listing = DB::select(
            "SELECT 
            (SELECT `user_id` FROM `vendor_details` WHERE `user_id` = `wedding_vendor`.`vendor_id`) AS `user_id`,
            (SELECT `user_id` FROM `vendor_details` WHERE `user_id` = `wedding_vendor`.`vendor_id`) AS `vendor_id`,
            (SELECT `name` FROM `users` WHERE `id` = `wedding_vendor`.`vendor_id`) AS `name`,
            (SELECT `id` FROM `vendor_details` WHERE `user_id` = `wedding_vendor`.`vendor_id`) AS `vendor_details_id`,
            (SELECT `business_name` FROM `vendor_details` WHERE `user_id` = `wedding_vendor`.`vendor_id`) AS `business_name`,
            (SELECT `business_type` FROM `vendor_details` WHERE `user_id` = `wedding_vendor`.`vendor_id`) AS `business_type`,
            (SELECT `average_cost` FROM `vendor_details` WHERE `user_id` = `wedding_vendor`.`vendor_id`) AS `average_cost`,

            (SELECT `image` FROM `vendor_portfolio_images` WHERE `user_id` = `wedding_vendor`.`vendor_id` LIMIT 1) AS `vendor_portfolio_image`,
            '' AS `vendor_portfolio_images`,
            (SELECT AVG(`rating`) FROM `vendor_reviews` WHERE `vendor_id` = `wedding_vendor`.`vendor_id`) AS `vendor_rating`,
            (SELECT `id` FROM `favorite` WHERE `user_id` = '$user_id' AND `vendor_id` = `wedding_vendor`.`vendor_id` LIMIT 1) AS `is_fav`,
            (SELECT count(`id`) FROM `favorite` WHERE `vendor_id` = `wedding_vendor`.`vendor_id` LIMIT 1) AS `fav_count`,
            (SELECT count(`id`) FROM `vendor_reviews` WHERE `vendor_id` = `wedding_vendor`.`vendor_id`) AS `review_count`,
            '' AS `created_at`,
            $distance_query        

            FROM `wedding_vendor`
            WHERE `user_id` = '$user_id'
        ");

        foreach ($vendor_listing as $key => $value) {                

            // Manage Portfolio Response
            if($vendor_listing[$key]->vendor_portfolio_image == null)
                $vendor_listing[$key]->vendor_portfolio_image  = "";
            else 
                $vendor_listing[$key]->vendor_portfolio_image = Users::getFormattedImage($vendor_listing[$key]->vendor_portfolio_image);

            // Manage Multiple Portfolio Response
            $curr_user_id = $vendor_listing[$key]->user_id;
            $vendor_portfolio_images_data = DB::select("SELECT `id`, `user_id`, `image` FROM `vendor_portfolio_images` WHERE `user_id` = '$curr_user_id' LIMIT 4");

            if(!empty($vendor_portfolio_images_data)) {

                foreach ($vendor_portfolio_images_data as $key2 => $value2) {
                    $value2->image = Users::getFormattedImage($value2->image);
                }

                 $vendor_listing[$key]->vendor_portfolio_images = $vendor_portfolio_images_data;

            }
            else {
                $vendor_listing[$key]->vendor_portfolio_images = array();
            }                            

            // Manage Favorite Response
            if($vendor_listing[$key]->is_fav == null)
                $vendor_listing[$key]->is_fav  = "0";
            else 
                $vendor_listing[$key]->is_fav  = "1";

            // Manage Favorite Response
            if($vendor_listing[$key]->vendor_rating == null)
                $vendor_listing[$key]->vendor_rating  = "";
            else {
                $vendor_listing[$key]->vendor_rating = round($vendor_listing[$key]->vendor_rating, 1);
                $vendor_listing[$key]->vendor_rating = strval($vendor_listing[$key]->vendor_rating);
            }                

            if($vendor_listing[$key]->distance != '-') {
                $vendor_listing[$key]->distance = round($vendor_listing[$key]->distance, 2);
                $vendor_listing[$key]->distance = strval($vendor_listing[$key]->distance);
            }                          

        }

        return $vendor_listing;

    }

    public static function getRecentlyViewedVendors($user_id, $lat, $lng) {

        // Recently Viewed Vendors
        if($lat!="" && $lng!="") {                                
            $distance_query = "( 6373 * acos( cos( radians($lat) ) * cos( radians( (SELECT `lat` FROM `vendor_details` WHERE `user_id` = `recently_viewed_vendors`.`vendor_id`) ) ) * cos( radians( (SELECT `lng` FROM `vendor_details` WHERE `user_id` = `recently_viewed_vendors`.`vendor_id`) ) - radians($lng) ) + sin( radians($lat) ) * sin(radians( (SELECT `lat` FROM `vendor_details` WHERE `user_id` = `recently_viewed_vendors`.`vendor_id`) )) ) ) AS `distance`";

            $distance_order = "ORDER BY `distance`";
        }
        else {                            
            $distance_query = "'-' AS `distance`";
            $distance_order = "";
        }

        $recently_viewed_vendors = DB::select(
            "SELECT `id`, `user_id`, `vendor_id`,
            (SELECT `name` FROM `users` WHERE `id` = `recently_viewed_vendors`.`vendor_id`) AS `vendor_name`,
            (SELECT `business_name` FROM `vendor_details` WHERE `user_id` = `recently_viewed_vendors`.`vendor_id`) AS `business_name`,
            (SELECT `business_type` FROM `vendor_details` WHERE `user_id` = `recently_viewed_vendors`.`vendor_id`) AS `business_type`,
            (SELECT `average_cost` FROM `vendor_details` WHERE `user_id` = `recently_viewed_vendors`.`vendor_id`) AS `average_cost`,

            (SELECT `image` FROM `vendor_portfolio_images` WHERE `user_id` = `recently_viewed_vendors`.`vendor_id` LIMIT 1) AS `vendor_portfolio_image`,
            (SELECT AVG(`rating`) FROM `vendor_reviews` WHERE `vendor_id` = `recently_viewed_vendors`.`vendor_id`) AS `vendor_rating`,
            (SELECT `id` FROM `favorite` WHERE `user_id` = '$user_id' AND `vendor_id` = `recently_viewed_vendors`.`vendor_id` LIMIT 1) AS `is_fav`,
            (SELECT count(`id`) FROM `vendor_reviews` WHERE `vendor_id` = `recently_viewed_vendors`.`vendor_id`) AS `review_count`,
        
            '-' AS `distance`

            FROM `recently_viewed_vendors`
            WHERE `user_id` = '$user_id'
            ORDER BY `id` DESC 
            LIMIT 3
        ");            

        foreach ($recently_viewed_vendors as $key => $value) {

            // Manage Portfolio Response
            if($recently_viewed_vendors[$key]->vendor_portfolio_image == null)
                $recently_viewed_vendors[$key]->vendor_portfolio_image  = "";
            else 
                $recently_viewed_vendors[$key]->vendor_portfolio_image = Users::getFormattedImage($recently_viewed_vendors[$key]->vendor_portfolio_image);            

            // Manage Favorite Response
            if($recently_viewed_vendors[$key]->is_fav == null)
                $recently_viewed_vendors[$key]->is_fav  = "0";
            else 
                $recently_viewed_vendors[$key]->is_fav  = "1";

            // Manage Favorite Response
            if($recently_viewed_vendors[$key]->vendor_rating == null)
                $recently_viewed_vendors[$key]->vendor_rating  = "";
            else {
                $recently_viewed_vendors[$key]->vendor_rating = round($recently_viewed_vendors[$key]->vendor_rating, 1);
                $recently_viewed_vendors[$key]->vendor_rating = strval($recently_viewed_vendors[$key]->vendor_rating);
            }    

            if($recently_viewed_vendors[$key]->distance != '-') {
                $recently_viewed_vendors[$key]->distance = round($recently_viewed_vendors[$key]->distance, 2);
                $recently_viewed_vendors[$key]->distance = strval($recently_viewed_vendors[$key]->distance);
            }    

            $recently_viewed_vendors[$key]->sub_string = $recently_viewed_vendors[$key]->business_type.' • '.$recently_viewed_vendors[$key]->review_count.' Review • '.$recently_viewed_vendors[$key]->distance.' km away';             

        }  

        return $recently_viewed_vendors;      

    }

    public static function getRecentlyViewedWeddings($user_id, $lat, $lng) {

        // Recently Viewed Weddings
        $recently_viewed_weddings = DB::select(
            "SELECT `id`, `user_id`, `wedding_id` AS `id`,
            (SELECT `name` FROM `wedding` WHERE `id` = `recently_viewed_weddings`.`wedding_id` LIMIT 1) AS `wedding_name`,
            (SELECT `description` FROM `wedding` WHERE `id` = `recently_viewed_weddings`.`wedding_id` LIMIT 1) AS `wedding_desc`,
            (SELECT `date` FROM `wedding` WHERE `id` = `recently_viewed_weddings`.`wedding_id` LIMIT 1) AS `wedding_date`,
            (SELECT `wedding_type` FROM `wedding` WHERE `id` = `recently_viewed_weddings`.`wedding_id` LIMIT 1) AS `wedding_type`,
            (SELECT `city` FROM `wedding` WHERE `id` = `recently_viewed_weddings`.`wedding_id` LIMIT 1) AS `location`,            
            (SELECT `image` FROM `wedding_photos` WHERE `wedding_id` = `recently_viewed_weddings`.`wedding_id` LIMIT 1) AS `wedding_image`,
            (SELECT `id` FROM `favorite` WHERE `user_id` = '$user_id' AND `wedding_id` = `recently_viewed_weddings`.`wedding_id` LIMIT 1) AS `is_fav`
            FROM `recently_viewed_weddings`
            WHERE `user_id` = '$user_id'
            ORDER BY `recently_viewed_weddings`.`id` DESC 
            LIMIT 3
        ");

        foreach ($recently_viewed_weddings as $key => $value) {

            if($recently_viewed_weddings[$key]->wedding_name == NULL)
                $recently_viewed_weddings[$key]->wedding_name = "";                

            if($recently_viewed_weddings[$key]->wedding_desc == NULL)
                $recently_viewed_weddings[$key]->wedding_desc = "";                

            if($recently_viewed_weddings[$key]->wedding_type == NULL)
                $recently_viewed_weddings[$key]->wedding_type = "";                

            if($recently_viewed_weddings[$key]->location == NULL)
                $recently_viewed_weddings[$key]->location = "";                

            // Manage Wedding Image
            if($recently_viewed_weddings[$key]->wedding_image != NULL)
                $recently_viewed_weddings[$key]->wedding_image = Users::getFormattedImage($recently_viewed_weddings[$key]->wedding_image);
            else 
                $recently_viewed_weddings[$key]->wedding_image = "";

            // Manage Favorite
            if($recently_viewed_weddings[$key]->is_fav != NULL)
                $recently_viewed_weddings[$key]->is_fav = "1";
            else 
                $recently_viewed_weddings[$key]->is_fav = "0";

            $recently_viewed_weddings[$key]->wedding_date = date("d F Y", strtotime($recently_viewed_weddings[$key]->wedding_date));
        }

        return $recently_viewed_weddings;

    }

    public static function getSponsorListing($user_id, $lat, $lng) {

        if($user_id=="-1")
            $is_fav_query = "'0' AS `is_fav`";
        else
            $is_fav_query = "(SELECT `id` FROM `favorite` WHERE `user_id` = '$user_id' AND `vendor_id` = `sponsors`.`vendor_id` LIMIT 1) AS `is_fav`";

        // Sponsors
        if($lat!="" && $lng!="") {                                
            $distance_query = "( 6373 * acos( cos( radians($lat) ) * cos( radians( (SELECT `lat` FROM `vendor_details` WHERE `user_id` = `sponsors`.`vendor_id`) ) ) * cos( radians( (SELECT `lng` FROM `vendor_details` WHERE `user_id` = `sponsors`.`vendor_id`) ) - radians($lng) ) + sin( radians($lat) ) * sin(radians( (SELECT `lat` FROM `vendor_details` WHERE `user_id` = `sponsors`.`vendor_id`) )) ) ) AS `distance`";

            $distance_order = "ORDER BY `distance`";
        }
        else {                            
            $distance_query = "'-' AS `distance`";
            $distance_order = "";
        }

        $sponsor_listing = DB::select(
            "SELECT `id`, `vendor_id`,
            (SELECT `name` FROM `users` WHERE `id` = `sponsors`.`vendor_id`) AS `vendor_name`,
            (SELECT `business_name` FROM `vendor_details` WHERE `user_id` = `sponsors`.`vendor_id`) AS `business_name`,
            (SELECT `business_type` FROM `vendor_details` WHERE `user_id` = `sponsors`.`vendor_id`) AS `business_type`,
            (SELECT `average_cost` FROM `vendor_details` WHERE `user_id` = `sponsors`.`vendor_id`) AS `average_cost`,

            (SELECT `image` FROM `vendor_portfolio_images` WHERE `user_id` = `sponsors`.`vendor_id` LIMIT 1) AS `vendor_portfolio_image`,
            (SELECT AVG(`rating`) FROM `vendor_reviews` WHERE `vendor_id` = `sponsors`.`vendor_id`) AS `vendor_rating`,
            $is_fav_query,
            (SELECT count(`id`) FROM `vendor_reviews` WHERE `vendor_id` = `sponsors`.`vendor_id`) AS `review_count`,        
            $distance_query
            FROM `sponsors`                    
            $distance_order
            LIMIT 10           
        ");            

        foreach ($sponsor_listing as $key => $value) {

            // Manage Portfolio Response
            if($sponsor_listing[$key]->vendor_portfolio_image == null)
                $sponsor_listing[$key]->vendor_portfolio_image  = "";
            else 
                $sponsor_listing[$key]->vendor_portfolio_image = Users::getFormattedImage($sponsor_listing[$key]->vendor_portfolio_image);            

            // Manage Favorite Response
            if($sponsor_listing[$key]->is_fav == null || $sponsor_listing[$key]->is_fav == "0")
                $sponsor_listing[$key]->is_fav  = "0";
            else 
                $sponsor_listing[$key]->is_fav  = "1";

            // Manage Favorite Response
            if($sponsor_listing[$key]->vendor_rating == null)
                $sponsor_listing[$key]->vendor_rating  = "";
            else {
                $sponsor_listing[$key]->vendor_rating = round($sponsor_listing[$key]->vendor_rating, 1);
                $sponsor_listing[$key]->vendor_rating = strval($sponsor_listing[$key]->vendor_rating);
            }    

            if($sponsor_listing[$key]->distance != '-') {
                $sponsor_listing[$key]->distance = round($sponsor_listing[$key]->distance, 2);
                $sponsor_listing[$key]->distance = strval($sponsor_listing[$key]->distance);
            }                

            $sponsor_listing[$key]->sub_string = $sponsor_listing[$key]->business_type.' • '.$sponsor_listing[$key]->review_count.' Review • '.$sponsor_listing[$key]->distance.' km away';

        }

        return $sponsor_listing;

    }

    public static function sendEmailNewsletter($email) {

        $mailchimp = app('Mailchimp');        
        $foo = new NewsletterManager($mailchimp);
        $foo->addEmailToList($email);

    }

    public static function sendVendorEmailNewsletter($email) {

        $mailchimp = app('Mailchimp');        
        $foo = new NewsletterManager($mailchimp);
        $foo->addEmailToVendorList($email);

    }

    public static function checkAutoAuthorize() {
        $auto_authorize_data = DB::table('auto_authorize')->select('status')->where('id', '1')->first();
        return $auto_authorize_data->status;
    }

    //****************************************************************************************************
    //                                      Vendor
    //****************************************************************************************************


    // Vendor User Functions

    public static function vendorSignUp($input) {

        $validation = Validator::make($input, Users::$vendorSignUpRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {
            $fb_id = isset($input['fb_id']) ? $input['fb_id'] : "";

            if($fb_id=="") {          

                $email = $input['email'];
                $check_user_email = DB::table('users')->select('id')->where('email', $email)->first();        

                if(empty($input['email'])) 
                    return Response::json(array('status'=> 0,'msg'=>'Email is required'), 200);
                else if(empty($input['phone_no']))
                    return Response::json(array('status'=> 0,'msg'=>'Phone no is required'), 200);
                else if(empty($input['password']))
                    return Response::json(array('status'=> 0,'msg'=>'Password is required'), 200);
                else if(empty($input['business_name']))
                    return Response::json(array('status'=> 0,'msg'=>'Business Name is required'), 200);
                else if(empty($input['business_type']))
                    return Response::json(array('status'=> 0,'msg'=>'Business Type is required'), 200);
                else if(empty($input['location']))
                    return Response::json(array('status'=> 0,'msg'=>'Location is required'), 200);
                else if(empty($input['image_count']))
                    return Response::json(array('status'=> 0,'msg'=>'Image count is required'), 200);
                else if(!empty($check_user_email))
                    return Response::json(array('status'=> 0,'msg'=>'Email exists'), 200);
                else {

                	$phone_no = $input['phone_no'];
                	$password = $input['password'];
                    $password = Hash::make($password);
                    $business_name = $input['business_name'];
                    $business_type = $input['business_type'];
                    $location = $input['location'];            
                    $lat = isset($input['lat']) ? $input['lat'] : '0';
                    $lng = isset($input['lng']) ? $input['lng'] : '0';
                    $imgfile = Input::file('image');
                    $image_count = $input['image_count'];
                    $current_time = Carbon::now();
                   
                    $access_token = Users::generateToken();                    

                    // Validating image count for atleast 1 image
                    if($image_count<1)
                        return Response::json(array('status'=>0, 'msg'=>'Please upload atleast one image'), 200);            

                    if($lat=='0' || $lng=='0') {
                        $latlng = Users::getLatLng($location);
                        $latlngarr = explode(',', $latlng);
                        $lat = $latlngarr[0];
                        $lng = $latlngarr[1];
                    }

                    // Handling User Profile Image
                    if($imgfile=="") {                
                        $image = "";
                    }
                    else {
                        $image = Users::uploadImage();
                    }            

                    // Check if business type is main business type
                    $check_main_business_type = DB::table('businesses')->select('id')->where('business', $business_type)->first();

                    if(!empty($check_main_business_type)){
                        $check_sub_business_type = DB::table('sub_businesses')->select('sub_business')->where('business_id', $check_main_business_type->id)->first();

                        $business_type = $check_sub_business_type->sub_business;
                    }

                    // Check auto authorize
                    $auto_authorize_status = Users::checkAutoAuthorize();

                    $user_id = DB::table('users')->insertGetId(
                        array(                    
                            'email' => $email,
                            'password' => $password,
                            'access_token' => $access_token,
                            'image' => $image,                    
                            'phone_no' => $phone_no,
                            'user_role' => '1',
                            'approved' => $auto_authorize_status,                       
                            'created_at' => $current_time,
                            'updated_at' => $current_time,
                        )
                    );

                    $vendor_detail_id = DB::table('vendor_details')->insertGetId(
                        array(                    
                            'user_id' => $user_id,
                            'business_name' => $business_name,
                            'business_type' => $business_type,
                            'location' => $location,
                            'lat' => $lat,
                            'lng' => $lng,
                            'created_at' => $current_time,
                            'updated_at' => $current_time,
                        )
                    );

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

                    // Users::sendVendorEmailNewsletter($email);
                    // $temp = Mail::send('emails.welcome-email', [], function($message)
                    // {
                    //     $input = Input::all();
                    //     $email_2 = $input['email'];

                    //     $message->to($email_2, '')->subject('Welcome to What A Shaadi');
                    // });

                    return Users::viewVendorProfileDetails($user_id);

                }

            }
            else {
                
                $fb_id = $input['fb_id'];            
                $fb_email = isset($input['email']) ? $input['email'] : null;
                $password = isset($input['password']) ? $input['password'] : "";
                $fb_image = "http://graph.facebook.com/$fb_id/picture?type=large";                                                
                $fb_phone_no = isset($input['phone_no']) ? $input['phone_no'] : '';
                $business_name = isset($input['business_name']) ? $input['business_name'] : "";
                $business_type = isset($input['business_type']) ? $input['business_type'] : "";            
                $location = isset($input['location']) ? $input['location'] : "";
                $lat = isset($input['lat']) ? $input['lat'] : '0';
                $lng = isset($input['lng']) ? $input['lng'] : '0';            
                $image_count = $input['image_count'];
                $current_time = Carbon::now();                                

                if($password!="") {
                    $password = Hash::make($password);
                }

                $access_token = Users::generateToken();

                $user_check = DB::table('users')->select('id', 'email', 'password', 'deleted_at')->where('fb_id', $fb_id)->first();

                // Fb user exists
                if(isset($user_check)) {
                    if($user_check->deleted_at!=null || $user_check->deleted_at!='')
                        return Response::json(array('status'=>0, 'msg'=>'This user is deactivated'), 200);
                    else {
                        // Updating the user Token
                        $token = Users::updateUserToken($user_check->id);
                        if($token) {                        
                            
                            $vendor_details = Users::viewVendorProfileDetails2($user_check->id);
                            $vendor_extra_details = Users::viewVendorExtraDetails($user_check->id);
                            $vendor_review_details = Users::viewVendorReviewDetails($user_check->id);
                            $vendor_portfolio_images = Users::viewVendorPortfolioImages($user_check->id);

                            return Response::json(array('status'=>1, 'msg'=>'Fb Login Success', 'vendor_details'=>$vendor_details, 'vendor_extra_details'=>$vendor_extra_details, 'vendor_review_details'=>$vendor_review_details, 'vendor_portfolio_images'=>$vendor_portfolio_images), 200);
                        }
                        // token null
                        else
                            return Response::json(array('status'=> 0,'msg'=>'User not found in database!'), 200);
                    }
                }
                // Fb first time login
                else {
                    if($fb_email!=null) {
                        $email_check = DB::table('users')->select('id')->where('email', $fb_email)->first();
                    }
                    else {
                        $email_check = array();
                    }

                    // First time login. Email does not exist
                    if(count($email_check)==0) {                    

                        if($location!="") {
                            if($lat=='0' || $lng=='0') {
                                $latlng = Users::getLatLng($location);
                                $latlngarr = explode(',', $latlng);
                                $lat = $latlngarr[0];
                                $lng = $latlngarr[1];
                            }
                        }         

                        // Check auto authorize
                        $auto_authorize_status = Users::checkAutoAuthorize();           

                        $user_id = DB::table('users')->insertGetId(
                            array( 
                                'email' => $fb_email, 
                                'password' => $password,                            
                                'access_token' => $access_token,
                                'fb_id' => $fb_id,  
                                'image' => $fb_image,                    
                                'phone_no' => $fb_phone_no,
                                'user_role' => '1',      
                                'approved' => $auto_authorize_status,                          
                                'created_at' => $current_time,
                                'updated_at' => $current_time,
                            )
                        );

                        $vendor_detail_id = DB::table('vendor_details')->insertGetId(
                            array(                    
                                'user_id' => $user_id,
                                'business_name' => $business_name,
                                'business_type' => $business_type,
                                'location' => $location,
                                'lat' => $lat,
                                'lng' => $lng,
                                'created_at' => $current_time,
                                'updated_at' => $current_time,
                            )
                        );

                        // Uploading Portfolio Photos
                        if($image_count>0) {
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
                        }                    

                        $vendor_details = Users::viewVendorProfileDetails2($user_id);
                        $vendor_extra_details = Users::viewVendorExtraDetails($user_id);
                        $vendor_review_details = Users::viewVendorReviewDetails($user_id);
                        $vendor_portfolio_images = Users::viewVendorPortfolioImages($user_id);

                        // Users::sendVendorEmailNewsletter($fb_email);
                        // $temp = Mail::send('emails.welcome-email', [], function($message)
                        // {
                        //     $input = Input::all();
                        //     if(isset($input['email'])) {
                        //         $email_2 = $input['email'];
                        //         $message->to($email_2, '')->subject('Welcome to What A Shaadi');
                        //     }                            
                        // });

                        return Response::json(array('status'=>1, 'msg'=>'First time fb login', 'vendor_details'=>$vendor_details, 'vendor_extra_details'=>$vendor_extra_details, 'vendor_review_details'=>$vendor_review_details, 'vendor_portfolio_images'=>$vendor_portfolio_images), 200);                    
                    }
                    // Email exists. Update fb id
                    else {
                        DB::table('users')->where('email', $fb_email)->update(['fb_id' => $fb_id]);
                        return Response::json(array('status'=> 0,'msg'=>'User email already exists.'), 200);
                    }
                }

            }

        }

    }

    public static function vendorLogin($input) {

        $validation = Validator::make($input, Users::$vendorLoginRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {

            $id = $input['id'];
            $password = $input['password'];
            $current_time = Carbon::now();

            $user_check = DB::table('users')->select('id', 'password', 'deleted_at')->where('email', $id)->first();            

            if(isset($user_check)) {
                if($user_check->deleted_at!=null || $user_check->deleted_at!='')
                    return Response::json(array('status'=>0, 'msg'=>'This user is deactivated'), 200);
                else {
                    if((Hash::check($password, $user_check->password))) {
                        // Updating the user Token
                        $token = Users::updateUserToken($user_check->id);
                        if($token) {                            

                        	$user_id = $user_check->id;

                            return Users::viewVendorProfileDetails($user_id);

                        }
                        // token null
                        else
                            return Response::json(array('status'=> 0,'msg'=>'User not found in database!'), 200);
                    }
                    // Password incorrect
                    else
                        return Response::json(array('status'=> 0,'msg'=>'Password Incorrect!'), 200);
                }
            }
            else
                return Response::json(array('status'=>0, 'msg'=>'Username/Email and Password do not match'), 200);
        }

    }

    public static function vendorLoginFbCheck($input) {

        $validation = Validator::make($input, Users::$vendorLoginFbCheckRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {

            $fb_id = $input['fb_id'];                        
            $current_time = Carbon::now();                                            

            $user_check = DB::table('users')->select('id', 'email', 'password', 'deleted_at')->where('fb_id', $fb_id)->first();

            // Fb user exists
            if(isset($user_check)) {
                if($user_check->deleted_at!=null || $user_check->deleted_at!='')
                    return Response::json(array('status'=>0, 'msg'=>'This user is deactivated'), 200);
                else {
                    // Updating the user Token
                    $token = Users::updateUserToken($user_check->id);
                    if($token) {                        
                        
                        $vendor_details = Users::viewVendorProfileDetails2($user_check->id);
                        $vendor_extra_details = Users::viewVendorExtraDetails($user_check->id);
                        $vendor_review_details = Users::viewVendorReviewDetails($user_check->id);
                        $vendor_portfolio_images = Users::viewVendorPortfolioImages($user_check->id);

                        return Response::json(array('status'=>1, 'msg'=>'Fb Login Success', 'vendor_details'=>$vendor_details, 'vendor_extra_details'=>$vendor_extra_details, 'vendor_review_details'=>$vendor_review_details, 'vendor_portfolio_images'=>$vendor_portfolio_images), 200);
                    }
                    // token null
                    else
                        return Response::json(array('status'=> 0,'msg'=>'User not found in database!'), 200);
                }
            }
            // Fb first time login
            else {
                return Response::json(array('status'=> 5,'msg'=>'First time user!'), 200);
            }
        }

    }

    public static function vendorLoginFb($input) {

        $validation = Validator::make($input, Users::$vendorLoginFbRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {

            $fb_id = $input['fb_id'];            
            $fb_email = isset($input['fb_email']) ? $input['fb_email'] : null;
            $fb_image = "http://graph.facebook.com/$fb_id/picture?type=large";                                                
            $fb_phone_no = isset($input['fb_phone_no']) ? $input['fb_phone_no'] : '';
            $business_name = isset($input['business_name']) ? $input['business_name'] : "";
            $business_type = isset($input['business_type']) ? $input['business_type'] : "";            
            $location = isset($input['location']) ? $input['location'] : "";
            $lat = isset($input['lat']) ? $input['lat'] : '0';
            $lng = isset($input['lng']) ? $input['lng'] : '0';            
            $image_count = $input['image_count'];
            $current_time = Carbon::now();                                

            $access_token = Users::generateToken();

            $user_check = DB::table('users')->select('id', 'email', 'password', 'deleted_at')->where('fb_id', $fb_id)->first();

            // Fb user exists
            if(isset($user_check)) {
                if($user_check->deleted_at!=null || $user_check->deleted_at!='')
                    return Response::json(array('status'=>0, 'msg'=>'This user is deactivated'), 200);
                else {
                    // Updating the user Token
                    $token = Users::updateUserToken($user_check->id);
                    if($token) {                        
                        
                        $vendor_details = Users::viewVendorProfileDetails2($user_check->id);
                        $vendor_extra_details = Users::viewVendorExtraDetails($user_check->id);
                        $vendor_review_details = Users::viewVendorReviewDetails($user_check->id);
                        $vendor_portfolio_images = Users::viewVendorPortfolioImages($user_check->id);

                        return Response::json(array('status'=>1, 'msg'=>'Fb Login Success', 'vendor_details'=>$vendor_details, 'vendor_extra_details'=>$vendor_extra_details, 'vendor_review_details'=>$vendor_review_details, 'vendor_portfolio_images'=>$vendor_portfolio_images), 200);
                    }
                    // token null
                    else
                        return Response::json(array('status'=> 0,'msg'=>'User not found in database!'), 200);
                }
            }
            // Fb first time login
            else {
                if($fb_email!=null) {
                    $email_check = DB::table('users')->select('id')->where('email', $fb_email)->first();
                }
                else {
                    $email_check = array();
                }

                // First time login. Email does not exist
                if(count($email_check)==0) {                    

                    if($location!="") {
                        if($lat=='0' || $lng=='0') {
                            $latlng = Users::getLatLng($location);
                            $latlngarr = explode(',', $latlng);
                            $lat = $latlngarr[0];
                            $lng = $latlngarr[1];
                        }
                    }                    

                    // Check auto authorize
                    $auto_authorize_status = Users::checkAutoAuthorize();

                    $user_id = DB::table('users')->insertGetId(
                        array(
                            'email' => $fb_email,                            
                            'access_token' => $access_token,
                            'fb_id' => $fb_id,  
                            'image' => $fb_image,                    
                            'phone_no' => $fb_phone_no,
                            'user_role' => '1',       
                            'approved' => $auto_authorize_status,                     
                            'created_at' => $current_time,
                            'updated_at' => $current_time,
                        )
                    );

                    $vendor_detail_id = DB::table('vendor_details')->insertGetId(
                        array(                    
                            'user_id' => $user_id,
                            'business_name' => $business_name,
                            'business_type' => $business_type,
                            'location' => $location,
                            'lat' => $lat,
                            'lng' => $lng,
                            'created_at' => $current_time,
                            'updated_at' => $current_time,
                        )
                    );

                    // Uploading Portfolio Photos
                    if($image_count>0) {
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
                    }                    

                    $vendor_details = Users::viewVendorProfileDetails2($user_id);
                    $vendor_extra_details = Users::viewVendorExtraDetails($user_id);
                    $vendor_review_details = Users::viewVendorReviewDetails($user_id);
                    $vendor_portfolio_images = Users::viewVendorPortfolioImages($user_id);

                    return Response::json(array('status'=>1, 'msg'=>'First time fb login', 'vendor_details'=>$vendor_details, 'vendor_extra_details'=>$vendor_extra_details, 'vendor_review_details'=>$vendor_review_details, 'vendor_portfolio_images'=>$vendor_portfolio_images), 200);                    
                }
                // Email exists. Update fb id
                else {
                    DB::table('users')->where('email', $fb_email)->update(['fb_id' => $fb_id]);
                    return Response::json(array('status'=> 0,'msg'=>'User email already exists.'), 200);
                }
            }
        }

    }

    public static function vendorLogout($input) {

        $validation = Validator::make($input, Users::$accessTokenRequired);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {

            $access_token = $input['access_token'];
            $user_id = Users::getUserIdByToken($access_token);

            DB::table('users')->where('id', $user_id)->update(['access_token' => null, 'device_token' => '', 'reg_id' => '']);
            
            return Response::json(array('status'=>1, 'msg'=>'Successfully logged out'), 200);
        }

    }

    public static function vendorEditProfile($input) {        

        $validation = Validator::make($input, Users::$vendorEditProfileRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {

        	$access_token = $input['access_token'];
        	$phone_no = $input['phone_no'];
            $phone_no_2 = isset($input['phone_no_2']) ? $input['phone_no_2'] : "";
            $phone_no_3 = isset($input['phone_no_3']) ? $input['phone_no_3'] : "";
            $business_name = $input['business_name'];
            $business_type = $input['business_type'];
            $location = $input['location'];            
            $lat = isset($input['lat']) ? $input['lat'] : '0';
            $lng = isset($input['lng']) ? $input['lng'] : '0';   
            $imgfile = Input::file('image');         
            $image_count = isset($input['image_count']) ? $input['image_count'] : '0';

            $name = isset($input['name']) ? $input['name'] : "";
            $gender = isset($input['gender']) ? $input['gender'] : "";
            $description = isset($input['description']) ? $input['description'] : "";

            $current_time = Carbon::now();
            
            $user_id = Users::getUserIdByToken($access_token);  

            // If lat lng not provided
            if($lat=='0' || $lng=='0' || $lat=='' || $lng=='') {
                $latlng = Users::getLatLng($location);
                $latlngarr = explode(',', $latlng);
                $lat = $latlngarr[0];
                $lng = $latlngarr[1];
            }          

            // Handling User Profile Image
            if($imgfile=="") {
                $user_data = DB::table('users')->select('image')->where('id', $user_id)->first();
                $image = $user_data->image;
            }
            else {
                $image = Users::uploadImage($user_id, $input);

                $profile_image = DB::table('users')->select('image')->where('id',$user_id)->first();
                if(isset($profile_image)) {
                    if((!empty($profile_image->image))) {
                        if(file_exists('uploads/'.$profile_image->image)){
                            unlink('uploads/'.$profile_image->image);
                        }
                    }
                }
            }

            // Update User Information
            DB::table('users')->where('id', $user_id)->update([
                'name' => $name, 
                'gender' => $gender, 
                'image' => $image, 
                'phone_no' => $phone_no,
                'phone_no_2' => $phone_no_2,
                'phone_no_3' => $phone_no_3
            ]);

            // Update Vendor Details Information
            DB::table('vendor_details')->where('user_id', $user_id)->update([
                'business_name' => $business_name, 
                'business_type' => $business_type, 
                'description' => $description, 
                'location' => $location,
                'lat' => $lat,
                'lng' => $lng,
            ]);

            // Uploading Editted Portfolio Photos
            // $multiple_images = Users::handleMultipleImageEdit($image_count, $input, $user_id);

            // DB::table('vendor_portfolio_images')->where('user_id', $user_id)->delete();

            // foreach ($multiple_images as $key => $value) {
                
            //     $vendor_portfolio_image_id = DB::table('vendor_portfolio_images')->insertGetId(
            //         array(                    
            //             'user_id' => $user_id,
            //             'image' => $multiple_images[$key],                        
            //             'created_at' => $current_time,
            //             'updated_at' => $current_time,
            //         )
            //     );

            // }

            // Response                     
            return Users::viewVendorProfileDetails($user_id);        

        }

    }

    public static function vendorChangePassword($input) {

        $validation = Validator::make($input, Users::$vendorChangePasswordRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {
            $access_token = $input['access_token'];
            $old_password = $input['old_password'];
            $new_password  = $input['new_password'];
            $new_password = Hash::make($new_password);

            $user_id = Users::getUserIdByToken($access_token);

            $user_check = DB::table('users')->select('password')->where('id', $user_id)->first();

            if(Hash::check($old_password, $user_check->password)) {
                DB::table('users')->where('id', $user_id)->update(['password' => $new_password]);

                $vendor_details = DB::select(
                    "SELECT `users`.`id` AS `user_id`, `users`.`name`, `users`.`email`, `users`.`password`, `users`.`access_token`, `users`.`fb_id`, `users`.`gender`, `users`.`image`, `users`.`phone_no`, `users`.`user_role`, `users`.`approved`,
                    `vendor_details`.`id` AS `vendor_details_id`, `vendor_details`.`business_name`, `vendor_details`.`business_type`, `vendor_details`.`description`, `vendor_details`.`location`, `vendor_details`.`lat`, `vendor_details`.`lng`, `vendor_details`.`average_cost`
                    FROM `users`
                    JOIN `vendor_details` ON `vendor_details`.`user_id` = `users`.`id`
                    WHERE `users`.`id` = '$user_id'
                ");

                $vendor_details[0]->image = Users::getFormattedImage($vendor_details[0]->image);

                // Manage Null

                if($vendor_details[0]->email==null) {
                    $vendor_details[0]->email="";
                }

                if($vendor_details[0]->access_token==null) {
                    $vendor_details[0]->access_token="";
                }            

                if($vendor_details[0]->fb_id==null) {
                    $vendor_details[0]->fb_id="";
                }          

                return Response::json(array('status'=>1, 'msg'=>'Login Success', 'vendor_details'=>$vendor_details[0]), 200);
            }
            else {
                return Response::json(array('status'=>0, 'msg'=>'Invalid Password'), 200);
            }
        }

    }

    public static function vendorDeactivateProfile($input) {

        $validation = Validator::make($input, Users::$accessTokenRequired);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {
            $access_token = $input['access_token'];
            $current_time = Carbon::now();
            $user_id = Users::getUserIdByToken($access_token);

            DB::table('users')->where('id', $user_id)->update(['deleted_at' => $current_time]);

            $vendor_details = DB::select(
                "SELECT `users`.`id` AS `user_id`, `users`.`name`, `users`.`email`, `users`.`password`, `users`.`access_token`, `users`.`fb_id`, `users`.`gender`, `users`.`image`, `users`.`phone_no`, `users`.`user_role`, `users`.`approved`,
                `vendor_details`.`id` AS `vendor_details_id`, `vendor_details`.`business_name`, `vendor_details`.`business_type`, `vendor_details`.`description`, `vendor_details`.`location`, `vendor_details`.`lat`, `vendor_details`.`lng`, `vendor_details`.`average_cost`
                FROM `users`
                JOIN `vendor_details` ON `vendor_details`.`user_id` = `users`.`id`
                WHERE `users`.`id` = '$user_id'
            ");

            $vendor_details[0]->image = Users::getFormattedImage($vendor_details[0]->image);

            // Manage Null

            if($vendor_details[0]->email==null) {
                $vendor_details[0]->email="";
            }

            if($vendor_details[0]->access_token==null) {
                $vendor_details[0]->access_token="";
            }            

            if($vendor_details[0]->fb_id==null) {
                $vendor_details[0]->fb_id="";
            }          

            return Response::json(array('status'=>1, 'msg'=>'Profile Deactivated', 'vendor_details'=>$vendor_details[0]), 200);

        }

    }

    public static function vendorViewMyProfile($input) {

        $validation = Validator::make($input, Users::$accessTokenRequired);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {
            $access_token = $input['access_token'];
            $user_id = Users::getUserIdByToken($access_token);

            $check_user = DB::table('users')->select('id')->where('id', $user_id)->where('user_role', '1')->first();
            if(empty($check_user)) {
                return Response::json(array('status'=>0, 'msg'=>'User is not a vendor'), 200);
            }

            return Users::viewVendorProfileDetails($user_id);            

        } 

    }

    public static function vendorViewProfileById($input) { 

        $validation = Validator::make($input, Users::$vendorViewProfileByIdRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {
            $access_token = $input['access_token'];
            $user_id_2 = $input['user_id_2'];

            $lat = isset($input['lat']) ? $input['lat'] : "";
            $lng = isset($input['lng']) ? $input['lng'] : "";

            $user_id = Users::getUserIdByToken($access_token);

            $check_user = DB::table('users')->select('id')->where('id', $user_id_2)->where('user_role', '1')->first();
            if(empty($check_user)) {
                return Response::json(array('status'=>0, 'msg'=>'User is not a vendor'), 200);
            }

            // Increment View Profile By Id Counter
            Users::viewProfileHit($user_id_2);               

            $vendor_details = Users::viewVendorProfileDetails3($user_id_2, $user_id);
            $vendor_extra_details = Users::viewVendorExtraDetails($user_id_2);
            $vendor_review_details = Users::viewVendorReviewDetails($user_id_2);
            $vendor_portfolio_images = Users::viewVendorPortfolioImages($user_id_2);
            $vendor_similar = Users::viewVendorSimilar($user_id_2, $user_id, $lat, $lng);            

            return Response::json(array('status'=>1, 'msg'=>'Vendor Details', 'vendor_details'=>$vendor_details, 'vendor_extra_details'=>$vendor_extra_details, 'vendor_review_details'=>$vendor_review_details, 'vendor_portfolio_images'=>$vendor_portfolio_images, 'vendor_similar'=>$vendor_similar), 200);                     

        }

    }

    // Set Device Token User
    public static function vendorSetDeviceToken($input) {

        $validation = Validator::make($input, Users::$setDeviceTokenRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {
            $access_token = $input['access_token'];            
            $device_token = $input['device_token'];            
            $user_id = Users::getUserIdByToken($access_token);

            $user_role = Users::checkUserRole($user_id);

            if($user_role == 1) {
                Users::updateDeviceToken($user_id, $device_token);                
                return Response::json(array('status'=>1, 'msg'=>'Device Token Set'), 200);
            }            
            else 
                return Response::json(array('status'=>0, 'msg'=>'Customer/Collaborator is not allowed'), 200);
                        
        }

    }

    // User Set Reg Id
    public static function vendorSetRegId($input) {

        $validation = Validator::make($input, Users::$setRegIdRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {
            $access_token = $input['access_token'];            
            $reg_id = $input['reg_id'];            
            $user_id = Users::getUserIdByToken($access_token);

            $user_role = Users::checkUserRole($user_id);

            if($user_role == 1) {
                Users::updateRegId($user_id, $reg_id);                
                return Response::json(array('status'=>1, 'msg'=>'Reg Id Set'), 200);
            }            
            else 
                return Response::json(array('status'=>0, 'msg'=>'Customer/Collaborator is not allowed'), 200);
                        
        }

    }

    // Forgot Password Functionality

    // public static function forgotPassword($input) {

    //     $validation = Validator::make($input, Users::$forgotPasswordRules);
    //     if($validation->fails()) {
    //         return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
    //     }
    //     else {
    //         $email = $input['email'];
    //         $current_time = Carbon::now();

    //         $existing_user_detail = DB::select("select `id`, `access_token` from `users` where `email`='$email'");            

    //         if(!empty($existing_user_detail)) {

    //             $access_token = $existing_user_detail[0]->access_token;

    //             $token = str_random(30);
    //             $user_id = $existing_user_detail[0]->id;

    //             // Clear old record for this email
    //             DB::table('password_resets')->where('email', $email)->delete();

    //             // Insert record into password resets
    //             DB::insert("INSERT INTO `password_resets` (`user_id`, `email`, `token`, `created_at`) VALUES (?, ?, ?, ?)", [$user_id, $email, $token, $current_time]);

    //             $to = $email;
    //             $subject = "Your What A Shaadi password has been changed";

    //             $txt = "We received a request to reset the password for your account. If you made this request, click the link below. If you didn't make this request, you can ignore this email."."\r\n";
    //             $txt = $txt."Reset your password"."\r\n";
    //             $txt = $txt."--link--"."\r\n";

    //             $message = "
    //             <html>
    //             <head>
    //             <title>HTML email</title>
    //             </head>
    //             <body>
    //             <p>We received a request to reset the password for your account. If you made this request, click the link below. If you didn't make this request, you can ignore this email.</p>
    //             <p>Reset your password</p>
    //             <p> <a href='".URL::to('/')."/api/password-reset/".$token."'>".URL::to('/')."/api/password-reset/$token</a> </p>
    //             </body>
    //             </html>
    //             ";

    //             // Always set content-type when sending HTML email
    //             $headers = "MIME-Version: 1.0" . "\r\n";
    //             $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

    //             // More headers
    //             $headers .= 'From: <harmanpreet.saini@hotmail.com>' . "\r\n";

    //             mail($to,$subject,$message,$headers);

    //             return Response::json(array('status'=>1, 'msg'=>'An email has been sent to your email id'), 200);
    //         }
    //         else
    //             return Response::json(array('status'=>0, 'msg'=>'Invalid Email'), 200);
    //     }

    // }

    public static function forgotPassword($input) {

        $validation = Validator::make($input, Users::$forgotPasswordRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {
            $email = $input['email'];
            $current_time = Carbon::now();

            $existing_user_detail =  DB::table('users')->select('id', 'access_token')->where('email', $email)->first();

            if(!empty($existing_user_detail)) {

                $access_token = $existing_user_detail->access_token;

                $token = str_random(30);
                $user_id = $existing_user_detail->id;

                // Clear old record for this email
                DB::table('password_resets')->where('email', $email)->delete();

                // Insert record into password resets                
                DB::table('password_resets')->insertGetId(array(
                    'user_id' => $user_id,
                    'email' => $email,
                    'token' => $token,
                    'created_at' => $current_time,
                ));                            

                // dd($email);

                // $temp = Mail::send('emails.forgot-password', ['token' => $token], function($message)
                // {
                //     $input = Input::all();
                //     $email_2 = $input['email'];

                //     $message->to($email_2, '')->subject('Your What A Shaadi password has been changed');
                // });

                // dd($temp);

                return Response::json(array('status'=>1, 'msg'=>'An email has been sent to your email id'), 200);

            }
            else
                return Response::json(array('status'=>0, 'msg'=>'Invalid Email'), 200);
        }

    }

    public static function forgotPassword2($token) {
        $forgot_pass_data = DB::select("
          SELECT `id`, `user_id`, `email`
          FROM `password_resets` WHERE `token`='$token'
        ");

        if(!empty($forgot_pass_data[0]->user_id)) {
            return $forgot_pass_data[0]->user_id;
        }
        else {
            return 0;
        }

    }

    public static function forgotPassword3($input) {

        $validation = Validator::make($input, Users::$forgotPassword3Rules);

        if ($validation->fails()) {
            return $validation->getMessageBag()->first();
        }
        else {
            $password = $input['password'];
            $confirm_password = $input['confirm_password'];
            $user_id = $input['user_id'];
            $token = $input['token'];

            //check if user is valid with valid token
            $check_reset = DB::table('password_resets')->select('id')->where('user_id', $user_id)->where('token', $token)->first();            
            if(!empty($check_reset->id)) {
                if($password==$confirm_password) {
                    $password = Hash::make($password);
                    DB::update("update users set password='$password' where id='$user_id'");
                    DB::delete("DELETE FROM `password_resets` WHERE `user_id`='$user_id' AND `token`='$token'");
                    $message = "success";
                }
                else {
                    $message = "Password Do not match";
                }
            }
            else {
                $message = 'Invalid Parameters';
            }
            return $message;    
        }


    }

    // Vendor Listing Function

    // public static function vendorListingByType($input) {

    //     $validation = Validator::make($input, Users::$vendorListingByTypeRules);
    //     if($validation->fails()) {
    //         return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
    //     }
    //     else {

    //         $access_token = $input['access_token'];
    //         $business_type = $input['business_type'];
    //         $user_id = Users::getUserIdByToken($access_token);
            
    //         $sub_business = DB::select(
    //             "SELECT `sub_business`
    //             FROM `sub_businesses` 
    //             WHERE `business_id` = (SELECT `id` FROM `businesses` WHERE `business` = '$business_type')
    //         ");

    //         $where_query = "";
    //         $tot_sub_business = count($sub_business);

    //         foreach ($sub_business as $key => $value) {
    //             $where_query = $where_query." `vendor_details`.`business_type` = '$value->sub_business'";
    //             if($key < $tot_sub_business-1)
    //                 $where_query = $where_query." || ";
    //         }            

    //         $vendor_listing = DB::select(
    //             "SELECT `users`.`id` AS `user_id`, `users`.`id` AS `vendor_id`, `users`.`name`, `users`.`email`, `users`.`password`, `users`.`access_token`, `users`.`fb_id`, `users`.`gender`, `users`.`image`, `users`.`phone_no`, `users`.`user_role`, `users`.`approved`,
    //             `vendor_details`.`id` AS `vendor_details_id`, `vendor_details`.`business_name`, `vendor_details`.`business_type`, `vendor_details`.`description`, `vendor_details`.`location`, `vendor_details`.`lat`, `vendor_details`.`lng`, `vendor_details`.`average_cost`,
    //             (SELECT `id` FROM `favorite` WHERE `user_id` = '$user_id' AND `vendor_id` = `users`.`id` LIMIT 1) AS `is_fav`,
    //             (SELECT AVG(`rating`) FROM `vendor_reviews` WHERE `vendor_id` = `users`.`id`) AS `vendor_rating`
    //             FROM `users`
    //             JOIN `vendor_details` ON `vendor_details`.`user_id` = `users`.`id`
    //             WHERE $where_query
    //         ");            

    //         foreach ($vendor_listing as $key => $value) {
                
    //             $vendor_listing[$key]->image = Users::getFormattedImage($vendor_listing[$key]->image);

    //             // Manage Null

    //             if($vendor_listing[$key]->email==null) {
    //                 $vendor_listing[$key]->email="";
    //             }

    //             if($vendor_listing[$key]->access_token==null) {
    //                 $vendor_listing[$key]->access_token="";
    //             }            

    //             if($vendor_listing[$key]->fb_id==null) {
    //                 $vendor_listing[$key]->fb_id="";
    //             }

    //             if($vendor_listing[$key]->is_fav==null) {
    //                 $vendor_listing[$key]->is_fav="0";
    //             }
    //             else {
    //                 $vendor_listing[$key]->is_fav="1";
    //             }

    //             if($vendor_listing[$key]->vendor_rating==null)
    //                 $vendor_listing[$key]->vendor_rating="";                

    //         }                    

    //         // Increment Category Counter            
    //         // Users::searchCategoryHit($business_type);            

    //         return Response::json(array('status'=>1, 'msg'=>'Vendor Listing By Type', 'vendor_listing'=>$vendor_listing), 200);            
    //     }

    // }

    public static function vendorListingByType($input) {

        $validation = Validator::make($input, Users::$vendorListingByTypeRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {

            $access_token = $input['access_token'];
            $business_type = $input['business_type'];
            $page = isset($input['page']) ? $input['page'] : "";             
            $lat = isset($input['lat']) ? $input['lat'] : "";                        
            $lng = isset($input['lng']) ? $input['lng'] : "";    
            $user_id = Users::getUserIdByToken($access_token);   

            $length = 10;
            $distance_query = "'-' AS `distance`";
            $distance_query_2 = "'-' AS `distance_2`";                   
            
            $sub_business = DB::select(
                "SELECT `sub_business`
                FROM `sub_businesses` 
                WHERE `business_id` = (SELECT `id` FROM `businesses` WHERE `business` = '$business_type')
            ");

            $where_query = "";
            $tot_sub_business = count($sub_business);

            foreach ($sub_business as $key => $value) {
                $where_query = $where_query." `vendor_details`.`business_type` = '$value->sub_business'";
                if($key < $tot_sub_business-1)
                    $where_query = $where_query." || ";
            }            

            $vendor_listing = DB::select(
                "SELECT `users`.`id` AS `user_id`, `users`.`id` AS `vendor_id`, `users`.`name`, `users`.`email`, `users`.`password`, `users`.`access_token`, `users`.`fb_id`, `users`.`gender`, `users`.`image`, `users`.`phone_no`, `users`.`user_role`, `users`.`approved`,
                `vendor_details`.`id` AS `vendor_details_id`, `vendor_details`.`business_name`, `vendor_details`.`business_type`, `vendor_details`.`description`, `vendor_details`.`location`, `vendor_details`.`lat`, `vendor_details`.`lng`, `vendor_details`.`average_cost`,
                (SELECT `id` FROM `favorite` WHERE `user_id` = '$user_id' AND `vendor_id` = `users`.`id` LIMIT 1) AS `is_fav`,
                (SELECT AVG(`rating`) FROM `vendor_reviews` WHERE `vendor_id` = `users`.`id`) AS `vendor_rating`
                FROM `users`
                JOIN `vendor_details` ON `vendor_details`.`user_id` = `users`.`id`
                WHERE $where_query
            ");            

            foreach ($vendor_listing as $key => $value) {
                
                $vendor_listing[$key]->image = Users::getFormattedImage($vendor_listing[$key]->image);

                // Manage Null

                if($vendor_listing[$key]->email==null) {
                    $vendor_listing[$key]->email="";
                }

                if($vendor_listing[$key]->access_token==null) {
                    $vendor_listing[$key]->access_token="";
                }            

                if($vendor_listing[$key]->fb_id==null) {
                    $vendor_listing[$key]->fb_id="";
                }

                if($vendor_listing[$key]->is_fav==null) {
                    $vendor_listing[$key]->is_fav="0";
                }
                else {
                    $vendor_listing[$key]->is_fav="1";
                }

                if($vendor_listing[$key]->vendor_rating==null)
                    $vendor_listing[$key]->vendor_rating="";                

            }                    

            // Increment Category Counter            
            // Users::searchCategoryHit($business_type);            

            return Response::json(array('status'=>1, 'msg'=>'Vendor Listing By Type', 'vendor_listing'=>$vendor_listing), 200);            
        }

    }

    public static function vendorListing($input) { 

        $validation = Validator::make($input, Users::$vendorListingRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {

            $access_token = $input['access_token'] ? $input['access_token'] : "";    
            $sub_business_type = isset($input['sub_business_type']) ? $input['sub_business_type'] : "";
            $business_type = isset($input['business_type']) ? $input['business_type'] : "";
            $city = isset($input['city']) ? $input['city'] : "";
            $page = isset($input['page']) ? $input['page'] : ""; 
            $sort_by = isset($input['sort_by']) ? $input['sort_by'] : ""; 
            $lat = isset($input['lat']) ? $input['lat'] : "";                        
            $lng = isset($input['lng']) ? $input['lng'] : "";            
            $radius = isset($input['radius']) ? $input['radius'] : "";
            $price_range_val_1 = isset($input['price_range_val_1']) ? $input['price_range_val_1'] : "";
            $price_range_val_2 = isset($input['price_range_val_2']) ? $input['price_range_val_2'] : "";

            if($access_token=="") 
                $user_id = "-1";
            else
                $user_id = Users::getUserIdByToken($access_token);

            //       

            $length = 10;
            $distance_query = "'-' AS `distance`";
            $distance_query_2 = "'-' AS `distance_2`";            

            $sub_business_where_query = "";
            $business_type_filter = "";

            if($sub_business_type != "") {

                // Vendor Type Filter
                if($sub_business_type == "") {
                    $business_type_filter = "";
                }
                else {            

                    $sub_business_type_arr = explode(',', $sub_business_type);

                    $tot_sub_business = count($sub_business_type_arr);

                    if($tot_sub_business>0)
                        $sub_business_where_query = " AND (";

                    foreach ($sub_business_type_arr as $key => $value) {

                        $sub_business_where_query = $sub_business_where_query." `vendor_details`.`business_type` = '$value'";
                        if($key < $tot_sub_business-1)
                            $sub_business_where_query = $sub_business_where_query." || ";
                    }

                    if($tot_sub_business>0)
                        $sub_business_where_query = $sub_business_where_query.")";

                    // return $sub_business_where_query;                    
                }

            }    
            else if($business_type != "") {

                // Sub Business
                $sub_business = DB::select(
                    "SELECT `sub_business`
                    FROM `sub_businesses` 
                    WHERE `business_id` = (SELECT `id` FROM `businesses` WHERE `business` = '$business_type')
                ");

                $tot_sub_business = count($sub_business);

                if($tot_sub_business>0)
                    $sub_business_where_query = " AND (";

                foreach ($sub_business as $key => $value) {

                    $sub_business_where_query = $sub_business_where_query." `vendor_details`.`business_type` = '$value->sub_business'";
                    if($key < $tot_sub_business-1)
                        $sub_business_where_query = $sub_business_where_query." || ";
                }

                if($tot_sub_business>0)
                    $sub_business_where_query = $sub_business_where_query.")";

            }  
            else {
                $sub_business_where_query = "";
                $business_type_filter = "";
            } 
            
            // City Filter
            if($city != "") {                
                $city_filter = " AND `vendor_details`.`city` = '$city'";    
            }
            else {            
                $city_filter = "";
            }                             

            // Distance
            if($lat!="" && $lng!="") {
                $distance_query_3 = "( 6373 * acos( cos( radians($lat) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin(radians(lat)) ) ) AS `distance_3`";                                   
            }   
            else {
                $distance_query_3 = "'-' AS `distance_3`";                                    
            } 

            // Radius Filter
            if($radius == "") {
                $radius_filter = "";
            }
            else {                            
                if($lat!="" && $lng!="") {
                    $distance_query_2 = "( 6373 * acos( cos( radians($lat) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin(radians(lat)) ) ) AS `distance_2`";                   
                    $radius_filter = " AND `temp`.`distance_2` BETWEEN '0' AND '$radius'";     
                }   
                else {
                    $distance_query_2 = "'-' AS `distance_2`";                    
                    $radius_filter = "";     
                }
            }

            // Price Range Filter
            if($price_range_val_1 != "" && $price_range_val_2 != "") {                
                $price_range_filter = " AND `vendor_details`.`average_cost` BETWEEN '$price_range_val_1' AND '$price_range_val_2'";    
            }
            else {            
                $price_range_filter = "";
            }

            // Sort Query            

            if($sort_by == "1") {
                if($lat!="" && $lng!="") {
                    $distance_query = "( 6373 * acos( cos( radians($lat) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin(radians(lat)) ) ) AS `distance`";
                    $sort_by_query = " ORDER BY `distance`"; 
                }   
                else {
                    $distance_query = "'-' AS `distance`";
                    $sort_by_query = " ORDER BY `users`.`created_at` DESC"; 
                }             
            }
            else if($sort_by == "2") {
                $sort_by_query = " ORDER BY `temp`.`fav_count` DESC";    
            }
            else {
                $sort_by_query = " ORDER BY `temp`.`created_at` DESC";     
            }

            // Paging
            if($page == "") {
                $limit_query = "LIMIT 0, $length";
            }
            else {            
                $offset = $page * $length;
                $limit_query = "LIMIT $offset, $length";
            }                                    

            // Vendor Listing
            $vendor_listing = DB::select(
                "SELECT * FROM (SELECT `users`.`id` AS `user_id`, `users`.`id` AS `vendor_id`, `users`.`name`,
                `vendor_details`.`id` AS `vendor_details_id`, `vendor_details`.`business_name`, `vendor_details`.`business_type`, `vendor_details`.`average_cost`,
                (SELECT `image` FROM `vendor_portfolio_images` WHERE `user_id` = `users`.`id` LIMIT 1) AS `vendor_portfolio_image`,
                '' AS `vendor_portfolio_images`,
                (SELECT AVG(`rating`) FROM `vendor_reviews` WHERE `vendor_id` = `users`.`id`) AS `vendor_rating`,
                (SELECT `id` FROM `favorite` WHERE `user_id` = '$user_id' AND `vendor_id` = `users`.`id` LIMIT 1) AS `is_fav`,
                (SELECT count(`id`) FROM `favorite` WHERE `vendor_id` = `users`.`id` LIMIT 1) AS `fav_count`,
                (SELECT count(`id`) FROM `vendor_reviews` WHERE `vendor_id` = `users`.`id`) AS `review_count`,
                `users`.`created_at` AS `created_at`,
                $distance_query,
                $distance_query_2,
                $distance_query_3
                FROM `users`
                JOIN `vendor_details` ON `vendor_details`.`user_id` = `users`.`id`
                WHERE `users`.`user_role` = '1' AND `users`.`approved` = '1' AND `users`.`deleted_at` IS Null  
                $business_type_filter                
                $sub_business_where_query
                $city_filter
                $price_range_filter
                ) AS `temp`                 
                WHERE `user_id` != '$user_id'
                $radius_filter
                $sort_by_query
                $limit_query      
            ");                    

            foreach ($vendor_listing as $key => $value) {                

                // Manage Portfolio Response
                if($vendor_listing[$key]->vendor_portfolio_image == null)
                    $vendor_listing[$key]->vendor_portfolio_image  = "";
                else 
                    $vendor_listing[$key]->vendor_portfolio_image = Users::getFormattedImage($vendor_listing[$key]->vendor_portfolio_image);

                // Manage Multiple Portfolio Response
                $curr_user_id = $vendor_listing[$key]->user_id;
                $vendor_portfolio_images_data = DB::select("SELECT `id`, `user_id`, `image` FROM `vendor_portfolio_images` WHERE `user_id` = '$curr_user_id' LIMIT 4");

                if(!empty($vendor_portfolio_images_data)) {

                    foreach ($vendor_portfolio_images_data as $key2 => $value2) {
                        $value2->image = Users::getFormattedImage($value2->image);
                    }

                     $vendor_listing[$key]->vendor_portfolio_images = $vendor_portfolio_images_data;

                }
                else {
                    $vendor_listing[$key]->vendor_portfolio_images = array();
                }                            

                // Manage Favorite Response
                if($vendor_listing[$key]->is_fav == null)
                    $vendor_listing[$key]->is_fav  = "0";
                else 
                    $vendor_listing[$key]->is_fav  = "1";

                // Manage Favorite Response
                if($vendor_listing[$key]->vendor_rating == null)
                    $vendor_listing[$key]->vendor_rating  = "";
                else {
                    $vendor_listing[$key]->vendor_rating = round($vendor_listing[$key]->vendor_rating, 1);
                    $vendor_listing[$key]->vendor_rating = strval($vendor_listing[$key]->vendor_rating);
                }                

                if($vendor_listing[$key]->distance != '-') {
                    $vendor_listing[$key]->distance = round($vendor_listing[$key]->distance, 2);
                    $vendor_listing[$key]->distance = strval($vendor_listing[$key]->distance);
                }       

                if($vendor_listing[$key]->distance_2 != '-') {
                    $vendor_listing[$key]->distance_2 = round($vendor_listing[$key]->distance_2, 2);
                    $vendor_listing[$key]->distance_2 = strval($vendor_listing[$key]->distance_2);
                }

                if($vendor_listing[$key]->distance_3 != '-') {
                    $vendor_listing[$key]->distance_3 = round($vendor_listing[$key]->distance_3, 2);
                    $vendor_listing[$key]->distance_3 = strval($vendor_listing[$key]->distance_3);
                }                

                $vendor_listing[$key]->sub_string = $vendor_listing[$key]->business_type.' • '.$vendor_listing[$key]->review_count.' Review • '.$vendor_listing[$key]->distance_3.' km away';

            }

            return Response::json(array('status'=>1, 'msg'=>'Vendor Details', 'vendor_listing'=>$vendor_listing), 200);            

        }

    }

    public static function viewVendorProfileById($input) { 

        $validation = Validator::make($input, Users::$vendorViewProfileByIdRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {
            $access_token = $input['access_token'] ? $input['access_token'] : "";
            $user_id_2 = $input['user_id_2'];

            if($access_token=="")
                $user_id = "-1";
            else {                
                $user_id = Users::getUserIdByToken($access_token);

                // Mark as recently viewed
                Users::markRecentlyViewedVendor($user_id_2, $user_id);
            }

            $lat = $input['lat'];
            $lng = $input['lng'];            

            $check_user = DB::table('users')->select('id')->where('id', $user_id_2)->where('user_role', '1')->first();
            if(empty($check_user)) {
                return Response::json(array('status'=>0, 'msg'=>'User is not a vendor'), 200);
            }

            // Increment View Profile By Id Counter
            Users::viewProfileHit($user_id_2);            

            $vendor_details = Users::viewVendorProfileDetails3($user_id_2, $user_id);
            $vendor_extra_details = Users::viewVendorExtraDetails($user_id_2);
            $vendor_review_details = Users::viewVendorReviewDetails($user_id_2);
            $vendor_portfolio_images = Users::viewVendorPortfolioImages($user_id_2);
            $vendor_similar = Users::viewVendorSimilar($user_id_2, $user_id, $lat, $lng);

            return Response::json(array('status'=>1, 'msg'=>'Vendor Details', 'vendor_details'=>$vendor_details, 'vendor_extra_details'=>$vendor_extra_details, 'vendor_review_details'=>$vendor_review_details, 'vendor_portfolio_images'=>$vendor_portfolio_images, 'vendor_similar'=>$vendor_similar), 200);            

        }

    }

    // User Functions
    public static function userSignUp($input) {

        $validation = Validator::make($input, Users::$userSignUpRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {

            $fb_id = isset($input['fb_id']) ? $input['fb_id'] : "";

            // Normal Sign Up
            if($fb_id=="") {          

                $email = $input['email'];
                $check_user_email = DB::table('users')->select('id')->where('email', $email)->first();

                if(empty($input['email'])) 
                    return Response::json(array('status'=> 0,'msg'=>'Email is required'), 200);                
                else if(empty($input['password']))
                    return Response::json(array('status'=> 0,'msg'=>'Password is required'), 200);      
                else if(!empty($check_user_email))
                    return Response::json(array('status'=> 0,'msg'=>'Email exists'), 200);          
                else {

                    $email = $input['email'];
                    $password = $input['password'];
                    $password = Hash::make($password);
                    $phone_no = isset($input['phone_no']) ? $input['phone_no'] : "";                    
                    $name = isset($input['name']) ? $input['name'] : "";            
                    $imgfile = Input::file('image');                    
                    $wedding_date = isset($input['wedding_date']) ? $input['wedding_date'] : "";
                    $wedding_name = isset($input['wedding_name']) ? $input['wedding_name'] : "";
                    $wedding_desc = isset($input['wedding_desc']) ? $input['wedding_desc'] : "";
                    $location = isset($input['location']) ? $input['location'] : "";
                    $lat = isset($input['lat']) ? $input['lat'] : '0';
                    $lng = isset($input['lng']) ? $input['lng'] : '0';            
                                
                    $current_time = Carbon::now();
                   
                    $access_token = Users::generateToken();

                    // Formatting date according to db
                    $wedding_date = date("Y-m-d 00:00:00", strtotime($wedding_date));

                    if($location!="") {
                        if($lat=='0' || $lng=='0') {
                            $latlng = Users::getLatLng($location);
                            $latlngarr = explode(',', $latlng);
                            $lat = $latlngarr[0];
                            $lng = $latlngarr[1];
                        }
                    }                    

                    // Handling User Profile Image
                    if($imgfile=="") {                
                        $image = "";
                    }
                    else {
                        $image = Users::uploadImage();
                    }                       

                    $user_id = DB::table('users')->insertGetId(
                        array(
                            'name' => $name,                    
                            'email' => $email,
                            'password' => $password,
                            'access_token' => $access_token,
                            'image' => $image,                            
                            'phone_no' => $phone_no,
                            'user_role' => '0',
                            'approved' => '1',
                            'created_at' => $current_time,
                            'updated_at' => $current_time,
                        )
                    );

                    $wedding_id = DB::table('wedding')->insertGetId(
                        array(                    
                            'user_id' => $user_id,
                            'name' => $wedding_name,
                            'description' => $wedding_desc,
                            'date' => $wedding_date,
                            'location' => $location,
                            'lat' => $lat,
                            'lng' => $lng,
                            'created_at' => $current_time,
                            'updated_at' => $current_time,
                        )
                    );            

                    $user_details = Users::viewUserProfileDetails($user_id);
                    $favorite_listing = Favorite::viewFavoriteListing($user_id);
                    $collaborator_listing = Collaborators::collaboratorsListing($user_id);  
                    $contracted_vendors = Users::getContractedVendors($user_id, "", ""); 

                    // Check User Invitation
                    Users::checkUserInvitation($user_id, $phone_no);

                    // Register for News Letter and Send Welcome Email
                    // Users::sendEmailNewsletter($email);
                    // $temp = Mail::send('emails.welcome-email', [], function($message)
                    // {
                    //     $input = Input::all();
                    //     $email_2 = $input['email'];

                    //     $message->to($email_2, '')->subject('Welcome to What A Shaadi');
                    // });

                    return Response::json(array('status' => 1, 'msg' => 'User Details', 'user_details' => $user_details, 'favorite_listing' => $favorite_listing, 'collaborator_listing' => $collaborator_listing, 'contracted_vendors' => $contracted_vendors), 200);              

                }
            }
            // If fb id is there
            else {

                $fb_id = $input['fb_id'];
                $fb_name = isset($input['name']) ? $input['name'] : '';
                $fb_email = isset($input['email']) ? $input['email'] : null;
                $fb_image = "http://graph.facebook.com/$fb_id/picture?type=large";                                    
                $fb_gender = isset($input['gender']) ? $input['gender'] : '';
                $fb_phone_no = isset($input['phone_no']) ? $input['phone_no'] : '';
                $wedding_date = isset($input['wedding_date']) ? $input['wedding_date'] : "";
                $wedding_name = isset($input['wedding_name']) ? $input['wedding_name'] : "";
                $wedding_desc = isset($input['wedding_desc']) ? $input['wedding_desc'] : "";
                $location = isset($input['location']) ? $input['location'] : "";
                $lat = isset($input['lat']) ? $input['lat'] : '0';
                $lng = isset($input['lng']) ? $input['lng'] : '0';            
                $current_time = Carbon::now();            

                $access_token = Users::generateToken();

                $user_check = DB::table('users')->select('id', 'email', 'password', 'deleted_at')->where('fb_id', $fb_id)->first();

                // Fb user exists
                if(isset($user_check)) {
                    if($user_check->deleted_at!=null || $user_check->deleted_at!='')
                        return Response::json(array('status'=>0, 'msg'=>'This user is deactivated'), 200);
                    else {
                        // Updating the user Token
                        $token = Users::updateUserToken($user_check->id);
                        if($token) {                                                    

                            $user_details = Users::viewUserProfileDetails($user_check->id);
                            $favorite_listing = Favorite::viewFavoriteListing($user_check->id);
                            $collaborator_listing = Collaborators::collaboratorsListing($user_check->id);  
                            $contracted_vendors = Users::getContractedVendors($user_check->id, "", "");

                            // Check User Invitation
                            Users::checkUserInvitation($user_check->id, $fb_phone_no);                    

                            return Response::json(array('status' => 1, 'msg' => 'User Details', 'user_details' => $user_details, 'favorite_listing' => $favorite_listing, 'collaborator_listing' => $collaborator_listing, 'contracted_vendors' => $contracted_vendors), 200);     
                        }
                        // token null
                        else
                            return Response::json(array('status'=> 0,'msg'=>'User not found in database!'), 200);
                    }
                }
                // Fb first time login
                else {
                    if($fb_email!=null) {
                        $email_check = DB::table('users')->select('id')->where('email', $fb_email)->first();
                    }
                    else {
                        $email_check = array();
                    }

                    // First time login. Email does not exist
                    if(count($email_check)==0) {                    

                        // Formatting date according to db
                        $wedding_date = date("Y-m-d 00:00:00", strtotime($wedding_date));

                        if($location!="") {
                            if($lat=='0' || $lng=='0') {
                                $latlng = Users::getLatLng($location);
                                $latlngarr = explode(',', $latlng);
                                $lat = $latlngarr[0];
                                $lng = $latlngarr[1];
                            }
                        }                    

                        $user_id = DB::table('users')->insertGetId(
                            array(
                                'name' => $fb_name,                    
                                'email' => $fb_email,                            
                                'access_token' => $access_token,
                                'fb_id' => $fb_id,
                                'image' => $fb_image,                    
                                'phone_no' => $fb_phone_no,
                                'user_role' => '0',
                                'approved' => '1',
                                'created_at' => $current_time,
                                'updated_at' => $current_time,
                            )
                        );

                        $wedding_id = DB::table('wedding')->insertGetId(
                            array(                    
                                'user_id' => $user_id,
                                'name' => $wedding_name,
                                'description' => $wedding_desc,
                                'date' => $wedding_date,
                                'location' => $location,
                                'lat' => $lat,
                                'lng' => $lng,
                                'created_at' => $current_time,
                                'updated_at' => $current_time,
                            )
                        );

                        $user_details = Users::viewUserProfileDetails($user_id);
                        $favorite_listing = Favorite::viewFavoriteListing($user_id);
                        $collaborator_listing = Collaborators::collaboratorsListing($user_id);    
                        $contracted_vendors = Users::getContractedVendors($user_id, "", "");

                        // Check User Invitation
                        Users::checkUserInvitation($user_id, $fb_phone_no);

                        // Register for News Letter and Send Welcome Email
                        // Users::sendEmailNewsletter($fb_email);
                        // $temp = Mail::send('emails.welcome-email', [], function($message)
                        // {
                        //     $input = Input::all();
                        //     if(isset($input['email'])) {
                        //         $email_2 = $input['email'];
                        //         $message->to($email_2, '')->subject('Welcome to What A Shaadi');
                        //     }
                            
                        // });

                        return Response::json(array('status' => 5, 'msg' => 'First time fb login', 'user_details' => $user_details, 'favorite_listing' => $favorite_listing, 'collaborator_listing' => $collaborator_listing, 'contracted_vendors' => $contracted_vendors), 200);     

                    }
                    // Email exists. Update fb id
                    else {
                        DB::table('users')->where('email', $fb_email)->update(['fb_id' => $fb_id]);
                        $user_data = DB::table('users')->select(`id`)->where('email', $fb_email)->first();                        

                        $user_details = Users::viewUserProfileDetails($user_data->user_id);
                        $favorite_listing = Favorite::viewFavoriteListing($user_data->user_id);
                        $collaborator_listing = Collaborators::collaboratorsListing($user_data->user_id);  
                        $contracted_vendors = Users::getContractedVendors($user_data->user_id, "", ""); 

                        // Check User Invitation
                        Users::checkUserInvitation($user_id, $fb_phone_no);

                        return Response::json(array('status' => 1, 'msg' => 'Email Already Exists', 'user_details' => $user_details, 'favorite_listing' => $favorite_listing, 'collaborator_listing' => $collaborator_listing, 'contracted_vendors' => $contracted_vendors), 200);                              
                    }
                }

            }

            // Check if user is a invited user or not

                

        }

    }

    public static function userLogin($input) {

        $validation = Validator::make($input, Users::$userLoginRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {

            $id = $input['id'];
            $password = $input['password'];
            $current_time = Carbon::now();

            $user_check = DB::table('users')->select('id', 'password', 'user_role', 'deleted_at')->where('email', $id)->first();

            if(isset($user_check)) {
                if($user_check->deleted_at!=null || $user_check->deleted_at!='')
                    return Response::json(array('status'=>0, 'msg'=>'This user is deactivated'), 200);
                elseif ($user_check->user_role=='1') {
                    return Response::json(array('status'=>0, 'msg'=>'Vendor cannot login'), 200);
                }
                else {
                    if((Hash::check($password, $user_check->password))) {
                        // Updating the user Token
                        $token = Users::updateUserToken($user_check->id);
                        if($token) {                            

                            $user_id = $user_check->id;

                            $user_details = Users::viewUserProfileDetails($user_id);
                            $favorite_listing = Favorite::viewFavoriteListing($user_id);
                            $collaborator_listing = Collaborators::collaboratorsListing($user_id);
                            $contracted_vendors = Users::getContractedVendors($user_id, "", ""); 

                            return Response::json(array('status' => 1, 'msg' => 'User Details', 'user_details' => $user_details, 'favorite_listing' => $favorite_listing, 'collaborator_listing' => $collaborator_listing, 'contracted_vendors' => $contracted_vendors), 200);    

                        }
                        // token null
                        else
                            return Response::json(array('status'=> 0,'msg'=>'User not found in database!'), 200);
                    }
                    // Password incorrect
                    else
                        return Response::json(array('status'=> 0,'msg'=>'Password Incorrect!'), 200);
                }
            }
            else
                return Response::json(array('status'=>0, 'msg'=>'Username/Email and Password do not match'), 200);
        }

    }

    public static function userLoginFbCheck($input) {

        $validation = Validator::make($input, Users::$userLoginFbRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {

            $fb_id = $input['fb_id'];                        
            $current_time = Carbon::now();                                            

            $user_check = DB::table('users')->select('id', 'email', 'password', 'user_role', 'deleted_at')->where('fb_id', $fb_id)->first();  

            if(!empty($user_check)) {
                if($user_check->user_role == '1') {
                    return Response::json(array('status'=>0, 'msg'=>'This user is a vendor'), 200);
                }   
            }                               

            // Fb user exists
            if(isset($user_check)) {
                if($user_check->deleted_at!=null || $user_check->deleted_at!='')
                    return Response::json(array('status'=>0, 'msg'=>'This user is deactivated'), 200);
                else {
                    // Updating the user Token
                    $token = Users::updateUserToken($user_check->id);
                    if($token) {                        
                        
                        $user_details = Users::viewUserProfileDetails($user_check->id);
                        $favorite_listing = Favorite::viewFavoriteListing($user_check->id);
                        $collaborator_listing = Collaborators::collaboratorsListing($user_check->id);     
                        $contracted_vendors = Users::getContractedVendors($user_check->id, "", "");                    

                        return Response::json(array('status' => 1, 'msg' => 'User Details', 'user_details' => $user_details, 'favorite_listing' => $favorite_listing, 'collaborator_listing' => $collaborator_listing, 'contracted_vendors' => $contracted_vendors), 200);    
                    }
                    // token null
                    else
                        return Response::json(array('status'=> 0,'msg'=>'User not found in database!'), 200);
                }
            }
            // Fb first time login
            else {
                return Response::json(array('status'=> 5,'msg'=>'First time user!'), 200);
            }
        }

    }

    public static function userLoginFb($input) {

        $validation = Validator::make($input, Users::$userLoginFbRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {

            $fb_id = $input['fb_id'];
            $fb_name = isset($input['fb_name']) ? $input['fb_name'] : '';
            $fb_email = isset($input['fb_email']) ? $input['fb_email'] : null;
            $fb_image = "http://graph.facebook.com/$fb_id/picture?type=large";                                    
            $fb_gender = isset($input['fb_gender']) ? $input['fb_gender'] : '';
            $fb_phone_no = isset($input['fb_phone_no']) ? $input['fb_phone_no'] : '';
            $wedding_date = isset($input['wedding_date']) ? $input['wedding_date'] : "";
            $wedding_name = isset($input['wedding_name']) ? $input['wedding_name'] : "";
            $wedding_desc = isset($input['wedding_desc']) ? $input['wedding_desc'] : "";
            $location = isset($input['location']) ? $input['location'] : "";
            $lat = isset($input['lat']) ? $input['lat'] : '0';
            $lng = isset($input['lng']) ? $input['lng'] : '0';            
            $current_time = Carbon::now();            

            $access_token = Users::generateToken();

            $user_check = DB::table('users')->select('id', 'email', 'password', 'deleted_at')->where('fb_id', $fb_id)->first();

            // Fb user exists
            if(isset($user_check)) {
                if($user_check->deleted_at!=null || $user_check->deleted_at!='')
                    return Response::json(array('status'=>0, 'msg'=>'This user is deactivated'), 200);
                else {
                    // Updating the user Token
                    $token = Users::updateUserToken($user_check->id);
                    if($token) {                        
                        
                        $user_details = Users::viewUserProfileDetails($user_check->id);
                        $favorite_listing = Favorite::viewFavoriteListing($user_check->id);
                        $collaborator_listing = Collaborators::collaboratorsListing($user_check->id);
                        $contracted_vendors = Users::getContractedVendors($user_check->id, "", "");

                        return Response::json(array('status' => 1, 'msg' => 'User Details', 'user_details' => $user_details, 'favorite_listing' => $favorite_listing, 'collaborator_listing' => $collaborator_listing, 'contracted_vendors' => $contracted_vendors), 200);    
                    }
                    // token null
                    else
                        return Response::json(array('status'=> 0,'msg'=>'User not found in database!'), 200);
                }
            }
            // Fb first time login
            else {
                if($fb_email!=null) {
                    $email_check = DB::table('users')->select('id')->where('email', $fb_email)->first();
                }
                else {
                    $email_check = array();
                }

                // First time login. Email does not exist
                if(count($email_check)==0) {                    

                    // Formatting date according to db
                    $wedding_date = date("Y-m-d 00:00:00", strtotime($wedding_date));

                    if($location!="") {
                        if($lat=='0' || $lng=='0') {
                            $latlng = Users::getLatLng($location);
                            $latlngarr = explode(',', $latlng);
                            $lat = $latlngarr[0];
                            $lng = $latlngarr[1];
                        }
                    }                    

                    $user_id = DB::table('users')->insertGetId(
                        array(
                            'name' => $fb_name,                    
                            'email' => $fb_email,                            
                            'access_token' => $access_token,
                            'fb_id' => $fb_id,
                            'image' => $fb_image,                    
                            'phone_no' => $fb_phone_no,
                            'user_role' => '0',
                            'approved' => '1',
                            'created_at' => $current_time,
                            'updated_at' => $current_time,
                        )
                    );

                    $wedding_id = DB::table('wedding')->insertGetId(
                        array(                    
                            'user_id' => $user_id,
                            'name' => $wedding_name,
                            'description' => $wedding_desc,
                            'date' => $wedding_date,
                            'location' => $location,
                            'lat' => $lat,
                            'lng' => $lng,
                            'created_at' => $current_time,
                            'updated_at' => $current_time,
                        )
                    );

                    $user_details = Users::viewUserProfileDetails($user_id);
                    $favorite_listing = Favorite::viewFavoriteListing($user_id);
                    $collaborator_listing = Collaborators::collaboratorsListing($user_id);     
                    $contracted_vendors = Users::getContractedVendors($user_id, "", "");                   

                    return Response::json(array('status' => 5, 'msg' => 'User Details', 'user_details' => $user_details, 'favorite_listing' => $favorite_listing, 'collaborator_listing' => $collaborator_listing, 'contracted_vendors' => $contracted_vendors), 200);    

                }
                // Email exists. Update fb id
                else {
                    DB::table('users')->where('email', $fb_email)->update(['fb_id' => $fb_id]);
                    return Response::json(array('status'=> 0,'msg'=>'User email already exists.'), 200);
                }
            }
        }

    }

    public static function userLogout($input) {

        $validation = Validator::make($input, Users::$accessTokenRequired);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {

            $access_token = $input['access_token'];
            $user_id = Users::getUserIdByToken($access_token);            

            DB::table('users')->where('id', $user_id)->update(['access_token' => null, 'device_token' => '', 'reg_id' => '']);
            
            return Response::json(array('status'=>1, 'msg'=>'Successfully logged out'), 200);
        }

    }

    public static function userEditProfile($input) {        

        $validation = Validator::make($input, Users::$userEditProfileRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {

            $access_token = $input['access_token'];
            $imgfile = Input::file('image');

            // Accepting Parameters :
            // access_token
            // name
            // gender
            // image
            // phone_no
            
            $current_time = Carbon::now();
            
            $user_id = Users::getUserIdByToken($access_token);  

            $check_user = DB::table('users')->select('id', 'name', 'gender', 'image', 'phone_no')->where('id', $user_id)->first();

            // Handling User Profile Image
            if($imgfile=="") {
                $user_data = DB::table('users')->select('image')->where('id', $user_id)->first();
                $image = $user_data->image;
            }
            else {
                $image = Users::uploadImage($user_id, $input);

                $profile_image = DB::table('users')->select('image')->where('id',$user_id)->first();
                if(isset($profile_image)) {
                    if((!empty($profile_image->image))) {
                        if(file_exists('uploads/'.$profile_image->image)){
                            unlink('uploads/'.$profile_image->image);
                        }
                    }
                }
            }

            // Update User Information
            DB::table('users')->where('id', $user_id)->update([
                'name' => isset($input['name']) ? $input['name'] : $check_user->name,
                'gender' => isset($input['gender']) ? $input['gender'] : $check_user->gender,
                'image' => $image, 
                'phone_no' => isset($input['phone_no']) ? $input['phone_no'] : $check_user->phone_no
            ]);

            $user_details = Users::viewUserProfileDetails($user_id);
            $favorite_listing = Favorite::viewFavoriteListing($user_id);
            $collaborator_listing = Collaborators::collaboratorsListing($user_id);   
            $contracted_vendors = Users::getContractedVendors($user_id, "", "");                     

            return Response::json(array('status' => 1, 'msg' => 'Successfully Updated', 'user_details' => $user_details, 'favorite_listing' => $favorite_listing, 'collaborator_listing' => $collaborator_listing, 'contracted_vendors' => $contracted_vendors), 200);                

        }

    }

    public static function userChangePassword($input) {

        $validation = Validator::make($input, Users::$userChangePasswordRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {
            $access_token = $input['access_token'];
            $old_password = $input['old_password'];
            $new_password  = $input['new_password'];
            $new_password = Hash::make($new_password);

            $user_id = Users::getUserIdByToken($access_token);

            $user_check = DB::table('users')->select('password')->where('id', $user_id)->first();

            if(Hash::check($old_password, $user_check->password)) {
                DB::table('users')->where('id', $user_id)->update(['password' => $new_password]);                        

                return Response::json(array('status'=>1, 'msg'=>'Password Updated'), 200);
            }
            else {
                return Response::json(array('status'=>0, 'msg'=>'Invalid Password'), 200);
            }
        }

    }

    public static function userDeactivateProfile($input) {

        $validation = Validator::make($input, Users::$accessTokenRequired);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {
            $access_token = $input['access_token'];
            $current_time = Carbon::now();
            $user_id = Users::getUserIdByToken($access_token);

            DB::table('users')->where('id', $user_id)->update(['deleted_at' => $current_time]);

            $user_details = Users::viewUserProfileDetails($user_id);
            $favorite_listing = Favorite::viewFavoriteListing($user_id);
            $collaborator_listing = Collaborators::collaboratorsListing($user_id);            
            $contracted_vendors = Users::getContractedVendors($user_id, "", "");            

            return Response::json(array('status' => 1, 'msg' => 'Profile Deactivated', 'user_details' => $user_details, 'favorite_listing' => $favorite_listing, 'collaborator_listing' => $collaborator_listing, 'contracted_vendors' => $contracted_vendors), 200);                

        }

    }

    public static function userViewMyProfile($input) {

        $validation = Validator::make($input, Users::$accessTokenRequired);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {
            $access_token = $input['access_token'];
            $user_id = Users::getUserIdByToken($access_token);

            $check_user =  DB::select("SELECT `id` FROM `users` WHERE `id` = '$user_id' AND (`user_role` = '0' OR `user_role` = '2')");
            if(empty($check_user)) {
                return Response::json(array('status'=>0, 'msg'=>'User is not a customer'), 200);
            }

            $user_details = Users::viewUserProfileDetails($user_id);
            $favorite_listing = Favorite::viewFavoriteListing($user_id);
            $collaborator_listing = Collaborators::collaboratorsListing($user_id);  
            $contracted_vendors = Users::getContractedVendors($user_id, "", "");                    

            return Response::json(array('status' => 1, 'msg' => 'User Details', 'user_details' => $user_details, 'favorite_listing' => $favorite_listing, 'collaborator_listing' => $collaborator_listing, 'contracted_vendors' => $contracted_vendors), 200);

        } 

    }

    public static function userViewProfileById($input) { 

        $validation = Validator::make($input, Users::$userViewProfileByIdRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {
            $access_token = $input['access_token'];
            $user_id_2 = $input['user_id_2'];
            $user_id = Users::getUserIdByToken($access_token);
            
            $check_user =  DB::select("SELECT `id` FROM `users` WHERE `id` = '$user_id_2' AND (`user_role` = '0' OR `user_role` = '2')");
            if(empty($check_user)) {
                return Response::json(array('status'=>0, 'msg'=>'User is not a customer'), 200);
            }

            $user_details = Users::viewUserProfileDetails($user_id_2);            
            $favorite_listing = Favorite::viewFavoriteListing($user_id_2);
            $collaborator_listing = Collaborators::collaboratorsListing($user_id_2);                        
            $contracted_vendors = Users::getContractedVendors($user_id_2, "", "");

            return Response::json(array('status' => 1, 'msg' => 'User Details', 'user_details' => $user_details, 'favorite_listing' => $favorite_listing, 'collaborator_listing' => $collaborator_listing, 'contracted_vendors' => $contracted_vendors), 200);       

        }

    }    

    public static function userStaticListing($input) {
        
        $wedding_types = DB::table('wedding_types')->select('id', 'wedding_type')->get();
        $vendor_business_types = DB::table('businesses')->select('id', 'business', 'category_search_hit')->get();
        
        return Response::json(array('status'=>1, 'msg'=>'Listing', 'wedding_types'=> $wedding_types, 'vendor_business_types'=> $vendor_business_types), 200);
        
    } 

    public static function userStaticListing2($input) {
        
        $wedding_types = DB::table('wedding_types')->select('id', 'wedding_type')->get();
        $vendor_business_types = Users::getVendorBusiness();
        $city_listing = Users::cityListing();
        
        return Response::json(array('status'=>1, 'msg'=>'Listing', 'wedding_types'=> $wedding_types, 'vendor_business_types'=> $vendor_business_types, 'city_listing'=> $city_listing), 200);
        
    } 

    // Set Device Token User
    public static function userSetDeviceToken($input) {

        $validation = Validator::make($input, Users::$setDeviceTokenRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {
            $access_token = $input['access_token'];            
            $device_token = $input['device_token'];            
            $user_id = Users::getUserIdByToken($access_token);

            $user_role = Users::checkUserRole($user_id);

            if($user_role == 0 || $user_role == 2) {
                Users::updateDeviceToken($user_id, $device_token);                
                return Response::json(array('status'=>1, 'msg'=>'Device Token Set'), 200);
            }            
            else 
                return Response::json(array('status'=>0, 'msg'=>'Vendor is not allowed'), 200);
                        
        }

    }

    // User Set Reg Id
    public static function userSetRegId($input) {

        $validation = Validator::make($input, Users::$setRegIdRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {
            $access_token = $input['access_token'];            
            $reg_id = $input['reg_id'];            
            $user_id = Users::getUserIdByToken($access_token);

            $user_role = Users::checkUserRole($user_id);

            if($user_role == 0 || $user_role == 2) {
                Users::updateRegId($user_id, $reg_id);                
                return Response::json(array('status'=>1, 'msg'=>'Reg Id Set'), 200);
            }            
            else 
                return Response::json(array('status'=>0, 'msg'=>'Vendor is not allowed'), 200);
                        
        }

    }

    // Contracted Vendors
    public static function contractedVendors($input) {

        $validation = Validator::make($input, Users::$accessTokenRequired);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {
            $access_token = $input['access_token'];    
            $lat = isset($input['lat']) ? $input['lat'] : "";                        
            $lng = isset($input['lng']) ? $input['lng'] : "";                    
            $user_id = Users::getUserIdByToken($access_token);

            $user_role = Users::checkUserRole($user_id);

            if($user_role == 0 || $user_role == 2) {
                
                $vendor_listing = Users::getContractedVendors($user_id, $lat, $lng);                
                return Response::json(array('status'=>1, 'msg'=>'Contracted Vendors', 'vendor_listing'=>$vendor_listing), 200);
            }            
            else 
                return Response::json(array('status'=>0, 'msg'=>'Vendor is not allowed'), 200);
                        
        }

    }

    // User Home Function
    public static function userHome($input) { 

        $validation = Validator::make($input, Users::$userHomeRules);
        if($validation->fails())
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);        

        $access_token = $input['access_token'] ? $input['access_token'] : "";
        $lat = isset($input['lat']) ? $input['lat'] : "";
        $lng = isset($input['lng']) ? $input['lng'] : "";            

        if($access_token=="") {
            $user_id = "-1";

            // Recently Viewed Vendors
            $recently_viewed_vendors = array();

            // Recently Viewed Weddings
            $recently_viewed_weddings = array();

            // Sponsor Listing
            $sponsor_listing = Users::getSponsorListing($user_id, $lat, $lng);  

            // Favorite Listing
            $favorite_listing = array();

            return Response::json(array('status'=>1, 'msg'=>'Home Listing', 'recently_viewed_vendors'=>$recently_viewed_vendors, 'recently_viewed_weddings'=>$recently_viewed_weddings, 'sponsor_listing'=>$sponsor_listing, 'favorite_listing'=>$favorite_listing), 200);
        }
        else {
            $user_id = Users::getUserIdByToken($access_token);

            // Recently Viewed Vendors
            $recently_viewed_vendors = Users::getRecentlyViewedVendors($user_id, $lat, $lng);

            // Recently Viewed Weddings
            $recently_viewed_weddings = Users::getRecentlyViewedWeddings($user_id, $lat, $lng);

            // Sponsor Listing
            $sponsor_listing = Users::getSponsorListing($user_id, $lat, $lng);  

            // Favorite Listing
            $favorite_listing = Favorite::viewFavoriteListing($user_id);          

            return Response::json(array('status'=>1, 'msg'=>'Home Listing', 'recently_viewed_vendors'=>$recently_viewed_vendors, 'recently_viewed_weddings'=>$recently_viewed_weddings, 'sponsor_listing'=>$sponsor_listing, 'favorite_listing'=>$favorite_listing), 200);
        }                        

    }

    // User Home Function
    public static function search($input) { 

        $access_token = $input['access_token'] ? $input['access_token'] : "";
        $keyword = isset($input['keyword']) ? $input['keyword'] : "";
        $lat = isset($input['lat']) ? $input['lat'] : "";                        
        $lng = isset($input['lng']) ? $input['lng'] : "";         

        if($access_token=="")
            $user_id = "-1";
        else
            $user_id = Users::getUserIdByToken($access_token);

        // Update Sub Category Search If Exists
        Users::updateSubCategorySearchCount($keyword);                                     

        if($keyword != "") {

            $vendor_listing = Users::searchVendorListing($user_id, $keyword, $lat, $lng); 
            $wedding_listing = Users::searchWeddingListing($user_id, $keyword);                                 

        }
        else {
            $vendor_listing = array();
            $wedding_listing = array();
        }

        return Response::json(array('status'=>1, 'msg'=>'Search Listing', 'vendor_listing'=>$vendor_listing, 'wedding_listing'=>$wedding_listing), 200);  

        // Reference query for business type filter
        // SELECT `sub_businesses`.`id`, `sub_businesses`.`business_id`, `sub_businesses`.`sub_business`,
        // `vendor_details`.`business_name`
        // FROM `sub_businesses` 
        // JOIN `businesses` ON `sub_businesses`.`business_id` = `businesses`.`id`
        // JOIN `vendor_details` ON `sub_businesses`.`sub_business` = `vendor_details`.`business_type`
        // WHERE `businesses`.`business` LIKE '%$keyword%'                  

    }

    // Top Sub Categories
    public static function topSubCategories($input) { 
        
        $sub_business_listing = DB::table('sub_businesses')->select('id', 'business_id', 'sub_business', 'search_count')->orderBy('search_count', 'desc')->limit(10)->get();

        return Response::json(array('status'=>1, 'msg'=>'Sub Business Listing', 'sub_business_listing'=>$sub_business_listing), 200);        

    }

}
