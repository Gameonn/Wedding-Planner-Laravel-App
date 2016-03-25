<?php namespace App\Http\Middleware;

class VerifyCsrf extends \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken
{
    /**
     * Routes we want to exclude.
     *
     * @var array
     */

    protected $routes = [

        'api/vendor-sign-up',
        'api/vendor-login', 
        'api/vendor-login-fb-check',
        'api/vendor-login-fb',       
        'api/vendor-logout',
        'api/vendor-edit-profile',
        'api/vendor-change-password',
        'api/vendor-deactivate-profile',
        'api/vendor-view-my-profile',
        'api/vendor-view-profile-by-id',
        'api/vendor-set-device-token',
        'api/vendor-set-reg-id',

        'api/forgot-password',    
        'api/password-reset-2',     

        'api/portfolio-create',
        'api/portfolio-delete', 

        'api/business-type-listing',
        'api/search-business-type',

        'api/vendor-listing-by-type',

        'api/phone-call-hit',
        'api/chat-hit',
        'api/view-dashboard',

        'api/view-clients-leads-listing',  

        'api/request-feedback',

        'api/user-sign-up',
        'api/user-login',
        'api/user-login-fb-check',
        'api/user-login-fb',
        'api/user-logout',
        'api/user-edit-profile',
        'api/user-change-password',
        'api/user-deactivate-profile',
        'api/user-view-my-profile',
        'api/user-view-profile-by-id',
        'api/user-static-listing',
        'api/user-static-listing-2',
        'api/user-set-device-token',
        'api/user-set-reg-id',
        'api/contracted-vendors',

        'api/user-home',

        'api/write-review',
        'api/delete-review',
        'api/review-listing',
        'api/review-listing-by-vendor-id',

        'api/vendor-listing',
        'api/view-vendor-profile-by-id',

        'api/wedding-listing',
        'api/view-wedding-profile-by-id',

        'api/make-favorite-wedding',
        'api/remove-favorite-wedding',
        'api/view-favorite-wedding-listing',
        'api/make-favorite-vendor',
        'api/remove-favorite-vendor',
        'api/view-favorite-vendor-listing',

        'api/create-collaborator-group',
        'api/create-collaborator-group-by-invite',
        'api/edit-collaborators',
        'api/add-members',
        'api/remove-members',
        'api/view-members',
        'api/remove-collaborator-group',
        'api/view-collaborators-listing',

        'api/create-contract',
        'api/change-contract-status',
        'api/delete-contract',
        'api/cron-event-ended',

        'api/send-message',
        'api/view-messages',
        'api/view-previous-messages',
        'api/view-current-messages',
        'api/view-message-listing',

        'api/get-notifications',
        'api/mark-read-notifications',        
        'api/remove-notification', 
        'api/clear-notifications',
        'api/mark-read-badge-notifications', 
        'api/get-badge-notification-count',

        'api/send-conceirge-user-message',
        'api/view-conceirge-user-messages',
        'api/view-previous-conceirge-user-messages',
        'api/view-current-conceirge-user-messages',
        'api/view-conceirge-user-message-listing',

        'api/search',
        'api/top-sub-categories',

        'admin/send-conceirge-admin-message',
        'admin/view-current-conceirge-admin-messages',
        'admin/view-conceirge-admin-message-listing',      

        'admin/view-previous-conceirge-admin-messages',

        'admin/vendor-disapprove',
        'admin/vendor-approve',
        'admin/remove-vendor',

        'admin/delete-sponsor',        

        'admin/delete-extra-details',

    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     * @throws \Illuminate\Session\TokenMismatchException
     */

    public function handle($request, \Closure $next)
    {
        if ($this->isReading($request)
            || $this->excludedRoutes($request)
            || $this->tokensMatch($request))
        {
            return $this->addCookieToResponse($request, $next($request));
        }
        throw new \TokenMismatchException;
    }

    /**
     * This will return a bool value based on route checking.
     * @param  Request $request
     * @return boolean
     */



    protected function excludedRoutes($request)
    {
        foreach($this->routes as $route)

            if ($request->is($route))
                return true;

        return false;
    }

}