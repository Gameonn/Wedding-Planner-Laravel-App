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

class AdminCity extends Model {
    

    public static function cityListing() {                

        return $city_listing = DB::table('tblcitylist')->get();

    }

    public static function addCity($input) {     

        $current_time = Carbon::now();           

        return $city_id = DB::table('tblcitylist')->insertGetId(array(
            'city_name' => $input['city_name'],
            'state' => $input['state'],     
            'created_at' => $current_time,            
            'updated_at' => $current_time,            
        ));

    }

    public static function editCity($input) {                   

        DB::table('tblcitylist')->where('city_id', $input['city_id'])->update(['city_name' => $input['city_name'], 'state' => $input['state']]);

    }

    public static function removeCity($input) {                   

        DB::table('tblcitylist')->where('city_id', $input['city_id'])->delete();

    }

}
