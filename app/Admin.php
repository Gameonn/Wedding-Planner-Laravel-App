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

class Admin extends Model {

    public static $loginRules = array(
        'username' => 'required',
        'password' => 'required',
    );

    public static $addOperatorRules = array(
        'operator_username' => 'required',
        'password' => 'required',
        'confirm_password' => 'required',        
        'operator_role' => 'required',
    );    

    public static $editOperatorRules = array(
        'operator_username' => 'required',        
    );        

    public static $userIdRequired = array(
        'user_id' => 'required',        
    );          

    public static $addVendorCodeRules = array(
        'name' => 'required',
        'email' => 'required|email|Unique:users',
        'password' => 'required',
        'phone_no' => 'required|Unique:users',
        'business_name' => 'required',
        'description' => 'required',
        'location' => 'required',
        'average_cost' => 'required'      
    );

//************************************************************************************************************************
//                                                      Common Functions
//************************************************************************************************************************
    
    // Generate and Save User Token
    public static function generateAndSaveUserToken($user_id) {

        $token = str_random(30);
        DB::table('admin')->where('id', $user_id)->update(['remember_token' => $token]);
        return $token;

    }

//************************************************************************************************************************
//                                                      Main Functions
//************************************************************************************************************************

    // Login Function
    public static function login($input) {

        $validation = Validator::make($input, Admin::$loginRules);
        if($validation->fails()) {
            return 0;
        }
        else {

            $username = $input['username'];
            $password = $input['password'];            

            $admin_check = DB::table('admin')->select('id', 'username', 'password')->where('username', $username)->first();            

            if(isset($admin_check)) {

                if((Hash::check($password, $admin_check->password))) {

                    $remember_token = Admin::generateAndSaveUserToken($admin_check->id);
                    Session::put('remember_token', $remember_token);
                    Session::put('admin_id', $admin_check->id);
                    Session::put('admin_username', $admin_check->username);

                    Session::get('remember_token');

                    return 1;
                }
                // Password incorrect
                else
                    return 0;
            }
            else
                return 0;
        }

    }

    // Dashboard
    public static function dashboard() {

        $dashboard_details = DB::select(
            "SELECT `id`,
            (SELECT count(`id`) FROM `users` WHERE `user_role` = '1') AS `total_vendors`,
            (SELECT count(`id`) FROM `wedding`) AS `total_weddings`,
            (SELECT count(`id`) FROM `users` WHERE `user_role` = '0') AS `total_users`,
            (SELECT count(`id`) FROM `users` WHERE `user_role` = '2') AS `total_collaborators`,
            (SELECT count(`id`) FROM `collaborators`) AS `total_collaborator_groups`
            FROM `admin`
            WHERE `id` = '1'
        ");

        return $dashboard_details;

    }

    public static function vendorListing($keyword, $page) {

        $length = 20;
        $offset = $page * $length;
        $sr_no = $page * $length;        

        if($keyword)
            $filter_by_email = "AND `users`.`email` LIKE '%$keyword%'";
        else
            $filter_by_email = "";        

        $vendor_listing = DB::select(
            "SELECT `users`.`id`,
            `users`.`name`,
            `users`.`email`,
            `users`.`gender`,
            `users`.`image`,
            `users`.`phone_no`,
            `users`.`user_role`,
            `users`.`approved`,
            `vendor_details`.`business_name`,
            `vendor_details`.`business_type`,
            `vendor_details`.`description`,
            `vendor_details`.`location`,
            `vendor_details`.`average_cost`,
            (SELECT `image` FROM `vendor_portfolio_images` WHERE `user_id` = `users`.`id` LIMIT 1) AS `vendor_portfolio_image`,
            (SELECT count(`id`) FROM `users` WHERE `user_role` = '1' AND `deleted_at` IS Null $filter_by_email) AS `total_users`,
            '$keyword' AS `keyword`,
            '$sr_no' AS `sr_no`,
            '$page' AS `page_no`
            FROM `users`
            JOIN `vendor_details` ON `vendor_details`.`user_id` = `users`.`id`
            WHERE `user_role` = '1' AND `deleted_at` IS Null
            $filter_by_email
            LIMIT $offset, $length
        ");

        foreach ($vendor_listing as $key => $value) {

            if($value->image==null || $value->image=="")
                $value->image = Users::getFormattedImage('default-profile-pic.png');
            else 
                $value->image = Users::getFormattedImage($value->image);

            $value->vendor_portfolio_image = Users::getFormattedImage($value->vendor_portfolio_image);      

            if($value->keyword == null || $value->keyword == "" || $value->keyword == "0")      
                $value->keyword = "0";


            $value->sr_no = $sr_no + 1;
            $sr_no++;            

            // Count Total Pages        
            $value->total_pages = ceil($value->total_users / 20);
        }        

        return $vendor_listing;

    }

    public static function vendorApprove($input) {    
        DB::table('users')->where('id', $input['user_id'])->update(['approved' => 1]);
        return 1;
    }

    public static function vendorDisapprove($input) {        
        DB::table('users')->where('id', $input['user_id'])->update(['approved' => 0]);
        return 1;
    }

    public static function viewVendorProfileDetails($user_id) {        

        // Vendor Details
        $vendor_details = DB::select(
            "SELECT `users`.`id` AS `user_id`, `users`.`name`, `users`.`email`, `users`.`password`, `users`.`access_token`, `users`.`fb_id`, `users`.`gender`, `users`.`image`, `users`.`phone_no`, `users`.`user_role`, `users`.`approved`,
            `vendor_details`.`id` AS `vendor_details_id`, `vendor_details`.`business_name`, `vendor_details`.`business_type`, `vendor_details`.`description`, `vendor_details`.`location`, `vendor_details`.`city`, `vendor_details`.`lat`, `vendor_details`.`lng`, `vendor_details`.`average_cost`,
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

        $vendor_details[0]->vendor_rating = round($vendor_details[0]->vendor_rating, 1);
        $vendor_details[0]->vendor_rating = strval($vendor_details[0]->vendor_rating);
        
        return $vendor_details;

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

        // if(empty($vendor_portfolio_images))
        //     $vendor_portfolio_images[0]->image = "URL::to('/')/images/default-pic.jpg";

        return $vendor_portfolio_images;

    }

    public static function getCityList() {        
        
        $city_list = DB::table('tblcitylist')->get();
        return $city_list;

    }

    public static function getSubBusinessList() {        
        
        $sub_business_list = DB::table('sub_businesses')->get();
        return $sub_business_list;

    }

    public static function vendorDetailsEditCode($input) {                

        $user_id = $input['user_id'];

        if($input['city'] == "-----Select-----") {
            $input['city'] = "";
        }

        if(isset($input['city']) && $input['city']!="") {            
            $latlng = Users::getLatLng($input['city']);
            $latlngarr = explode(',', $latlng);
            $lat = $latlngarr[0];
            $lng = $latlngarr[1];
        }
        else if(isset($input['location']) && $input['location']!="") {            
            $latlng = Users::getLatLng($input['location']);
            $latlngarr = explode(',', $latlng);
            $lat = $latlngarr[0];
            $lng = $latlngarr[1];
        }
        else {
            $lat = 0;
            $lng = 0;
        }

        // Vendor Details Edit        
        DB::table('users')
            ->where('id', $user_id)
            ->update([
                    'name' => $input['name'],
                    'email' => $input['email'],
                    'phone_no' => $input['phone_no'],                    
                ]);

        DB::table('vendor_details')
            ->where('user_id', $user_id)
            ->update([
                    'business_name' => $input['business_name'],
                    'business_type' => $input['business_type'],
                    'description' => $input['description'],
                    'location' => $input['location'],
                    'city' => $input['city'],
                    'lat' => $lat,
                    'lng' => $lng,
                    'average_cost' => $input['average_cost'],                    
                ]);

        return 1;

    }

    public static function vendorExtraDetailsEditCode($input) {                

        $user_id = $input['user_id'];
        $current_time = Carbon::now();        
        
        DB::table('vendor_extra_details')
            ->where('user_id', $user_id)
            ->delete();

        for($i=0; $i<=$input['curr_val']; $i++) {

            DB::table('vendor_extra_details')
                ->insertGetId(array(
                    'user_id' => $user_id,
                    'detail_name' => $input['detail_name_'.$i],
                    'detail_desc' => $input['detail_desc_'.$i],
                    'created_at' => $current_time,
                    'updated_at' => $current_time,
                ));

        }                 

        return 1;

    }

    public static function deleteExtraDetails($input) {                

        DB::table('vendor_extra_details')->where('user_id', $input['user_id'])->where('detail_name', $input['detail_name'])->where('detail_desc', $input['detail_desc'])->delete();

        return 1;

    }    

    public static function removeVendor($input) {                

        $validation = Validator::make($input, Admin::$userIdRequired);
        if($validation->fails()) {
            return $validation->getMessageBag()->first();
        }

        $user_data = DB::table('users')->select('user_role')->where('id', $input['user_id'])->first();
        if($user_data->user_role != '1') 
            return "Invalid Vendor";

        $current_time = Carbon::now();
        DB::table('users')->where('id', $input['user_id'])->update(['deleted_at' => $current_time]);
        return 1;

    }        

    // public static function uploadImagesCode($input) {                

    //     $current_time = Carbon::now(); 
    //     foreach ($input['files'] as $key => $value) {

    //         $file = $input['files'][$key];            
    
    //         if($file->isValid()){                            
    //             //get extension of file
    //             $ext = $file->getClientOriginalExtension();
    //             //directory to store images
    //             $dir = 'uploads';
    //             // change filename to random name
    //             $filename = substr(time(), 0, 15).str_random(30) . ".{$ext}";
    //             // move uploaded file to temp. directory
    //             $upload_success = $file->move($dir, $filename);
    //             $img = $upload_success ? $filename : '';

    //             $vendor_portfolio_image_id = DB::table('vendor_portfolio_images')->insertGetId(array(
    //                 'user_id' => $input['user_id'],
    //                 'image' => $img,
    //                 'created_at' => $current_time,
    //                 'updated_at' => $current_time,
    //             ));
    //         }

    //     }                            
    //     return 1;
    // }

    public static function uploadImagesCode($input) {                

        $current_time = Carbon::now(); 
        foreach ($input['files'] as $key => $value) {

            $file = $input['files'][$key];            
    
            if($file->isValid()) {                            
                
                //get extension of file
                $ext = $file->getClientOriginalExtension();
                // change filename to random name
                $filename = substr(time(), 0, 15).str_random(30) . ".{$ext}";            

                $s3 = AWS::get('s3');
                $s3->putObject(array(
                    'Bucket'     => 'whatashaadi',
                    'Key'        => 'uploads/'.$filename,
                    'SourceFile' => $file->getPathname(),
                    'ContentType' => 'images/jpeg',
                    'ACL' => 'public-read'
                ));                

                $img = $filename;

                $vendor_portfolio_image_id = DB::table('vendor_portfolio_images')->insertGetId(array(
                    'user_id' => $input['user_id'],
                    'image' => $img,
                    'created_at' => $current_time,
                    'updated_at' => $current_time,
                ));
            }

        }                            
        return 1;
    }

    // public static function uploadImage() {
    //     if(Input::file('image')->isValid()) {
                        
    //         // change filename to random name
    //         $filename = substr(time(), 0, 15).str_random(30) . ".{$ext}";            

    //         $s3 = AWS::get('s3');
    //         $s3->putObject(array(
    //             'Bucket'     => 'whatashaadi',
    //             'Key'        => 'uploads/'.$filename,
    //             'SourceFile' => $file->getPathname(),
    //             'ContentType' => 'images/jpeg',
    //             'ACL' => 'public-read'
    //         ));

    //         return $filename;            
    //     }
    // }

    public static function delVendorPortfolioImg($input) {     

        DB::table('vendor_portfolio_images')->where('id', $input['img_del_id'])->delete();
                    
        return 1;

    }

    // User Listing
    public static function userListing($keyword, $page) {

        $length = 20;
        $offset = $page * $length;
        $sr_no = $page * $length;

        if($keyword)
            $where_query = "AND `users`.`email` LIKE '%$keyword%'";
        else
            $where_query = "";

        $user_listing = DB::select(
            "SELECT `users`.`id`,
            `users`.`name`,
            `users`.`email`,
            `users`.`gender`,
            `users`.`image`,
            `users`.`phone_no`,
            `users`.`user_role`,
            `users`.`approved`,
            `wedding`.`name` AS `wedding_name`,
            `wedding`.`description` AS `wedding_desc`,
            `wedding`.`date`,
            `wedding`.`wedding_type`,
            `wedding`.`location`,
            `wedding`.`city`,
            (SELECT `image` FROM `wedding_photos` WHERE `user_id` = `users`.`id` LIMIT 1) AS `wedding_photo`,
            (SELECT count(`id`) FROM `users` WHERE `user_role` = '0' $where_query) AS `total_users`,
            '$keyword' AS `keyword`,
            '$sr_no' AS `sr_no`,
            '$page' AS `page_no`
            FROM `users`
            LEFT JOIN `wedding` ON `wedding`.`user_id` = `users`.`id`
            WHERE `user_role` = '0'
            $where_query
            LIMIT $offset, $length
        ");

        foreach ($user_listing as $key => $value) {

            if($value->image==null || $value->image=="")
                $value->image = Users::getFormattedImage('default-profile-pic.png');
            else 
                $value->image = Users::getFormattedImage($value->image);

            $value->wedding_photo = Users::getFormattedImage($value->wedding_photo);

            if($value->keyword == null || $value->keyword == "" || $value->keyword == "0")      
                $value->keyword = "0";

            $value->sr_no = $sr_no + 1;
            $sr_no++;            

            // Count Total Pages
            $value->total_pages = ceil($value->total_users / 20);
        }        

        return $user_listing;

    }

    // User Details
    public static function userDetails($user_id) {

        $user_details = DB::select(
            "SELECT `users`.`id`,
            `users`.`name`,
            `users`.`email`,
            `users`.`gender`,
            `users`.`image`,
            `users`.`phone_no`,
            `users`.`user_role`,
            `users`.`approved`,
            `wedding`.`name` AS `wedding_name`,
            `wedding`.`description` AS `wedding_desc`,
            `wedding`.`date`,
            `wedding`.`wedding_type`,
            `wedding`.`location`,
            `wedding`.`city`,
            (SELECT `image` FROM `wedding_photos` WHERE `user_id` = `users`.`id` LIMIT 1) AS `wedding_photo`
            FROM `users`
            JOIN `wedding` ON `wedding`.`user_id` = `users`.`id`
            WHERE `users`.`id` = '$user_id'            
        ");

        foreach ($user_details as $key => $value) {

            if($value->image==null || $value->image=="")
                $value->image = Users::getFormattedImage('default-profile-pic.png');
            else 
                $value->image = Users::getFormattedImage($value->image);

            $value->wedding_photo = Users::getFormattedImage($value->wedding_photo);

        }

        return $user_details;

    }

    // Collaborator Listing
    public static function collaboratorListing($keyword, $page) {

        $length = 20;
        $offset = $page * $length;
        $sr_no = $page * $length;

        if($keyword)
            $where_query = "AND `users`.`email` LIKE '%$keyword%'";
        else
            $where_query = "";

        $collaborator_listing = DB::select(
            "SELECT `users`.`id`,
            `users`.`name`,
            `users`.`email`,
            `users`.`gender`,
            `users`.`image`,
            `users`.`phone_no`,
            `users`.`user_role`,
            `users`.`approved`,            
            (SELECT count(`id`) FROM `users` WHERE `user_role` = '2' $where_query) AS `total_users`,
            '$keyword' AS `keyword`,
            '$sr_no' AS `sr_no`,
            '$page' AS `page_no`
            FROM `users`            
            WHERE `user_role` = '2'
            $where_query
            LIMIT $offset, $length
        ");

        foreach ($collaborator_listing as $key => $value) {

            if($value->image==null || $value->image=="")
                $value->image = Users::getFormattedImage('default-profile-pic.png');
            else 
                $value->image = Users::getFormattedImage($value->image);     

            if($value->keyword == null || $value->keyword == "" || $value->keyword == "0")      
                $value->keyword = "0";       

            $value->sr_no = $sr_no + 1;
            $sr_no++;            

            // Count Total Pages
            $value->total_pages = ceil($value->total_users / 20);
        }        

        return $collaborator_listing;

    }

    // User Details
    public static function collaboratorDetails($user_id) {

        $collaborator_details = DB::select(
            "SELECT `users`.`id`,
            `users`.`name`,
            `users`.`email`,
            `users`.`gender`,
            `users`.`image`,
            `users`.`phone_no`,
            `users`.`user_role`,
            `users`.`approved`
            FROM `users`            
            WHERE `users`.`id` = '$user_id'            
        ");

        foreach ($collaborator_details as $key => $value) {

            if($value->image==null || $value->image=="")
                $value->image = Users::getFormattedImage('default-profile-pic.png');
            else 
                $value->image = Users::getFormattedImage($value->image);            

        }

        return $collaborator_details;

    }

    // Chat Operator Listing
    public static function chatOperatorListing() {

        $chat_operator_listing = DB::select(
            "SELECT `id`, `username`
            FROM `admin`
            WHERE `user_role` = 'chat_operator'
        ");

        return $chat_operator_listing;

    }

    // Admin Listing
    public static function adminListing() {

        $admin_listing = DB::select(
            "SELECT `id`, `username`, `user_role`
            FROM `admin`
            WHERE `user_role` = 'chat_operator' || `user_role` = 'data_operator'
        ");

        return $admin_listing;

    }

    // Add Operator
    public static function addOperator($input) {

        $validation = Validator::make($input, Admin::$addOperatorRules);
        if($validation->fails()) {
            return $validation->getMessageBag()->first();
        }

        $current_time = Carbon::now();

        if($input['password'] == $input['confirm_password'])
            $password = Hash::make($input['password']);        
        else 
            return "Password do not match";

        $admin_id = DB::table('admin')->insertGetId(array(
            'username' => $input['operator_username'],
            'password' => $password,            
            'remember_token' => $input['_token'],
            'user_role' => $input['operator_role'],
            'created_at' => $current_time,
            'updated_at' => $current_time,
        ));

        return 1;

    }

    // Edit Operator 
    public static function editOperator($input) {              

        $validation = Validator::make($input, Admin::$editOperatorRules);
        if($validation->fails()) {
            return $validation->getMessageBag()->first();
        }

        $current_time = Carbon::now();

        if(isset($input['password'])) {

            if(!isset($input['confirm_password']))
                return "Password do not match";

            if($input['password'] == $input['confirm_password'])
                $password = Hash::make($input['password']);        
            else 
                return "Password do not match";

            DB::table('admin')->where('id', $input['operator_id'])->update([
                'username' => $input['operator_username'],
                'password' => $password,            
                'remember_token' => $input['_token'],                
                'updated_at' => $current_time,
            ]);

        }   
        else {

            DB::table('admin')->where('id', $input['operator_id'])->update([
                'username' => $input['operator_username'],                
                'remember_token' => $input['_token'],                
                'updated_at' => $current_time,
            ]);

        }     

        return 1;

    }

    // Add Vendor
    public static function addVendorCode($input) {

        $validation = Validator::make($input, Admin::$addVendorCodeRules);
        if($validation->fails()) {
            return $validation->getMessageBag()->first();
        }   

        $password = Hash::make($input['password']);     

        $current_time = Carbon::now();

        if($input['city'] == "-----Select-----") {
            $input['city'] = "";
        }

        if(isset($input['city']) && $input['city']!="") {            
            $latlng = Users::getLatLng($input['city']);
            $latlngarr = explode(',', $latlng);
            $lat = $latlngarr[0];
            $lng = $latlngarr[1];
        }
        else if(isset($input['location']) && $input['location']!="") {            
            $latlng = Users::getLatLng($input['location']);
            $latlngarr = explode(',', $latlng);
            $lat = $latlngarr[0];
            $lng = $latlngarr[1];
        }
        else {
            $lat = 0;
            $lng = 0;
        }

        // Vendor Details Edit        
        $user_id = DB::table('users')->insertGetId(array(
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $password,
            'phone_no' => $input['phone_no'],
            'user_role' => '1',
            'approved' => '1',
            'created_at' => $current_time,
            'updated_at' => $current_time
        ));

        DB::table('vendor_details')->insertGetId(array(
            'user_id' => $user_id,
            'business_name' => $input['business_name'],
            'business_type' => $input['business_type'],
            'description' => $input['description'],
            'location' => $input['location'],
            'city' => $input['city'],
            'lat' => $lat,
            'lng' => $lng,
            'average_cost' => $input['average_cost'],
            'created_at' => $current_time,
            'updated_at' => $current_time
        ));
        
        return 1;

    }


}
