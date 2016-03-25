<?php namespace App;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use \DateTime;

class Wedding extends Model {

	// Validation Rules

	public static $accessTokenRequired = array(
        'access_token' => 'required|exists:users,access_token',
    );

    public static $viewWeddingProfileByIdRules = array(
        'access_token' => 'exists:users,access_token',
        'wedding_id' => 'required',
    );

    public static $weddingListingRules = array(
        'access_token' => 'exists:users,access_token',        
    );    

    // Common Functions

    public static function viewWeddingListing($user_id, $wedding_type, $city, $sort_by, $page, $lat, $lng) { 

        $length = 10;
        $current_time = Carbon::now();
        $zone = "Asia/Kolkata";

        $date = new DateTime($current_time);
        $date->setTimezone(new \DateTimeZone($zone)); 
        $current_date_time = $date->format('Y-m-d h:m:s');

        // Wedding Type Filter
        if($wedding_type == "") {
            $wedding_type_filter = "";
        }
        else {            
            $wedding_type_filter = " AND `wedding_type` = '$wedding_type'";    
        }

        // Wedding City Filter
        if($city == "") {
            $wedding_city_filter = "";
        }
        else {            
            $wedding_city_filter = " AND `city` = '$city'";    
        }

        // Sort Query
        $distance_query = "'-' AS `distance`";

        if($sort_by == "1") {
            if($lat!="" && $lng!="") {
                $distance_query = "( 6373 * acos( cos( radians($lat) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin(radians(lat)) ) ) AS `distance`";
                $sort_by_query = " ORDER BY `distance`"; 
            }   
            else {
                $distance_query = "'-' AS `distance`";
                $sort_by_query = " ORDER BY `created_at` DESC"; 
            }             
        }
        else if($sort_by == "2") {
            $sort_by_query = " ORDER BY `fav_count` DESC";    
        }
        else {
            $sort_by_query = " ORDER BY `created_at` DESC";     
        }

        // Paging
        if($page == "") {
            $limit_query = "LIMIT 0, $length";
        }
        else {            
            $offset = $page * $length;
            $limit_query = "LIMIT $offset, $length";
        }        

        $wedding_listing = DB::select(
            "SELECT * FROM 
                (SELECT `id`, `user_id`, `name` AS `wedding_name`, `description`, `date`, `wedding_type`, `lat`, `lng`, `city` AS `location`,
                (SELECT `name` FROM `users` WHERE `id` = `wedding`.`user_id` LIMIT 1) AS `name`,
                (SELECT `image` FROM `wedding_photos` WHERE `wedding_id` = `wedding`.`id` LIMIT 1) AS `wedding_image`,
                '' AS `wedding_images`,
                (SELECT `id` FROM `favorite` WHERE `user_id` = '$user_id' AND `wedding_id` = `wedding`.`id`) AS `is_fav`,
                (SELECT count(`id`) FROM `favorite` WHERE `wedding_id` = `wedding`.`id`) AS `fav_count`,
                $distance_query
                FROM `wedding`   
                WHERE `user_id` != '$user_id' AND `date` < '$current_date_time'
                $wedding_type_filter
                $wedding_city_filter
                $sort_by_query
                ) AS `temp`
            WHERE `temp`.`wedding_image` IS NOT NULL
            $limit_query
        ");

        foreach ($wedding_listing as $key => $value) {

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

    }

    public static function viewWeddingDetailsById($wedding_id, $user_id) { 

    	$wedding_details = DB::select(
    		"SELECT `id`, `user_id`, `name` AS `wedding_name`, `description`, `date`, `wedding_type`, `location`, `lat`, `lng`,
            (SELECT `name` FROM `users` WHERE `id` = `wedding`.`user_id` LIMIT 1) AS `name`,
            (SELECT `image` FROM `wedding_photos` WHERE `wedding_id` = '$wedding_id' LIMIT 1) AS `wedding_image`,
            (SELECT `id` FROM `favorite` WHERE `user_id` = '$user_id' AND `wedding_id` = '$wedding_id') AS `is_fav`
    		FROM `wedding`
    		WHERE `id` = '$wedding_id'
		");

        if(!empty($wedding_details)) {

            if($wedding_details[0]->wedding_image != NULL)
                $wedding_details[0]->wedding_image = Users::getFormattedImage($wedding_details[0]->wedding_image);
            else 
                $wedding_details[0]->wedding_image = "";

            if($wedding_details[0]->is_fav == null)
                $wedding_details[0]->is_fav = "0";
            else 
                $wedding_details[0]->is_fav = "1";

            return $wedding_details[0];    

        }        
        else {
            return $wedding_details;
        }

    }

    public static function weddingPhotosByWeddingId($wedding_id) { 

        $wedding_photos = DB::select(
            "SELECT `wedding_photos`.`id` AS `wedding_photos_id`, `wedding_photos`.`user_id`, `wedding_photos`.`wedding_id`, `wedding_photos`.`image`,
            `wedding_photo_tags`.`id` AS `wedding_photo_tags_id`, `wedding_photo_tags`.`vendor_id`,
            (SELECT `business_name` FROM `vendor_details` WHERE `user_id` = `wedding_photo_tags`.`vendor_id`) AS `business_name`,
            (SELECT `business_type` FROM `vendor_details` WHERE `user_id` = `wedding_photo_tags`.`vendor_id`) AS `business_type`
            FROM `wedding_photos`
            LEFT JOIN `wedding_photo_tags` ON `wedding_photos`.`id` = `wedding_photo_tags`.`wedding_photo_id`
            WHERE `wedding_photos`.`wedding_id` = '$wedding_id'
        ");

        foreach ($wedding_photos as $key => $value) {
                
            if(!isset($final[$value->wedding_photos_id])){

                $final[$value->wedding_photos_id]=array(
                    "wedding_photos_id"=>$value->wedding_photos_id,
                    "user_id"=>$value->user_id,
                    "wedding_id"=>$value->wedding_id,
                    "image"=>$value->image,
                    "vendor_tagged"=>array()
                );
            }

            if(!isset($final[$value->wedding_photos_id]['vendor_tagged'][$value->wedding_photo_tags_id])) {

                if($wedding_photos[0]->wedding_photo_tags_id != null) {

                    $final[$value->wedding_photos_id]['vendor_tagged'][$value->wedding_photo_tags_id]=array(
                        "wedding_photo_tags_id"=>$value->wedding_photo_tags_id,
                        "vendor_id"=>$value->vendor_id,
                        "business_name"=>$value->business_name,
                        "business_type"=>$value->business_type,
                    );
                
                }                

            }
        }            

        if(empty($final)) {
            $data = array();
        }            
        else {

            foreach($final as $value){
                $sub=array();
                foreach($value['vendor_tagged'] as $value2) {
                    $sub[]=$value2;
                }
                $value['vendor_tagged']=$sub;
                                                     
                $data[]=$value;
            }

            foreach ($data as $key => $value) {
                $data[$key]['image'] = Users::getFormattedImage($value['image']);

                foreach ($data[$key]['vendor_tagged'] as $key2 => $value2) {
                    if($data[$key]['vendor_tagged'][$key2]['wedding_photo_tags_id'] == null)                
                        $data[$key]['vendor_tagged'][$key2]['wedding_photo_tags_id'] = "";

                    if($data[$key]['vendor_tagged'][$key2]['vendor_id'] == null)                
                        $data[$key]['vendor_tagged'][$key2]['vendor_id'] = "";

                    if($data[$key]['vendor_tagged'][$key2]['business_name'] == null)                
                        $data[$key]['vendor_tagged'][$key2]['business_name'] = "";

                    if($data[$key]['vendor_tagged'][$key2]['business_type'] == null)                
                        $data[$key]['vendor_tagged'][$key2]['business_type'] = "";
                }
                
            }

        }
            
        return $data;

    }

    public static function weddingVendorsByWeddingId($wedding_id, $user_id, $lat, $lng) { 

        if($lat!="" && $lng!="") {
            $distance_query = "( 6373 * acos( cos( radians($lat) ) * cos( radians( (SELECT `lat` FROM `vendor_details` WHERE `user_id` = `wedding_vendor`.`vendor_id`) ) ) * cos( radians( (SELECT `lng` FROM `vendor_details` WHERE `user_id` = `wedding_vendor`.`vendor_id`) ) - radians($lng) ) + sin( radians($lat) ) * sin(radians( (SELECT `lat` FROM `vendor_details` WHERE `user_id` = `wedding_vendor`.`vendor_id`) )) ) ) AS `distance`";
        }
        else {
            $distance_query = "'-' AS `distance`";
        }

        $wedding_vendors = DB::select(
            "SELECT `id`, `user_id`, `wedding_id`, `vendor_id`,
            (SELECT `name` FROM `users` WHERE `id` = `wedding_vendor`.`vendor_id`) AS `name`,
            (SELECT `business_name` FROM `vendor_details` WHERE `user_id` = `wedding_vendor`.`vendor_id`) AS `business_name`,
            (SELECT `business_type` FROM `vendor_details` WHERE `user_id` = `wedding_vendor`.`vendor_id`) AS `business_type`,
            (SELECT `average_cost` FROM `vendor_details` WHERE `user_id` = `wedding_vendor`.`vendor_id`) AS `average_cost`,
            (SELECT `image` FROM `vendor_portfolio_images` WHERE `user_id` = `wedding_vendor`.`vendor_id` LIMIT 1) AS `vendor_portfolio_image`,
            (SELECT AVG(`rating`) FROM `vendor_reviews` WHERE `vendor_id` = `wedding_vendor`.`vendor_id`) AS `vendor_rating`,
            (SELECT `id` FROM `favorite` WHERE `user_id` = '$user_id' AND `vendor_id` = `wedding_vendor`.`vendor_id` LIMIT 1) AS `is_fav`,
            (SELECT count(`id`) FROM `vendor_reviews` WHERE `vendor_id` = `wedding_vendor`.`vendor_id`) AS `review_count`,
            $distance_query
            FROM `wedding_vendor`
            WHERE `wedding_id` = '$wedding_id'
        ");

        foreach ($wedding_vendors as $key => $value) {

            // Manage Portfolio Response
            if($wedding_vendors[$key]->vendor_portfolio_image == null) 
                $wedding_vendors[$key]->vendor_portfolio_image  = "";
            else 
                $wedding_vendors[$key]->vendor_portfolio_image = Users::getFormattedImage($wedding_vendors[$key]->vendor_portfolio_image);            

            // Manage Favorite Response
            if($wedding_vendors[$key]->is_fav == null)
                $wedding_vendors[$key]->is_fav  = "0";
            else 
                $wedding_vendors[$key]->is_fav  = "1";

            // Manage Favorite Response
            if($wedding_vendors[$key]->vendor_rating == null)
                $wedding_vendors[$key]->vendor_rating  = "";
            else {
                $wedding_vendors[$key]->vendor_rating = round($wedding_vendors[$key]->vendor_rating, 1);
                $wedding_vendors[$key]->vendor_rating = strval($wedding_vendors[$key]->vendor_rating);
            }                

            if($wedding_vendors[$key]->distance != '-') {
                $wedding_vendors[$key]->distance = round($wedding_vendors[$key]->distance, 2);
                $wedding_vendors[$key]->distance = strval($wedding_vendors[$key]->distance);
            }       

        }

        return $wedding_vendors;

    }

    public static function weddingSimilarByWeddingId($wedding_id) { 

        $wedding_similar = DB::select(
            "SELECT `id`, `user_id`, `name` AS `wedding_name`, `description`, `date`, `wedding_type`, `location`, `lat`, `lng`,
            (SELECT `image` FROM `wedding_photos` WHERE `wedding_id` = `wedding`.`id` LIMIT 1) AS `wedding_image`
            FROM `wedding`
            WHERE `wedding_type` = (SELECT `wedding_type` FROM `wedding` WHERE `id` = '$wedding_id') AND `id` != '$wedding_id'
        ");

        foreach ($wedding_similar as $key => $value) {
            if($wedding_similar[$key]->wedding_image != NULL)
                $wedding_similar[$key]->wedding_image = Users::getFormattedImage($wedding_similar[$key]->wedding_image);
            else 
                $wedding_similar[$key]->wedding_image = "";
        }

        return $wedding_similar;

    }

    public static function markRecentlyViewedWedding($wedding_id, $user_id) { 

        $current_time = Carbon::now();

        $check_recently_viewed_weddings = DB::table('recently_viewed_weddings')->select('id')->where('user_id', $user_id)->where('wedding_id', $wedding_id)->first();

        if(empty($check_recently_viewed_weddings)) {
            DB::table('recently_viewed_weddings')->insertGetId(array(
                'user_id' => $user_id,
                'wedding_id' => $wedding_id,
                'created_at' => $current_time,
                'updated_at' => $current_time,
            ));
        }      
        else {
            DB::table('recently_viewed_weddings')->where('id', $check_recently_viewed_weddings->id)->delete();

            DB::table('recently_viewed_weddings')->insertGetId(array(
                'user_id' => $user_id,
                'wedding_id' => $wedding_id,
                'created_at' => $current_time,
                'updated_at' => $current_time,
            ));
        }  

        $count_recently_viewed_weddings = DB::table('recently_viewed_weddings')->select('id')->where('user_id', $user_id)->orderBy('id', 'desc')->get();        

        if(count($count_recently_viewed_weddings)>5) {
            $last_id = $count_recently_viewed_weddings[4]->id;

            DB::table('recently_viewed_weddings')->where('id', '<', $last_id)->delete(); 
        }

    }

    // Wedding Listing

    public static function weddingListing($input) { 

        $validation = Validator::make($input, Wedding::$weddingListingRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }

        $access_token = $input['access_token'] ? $input['access_token'] : "";
        $wedding_type = isset($input['wedding_type']) ? $input['wedding_type'] : "";
        $city = isset($input['city']) ? $input['city'] : "";
        $sort_by = isset($input['sort_by']) ? $input['sort_by'] : "";   // 1 : Near By. 2 : Popular. Default : Recent
        $page = isset($input['page']) ? $input['page'] : "";
        $lat = isset($input['lat']) ? $input['lat'] : "";
        $lng = isset($input['lng']) ? $input['lng'] : "";

        if($access_token=="")
            $user_id = "-1";   
        else
            $user_id = Users::getUserIdByToken($access_token);            

        $wedding_listing = Wedding::viewWeddingListing($user_id, $wedding_type, $city, $sort_by, $page, $lat, $lng);   

        return Response::json(array('status' => 1, 'msg' => 'Wedding Listing', 'wedding_listing' => $wedding_listing), 200);            

    }    

	// Wedding Profile By Id

    public static function viewWeddingProfileById($input) { 

        $validation = Validator::make($input, Wedding::$viewWeddingProfileByIdRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {
            $access_token = $input['access_token'] ? $input['access_token'] : "";
            $wedding_id = $input['wedding_id'];
            $lat = isset($input['lat']) ? $input['lat'] : "";
            $lng = isset($input['lng']) ? $input['lng'] : "";

            if($access_token=="")
                $user_id = "-1"; 
            else {
                $user_id = Users::getUserIdByToken($access_token); 

                // Mark as recently viewed
                Wedding::markRecentlyViewedWedding($wedding_id, $user_id);
            }            

            $wedding_details = Wedding::viewWeddingDetailsById($wedding_id, $user_id); 
            $wedding_photos = Wedding::weddingPhotosByWeddingId($wedding_id);
            $wedding_vendors = Wedding::weddingVendorsByWeddingId($wedding_id, $user_id, $lat, $lng);
            $wedding_similar = Wedding::weddingSimilarByWeddingId($wedding_id);   

            return Response::json(array(
                'status' => 1, 
                'msg' => 'Wedding Details', 
                'wedding_details' => $wedding_details, 
                'wedding_photos' => $wedding_photos, 
                'wedding_vendors' => $wedding_vendors,
                'wedding_similar' => $wedding_similar
            ), 200);    

        }

    }

}
