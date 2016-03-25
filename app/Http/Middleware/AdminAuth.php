<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

class AdminAuth {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{

        $remember_token = Session::get('remember_token');

        if(empty($remember_token)) {
            return redirect('admin/login');
        }

		return $next($request);
	}

}
