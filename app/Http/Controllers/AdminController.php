<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Admin;
use App\AdminChat;
use App\AdminBusinesses;
use App\Conceirge;
use App\AdminWedding;
use App\AdminCollaborators;
use App\Users;
use App\AdminCity;
use App\AdminSponsor;
use App\NewsletterManager;
 
class AdminController extends Controller {

	public function __construct()
    {
        $remember_token = Session::get('remember_token');
        $this->middleware('admin');

        // Get Admin Role
        $admin_data = DB::table('admin')->select('user_role')->where('remember_token', $remember_token)->first();

        if(!empty($admin_data)) {

            if($admin_data->user_role == 'super_user') {            
                $this->middleware('super_user');
            }
            elseif ($admin_data->user_role == 'data_operator') {                            
                $this->middleware('data_operator', ['except' => [
                    'vendorListing', 
                    'vendorApprove',
                    'vendorDisapprove',
                    'vendorDetails',
                    'vendorDetailsEdit',
                    'vendorDetailsEditCode',
                    'vendorExtraDetailsEdit',
                    'vendorExtraDetailsEditCode',
                    'uploadImages',
                    'uploadImagesCode',
                    'delVendorPortfolioImg',   
                    'dashboard',
                    'viewConceirgeAdminMessageListing', 
                    'unauthorizeAccess'
                ]]);
            }
            elseif ($admin_data->user_role == 'chat_operator') {
                $this->middleware('chat_operator', ['except' => [
                    'sendConceirgeAdminMessage',                    
                    'viewCurrentConceirgeAdminMessages',
                    'viewPreviousConceirgeAdminMessages',
                    'viewConceirgeAdminMessageListing',
                    'conceirge',
                    'conceirge2',
                    'uploadChatImage',
                    'chatWedding',
                    'chatVendor',
                    'dashboard',
                    'viewConceirgeAdminMessageListing', 
                    'unauthorizeAccess'
                ]]);
            }
            else {
                return redirect()->route('admin.login'); 
            }

        }

    }

//************************************************************************************************************************
//                                                      Dashboard
//************************************************************************************************************************
    
    // Dashboard
	public function dashboard() {
        $response = Admin::dashboard();
        return view('admin/dashboard')->with('response', $response);
    }

//************************************************************************************************************************
//                                                  Vendor Functions
//************************************************************************************************************************

    // Vendor Listing
    public function vendorListing($keyword, $page) {        

        $vendor_listing = Admin::vendorListing($keyword, $page);         
        return view('admin/vendor-listing')->with('vendor_listing', $vendor_listing);
    }

    // Vendor Approve
    public function vendorApprove() {
        $input = Input::all();
        $response = Admin::vendorApprove($input);         
        return redirect()->back();
        
    }

    // Vendor Disapprove
    public function vendorDisapprove() {
        $input = Input::all();
        return $response = Admin::vendorDisapprove($input);         
        return redirect()->back();
    }

    public function vendorDetails($user_id) {

        $vendor_details = Admin::viewVendorProfileDetails($user_id);
        $vendor_extra_details = Admin::viewVendorExtraDetails($user_id);
        $vendor_review_details = Admin::viewVendorReviewDetails($user_id);
        $vendor_portfolio_images = Admin::viewVendorPortfolioImages($user_id);

        return view('admin/vendor-details')->with('vendor_details', $vendor_details)->with('vendor_extra_details', $vendor_extra_details)->with('vendor_review_details', $vendor_review_details)->with('vendor_portfolio_images', $vendor_portfolio_images);

    }

    public function vendorDetailsEdit($user_id) {
        $vendor_details = Admin::viewVendorProfileDetails($user_id);        
        // City List
        $city_list = Admin::getCityList();
        // Sub Business List
        $sub_business_list = Admin::getSubBusinessList();
        return view('admin/vendor-details-edit')->with('vendor_details', $vendor_details)->with('city_list', $city_list)->with('sub_business_list', $sub_business_list);
    }

    public function vendorDetailsEditCode() {

        $input = Input::all();
        $user_id = $input['user_id'];
        // Update Details 
        Admin::vendorDetailsEditCode($input);        

        return redirect()->route('admin.vendorDetails', [$user_id]);
    }

    // Vendor Extra Details
    public function vendorExtraDetailsEdit($user_id) {
        $vendor_extra_details = Admin::viewVendorExtraDetails($user_id);  
        return view('admin/vendor-extra-details-edit')->with('user_id', $user_id)->with('vendor_extra_details', $vendor_extra_details);
    }

    public function vendorExtraDetailsEditCode() {

        $input = Input::all();
        $user_id = $input['user_id'];

        // Update Details 
        Admin::vendorExtraDetailsEditCode($input);

        return redirect()->route('admin.vendorDetails', [$user_id]);

    }

    // Delete Extra Details
    public function deleteExtraDetails() {
        $input = Input::all(); 
        return Admin::deleteExtraDetails($input);        
    }

    // Remove Vendor
    public function removeVendor() {
        $input = Input::all(); 
        return Admin::removeVendor($input);
        return redirect()->back();
    }

    // Upload Vendor Portfolio Images
    public function uploadImages($user_id) {       

        $vendor_portfolio_images = Admin::viewVendorPortfolioImages($user_id);     
        return view('admin/upload-images')->with('vendor_portfolio_images', $vendor_portfolio_images)->with('user_id', $user_id);
    }

    public function uploadImagesCode() {       

        $input = Input::all();
        $response = Admin::uploadImagesCode($input);    

        return redirect()->back(); 
        
    }

    // Delete Vendor Portfolio Images
    public function delVendorPortfolioImg() {       

        $input = Input::all();
        $response = Admin::delVendorPortfolioImg($input);    

        return redirect()->back(); 
        
    }

    // Add Vendor
    public function addVendor() {
        // City List
        $city_list = Admin::getCityList();
        // Sub Business List
        $sub_business_list = Admin::getSubBusinessList();    
        return view('admin.add-vendor')->with('city_list', $city_list)->with('sub_business_list', $sub_business_list);
    }

    // Add Vendor Code
    public function addVendorCode() {
        $input = Input::all();        
        $response = Admin::addVendorCode($input);

        if($response==1) 
            return redirect('admin/dashboard');
        else 
            return redirect()->back()->with('message', $response);               
    }

    //*******************************************************************************************************
    //                                           User Controllers
    //*******************************************************************************************************

    // User Listing
    public function userListing($keyword, $page) {        
        $user_listing = Admin::userListing($keyword, $page);        
        return view('admin/user-listing')->with('user_listing', $user_listing);
    }

    // User Details
    public function userDetails($user_id) {        
        $user_details = Admin::userDetails($user_id);
        $wedding_detail = DB::table('wedding')->select('id')->where('user_id', $user_id)->first();        
        $wedding_photos = AdminWedding::weddingPhotos($wedding_detail->id);
        return view('admin/user-details')->with('user_details', $user_details)->with('wedding_photos', $wedding_photos);
    }

    //*******************************************************************************************************
    //                                           Collaborator Controllers
    //*******************************************************************************************************

    // Collaborator Listing
    public function collaboratorListing($keyword, $page) {        
        $collaborator_listing = Admin::collaboratorListing($keyword, $page);        
        return view('admin/collaborator-listing')->with('collaborator_listing', $collaborator_listing);
    }

    // Collaborator Details
    public function collaboratorDetails($user_id) {        
        $collaborator_details = Admin::collaboratorDetails($user_id);            
        return view('admin/collaborator-details')->with('collaborator_details', $collaborator_details);
    }

    // Collaborator Group Listing
    public function collaboratorGroupListing($keyword, $page) {        
        $collaborator_group_listing = AdminCollaborators::collaboratorGroupListing($keyword, $page);            
        return view('admin/collaborator-group-listing')->with('collaborator_group_listing', $collaborator_group_listing);
    }

    // Collaborator Group Members
    public function collaboratorGroupMembers($collaborator_id) {        
        $collaborator_group_members = AdminCollaborators::collaboratorGroupMembers($collaborator_id);            
        return view('admin/collaborator-group-members')->with('collaborator_group_members', $collaborator_group_members);
    }

    //*******************************************************************************************************
    //                                           Wedding Controllers
    //*******************************************************************************************************

    // New Wedding
    public function newWedding() {    
        $city_list = Admin::getCityList();
        $wedding_type_list = AdminWedding::weddingTypeList();
        return view('admin/new-wedding')->with('city_list', $city_list)->with('wedding_type_list', $wedding_type_list);
    }

    // New Wedding Code
    public function newWeddingCode() {  
        $input = Input::all();          
        $response = AdminWedding::newWeddingCode($input);

        if($response==1)
            return redirect()->route('admin.dashboard');
        else 
            return redirect()->back()->with('message', $response);
        
    }

    // Wedding Listing
    public function weddingListing($keyword, $page) {            
        $wedding_listing = AdminWedding::weddingListing($keyword, $page);
        return view('admin/wedding-listing')->with('wedding_listing', $wedding_listing);
    }

    // Wedding Details
    public function weddingDetails($wedding_id) {            
        $wedding_details = AdminWedding::weddingDetails($wedding_id);
        $wedding_photos = AdminWedding::weddingPhotos($wedding_id);
        return view('admin/wedding-details')->with('wedding_details', $wedding_details)->with('wedding_photos', $wedding_photos);
    }

    // Wedding Edit
    public function weddingEdit($wedding_id) {                            
        $city_list = Admin::getCityList();
        $wedding_type_list = AdminWedding::weddingTypeList();
        $wedding_details = AdminWedding::weddingDetails($wedding_id);
        return view('admin/wedding-edit')->with('wedding_details', $wedding_details)->with('city_list', $city_list)->with('wedding_type_list', $wedding_type_list);
    }

    public function weddingEditCode() {                            
        
        $input = Input::all();
        $wedding_id = $input['wedding_id'];
        $response = AdminWedding::weddingEditCode($input);

        if($response == 1)
            return redirect()->route('admin.weddingDetails', [$wedding_id]);            
        else 
            return redirect()->back()->with('message', $response);

    }

    // Add Wedding Photos    
    public function addWeddingPhotos($wedding_id) {            
        $wedding_photos = AdminWedding::weddingPhotos($wedding_id);

        $wedding_detail = DB::table('wedding')->select('user_id')->where('id', $wedding_id)->first();        

        return view('admin/wedding-photos')
            ->with('wedding_photos', $wedding_photos)
            ->with('wedding_id', $wedding_id)
            ->with('user_id', $wedding_detail->user_id);
    }

    public function uploadWeddingImages() {       
        $input = Input::all();
        $response = AdminWedding::uploadWeddingImages($input);    

        return redirect()->back();         
    }

    public function removeWeddingImage() {       
        $input = Input::all();
        $response = AdminWedding::removeWeddingImage($input);    

        return redirect()->back();         
    }

    //***************************************************
    //               Wedding Photos Tags
    //***************************************************

    // Wedding image by Id
    public function viewWeddingImageById($image_id) {                 
        $response = AdminWedding::viewWeddingImageById($image_id);
        $vendor_listing = DB::table('vendor_details')->select('user_id', 'business_name')->get();
        return view('admin/view-wedding-image-by-id')->with('response', $response)->with('image_id', $image_id)->with('vendor_listing', $vendor_listing);  
    }

    // Tag Vendor
    public function tagVendors() {       
        $input = Input::all();
        $response = AdminWedding::tagVendors($input);    

        return redirect()->back();         
    }

    //***************************************************
    //               Wedding Types
    //***************************************************

    // Wedding Types
    public function weddingTypes() {               
        $wedding_types = AdminWedding::weddingTypes();    
        return view('admin/wedding-types')->with('wedding_types', $wedding_types);  
    }

    // Add Wedding Type
    public function addWeddingType() {               
        $input = Input::all();
        $response = AdminWedding::addWeddingType($input);    
        return redirect()->back();  
    }

    // Edit Wedding Type
    public function editWeddingType() {               
        $input = Input::all();
        $response = AdminWedding::editWeddingType($input);
        return redirect()->back();  
    }

    // Delete Wedding Type
    public function deleteWeddingType() {               
        $input = Input::all();
        $response = AdminWedding::deleteWeddingType($input);
        return redirect()->back();  
    }

    //********************************************************************************************************
    //                                          Conceirge Controllers
    //********************************************************************************************************    

    // Conceirge
    public function sendConceirgeAdminMessage() { 
        $input = Input::all();
        Conceirge::sendConceirgeAdminMessage($input);                 
        return Conceirge::viewCurrentConceirgeAdminMessages($input);                 
    }

    public function viewCurrentConceirgeAdminMessages() { 
        $input = Input::all();
        return $response = Conceirge::viewCurrentConceirgeAdminMessages($input);                 
    }

    public function viewPreviousConceirgeAdminMessages() { 
        $input = Input::all();
        return $response = Conceirge::viewPreviousConceirgeAdminMessages($input);                 
    }

    public function viewConceirgeAdminMessageListing() {         
        $admin_id = Session::get('admin_id');
        if($admin_id != 1) {
            $admin_id = 'admin'.$admin_id;
            return $response = Conceirge::viewConceirgeAdminMessageListing($admin_id);                 
        }        
    }

	public function conceirge() {            
        $admin_id = Session::get('admin_id');

        $admin_data = DB::table('admin')->select('user_role')->where('id', $admin_id)->first();

        if($admin_data->user_role == 'super_user') {
            $chat_operator_listing = Admin::chatOperatorListing();
            return view('admin/chat-operator-listing')->with('chat_operator_listing', $chat_operator_listing);

            // $messages = Conceirge::viewConceirgeAdminMessageListing3($admin_id);
            // return view('admin/conceirge-3')->with('messages', $messages)->with('admin_id', $admin_id);        
        }
        elseif ($admin_data->user_role == 'chat_operator') {        
            $admin_id = 'admin'.$admin_id;
            $messages = Conceirge::viewConceirgeAdminMessageListing2($admin_id);
            return view('admin/conceirge')->with('messages', $messages)->with('admin_id', $admin_id);
        }
        else {
            return "Something went wrong";
        }
        
    }

    public function chatOperatorConversations($chat_operator_id) {            
        $admin_id = Session::get('admin_id');
        $chat_operator_id = 'admin'.$chat_operator_id;

        $messages = Conceirge::viewConceirgeAdminMessageListing2($chat_operator_id);
        return view('admin/conceirge')->with('messages', $messages)->with('admin_id', $chat_operator_id);
        
    }

    public function conceirge2($user_id, $user_id_2) {   
        $zone = "Asia/Kolkata";
        $messages = Conceirge::getCurrentConceirgeUserMessages($user_id, $user_id_2, $zone);
        $vendor_listing = Conceirge::vendorListing();
        $wedding_listing = Conceirge::weddingListing();

        // Formating Image For Admin View
        foreach ($messages as $key => $value) {             
            if($messages[$key]->image == "") 
                $messages[$key]->image = Users::getFormattedImage('default-profile-pic.png'); 
        }        

        // return $messages;        

        return view('admin/conceirge-2')
            ->with('messages', $messages)
            ->with('user_id', $user_id)
            ->with('user_id_2', $user_id_2)
            ->with('vendor_listing', $vendor_listing)
            ->with('wedding_listing', $wedding_listing);
    }

    public function uploadChatImage() {         
        $input = Input::all();
        $response = Conceirge::uploadChatImage($input);                 
        return redirect()->back();
    }

    public function chatWedding() {         
        $input = Input::all();
        $response = Conceirge::chatWedding($input);                 
        return redirect()->back();
    }

    public function chatVendor() {         
        $input = Input::all();
        $response = Conceirge::chatVendor($input);                 
        return redirect()->back();
    }

    //********************************************************************************************************
    //                                          Vendor Business
    //********************************************************************************************************    

    // Vendor Businesses
    public function vendorBusinesses() {                  
        $businesses = AdminBusinesses::vendorBusinesses();
        return view('admin/vendor-businesses')->with('businesses', $businesses);
    }

    public function addBusiness() {
        $input = Input::all();
        $response = AdminBusinesses::addBusiness($input);
        
        return redirect()->back()->with('message', $response);        
    }

    public function editBusiness() {
        $input = Input::all();                  
        AdminBusinesses::editBusiness($input);
        return redirect()->back();
    }

    public function deleteBusiness() {
        $input = Input::all();                  
        AdminBusinesses::deleteBusiness($input);
        return redirect()->back();
    }

    // Vendor Sub Businesses
    public function addSubBusiness() {
        $input = Input::all();                  
        $response = AdminBusinesses::addSubBusiness($input);

        if($response != 1)
            return redirect()->back()->with('message', $response);
        else
            return redirect()->back();
    }

    public function deleteSubBusiness() {
        $input = Input::all();                  
        AdminBusinesses::deleteSubBusiness($input);
        return redirect()->back();
    }

    //********************************************************************************************************
    //                                          City Controllers
    //********************************************************************************************************    

    // City Listing
    public function cityListing() {                  
        $city_listing = AdminCity::cityListing();
        return view('admin/city-listing')->with('city_listing', $city_listing);
    }

    public function addCity() {
        $input = Input::all();                  
        AdminCity::addCity($input);
        return redirect()->back();
    }

    public function editCity() {
        $input = Input::all();                  
        AdminCity::editCity($input);
        return redirect()->back();
    }

    public function removeCity() {
        $input = Input::all();                  
        AdminCity::removeCity($input);
        return redirect()->back();
    }

    //********************************************************************************************************
    //                                            Sponsor Controllers
    //********************************************************************************************************    

    public function getSponsor() {                       

        $vendor_listing = AdminSponsor::getSponsorListing();
        $all_vendor_listing = DB::table('users')->join('vendor_details', 'users.id', '=', 'vendor_details.user_id')->select('users.id', 'vendor_details.business_name')->get();
        return view('admin/sponsors')->with('all_vendor_listing', $all_vendor_listing)->with('vendor_listing', $vendor_listing);
    }

    public function addSponsor() {
        $input = Input::all();                  
        AdminSponsor::addSponsor($input);
        return redirect()->back();
    }

    public function deleteSponsor() {
        $input = Input::all();                  
        AdminSponsor::deleteSponsor($input);
        return redirect()->back();
    }    

    //********************************************************************************************************
    //                                     Admin Account Controllers
    //********************************************************************************************************    

    public function adminListing() {        
        $admin_listing = Admin::adminListing();
        return view('admin/admin-listing')->with('admin_listing', $admin_listing);
    }

    public function addOperator() {
        $input = Input::all();                  
        $response = Admin::addOperator($input);
        
        if($response != 1)
            return redirect()->back()->with('message', $response);
        else
            return redirect()->back();
    }

    public function editOperator() {
        $input = Input::all();                  
        $response = Admin::editOperator($input);
        
        if($response != 1)
            return redirect()->back()->with('message', $response);
        else
            return redirect()->back();
    }

    //********************************************************************************************************
    //                                     Auto Authorization Controllers
    //********************************************************************************************************    

    // Auto Authorization
    public function autoAuthorization() {
        $auto_authorize_data = DB::table('auto_authorize')->select('id', 'status')->first();
        return view('admin/auto-authorization')->with('auto_authorize_data', $auto_authorize_data);
    }

    // Change Authorization Status
    public function changeAuthorizationStatus() {
        $input = Input::all();
        DB::table('auto_authorize')->where('id', '1')->update(['status' => $input['status']]);
        return redirect()->back();
    }

    //********************************************************************************************************
    //                                     Unauthorize Access Controllers
    //********************************************************************************************************    

    // Unauthorize access
    public function unauthorizeAccess() {
        return view('admin/unauthorize-access');
    }

    //********************************************************************************************************
    //                                                  Test
    //********************************************************************************************************    

    // Test
	public function adminTest() {
        return view('admin-layout');
    }

    // Unauthorize access
    public function testMailChimp() {

        // return NewsletterManager::test();

        $mailchimp = app('Mailchimp');        
        $foo = new NewsletterManager($mailchimp);
        $foo->addEmailToList('harman.codebrew@gmail.com');

        // $mailchimp = \App::make('Mailchimp');
        // NewsletterManager::addEmailToList('harman.codebrew@gmail.com');
    
    }

    // Test Scrolling
    public function testScrolling() {
        return 1;
        return view('test-scrolling');
    }    

    // Open Tok
    public function opentok() {        
        // return 1;
        return view('opentok');
    }    

}
