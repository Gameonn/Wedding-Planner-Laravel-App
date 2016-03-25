<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

use App\Users;
use App\Business;
use App\DashboardVendor;
use App\VendorPortfolioImages;
use App\VendorReviews;
use App\Wedding;
use App\Favorite;
use App\Collaborators;
use App\Contract;
use App\Chat;
use App\Notifications; 
use App\Conceirge;

class ApiController extends Controller {

	// Vendor Functions

    public function vendorSignUp() {
        $input = Input::all();
        return Users::vendorSignUp($input);
    }

    public function vendorLogin() {
        $input = Input::all();
        return Users::vendorLogin($input);
    }

    public function vendorLoginFbCheck() {
        $input = Input::all();
        return Users::vendorLoginFbCheck($input);
    }    

    public function vendorLoginFb() {
        $input = Input::all();
        return Users::vendorLoginFb($input);
    }    

    public function vendorLogout() {
        $input = Input::all();
        return Users::vendorLogout($input);
    }

    public function vendorEditProfile() {
        $input = Input::all();
        return Users::vendorEditProfile($input);
    }

    public function vendorChangePassword() {
        $input = Input::all();
        return Users::vendorChangePassword($input);
    }

    public function vendorDeactivateProfile() {
        $input = Input::all();
        return Users::vendorDeactivateProfile($input);
    }

    public function vendorViewMyProfile() {
        $input = Input::all();
        return Users::vendorViewMyProfile($input);
    }

    public function vendorViewProfileById() { 
        $input = Input::all();
        return Users::vendorViewProfileById($input);
    }

    public function vendorSetDeviceToken() { 
        $input = Input::all();
        return Users::vendorSetDeviceToken($input);
    }

    public function vendorSetRegId() { 
        $input = Input::all();
        return Users::vendorSetRegId($input);
    }

    // Forgot Password Funtionality

    public function forgotPassword() {
        $input = Input::all();
        return Users::forgotPassword($input);
    }

    public function forgotPassword2($token) {

        //return view('api-forgot-password.reset-password');    

        if($token) {
            $user_id = Users::forgotPassword2($token);
            if($user_id!=0) {
                $confirmtoken = 1;
                return view('api-forgot-password.reset-password')
                    ->with('user_id', $user_id)
                    ->with('token', $token)
                    ->with('confirm_token', $confirmtoken);
            }
            else {
                $confirmtoken = 0;
                $user_id = "";
                return view('api-forgot-password.reset-password')
                    ->with('user_id', $user_id)
                    ->with('confirm_token', $confirmtoken);
            }
        }

    }

    public function forgotPassword3() {

        $input = Input::all();
        $message = Users::forgotPassword3($input);
                
        if($message=='success') {
            return view('api-forgot-password.password-success')
                ->with('message', $message);
        }
        else {            
            return redirect()->back()->with('message', $message);
        }
        
    }

    // Portfolio Functions

    public function portfolioCreate() { 
        $input = Input::all();
        return VendorPortfolioImages::portfolioCreate($input); 
    }

    public function portfolioDelete() { 
        $input = Input::all();
        return VendorPortfolioImages::portfolioDelete($input); 
    }

    // Business Type Functions

    public function businessTypeListing() { 
        $input = Input::all();
        return Business::businessTypeListing($input); 
    }

    public function searchBusinessType() { 
        $input = Input::all();
        return Business::searchBusinessType($input);
    }

    // Vendor Listing Functions

    public function vendorListingByType() { 
        $input = Input::all();
        return Users::vendorListingByType($input);
    }

    // Dashboard Functions

    public function phoneCallHit() { 
        $input = Input::all();   
        return DashboardVendor::phoneCallHit($input);
    }

    public function chatHit() { 
        $input = Input::all();
        return DashboardVendor::chatHit($input);
    }

    public function viewDashboard() { 
        $input = Input::all();
        return DashboardVendor::viewDashboard($input);
    }

    // View Clients Leads Listing Functions

    public function viewClientsLeadsListing() { 
        $input = Input::all();
        return DashboardVendor::viewClientsLeadsListing($input);
    }

    // Request Feedback

    public function requestFeedback() { 
        $input = Input::all();
        return VendorReviews::requestFeedback($input);
    }

    // User Functions

    public function userSignUp() {
        $input = Input::all();
        return Users::userSignUp($input);
    }

    public function userLogin() {
        $input = Input::all();
        return Users::userLogin($input);
    }

    public function userLoginFbCheck() {
        $input = Input::all();
        return Users::userLoginFbCheck($input);
    }

    public function userLoginFb() {
        $input = Input::all();
        return Users::userLoginFb($input);
    }

    public function userLogout() {
        $input = Input::all();
        return Users::userLogout($input);
    }

    public function userEditProfile() {
        $input = Input::all();
        return Users::userEditProfile($input);
    }

    public function userChangePassword() {
        $input = Input::all();
        return Users::userChangePassword($input);
    }

    public function userDeactivateProfile() {
        $input = Input::all();
        return Users::userDeactivateProfile($input);
    }

    public function userViewMyProfile() {
        $input = Input::all();
        return Users::userViewMyProfile($input);
    }

    public function userViewProfileById() { 
        $input = Input::all();
        return Users::userViewProfileById($input);
    }

    public function userStaticListing() { 
        $input = Input::all();
        return Users::userStaticListing($input);
    }

    public function userStaticListing2() { 
        $input = Input::all();
        return Users::userStaticListing2($input);
    }

    public function userSetDeviceToken() { 
        $input = Input::all();
        return Users::userSetDeviceToken($input);
    }

    public function userSetRegId() { 
        $input = Input::all();
        return Users::userSetRegId($input);
    }

    public function contractedVendors() { 
        $input = Input::all();
        return Users::contractedVendors($input);
    }

    // Home Functions

    public function userHome() { 
        $input = Input::all();
        return Users::userHome($input);
    }

    // Review Functions

    public function writeReview() { 
        $input = Input::all();
        return VendorReviews::writeReview($input);
    }

    public function deleteReview() { 
        $input = Input::all();
        return VendorReviews::deleteReview($input);
    }

    public function reviewListing() { 
        $input = Input::all();
        return VendorReviews::reviewListing($input);
    }

    public function reviewListingByVendorId() { 
        $input = Input::all();
        return VendorReviews::reviewListingByVendorId($input);
    }

    // Wedding Functions

    public function vendorListing() { 
        $input = Input::all();
        return Users::vendorListing($input);
    }

    public function viewVendorProfileById() { 
        $input = Input::all();
        return Users::viewVendorProfileById($input);
    }

    // Wedding Functions

    public function weddingListing() { 
        $input = Input::all();
        return Wedding::weddingListing($input);
    }

    public function viewWeddingProfileById() { 
        $input = Input::all();
        return Wedding::viewWeddingProfileById($input);
    }

    // Favorite Functions

    public function makeFavoriteWedding() { 
        $input = Input::all();
        return Favorite::makeFavoriteWedding($input);
    }

    public function removeFavoriteWedding() { 
        $input = Input::all();
        return Favorite::removeFavoriteWedding($input);
    }

    public function viewFavoriteWeddingListing() { 
        $input = Input::all();
        return Favorite::viewFavoriteWeddingListing($input);
    }

    public function makeFavoriteVendor() { 
        $input = Input::all();
        return Favorite::makeFavoriteVendor($input);
    }

    public function removeFavoriteVendor() { 
        $input = Input::all();
        return Favorite::removeFavoriteVendor($input);
    }

    public function viewFavoriteVendorListing() { 
        $input = Input::all();
        return Favorite::viewFavoriteVendorListing($input);
    }

    // Collaborators Functions

    public function createCollaboratorGroup() { 
        $input = Input::all();
        return Collaborators::createCollaboratorGroup($input);
    }

    public function createCollaboratorGroupByInvite() { 
        $input = Input::all();
        return Collaborators::createCollaboratorGroupByInvite($input);
    }

    public function editCollaborators() { 
        $input = Input::all();
        return Collaborators::editCollaborators($input);
    }

    public function addMembers() { 
        $input = Input::all();
        return Collaborators::addMembers($input);
    }

    public function removeMembers() { 
        $input = Input::all();
        return Collaborators::removeMembers($input);
    }

    public function viewMembers() { 
        $input = Input::all();
        return Collaborators::viewMembers($input);
    }

    public function removeCollaboratorGroup() { 
        $input = Input::all();
        return Collaborators::removeCollaboratorGroup($input);
    }

    public function viewCollaboratorsListing() { 
        $input = Input::all();
        return Collaborators::viewCollaboratorsListing($input);
    }

    // Contract Functions

    public function createContract() { 
        $input = Input::all();
        return Contract::createContract($input);
    }    

    public function changeContractStatus() { 
        $input = Input::all();
        return Contract::changeContractStatus($input);
    }

    public function deleteContract() { 
        $input = Input::all();
        return Contract::deleteContract($input);
    }

    public function cronEventEnded() { 
        $input = Input::all();
        return Notifications::cronEventEnded($input);
    }

    // Chat Funtionalities

    public function sendmessage() {
        $input = Input::all();
        return Chat::sendmessage($input);
    }

    public function viewmessages() {
        $input = Input::all();
        return Chat::viewmessages($input);
    }

    public function viewPreviousMessages() {
        $input = Input::all();
        return Chat::viewPreviousMessages($input);
    }

    public function viewCurrentMessages() {
        $input = Input::all();
        return Chat::viewCurrentMessages($input);
    }

    public function viewmessagelisting() {
        $input = Input::all();
        return Chat::viewmessagelisting($input);
    }
    
//***********************************
//    Notification Funtionalities
//***********************************

    // Get Notifications
    public function getNotifications() {
        $input = Input::all();
        return Notifications::getNotifications($input);
    }

    // Mark Read Notifications
    public function markReadNotifications() {
        $input = Input::all();
        return Notifications::markReadNotifications($input);
    }    

    // Mark Read Notifications
    public function removeNotification() {
        $input = Input::all();
        return Notifications::removeNotification($input);
    }

    // Clear Notifications
    public function clearNotifications() {
        $input = Input::all();
        return Notifications::clearNotifications($input);
    }

    // Mark Read Notifications
    public function markReadBadgeNotifications() {
        $input = Input::all();
        return Notifications::markReadBadgeNotifications($input);
    }

    // Get Badge Notification Count
    public function getBadgeNotificationCount() {
        $input = Input::all();
        return Notifications::getBadgeNotificationCount($input);
    }

//***********************************
//    Conceirge Funtionalities
//***********************************

    public function sendConceirgeUserMessage() {
        $input = Input::all();
        return Conceirge::sendConceirgeUserMessage($input);
    }

    public function viewConceirgeUserMessages() {
        $input = Input::all();
        return Conceirge::viewConceirgeUserMessages($input);
    }

    public function viewPreviousConceirgeUserMessages() {
        $input = Input::all();
        return Conceirge::viewPreviousConceirgeUserMessages($input);
    }

    public function viewCurrentConceirgeUserMessages() {
        $input = Input::all();
        return Conceirge::viewCurrentConceirgeUserMessages($input);
    }

    public function viewConceirgeUserMessageListing() {
        $input = Input::all(); 
        return Conceirge::viewConceirgeUserMessageListing($input);
    }

//***********************************
//    Search Funtionalities
//***********************************

    public function search() {
        $input = Input::all(); 
        return Users::search($input);
    }

    public function topSubCategories() {
        $input = Input::all(); 
        return Users::topSubCategories($input);
    }    

}
