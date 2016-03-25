<?php namespace App;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Davibennun\LaravelPushNotification\Facades\PushNotification;

class AdminSponsor extends Model {

	// Validation Rules

	public static $accessTokenRequired = array(
        'access_token' => 'required|exists:users,access_token',
    );

//************************************************************************************************
//                                      Common Functions
//************************************************************************************************ 

    

//************************************************************************************************
//                                      Notification Functions
//************************************************************************************************    

    public static function getSponsorListing() {

        $vendor_listing = DB::select(
            "SELECT `users`.`id`,
                `sponsors`.`id`AS `sponsor_id`,
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
                (SELECT `image` FROM `vendor_portfolio_images` WHERE `user_id` = `users`.`id` LIMIT 1) AS `vendor_portfolio_image`
                FROM `users`
                JOIN `vendor_details` ON `vendor_details`.`user_id` = `users`.`id`
                JOIN `sponsors` ON `sponsors`.`vendor_id` = `users`.`id`
                WHERE `user_role` = '1'                       
        ");

        foreach ($vendor_listing as $key => $value) {

            if($value->image==null || $value->image=="")
                $value->image = Users::getFormattedImage('default-profile-pic.png');
            else 
                $value->image = Users::getFormattedImage($value->image);

            $value->vendor_portfolio_image = Users::getFormattedImage($value->vendor_portfolio_image);      
            
        }        

        return $vendor_listing;

        // $sponsor_data = DB::table('sponsors')->select('vendor_id')->first();
        // return $sponsor_data->vendor_id;

    }

    public static function addSponsor($input) {

        $vendor_id = $input['vendor_id'];
        $current_time = Carbon::now();

        DB::table('sponsors')->insertGetId(array(
            'vendor_id' => $vendor_id,
            'created_at' => $current_time,
            'updated_at' => $current_time,
        ));
        return 1;

    }

    public static function deleteSponsor($input) {

        $vendor_id = $input['vendor_id'];        
        DB::table('sponsors')->where('vendor_id', $vendor_id)->delete();
        return 1;

    }

}
