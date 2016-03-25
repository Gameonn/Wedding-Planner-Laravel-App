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
use AWS;  

class AdminWedding extends Model {
	
//********************************************************************************************************
//                                          Validation Rules
//********************************************************************************************************

	public static $newWeddingCodeRules = array(
        'user_id' => 'required',
        'wedding_name' => 'required',
        'wedding_description' => 'required',
        'wedding_date' => 'required',
        'wedding_type' => 'required',
        'wedding_location' => 'required',
        'wedding_city' => 'required',
    );

    public static $weddingEditCodeRules = array(
        'wedding_id' => 'required',
        'user_id' => 'required',
        'wedding_name' => 'required',
        'wedding_description' => 'required',
        'wedding_date' => 'required',
        'wedding_type' => 'required',
        'wedding_location' => 'required',
        'wedding_city' => 'required',
    );    

//********************************************************************************************************
//                                          Common Functions
//********************************************************************************************************

    public static function weddingTypeList() {

        return $wedding_type_list = DB::table('wedding_types')->get();

    }    

//********************************************************************************************************
//                                           Admin Functions
//********************************************************************************************************

    // New Wedding
    public static function newWeddingCode($input) {        

        $validation = Validator::make($input, AdminWedding::$newWeddingCodeRules);
        if($validation->fails()) {
            return $validation->getMessageBag()->first();
        }        

        $current_time = Carbon::now();

        if(isset($input['wedding_city']) && $input['wedding_city']!="") {            
            $latlng = Users::getLatLng($input['wedding_city']);
            $latlngarr = explode(',', $latlng);
            $lat = $latlngarr[0];
            $lng = $latlngarr[1];
        }
        else if(isset($input['wedding_location']) && $input['wedding_location']!="") {            
            $latlng = Users::getLatLng($input['wedding_location']);
            $latlngarr = explode(',', $latlng);
            $lat = $latlngarr[0];
            $lng = $latlngarr[1];
        }
        else {
            $lat = 0;
            $lng = 0;
        }

        DB::table('wedding')->insertGetId(array(
            'user_id' => $input['user_id'],
            'name' => $input['wedding_name'],
            'description' => $input['wedding_description'],
            'date' => $input['wedding_date'],
            'wedding_type' => $input['wedding_type'],
            'location' => $input['wedding_location'],
            'lat' => $lat,
            'lng' => $lat,
            'city' => $input['wedding_city'],
            'created_at' => $current_time,
            'updated_at' => $current_time,
        ));

        return 1;

    }

    // Wedding Listing
    public static function weddingListing($keyword, $page) {  

        $length = 20;
        $offset = $page * $length;
        $sr_no = $page * $length;    
        $zone = 'Asia/Kolkata';    

        if($keyword)
            $where_query = "AND `wedding`.`name` LIKE '%$keyword%'";
        else
            $where_query = "";

        $wedding_listing = DB::select(
            "SELECT `wedding`.`id`,
            `wedding`.`user_id`,
            (SELECT `name` FROM `users` WHERE `id` = `wedding`.`user_id`) AS `user_name`,
            `wedding`.`name`,
            `wedding`.`description`,
            `wedding`.`date`,
            `wedding`.`wedding_type`,
            `wedding`.`location`,
            `wedding`.`lat`,
            `wedding`.`lng`,
            `wedding`.`city`,            
            '$keyword' AS `keyword`,
            '$sr_no' AS `sr_no`,
            '$page' AS `page_no`,
            (SELECT count(`id`) FROM `wedding` WHERE 1 $where_query) AS `total_weddings`
            FROM `wedding`
            WHERE 1    
            $where_query                    
            LIMIT $offset, $length
        ");        

        foreach ($wedding_listing as $key => $value) {
            if($value->user_id == 0)
                $value->user_name = 'Admin';

            if($value->keyword == null || $value->keyword == "" || $value->keyword == "0")      
                $value->keyword = "0";

            $date = new DateTime($value->date);
            $date->setTimezone(new \DateTimeZone($zone));             
            $value->date = $date->format('d M Y');            

            $value->sr_no = $sr_no + 1;
            $sr_no++;            
        }

        // Count Total Pages
        $wedding_listing[0]->total_pages = ceil($wedding_listing[0]->total_weddings / 20);        

        return $wedding_listing;

    }

    // Wedding Details
    public static function weddingDetails($wedding_id) {  

        $zone = "Asia/Kolkata";

        $wedding_details = DB::select(
            "SELECT `wedding`.`id`,
            `wedding`.`user_id`,
            (SELECT `name` FROM `users` WHERE `id` = `wedding`.`user_id`) AS `user_name`,
            `wedding`.`name`,
            `wedding`.`description`,
            `wedding`.`date`,
            `wedding`.`wedding_type`,
            `wedding`.`location`,
            `wedding`.`lat`,
            `wedding`.`lng`,
            `wedding`.`city`             
            FROM `wedding`
            WHERE `id` = '$wedding_id'            
        ");        

        foreach ($wedding_details as $key => $value) {
            if($value->user_id == 0)
                $value->user_name = 'Admin';

            $date = new DateTime($wedding_details[$key]->date);
            $date->setTimezone(new \DateTimeZone($zone));             
            $wedding_details[$key]->date = $date->format('Y-m-d');            
     
        }

        return $wedding_details;

    }    

    // Wedding Edit
    public static function weddingEditCode($input) {        

        $validation = Validator::make($input, AdminWedding::$weddingEditCodeRules);
        if($validation->fails()) {
            return $validation->getMessageBag()->first();
        }        

        $wedding_id = $input['wedding_id'];
        $current_time = Carbon::now();        

        if(isset($input['wedding_city']) && $input['wedding_city']!="") {            
            $latlng = Users::getLatLng($input['wedding_city']);
            $latlngarr = explode(',', $latlng);
            $lat = $latlngarr[0];
            $lng = $latlngarr[1];
        }
        else if(isset($input['wedding_location']) && $input['wedding_location']!="") {            
            $latlng = Users::getLatLng($input['wedding_location']);
            $latlngarr = explode(',', $latlng);
            $lat = $latlngarr[0];
            $lng = $latlngarr[1];
        }
        else {
            $lat = 0;
            $lng = 0;
        }

        DB::table('wedding')->where('id', $wedding_id)->update([            
            'name' => $input['wedding_name'],
            'description' => $input['wedding_description'],
            'date' => $input['wedding_date'],
            'wedding_type' => $input['wedding_type'],
            'location' => $input['wedding_location'],
            'lat' => $lat,
            'lng' => $lat,
            'city' => $input['wedding_city'],
            'created_at' => $current_time,
            'updated_at' => $current_time,
        ]);

        return 1;

    }

    // Wedding Photos
    public static function weddingPhotos($wedding_id) {

        $wedding_photos = DB::select(
            "SELECT `wedding_photos`.`id`,
            `wedding_photos`.`user_id`,            
            `wedding_photos`.`wedding_id`,
            `wedding_photos`.`image`            
            FROM `wedding_photos`
            WHERE `wedding_id` = '$wedding_id'            
        ");        

        foreach ($wedding_photos as $key => $value) {
            
            if($value->image == null)
                $value->image = 'Admin';
            else 
                $value->image = Users::getFormattedImage($value->image);
     
        }

        return $wedding_photos;

    }

    public static function uploadWeddingImages($input) {                

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

                $wedding_photos = DB::table('wedding_photos')->insertGetId(array(
                    'user_id' => $input['user_id'],
                    'wedding_id' => $input['wedding_id'],
                    'image' => $img,
                    'created_at' => $current_time,
                    'updated_at' => $current_time,
                ));
            }

        }                            
        return 1;
    }

    // Remove Wedding Image
    public static function removeWeddingImage($input) {

        $wedding_photos = DB::table('wedding_photos')->where('id', $input['img_del_id'])->delete();        
        return 1;

    }

    // Wedding image by Id
    public static function viewWeddingImageById($image_id) {

        $wedding_image = DB::table('wedding_photos')->select('image')->where('id', $image_id)->first();
        
        $wedding_photos = DB::select(
            "SELECT `wedding_photos`.`id` AS `wedding_photos_id`, `wedding_photos`.`user_id`, `wedding_photos`.`wedding_id`, `wedding_photos`.`image`,
            `wedding_photo_tags`.`id` AS `wedding_photo_tags_id`, `wedding_photo_tags`.`vendor_id`,
            (SELECT `business_name` FROM `vendor_details` WHERE `user_id` = `wedding_photo_tags`.`vendor_id`) AS `business_name`,
            (SELECT `business_type` FROM `vendor_details` WHERE `user_id` = `wedding_photo_tags`.`vendor_id`) AS `business_type`
            FROM `wedding_photos`
            LEFT JOIN `wedding_photo_tags` ON `wedding_photos`.`id` = `wedding_photo_tags`.`wedding_photo_id`
            WHERE `wedding_photos`.`id` = '$image_id'
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

            if(!isset($final[$value->wedding_photos_id]['vendor_tagged'][$value->wedding_photo_tags_id])){

                $final[$value->wedding_photos_id]['vendor_tagged'][$value->wedding_photo_tags_id]=array(
                    "wedding_photo_tags_id"=>$value->wedding_photo_tags_id,
                    "vendor_id"=>$value->vendor_id,
                    "business_name"=>$value->business_name,
                    "business_type"=>$value->business_type,
                    );

            }
        }        
                                    
        foreach($final as $value){
            $sub=array();
            foreach($value['vendor_tagged'] as $value2){
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

        return $data;        

    }

    // Tag Vendor
    public static function tagVendors($input) { 

        $current_time = Carbon::now();       

        foreach ($input['vendor_ids'] as $key => $value) {

            $check_wedding_photo_tags = DB::table('wedding_photo_tags')->select('id')->where('wedding_photo_id', $input['image_id'])->where('vendor_id', $value)->first();

            if(empty($check_wedding_photo_tags)) {

                $wedding_photo_tags_id = DB::table('wedding_photo_tags')->insertGetId(array(
                    'wedding_photo_id' => $input['image_id'],
                    'vendor_id' => $value,
                    'created_at' => $current_time,
                    'updated_at' => $current_time,
                )); 

            }                    

        }                       

        return 1;
    }

    // Wedding Types
    public static function weddingTypes() {
        return $wedding_types = DB::table('wedding_types')->get();                
    }

    // Add Wedding Types
    public static function addWeddingType($input) {

        $current_time = Carbon::now();

        DB::table('wedding_types')->insertGetId(array(
            'wedding_type' => $input['wedding_type_name'],
            'created_at' => $current_time,
            'updated_at' => $current_time,
        ));           

        return 1;     
    }

    // Edit Wedding Types
    public static function editWeddingType($input) {
        $current_time = Carbon::now();    

        DB::table('wedding_types')->where('id', $input['wedding_type_id'])->update([
            'wedding_type' => $input['wedding_type_name'],            
            'updated_at' => $current_time,
        ]);           

        return 1;                  
    }

    // Remove Wedding Types
    public static function deleteWeddingType($input) {        

        DB::table('wedding_types')->where('id', $input['wedding_type_id'])->delete();           
        return 1;
    }

}
