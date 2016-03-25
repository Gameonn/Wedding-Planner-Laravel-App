<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'WelcomeController@index');

Route::get('home', 'HomeController@index');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);

//************************************************************************************************************************
//                                                      Api Routes
//************************************************************************************************************************

// Vendor Routes

Route::post('api/vendor-sign-up', array('uses' => 'ApiController@vendorSignUp', 'as' => 'api.vendorSignUp'));

Route::post('api/vendor-login', array('uses' => 'ApiController@vendorLogin', 'as' => 'api.vendorLogin'));

Route::post('api/vendor-login-fb-check', array('uses' => 'ApiController@vendorLoginFbCheck', 'as' => 'api.vendorLoginFbCheck'));

Route::post('api/vendor-login-fb', array('uses' => 'ApiController@vendorLoginFb', 'as' => 'api.vendorLoginFb'));

Route::post('api/vendor-logout', array('uses' => 'ApiController@vendorLogout', 'as' => 'api.vendorLogout'));

Route::post('api/vendor-edit-profile', array('uses' => 'ApiController@vendorEditProfile', 'as' => 'api.vendorEditProfile'));

Route::post('api/vendor-change-password', array('uses' => 'ApiController@vendorChangePassword', 'as' => 'api.vendorChangePassword'));

Route::post('api/vendor-deactivate-profile', array('uses' => 'ApiController@vendorDeactivateProfile', 'as' => 'api.vendorDeactivateProfile'));

Route::post('api/vendor-view-my-profile', array('uses' => 'ApiController@vendorViewMyProfile', 'as' => 'api.vendorViewMyProfile'));

Route::post('api/vendor-view-profile-by-id', array('uses' => 'ApiController@vendorViewProfileById', 'as' => 'api.vendorViewProfileById'));

Route::post('api/vendor-set-device-token', array('uses' => 'ApiController@vendorSetDeviceToken', 'as' => 'api.vendorSetDeviceToken'));

Route::post('api/vendor-set-reg-id', array('uses' => 'ApiController@vendorSetRegId', 'as' => 'api.vendorSetRegId'));

// Forgot Password Routes

Route::post('api/forgot-password', array('uses' => 'ApiController@forgotPassword', 'as' => 'api.forgotPassword'));

Route::get('api/password-reset/{token}', array('uses' => 'ApiController@forgotPassword2', 'as' => 'api.forgotPassword2'));

Route::post('api/password-reset-2', array('uses' => 'ApiController@forgotPassword3', 'as' => 'api.forgotPassword3'));

// Portfolio Routes

Route::post('api/portfolio-create', array('uses' => 'ApiController@portfolioCreate', 'as' => 'api.portfolioCreate'));

Route::post('api/portfolio-delete', array('uses' => 'ApiController@portfolioDelete', 'as' => 'api.portfolioDelete'));

// Business Type Listing Routes

Route::post('api/business-type-listing', array('uses' => 'ApiController@businessTypeListing', 'as' => 'api.businessTypeListing'));

Route::post('api/search-business-type', array('uses' => 'ApiController@searchBusinessType', 'as' => 'api.searchBusinessType'));

// Vendor Listing Routes

Route::post('api/vendor-listing-by-type', array('uses' => 'ApiController@vendorListingByType', 'as' => 'api.vendorListingByType'));

// Dashboard Routes

Route::post('api/phone-call-hit', array('uses' => 'ApiController@phoneCallHit', 'as' => 'api.phoneCallHit'));

Route::post('api/chat-hit', array('uses' => 'ApiController@chatHit', 'as' => 'api.chatHit'));

Route::post('api/view-dashboard', array('uses' => 'ApiController@viewDashboard', 'as' => 'api.viewDashboard'));

// View Clients Leads Listing Routes

Route::post('api/view-clients-leads-listing', array('uses' => 'ApiController@viewClientsLeadsListing', 'as' => 'api.viewClientsLeadsListing'));

// Request Feedback

Route::post('api/request-feedback', array('uses' => 'ApiController@requestFeedback', 'as' => 'api.requestFeedback'));

// User Routes

Route::post('api/user-sign-up', array('uses' => 'ApiController@userSignUp', 'as' => 'api.userSignUp'));

Route::post('api/user-login', array('uses' => 'ApiController@userLogin', 'as' => 'api.userLogin'));

Route::post('api/user-login-fb-check', array('uses' => 'ApiController@userLoginFbCheck', 'as' => 'api.userLoginFbCheck'));

Route::post('api/user-login-fb', array('uses' => 'ApiController@userLoginFb', 'as' => 'api.userLoginFb'));

Route::post('api/user-logout', array('uses' => 'ApiController@userLogout', 'as' => 'api.userLogout'));

Route::post('api/user-edit-profile', array('uses' => 'ApiController@userEditProfile', 'as' => 'api.userEditProfile'));

Route::post('api/user-change-password', array('uses' => 'ApiController@userChangePassword', 'as' => 'api.userChangePassword'));

Route::post('api/user-deactivate-profile', array('uses' => 'ApiController@userDeactivateProfile', 'as' => 'api.userDeactivateProfile'));

Route::post('api/user-view-my-profile', array('uses' => 'ApiController@userViewMyProfile', 'as' => 'api.userViewMyProfile'));

Route::post('api/user-view-profile-by-id', array('uses' => 'ApiController@userViewProfileById', 'as' => 'api.userViewProfileById'));

Route::post('api/user-static-listing', array('uses' => 'ApiController@userStaticListing', 'as' => 'api.userStaticListing'));

Route::post('api/user-static-listing-2', array('uses' => 'ApiController@userStaticListing2', 'as' => 'api.userStaticListing2'));

Route::post('api/user-set-device-token', array('uses' => 'ApiController@userSetDeviceToken', 'as' => 'api.userSetDeviceToken'));

Route::post('api/user-set-reg-id', array('uses' => 'ApiController@userSetRegId', 'as' => 'api.userSetRegId'));

Route::post('api/contracted-vendors', array('uses' => 'ApiController@contractedVendors', 'as' => 'api.contractedVendors'));

// Home Route

Route::post('api/user-home', array('uses' => 'ApiController@userHome', 'as' => 'api.userHome'));

// Review Routes

Route::post('api/write-review', array('uses' => 'ApiController@writeReview', 'as' => 'api.writeReview'));

Route::post('api/delete-review', array('uses' => 'ApiController@deleteReview', 'as' => 'api.deleteReview'));

Route::post('api/review-listing', array('uses' => 'ApiController@reviewListing', 'as' => 'api.reviewListing'));

Route::post('api/review-listing-by-vendor-id', array('uses' => 'ApiController@reviewListingByVendorId', 'as' => 'api.reviewListingByVendorId'));

// Vendor Listing

Route::post('api/vendor-listing', array('uses' => 'ApiController@vendorListing', 'as' => 'api.vendorListing'));

Route::post('api/view-vendor-profile-by-id', array('uses' => 'ApiController@viewVendorProfileById', 'as' => 'api.viewVendorProfileById'));

// Wedding Listing

Route::post('api/wedding-listing', array('uses' => 'ApiController@weddingListing', 'as' => 'api.weddingListing'));

Route::post('api/view-wedding-profile-by-id', array('uses' => 'ApiController@viewWeddingProfileById', 'as' => 'api.viewWeddingProfileById'));

// Wedding Photos Routes ----- Pending

Route::post('api/wedding-photos-add', array('uses' => 'ApiController@weddingPhotosAdd', 'as' => 'api.weddingPhotosAdd'));

Route::post('api/wedding-photos-delete', array('uses' => 'ApiController@weddingPhotosDelete', 'as' => 'api.weddingPhotosDelete'));

// Favorite Routes

Route::post('api/make-favorite-wedding', array('uses' => 'ApiController@makeFavoriteWedding', 'as' => 'api.makeFavoriteWedding'));

Route::post('api/remove-favorite-wedding', array('uses' => 'ApiController@removeFavoriteWedding', 'as' => 'api.removeFavoriteWedding'));

Route::post('api/view-favorite-wedding-listing', array('uses' => 'ApiController@viewFavoriteWeddingListing', 'as' => 'api.viewFavoriteWeddingListing'));

Route::post('api/make-favorite-vendor', array('uses' => 'ApiController@makeFavoriteVendor', 'as' => 'api.makeFavoriteVendor'));

Route::post('api/remove-favorite-vendor', array('uses' => 'ApiController@removeFavoriteVendor', 'as' => 'api.removeFavoriteVendor'));

Route::post('api/view-favorite-vendor-listing', array('uses' => 'ApiController@viewFavoriteVendorListing', 'as' => 'api.viewFavoriteVendorListing'));

// Collaborators Routes

Route::post('api/create-collaborator-group', array('uses' => 'ApiController@createCollaboratorGroup', 'as' => 'api.createCollaboratorGroup'));

Route::post('api/create-collaborator-group-by-invite', array('uses' => 'ApiController@createCollaboratorGroupByInvite', 'as' => 'api.createCollaboratorGroupByInvite'));

Route::post('api/edit-collaborators', array('uses' => 'ApiController@editCollaborators', 'as' => 'api.editCollaborators'));

Route::post('api/add-members', array('uses' => 'ApiController@addMembers', 'as' => 'api.addMembers'));

Route::post('api/remove-members', array('uses' => 'ApiController@removeMembers', 'as' => 'api.removeMembers'));

Route::post('api/view-members', array('uses' => 'ApiController@viewMembers', 'as' => 'api.viewMembers'));

Route::post('api/remove-collaborator-group', array('uses' => 'ApiController@removeCollaboratorGroup', 'as' => 'api.removeCollaboratorGroup'));

Route::post('api/view-collaborators-listing', array('uses' => 'ApiController@viewCollaboratorsListing', 'as' => 'api.viewCollaboratorsListing'));

// Contract

Route::post('api/create-contract', array('uses' => 'ApiController@createContract', 'as' => 'api.createContract'));

Route::post('api/change-contract-status', array('uses' => 'ApiController@changeContractStatus', 'as' => 'api.changeContractStatus'));

Route::post('api/delete-contract', array('uses' => 'ApiController@deleteContract', 'as' => 'api.deleteContract'));

Route::get('api/cron-event-ended', array('uses' => 'ApiController@cronEventEnded', 'as' => 'api.cronEventEnded'));

// Chat

Route::post('api/send-message', array('uses' => 'ApiController@sendmessage', 'as' => 'api.sendmessage'));

Route::post('api/view-messages', array('uses' => 'ApiController@viewmessages', 'as' => 'api.viewmessages'));

Route::post('api/view-previous-messages', array('uses' => 'ApiController@viewPreviousMessages', 'as' => 'api.viewPreviousMessages'));

Route::post('api/view-current-messages', array('uses' => 'ApiController@viewcurrentmessages', 'as' => 'api.viewcurrentmessages'));

Route::post('api/view-message-listing', array('uses' => 'ApiController@viewmessagelisting', 'as' => 'api.viewmessagelisting'));

//***********************************
// 		Notifications Route 
//***********************************

// Get Notifications
Route::post('api/get-notifications', array('uses' => 'ApiController@getNotifications', 'as' => 'api.getNotifications'));

// Mark Read Notification
Route::post('api/mark-read-notifications', array('uses' => 'ApiController@markReadNotifications', 'as' => 'api.markReadNotifications'));

// Remove Notification
Route::post('api/remove-notification', array('uses' => 'ApiController@removeNotification', 'as' => 'api.removeNotification'));

// Clear Notifications
Route::post('api/clear-notifications', array('uses' => 'ApiController@clearNotifications', 'as' => 'api.clearNotifications'));

// Mark Read Badge Notification
Route::post('api/mark-read-badge-notifications', array('uses' => 'ApiController@markReadBadgeNotifications', 'as' => 'api.markReadBadgeNotifications'));

// Get Badge Notification Count
Route::post('api/get-badge-notification-count', array('uses' => 'ApiController@getBadgeNotificationCount', 'as' => 'api.getBadgeNotificationCount'));

//***********************************
// 		Search Route 
//***********************************

// Seach
Route::post('api/search', array('uses' => 'ApiController@search', 'as' => 'api.search'));

// Top Sub Categories
Route::post('api/top-sub-categories', array('uses' => 'ApiController@topSubCategories', 'as' => 'api.topSubCategories'));


//************************************************************************************************************************
//************************************************************************************************************************
//                                                      Admin Panel
//************************************************************************************************************************
//************************************************************************************************************************

// Login Routes

Route::get('admin/login', array('uses' => 'AdminAuthController@login', 'as' => 'admin.login'));

Route::post('admin/login', array('uses' => 'AdminAuthController@loginCode', 'as' => 'admin.loginCode'));

Route::get('admin/logout', array('uses' => 'AdminAuthController@logout', 'as' => 'admin.logout'));

// Dashboard Routes
Route::get('admin/dashboard', array('uses' => 'AdminController@dashboard', 'as' => 'admin.dashboard'));

//***********************************
// 		Vendor Route 
//***********************************

// Vendor Listing 
Route::get('admin/vendor-listing/{keyword}/{page}', array('uses' => 'AdminController@vendorListing', 'as' => 'admin.vendorListing')); 

// Vendor Approve
Route::post('admin/vendor-approve', array('uses' => 'AdminController@vendorApprove', 'as' => 'admin.vendorApprove')); 

// Vendor Disapprove
Route::post('admin/vendor-disapprove', array('uses' => 'AdminController@vendorDisapprove', 'as' => 'admin.vendorDisapprove')); 

// Vendor Details
Route::get('admin/vendor-details/{user_id}', array('uses' => 'AdminController@vendorDetails', 'as' => 'admin.vendorDetails'));

// 
Route::get('admin/vendor-details-edit/{user_id}', array('uses' => 'AdminController@vendorDetailsEdit', 'as' => 'admin.vendorDetailsEdit'));

Route::post('admin/vendor-details-edit', array('uses' => 'AdminController@vendorDetailsEditCode', 'as' => 'admin.vendorDetailsEditCode'));

Route::get('admin/vendor-extra-details-edit/{user_id}', array('uses' => 'AdminController@vendorExtraDetailsEdit', 'as' => 'admin.vendorExtraDetailsEdit'));

Route::post('admin/vendor-extra-details-edit', array('uses' => 'AdminController@vendorExtraDetailsEditCode', 'as' => 'admin.vendorExtraDetailsEditCode'));

Route::post('admin/delete-extra-details', array('uses' => 'AdminController@deleteExtraDetails', 'as' => 'admin.deleteExtraDetails'));

// Remove Vendor
Route::post('admin/remove-vendor', array('uses' => 'AdminController@removeVendor', 'as' => 'admin.removeVendor'));

// Add Vendor
Route::get('admin/add-vendor', array('uses' => 'AdminController@addVendor', 'as' => 'admin.addVendor'));

Route::post('admin/add-vendor', array('uses' => 'AdminController@addVendorCode', 'as' => 'admin.addVendorCode'));

//***********************************
// 			User Route 
//***********************************

// User Listing
Route::get('admin/user-listing/{keyword}/{page}', array('uses' => 'AdminController@userListing', 'as' => 'admin.userListing')); 

// User Details
Route::get('admin/user-details/{user_id}', array('uses' => 'AdminController@userDetails', 'as' => 'admin.userDetails')); 

//***********************************
// 			Collaborator Route 
//***********************************

// Collaborator Listing
Route::get('admin/collaborator-listing/{keyword}/{page}', array('uses' => 'AdminController@collaboratorListing', 'as' => 'admin.collaboratorListing')); 

// Collaborator Details
Route::get('admin/collaborator-details/{user_id}', array('uses' => 'AdminController@collaboratorDetails', 'as' => 'admin.collaboratorDetails')); 

// Collaborator Group Listing
Route::get('admin/collaborator-group-listing/{keyword}/{page}', array('uses' => 'AdminController@collaboratorGroupListing', 'as' => 'admin.collaboratorGroupListing')); 

// Collaborator Group Members
Route::get('admin/collaborator-group-members/{collaborator_id}', array('uses' => 'AdminController@collaboratorGroupMembers', 'as' => 'admin.collaboratorGroupMembers')); 

//***********************************
// 		Upload Images Route 
//***********************************

Route::get('admin/upload-images/{user_id}', array('uses' => 'AdminController@uploadImages', 'as' => 'admin.uploadImages')); 

Route::post('admin/upload-images', array('uses' => 'AdminController@uploadImagesCode', 'as' => 'admin.uploadImagesCode')); 

Route::post('admin/del-vendor-portfolio-img', array('uses' => 'AdminController@delVendorPortfolioImg', 'as' => 'admin.delVendorPortfolioImg')); 

//***********************************
// 		   Vendor Businesses
//***********************************

Route::get('admin/vendor-businesses', array('uses' => 'AdminController@vendorBusinesses', 'as' => 'admin.vendorBusinesses'));

Route::post('admin/add-business', array('uses' => 'AdminController@addBusiness', 'as' => 'admin.addBusiness'));

Route::post('admin/edit-business', array('uses' => 'AdminController@editBusiness', 'as' => 'admin.editBusiness'));

Route::post('admin/delete-business', array('uses' => 'AdminController@deleteBusiness', 'as' => 'admin.deleteBusiness'));

// Vendor Sub Business

Route::post('admin/add-sub-business', array('uses' => 'AdminController@addSubBusiness', 'as' => 'admin.addSubBusiness'));

Route::post('admin/delete-sub-business', array('uses' => 'AdminController@deleteSubBusiness', 'as' => 'admin.deleteSubBusiness'));

//******************************
//        Wedding Routes
//******************************

// New Wedding
Route::get('admin/new-wedding', array('uses' => 'AdminController@newWedding', 'as' => 'admin.newWedding')); 

Route::post('admin/new-wedding', array('uses' => 'AdminController@newWeddingCode', 'as' => 'admin.newWeddingCode')); 

// Wedding Listing
Route::get('admin/wedding-listing/{keyword}/{page}', array('uses' => 'AdminController@weddingListing', 'as' => 'admin.weddingListing')); 

// Wedding Details
Route::get('admin/wedding-details/{wedding_id}', array('uses' => 'AdminController@weddingDetails', 'as' => 'admin.weddingDetails')); 

// Wedding Edit
Route::get('admin/wedding-edit/{wedding_id}', array('uses' => 'AdminController@weddingEdit', 'as' => 'admin.weddingEdit')); 

Route::post('admin/wedding-edit', array('uses' => 'AdminController@weddingEditCode', 'as' => 'admin.weddingEditCode')); 

// Add Wedding Photos
Route::get('admin/add-wedding-photos/{wedding_id}', array('uses' => 'AdminController@addWeddingPhotos', 'as' => 'admin.addWeddingPhotos')); 

Route::post('admin/upload-wedding-images', array('uses' => 'AdminController@uploadWeddingImages', 'as' => 'admin.uploadWeddingImages')); 

// Remove Wedding Photos
Route::post('admin/remove-wedding-image', array('uses' => 'AdminController@removeWeddingImage', 'as' => 'admin.removeWeddingImage')); 

// Wedding Photos Tags
Route::get('admin/view-wedding-image-by-id/{image_id}', array('uses' => 'AdminController@viewWeddingImageById', 'as' => 'admin.viewWeddingImageById'));

Route::post('admin/tag-vendors', array('uses' => 'AdminController@tagVendors', 'as' => 'admin.tagVendors'));

// Wedding Types
Route::get('admin/wedding-types', array('uses' => 'AdminController@weddingTypes', 'as' => 'admin.weddingTypes')); 

Route::post('admin/add-wedding-type', array('uses' => 'AdminController@addWeddingType', 'as' => 'admin.addWeddingType')); 

Route::post('admin/edit-wedding-type', array('uses' => 'AdminController@editWeddingType', 'as' => 'admin.editWeddingType')); 

Route::post('admin/delete-wedding-type', array('uses' => 'AdminController@deleteWeddingType', 'as' => 'admin.deleteWeddingType')); 

//***********************************
//		  City Routes
//***********************************

// City Listing
Route::get('admin/city-listing', array('uses' => 'AdminController@cityListing', 'as' => 'admin.cityListing')); 

// Add City
Route::post('admin/add-city', array('uses' => 'AdminController@addCity', 'as' => 'admin.addCity')); 

// Edit City
Route::post('admin/edit-city', array('uses' => 'AdminController@editCity', 'as' => 'admin.editCity')); 

// Remove City
Route::post('admin/remove-city', array('uses' => 'AdminController@removeCity', 'as' => 'admin.removeCity')); 

//***********************************
//		  Sponsor Routes
//***********************************

// Get Sponsor
Route::get('admin/get-sponsor', array('uses' => 'AdminController@getSponsor', 'as' => 'admin.getSponsor')); 

// Add Sponsor
Route::post('admin/add-sponsor', array('uses' => 'AdminController@addSponsor', 'as' => 'admin.addSponsor')); 

// Delete Sponsor
Route::post('admin/delete-sponsor', array('uses' => 'AdminController@deleteSponsor', 'as' => 'admin.deleteSponsor')); 

//***********************************
//		  Conceirge Routes
//***********************************

// User Side

Route::post('api/send-conceirge-user-message', array('uses' => 'ApiController@sendConceirgeUserMessage', 'as' => 'api.sendConceirgeUserMessage'));

Route::post('api/view-conceirge-user-messages', array('uses' => 'ApiController@viewConceirgeUserMessages', 'as' => 'api.viewConceirgeUserMessages'));

Route::post('api/view-previous-conceirge-user-messages', array('uses' => 'ApiController@viewPreviousConceirgeUserMessages', 'as' => 'api.viewPreviousConceirgeUserMessages'));

Route::post('api/view-current-conceirge-user-messages', array('uses' => 'ApiController@viewCurrentConceirgeUserMessages', 'as' => 'api.viewCurrentConceirgeUserMessages'));

Route::post('api/view-conceirge-user-message-listing', array('uses' => 'ApiController@viewConceirgeUserMessageListing', 'as' => 'api.viewConceirgeUserMessageListing')); 

// Vendor Side

Route::post('admin/send-conceirge-admin-message', array('uses' => 'AdminController@sendConceirgeAdminMessage', 'as' => 'api.sendConceirgeAdminMessage'));

Route::post('admin/view-current-conceirge-admin-messages', array('uses' => 'AdminController@viewCurrentConceirgeAdminMessages', 'as' => 'api.viewCurrentConceirgeAdminMessages'));

Route::post('admin/view-previous-conceirge-admin-messages', array('uses' => 'AdminController@viewPreviousConceirgeAdminMessages', 'as' => 'api.viewPreviousConceirgeAdminMessages'));

Route::post('admin/view-conceirge-admin-message-listing', array('uses' => 'AdminController@viewConceirgeAdminMessageListing', 'as' => 'api.viewConceirgeAdminMessageListing'));

Route::get('admin/conceirge', array('uses' => 'AdminController@conceirge', 'as' => 'admin.conceirge'));

Route::get('admin/chat-operator-conversations/{chat_operator_id}', array('uses' => 'AdminController@chatOperatorConversations', 'as' => 'admin.chatOperatorConversations'));

Route::get('admin/conceirge/{user_id}/{user_id_2}', array('uses' => 'AdminController@conceirge2', 'as' => 'admin.conceirge2'));

Route::post('admin/upload-chat-image', array('uses' => 'AdminController@uploadChatImage', 'as' => 'api.uploadChatImage'));

Route::post('admin/chat-wedding', array('uses' => 'AdminController@chatWedding', 'as' => 'api.chatWedding'));

Route::post('admin/chat-vendor', array('uses' => 'AdminController@chatVendor', 'as' => 'api.chatVendor'));

//*********************************************
// 		      Admin Account Routes
//*********************************************

// Admin Listing
Route::get('admin/admin-listing', array('uses' => 'AdminController@adminListing', 'as' => 'admin.adminListing'));

Route::post('admin/add-operator', array('uses' => 'AdminController@addOperator', 'as' => 'api.addOperator'));

Route::post('admin/edit-operator', array('uses' => 'AdminController@editOperator', 'as' => 'api.editOperator'));

// Unauthorize access Routes
Route::get('admin/unauthorize-access', array('uses' => 'AdminController@unauthorizeAccess', 'as' => 'admin.unauthorizeAccess'));

// Admin Test Routes
Route::get('admin-test', array('uses' => 'AdminController@adminTest', 'as' => 'admin.adminTest'));

// Video Conferencing
Route::get('opentok', array('uses' => 'AdminController@opentok', 'as' => 'admin.opentok'));

//*********************************************
// 		      Auto Authorization Routes
//*********************************************

// Auto Authorization 
Route::get('admin/auto-authorization', array('uses' => 'AdminController@autoAuthorization', 'as' => 'admin.autoAuthorization'));

// Change Authorization Status
Route::post('admin/change-authorization-status', array('uses' => 'AdminController@changeAuthorizationStatus', 'as' => 'admin.changeAuthorizationStatus'));

//************************************************************************************************************************
//                                                      Miscellaneous
//************************************************************************************************************************

// Image Routes

Route::get('photos/thumb/{id}/{ext}', function($id, $ext)
{
    $img = Image::make('https://s3.amazonaws.com/whatashaadi/uploads/'.$id.'.'.$ext);
    return $img->response($img);
});

Route::get('photos/thumb/{id}/{ext}/{width}', function($id, $ext, $width)
{
    $img = Image::make('https://s3.amazonaws.com/whatashaadi/uploads/'.$id.'.'.$ext);
    $img->resize($width, null, function ($constraint) {
        $constraint->aspectRatio();
    });
    return $img->response($img);
});

Route::get('photos/thumb/{id}/{ext}/{width}/{height}', function($id, $ext, $width, $height)
{
    $img = Image::make('https://s3.amazonaws.com/whatashaadi/uploads/'.$id.'.'.$ext);
    $img->resize($width, $height);
    return $img->response($img);
});

// Circle
Route::get('photos/circle/{id}/{ext}', function($id, $ext)
{

	$image = Image::make('https://s3.amazonaws.com/whatashaadi/uploads/'.$id.'.'.$ext);
	$image->resize(100, 100);

	$width = $image->getWidth();
    $height = $image->getHeight();
    $mask = \Image::canvas($width, $height);

    // draw a white circle
    $mask->circle($width, $width/2, $height/2, function ($draw) {
        $draw->background('#fff');
    });

    $image->mask($mask, false);

    return $image->response($image);

	// $img = Image::make('https://s3.amazonaws.com/whatashaadi/uploads/'.$id.'.'.$ext);
	// $img->circle(100, 50, 50, function ($img) {
	//     $img->background('#0000ff');
	// });

 //    return $img->response($img);
});

// Cover Image Routes

Route::get('cover/thumb/{id}/{ext}', function($id, $ext)
{
    $img = Image::make('https://s3.amazonaws.com/whatashaadi/uploads/'.$id.'.'.$ext);    

	// apply blur
	$img->blur(25);

	return $img->response($img);

});

Route::get('cover/thumb/{id}/{ext}/{width}', function($id, $ext, $width)
{
    $img = Image::make('https://s3.amazonaws.com/whatashaadi/uploads/'.$id.'.'.$ext);
    $img->resize($width, null, function ($constraint) {
        $constraint->aspectRatio();
    });

    // apply blur
	$img->blur(25);

    return $img->response($img);
});

Route::get('cover/thumb/{id}/{ext}/{width}/{height}', function($id, $ext, $width, $height)
{
    $img = Image::make('https://s3.amazonaws.com/whatashaadi/uploads/'.$id.'.'.$ext);
    $img->resize($width, $height);

    // apply blur
	$img->blur(80);

    return $img->response($img);
});

//************************************************************************************************************************
//                                                      Test Routes
//************************************************************************************************************************

// Test Badge Scores
Route::get('test-badge-score', function() {

	$user_id = 7;

	$val1 = DB::select("SELECT count(`id`) AS `count` FROM `chat` WHERE `sent_to` = '$user_id' AND `is_read` = '0'");	

	$val2 = DB::select(
		"SELECT `collaborator_id`,
		(SELECT count(`id`) AS `count` 
			FROM `chat` 
			WHERE `group_id` = `collaborator_members`.`collaborator_id` AND 
			`id` > (SELECT `chat_id` FROM `chat_group_read` WHERE `user_id` = '$user_id' AND `group_id` = `collaborator_members`.`collaborator_id`)) AS `count`
		FROM `collaborator_members` 
		WHERE `user_id_2` = '$user_id'
	");
	$tot_count = 0;
	foreach ($val2 as $key => $value) {
		$tot_count = $tot_count + $value->count;
	}	

	$val2 = DB::select("SELECT count(`id`) AS `count` FROM `conceirge` WHERE `sent_to` = '$user_id' AND `is_read` = '0'");	

	var_dump($val1[0]->count); // 1 to 1 unread msgs
	var_dump($tot_count);	// Group Unread msgs	
	var_dump($val2[0]->count);	// Conceirge unread msgs

});

// Test push notifications
Route::get('testing-push', function(){

	// message to be send in push
	$message = PushNotification::Message('This is User',array(
		'badge' => 1,
		'sound' => 'example.aiff',

		'locArgs' => array(
			'u1' => 3,
			'u2' => 2,
			't' => 6,
		),
	));
	
	// send push
	$send = PushNotification::app('appNameAndroid')
		->to('APA91bFu_IZxBhfdrnNZUcsAAh-MVFhWSTFur7rCXk5eE07j3Ujn1attmnjYEkZJc3bnpghZ7UP5spDUqj0SW9mGk51FyqR6otYAP0VwtyBleAunu6rRucjrztZe5YgDt5kctceJTrhF')
		->send($message);

	dd($send);
	if($send)
		echo('Sent!');
	else
		echo('Not sent!');
});

// Test push notifications 2
Route::get('testing-push-2', function(){

	// message to be send in push
	$message = PushNotification::Message('This is Vendor',array(
		'badge' => 1,
		'sound' => 'example.aiff',

		'locArgs' => array(
			'u1' => 1,
			'u2' => 1,
			't' => 12,
		),
	));
	
	// send push
	$send = PushNotification::app('appNameAndroid2')
		->to('APA91bHrevbjm9YCZErtZjnJe-GRue_FN_wstLG6wioiDPBHJ5Kk_YuL9Tr-VkZtfmb3wPnjllNFiSleuZzoTfxAwtS2O0P8f-kd_5Ow8O6xzZPTe0Xj8d_bc3xmLe3F54WVKdf6HSt_4ShZ1nZPjDJfTm68b18WkA')
		->send($message);

	dd($send);
	if($send)
		echo('Sent!');
	else
		echo('Not sent!');
});

// Test push notifications 3
Route::get('testing-push-3', function(){

	// message to be send in push
	$message = PushNotification::Message('This is Vendor',array(
		'badge' => "1",
		'sound' => 'example.aiff',

		'locArgs' => array(
			'u1' => "1",
			'u2' => "1",
			't' => "12",
		),
	));
	
	// send push
	$send = PushNotification::app('appNameIOS2') 
		->to('de9f78b399930de4c2b19ab44b1a6abf4345e20c3115764fec185700f5afd7e7')
		->send($message);

	dd($send);
	if($send)
		echo('Sent!');
	else
		echo('Not sent!');
});

// Test push notifications 4
Route::get('testing-push-4', function(){

	// message to be send in push
	$message = PushNotification::Message('This is User',array(
		'badge' => "1",
		'sound' => 'example.aiff',

		'locArgs' => array(
			'u1' => "1",
			'u2' => "1",
			't' => "12",
		),
	));
	
	// send push
	$send = PushNotification::app('appNameIOS') 
		->to('991a710d68b35135f8ee9be2ff44a3dec577c2ae95a4b2c000577406dca25bf8')
		->send($message);

	dd($send);
	if($send)
		echo('Sent!');
	else
		echo('Not sent!');
});

// Test Mail Chimp
Route::get('test-mailchimp', array('uses' => 'AdminController@testMailChimp', 'as' => 'admin.testMailChimp')); 

// Test Scrolling
Route::get('test-scrolling', array('uses' => 'AdminController@testScrolling', 'as' => 'admin.testScrolling')); 