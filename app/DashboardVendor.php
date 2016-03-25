<?php namespace App;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class DashboardVendor extends Model {

	// Validation Rules

	public static $accessTokenRequired = array(
        'access_token' => 'required|exists:users,access_token',
    );

	public static $phoneCallHitRules = array(
        'access_token' => 'required|exists:users,access_token',
        'user_id_2' => 'required',
    );

    public static $dashboardScoreHitRules = array(
        'access_token' => 'required|exists:users,access_token',
        'user_id_2' => 'required',
    );   

    public static $viewDashboardRules = array(
        'access_token' => 'required|exists:users,access_token',        
    );   

	// Dashboard Functions 

	// Phone Call Hit

	public static function phoneCallHit($input) {

        $validation = Validator::make($input, DashboardVendor::$phoneCallHitRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {

            $access_token = $input['access_token'];
            $user_id_2 = $input['user_id_2'];
            $user_id = Users::getUserIdByToken($access_token);

            $current_time = Carbon::now();

            $check_customer = DB::table('users')->select('id')->where('id', $user_id)->where('user_role', 0)->orWhere('user_role', 2)->first();
            $check_vendor = DB::table('users')->select('id')->where('id', $user_id_2)->where('user_role', 1)->first();

            if(!empty($check_customer) && !empty($check_vendor)) {

            	$dashboard_vendor_id = DB::table('dashboard_vendor')->insertGetId(array(
            		'user_id_1' => $user_id,
            		'user_id_2' => $user_id_2,
            		'call_hits' => '1',
            		'created_at' => $current_time,
            		'updated_at' => $current_time,
        		));

        		return Response::json(array('status'=>1, 'msg'=>'Successfull Phone Call Hit'), 200);

            }   
            else {
            	return Response::json(array('status'=>0, 'msg'=>'Invalid parameters'), 200);
            }         
                       
        }

    }

    // Chat Hit

    public static function chatHit($input) {

        $validation = Validator::make($input, DashboardVendor::$dashboardScoreHitRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {

            $access_token = $input['access_token'];
            $user_id_2 = $input['user_id_2'];
            $user_id = Users::getUserIdByToken($access_token);

            $current_time = Carbon::now();

            $check_customer = DB::table('users')->select('id')->where('id', $user_id)->where('user_role', 0)->orWhere('user_role', 2)->first();
            $check_vendor = DB::table('users')->select('id')->where('id', $user_id_2)->where('user_role', 1)->first();

            if(!empty($check_customer) && !empty($check_vendor)) {

            	$dashboard_vendor_id = DB::table('dashboard_vendor')->insertGetId(array(
            		'user_id_1' => $user_id,
            		'user_id_2' => $user_id_2,
            		'chat_hits' => '1',
            		'created_at' => $current_time,
            		'updated_at' => $current_time,
        		));

        		return Response::json(array('status'=>1, 'msg'=>'Successfull Chat Hit'), 200);

            }   
            else {
            	return Response::json(array('status'=>0, 'msg'=>'Invalid parameters'), 200);
            }         
                       
        }

    }

    // Chat Hit

    public static function viewDashboard($input) {

        $validation = Validator::make($input, DashboardVendor::$viewDashboardRules);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {

            $access_token = $input['access_token'];            
            $user_id = Users::getUserIdByToken($access_token);

            $current_time = Carbon::now('Asia/Kolkata');

            $current_year_start = $current_time->year.'-01-01';            
            $current_year_end = $current_time->year.'-12-31';            
            $current_month_start = $current_time->year.'-'.$current_time->month.'-01';            
            $current_month_end = $current_time->year.'-'.$current_time->month.'-31';            
            $current_time_start = $current_time->year.'-'.$current_time->month.'-'.$current_time->day.' 00:00:00';
            $current_time_end = $current_time->year.'-'.$current_time->month.'-'.$current_time->day.' 23:59:59';        
            
            $check_vendor = DB::table('users')->select('id')->where('id', $user_id)->where('user_role', 1)->first();

            if(!empty($check_vendor)) {

                $all_time_analytic = DB::select(
                    "SELECT `id`,
                    (SELECT SUM(`call_hits`) FROM `dashboard_vendor` WHERE `user_id_2` = `users`.`id`) AS `phone_call_hits`,
                    (SELECT SUM(`chat_hits`) FROM `dashboard_vendor` WHERE `user_id_2` = `users`.`id`) AS `chat_hits`,
                    (SELECT SUM(`profile_view_count`) FROM `profile_view_count` WHERE `user_id` = `users`.`id`) AS `page_views`,
                    (SELECT SUM(`search_category_count`) FROM `category_search_hits` WHERE `business_type_id` = (SELECT `business_type` FROM `vendor_details` WHERE `user_id` = `users`.`id`)) AS `category_search_hits`
                    FROM `users`
                    WHERE `id` = '$user_id'
                ");

                $yearly_analytic = DB::select(
                    "SELECT `id`,
                    (SELECT SUM(`call_hits`) FROM `dashboard_vendor` WHERE `user_id_2` = `users`.`id` AND `created_at` BETWEEN '$current_year_start' AND '$current_year_end') AS `phone_call_hits`,
                    (SELECT SUM(`chat_hits`) FROM `dashboard_vendor` WHERE `user_id_2` = `users`.`id` AND `created_at` BETWEEN '$current_year_start' AND '$current_year_end') AS `chat_hits`,
                    (SELECT SUM(`profile_view_count`) FROM `profile_view_count` WHERE `user_id` = `users`.`id` AND `created_at` BETWEEN '$current_year_start' AND '$current_year_end') AS `page_views`,
                    (SELECT SUM(`search_category_count`) FROM `category_search_hits` WHERE `business_type_id` = (SELECT `business_type` FROM `vendor_details` WHERE `user_id` = `users`.`id`) AND `created_at` BETWEEN '$current_year_start' AND '$current_year_end') AS `category_search_hits`
                    FROM `users`
                    WHERE `id` = '$user_id'
                ");

                $monthly_analytic = DB::select(
                    "SELECT `id`,
                    (SELECT SUM(`call_hits`) FROM `dashboard_vendor` WHERE `user_id_2` = `users`.`id` AND `created_at` BETWEEN '$current_month_start' AND '$current_month_end') AS `phone_call_hits`,
                    (SELECT SUM(`chat_hits`) FROM `dashboard_vendor` WHERE `user_id_2` = `users`.`id` AND `created_at` BETWEEN '$current_month_start' AND '$current_month_end') AS `chat_hits`,
                    (SELECT SUM(`profile_view_count`) FROM `profile_view_count` WHERE `user_id` = `users`.`id` AND `created_at` BETWEEN '$current_month_start' AND '$current_month_end') AS `page_views`,
                    (SELECT SUM(`search_category_count`) FROM `category_search_hits` WHERE `business_type_id` = (SELECT `business_type` FROM `vendor_details` WHERE `user_id` = `users`.`id`) AND `created_at` BETWEEN '$current_month_start' AND '$current_month_end') AS `category_search_hits`
                    FROM `users`
                    WHERE `id` = '$user_id'
                ");

                $daily_analytic = DB::select(
                    "SELECT `id`,
                    (SELECT SUM(`call_hits`) FROM `dashboard_vendor` WHERE `user_id_2` = `users`.`id` AND `created_at` BETWEEN '$current_time_start' AND '$current_time_end') AS `phone_call_hits`,
                    (SELECT SUM(`chat_hits`) FROM `dashboard_vendor` WHERE `user_id_2` = `users`.`id` AND `created_at` BETWEEN '$current_time_start' AND '$current_time_end') AS `chat_hits`,
                    (SELECT SUM(`profile_view_count`) FROM `profile_view_count` WHERE `user_id` = `users`.`id` AND `created_at` BETWEEN '$current_time_start' AND '$current_time_end') AS `page_views`,
                    (SELECT SUM(`search_category_count`) FROM `category_search_hits` WHERE `business_type_id` = (SELECT `business_type` FROM `vendor_details` WHERE `user_id` = `users`.`id`) AND `created_at` BETWEEN '$current_time_start' AND '$current_time_end') AS `category_search_hits`
                    FROM `users`
                    WHERE `id` = '$user_id'
                ");

                if($all_time_analytic[0]->phone_call_hits==null) 
                    $all_time_analytic[0]->phone_call_hits = "";
                if($all_time_analytic[0]->chat_hits==null) 
                    $all_time_analytic[0]->chat_hits = "";
                if($all_time_analytic[0]->page_views==null) 
                    $all_time_analytic[0]->page_views = "";
                if($all_time_analytic[0]->category_search_hits==null) 
                    $all_time_analytic[0]->category_search_hits = "";

                if($yearly_analytic[0]->phone_call_hits==null) 
                    $yearly_analytic[0]->phone_call_hits = "";
                if($yearly_analytic[0]->chat_hits==null) 
                    $yearly_analytic[0]->chat_hits = "";
                if($yearly_analytic[0]->page_views==null) 
                    $yearly_analytic[0]->page_views = "";
                if($yearly_analytic[0]->category_search_hits==null) 
                    $yearly_analytic[0]->category_search_hits = "";

                if($monthly_analytic[0]->phone_call_hits==null) 
                    $monthly_analytic[0]->phone_call_hits = "";
                if($monthly_analytic[0]->chat_hits==null) 
                    $monthly_analytic[0]->chat_hits = "";
                if($monthly_analytic[0]->page_views==null) 
                    $monthly_analytic[0]->page_views = "";
                if($monthly_analytic[0]->category_search_hits==null) 
                    $monthly_analytic[0]->category_search_hits = "";

                if($daily_analytic[0]->phone_call_hits==null) 
                    $daily_analytic[0]->phone_call_hits = "";
                if($daily_analytic[0]->chat_hits==null) 
                    $daily_analytic[0]->chat_hits = "";
                if($daily_analytic[0]->page_views==null) 
                    $daily_analytic[0]->page_views = "";
                if($daily_analytic[0]->category_search_hits==null) 
                    $daily_analytic[0]->category_search_hits = "";

                // Get Notifications
                $notification_listing = Notifications::viewNotifications($user_id);                       

                return Response::json(array('status'=>1, 'msg'=>'Dashboard Details', 'all_time_analytic'=>$all_time_analytic[0], 'yearly_analytic'=>$yearly_analytic[0], 'monthly_analytic'=>$monthly_analytic[0], 'daily_analytic'=>$daily_analytic[0], 'notification_listing' => $notification_listing), 200);

            }   
            else {
                return Response::json(array('status'=>0, 'msg'=>'Invalid parameters'), 200);
            }         
                       
        }

    }

    // View Client Leads Listing

    public static function viewClientsLeadsListing($input) {

        $validation = Validator::make($input, DashboardVendor::$accessTokenRequired);
        if($validation->fails()) {
            return Response::json(array('status'=>0, 'msg'=>$validation->getMessageBag()->first()), 200);
        }
        else {

            $access_token = $input['access_token']; 
            //$lead_order = isset($input['lead_order']) ? $input['lead_order'] : 0;   // 0 : Strongest Lead, 1 : Average Lead, 2 : Weak Lead
            $user_id = Users::getUserIdByToken($access_token);

            $leads_listing = DB::select(
                "SELECT `id`, `user_id`, `vendor_id`, `session_time`,
                (SELECT `name` FROM `users` WHERE `id` = `profile_leads`.`user_id`) AS `user_name`,
                (SELECT `image` FROM `users` WHERE `id` = `profile_leads`.`user_id`) AS `user_image`,
                (SELECT `phone_no` FROM `users` WHERE `id` = `profile_leads`.`user_id`) AS `user_phone_no`,
                '2 min ago' AS `active_ago`
                FROM `profile_leads`
                WHERE `vendor_id` = '$user_id'
                ORDER BY `session_time` DESC
            ");
  
            $client_listing = DB::select(
                "SELECT `id`, `user_id`, `vendor_id`, `wedding_id`,
                (SELECT `name` FROM `users` WHERE `id` = `contract`.`user_id`) AS `user_name`,
                (SELECT `image` FROM `users` WHERE `id` = `contract`.`user_id`) AS `user_image`,
                (SELECT `phone_no` FROM `users` WHERE `id` = `contract`.`user_id`) AS `user_phone_no`,
                (SELECT `name` FROM `wedding` WHERE `id` = `contract`.`wedding_id`) AS `wedding_name`,
                (SELECT `date` FROM `wedding` WHERE `id` = `contract`.`wedding_id`) AS `wedding_date`,
                '2 min ago' AS `active_ago`
                FROM `contract`
                WHERE `vendor_id` = '$user_id'
                ORDER BY `wedding_date` DESC
            ");  

            $leads_count = count($leads_listing);                 
            $leads_div = $leads_count/3;            
            $strong_leads = ceil($leads_div);            
            $avg_leads = floor($leads_div);        
            $weak_leads = floor($leads_div);
            
            $counter = 0;

            // By default putting Weak Lead cause whatever element leftout in the end will be weak lead
            foreach ($leads_listing as $key => $value) {
                $leads_listing[$key]->lead_type = "Weak Lead";
            }

            for ($i=0; $i < $strong_leads; $i++) { 
                $leads_listing[$i]->lead_type = "Strong Lead";
                $counter++;
            }

            for ($j=0; $j < $avg_leads; $j++) { 
                $leads_listing[$counter]->lead_type = "Average Lead";
                $counter++;
            }

            for ($k=0; $k < $weak_leads; $k++) {  
                $leads_listing[$counter]->lead_type = "Weak Lead";
                $counter++;
            }

            foreach ($leads_listing as $key => $value) {
                $leads_listing[$key]->user_image = Users::getFormattedImage($leads_listing[$key]->user_image);                
            }

            foreach ($client_listing as $key2 => $value2) {
                $client_listing[$key2]->user_image = Users::getFormattedImage($client_listing[$key2]->user_image);
                $client_listing[$key2]->wedding_date = date("d M Y", strtotime($client_listing[$key2]->wedding_date));
            }

            return Response::json(array('status'=>1, 'msg'=>'Client Leads Listing', 'leads_listing'=>$leads_listing, 'client_listing'=>$client_listing), 200);  
                       
        }

    }

}
