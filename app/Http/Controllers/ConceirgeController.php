<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Admin;
use App\AdminChat;
use App\AdminBusinesses;
use App\Conceirge;

class ConceirgeController extends Controller {

	public function __construct()
    {
        $this->middleware('admin', ['except' => 'admin/login']);
    }

    // Dashboard
	public function sendConceirgeUserMessage() {
             
        return $Input::all();
                
    }    

}
