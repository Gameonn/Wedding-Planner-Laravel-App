<?php namespace App\Http\Controllers;

use App\Admin;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;

class AdminAuthController extends Controller {

    public function login() {
        return view('admin.login');
    }

    public function loginCode() {
        $input = Input::all();
        $feedback_data =  Admin::login($input);

        if($feedback_data == 1) {
            return redirect('admin/dashboard');
        }
        else {
            return redirect()->back()->with('message', 'Username or Password do not match');
        }
    }

    public function logout() {
        Session::forget('remember_token');
        return redirect('admin/login');
    }

}
