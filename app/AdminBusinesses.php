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

class AdminBusinesses extends Model {
    
    // Validation Rules

    public static $addSubBusinessRules = array(
        'business_id' => 'required',
        'sub_business_name' => 'required',        
    );

    public static $addBusinessRules = array(
        'business_name' => 'required',        
        'image' => 'required',
    );  

    public static $editBusinessRules = array(
        'business_id' => 'required',  
        'business_name' => 'required',                
    );      

    // Admin Businesses Functions

    public static function vendorBusinesses() {                

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

        if(!empty($businesses)) {

            foreach($final as $value) {
                $sub=array();
                foreach($value['sub_business'] as $value2){
                    $sub[]=$value2;
                }
                $value['sub_business']=$sub;
                                                     
                $data[]=$value;
            }

            foreach ($data as $key3 => $value3) {
                $data[$key3]['business_image'] = Users::getFormattedImage($data[$key3]['business_image']);

                $sub_business_string = "";            

                foreach ($data[$key3]['sub_business'] as $key2 => $value2) {                    
                    $sub_business_string = $sub_business_string."<li>".$data[$key3]['sub_business'][$key2]['sub_business_name']."</li>";
                } 

                $data[$key3]['sub_business_string'] = $sub_business_string; 
                
            }

            return $data;

        }      
        else {
            $data = array();
        }                                      

    }

    public static function addBusiness($input) { 

        if(empty($input['business_name'])) {
            return "Please provide business name.";
        }
        elseif(empty($input['tags'])) {
            return "Please provide atleast one subcategory.";
        }
        else {}

        $validation = Validator::make($input, AdminBusinesses::$addBusinessRules);
        if($validation->fails()) {
            return $validation->getMessageBag()->first();
        }             

        $current_time = Carbon::now();      
        $image = Users::uploadImage();  

        $business_data = DB::table('businesses')->select('id')->where('business', $input['business_name'])->first();   

        if(!empty($business_data))
            return "Already a category with same name.";

        $business_id = DB::table('businesses')->insertGetId(array(
            'business' => $input['business_name'],
            'image' => $image,
            'created_at' => $current_time,
            'updated_at' => $current_time,
        ));

        foreach ($input['tags'] as $key => $value) {

            $sub_business_data = DB::table('sub_businesses')->select('id')->where('business_id', $business_id)->where('sub_business', $value)->first();

            if(empty($sub_business_data)) {

                $sub_business_id = DB::table('sub_businesses')->insertGetId(array(
                    'business_id' => $business_id,
                    'sub_business' => $value,
                    'created_at' => $current_time,
                    'updated_at' => $current_time,
                ));

            }                    

        }

        return 1;

    }

    public static function editBusiness($input) {                           

        $validation = Validator::make($input, AdminBusinesses::$editBusinessRules);
        if($validation->fails()) {
            return $validation->getMessageBag()->first();
        }             

        $imgfile = isset($input['image']) ? $input['image'] : "";
        $current_time = Carbon::now();        

        if($imgfile=="") {

            DB::table('businesses')->where('id', $input['business_id'])->update([
                'business' => $input['business_name'],                                
                'updated_at' => $current_time,
            ]);

        }     
        else {

            $image = Users::uploadImage();  

            DB::table('businesses')->where('id', $input['business_id'])->update([
                'business' => $input['business_name'],
                'image' => $image,                
                'updated_at' => $current_time,
            ]);

        }

        DB::table('sub_businesses')->where('business_id', $input['business_id'])->delete();   

        foreach ($input['editTags'] as $key => $value) {

            $sub_business_data = DB::table('sub_businesses')->select('id')->where('business_id', $input['business_id'])->where('sub_business', $value)->first();

            if(empty($sub_business_data)) {

                $sub_business_id = DB::table('sub_businesses')->insertGetId(array(
                    'business_id' => $input['business_id'],
                    'sub_business' => $value,
                    'created_at' => $current_time,
                    'updated_at' => $current_time,
                ));

            }                    

        }

        return 1;

    }

    public static function deleteBusiness($input) {                   

        DB::table('sub_businesses')->where('business_id', $input['business_id'])->delete();
        DB::table('businesses')->where('id', $input['business_id'])->delete();

    }

    public static function addSubBusiness($input) {  

        $validation = Validator::make($input, AdminBusinesses::$addSubBusinessRules);
        if($validation->fails()) {
            return "Please provide both parameters";
        }           

        $current_time = Carbon::now();           

        $business_id = DB::table('sub_businesses')->insertGetId(array(
            'business_id' => $input['business_id'],
            'sub_business' => $input['sub_business_name'],
            'created_at' => $current_time,
            'updated_at' => $current_time,
        ));

        return 1; 

    }

    public static function deleteSubBusiness($input) {                  

        DB::table('sub_businesses')->where('id', $input['sub_business_id'])->delete();

        return 1; 

    }

}
