<?php namespace App;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class Business extends Model {

	// Validation Rules

	public static $accessTokenRequired = array(
        'access_token' => 'required|exists:users,access_token',
    );

    public static $searchBusinessTypeRules = array(
        'access_token' => 'required|exists:users,access_token',
        'keyword' => 'required',
    );

    // Common Functions

    public static function categorySearchHit() {

        return 'hi';

    }

	// Business Functions

    public static function businessTypeListing($input) {  
        
        $business_details = Business::all();
        
        return Response::json(array('status'=>1, 'msg'=>'Business Details', 'business_details'=>$business_details), 200);

    }

    public static function searchBusinessType($input) {   

        $validation = Validator::make($input, Business::$searchBusinessTypeRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {
            $access_token = $input['access_token'];
            $keyword = $input['keyword'];
            $user_id = Users::getUserIdByToken($access_token);

            $business_details = DB::select(
            	"SELECT * FROM `businesses` WHERE `business` LIKE '%$keyword'
            	UNION
            	SELECT * FROM `businesses` WHERE `business` LIKE '%$keyword%'
        	");
            
            return Response::json(array('status'=>1, 'msg'=>'Business Details', 'business_details'=>$business_details), 200);

        }

    }

}
