$(document).ready(function() {	

	var base_path = window.location.protocol + "//" + window.location.host + "/";           
    var post_path = base_path+"admin/view-conceirge-admin-message-listing";

    $.post(post_path, {}, function(result) {        
        $(".my-header-dropdown").html(result);  
        // var message_count = $(".message-count").value();
        // $(".my-message-count").text('100');
    });     

    $(".vendor-tag-select2").select2();    

    // Conceirge
    $(".select2-select").select2();

    $("#myTags").tagit({
	    fieldName: 'tags[]'
	});	

	setInterval(function() {
        
        var base_path = window.location.protocol + "//" + window.location.host + "/";           
        var post_path = base_path+"admin/view-conceirge-admin-message-listing";

        $.post(post_path, {}, function(result) {

            // alert(result);
            $(".my-header-dropdown").html(result);                             
                            
        });     

    }, 3000);

	$('.btn-add-more').click(function() {

		var curr_val = $('.curr_val').val();
		curr_val++;

		$(".form-content-cont").append('<div class="extra-detail-cont"><div class="form-group"><label class="col-sm-2 control-label"> Detail Name </label> <div class="col-sm-9"><input type="text" class="form-control" name="detail_name_'+curr_val+'"></div> </div> <div class="form-group"><label class="col-sm-2 control-label"> Detail Description </label> <div class="col-sm-9"><input type="text" class="form-control" name="detail_desc_'+curr_val+'"></div> </div> <div class="hr-line-dashed"></div></div>');

		$('.curr_val').val(curr_val);

	});

	$('.btn-delete-extra-details').click(function() {
		
		var user_id = $('.user_id').val();
		var detail_name = $(this).attr('detail-name');
		var detail_desc = $(this).attr('detail-desc');

		var base_path = window.location.protocol + "//" + window.location.host + "/";           
        var post_path = base_path+"admin/delete-extra-details";

        $.post(post_path, {user_id: user_id, detail_name: detail_name, detail_desc: detail_desc}, function(result) {
            location.reload();         
        });     		

	});

	$('.btn-disapprove').click(function() {	

		var user_id = $(this).siblings('#user_id').val();		

		swal({
	        title: "Are you sure?",
	        text: "You want to disapprove this user!",
	        type: "warning",
	        showCancelButton: true,
	        confirmButtonColor: "#DD6B55",
	        confirmButtonText: "Disapprove!",
	        closeOnConfirm: false
	    }, function () {	    		    	
	    	
	    	var base_path = window.location.protocol + "//" + window.location.host + "/";           
	        var post_path = base_path+"admin/vendor-disapprove";

	        $.post(post_path, {user_id: user_id}, function(result) {
	            location.reload();         
	        });     

	        swal("Approved!", "You successfully disapproved this user.", "success");	        
	    });			 	    		    

	});

	$('.btn-approve').click(function () {

		var user_id = $(this).siblings('#user_id').val();		

	    swal({
	        title: "Are you sure?",
	        text: "You want to approve this user!",
	        type: "warning",
	        showCancelButton: true,
	        confirmButtonColor: "#AEDEF4",
	        confirmButtonText: "Approve!",
	        closeOnConfirm: false
	    }, function () {	

	    	var base_path = window.location.protocol + "//" + window.location.host + "/";           
	        var post_path = base_path+"admin/vendor-approve";

	        $.post(post_path, {user_id: user_id}, function(result) {
	            location.reload();         
	        });  

	        swal("Approved!", "You successfully approved this user.", "success");	        
	    });	    

	});

	$(".my-business-edit-btn").click(function() {

		var business_id = $(this).attr('business-id');
		var business_name = $(this).attr('business-name');	
		var sub_business_string = $(this).attr('sub-business-string');			

		var html_data = '<div class="modal-body"> <div class="form-group"> <label>Business Name</label> <input type="hidden" class="form-control" name="business_id" value="'+business_id+'"> <input type="text" class="form-control business-name-2" name="business_name" value="'+business_name+'"> </div> <div class="form-group"> <label>Sub Categories</label> <ul id="myTagsEdit"> '+sub_business_string+' </ul> </div> <div class="form-group"> <label>Change Image</label> <input type="file" class="filestyle2" name="image"> </div> </div> <div class="modal-footer"> <button type="button" class="btn btn-white" data-dismiss="modal">Close</button> <button type="submit" class="btn btn-primary my-business-edit-btn-2">Edit</button> </div>';

		$(".place-modal-content").html(html_data);

		$("#myTagsEdit").tagit({
		    fieldName: 'editTags[]'
		});

		$(".filestyle2").filestyle();

		// alert(business_id);
	});

	$(".business-edit-form").submit(function(e) {	   	    

    	var business_name_2 = $('.business-name-2').val();
    	business_name_2 = $.trim(business_name_2);    	

		if(business_name_2=="") {
			sweetAlert("Oops...", "Business name cannot be empty", "error");
			return false;
		}
		else {
			if($("#myTagsEdit").children("li").length <= 1) {
	    		sweetAlert("Oops...", "Sub Categories cannot be empty", "error");
	    		return false;
	    	}
	    	else {
	    		return true;
	    	}
		}		

  	});

  	 $('.delete-business-form').click(function(e){
	     e.preventDefault();

      	swal({   
    		title: "Are you sure?",   
    		text: "You want to delete this Category!",
    		type: "warning",   
    		showCancelButton: true,   
    		confirmButtonColor: "#DD6B55",   
    		confirmButtonText: "Yes, delete it!",   
    		closeOnConfirm: false 
    	}, function(){   
    		$('.delete-business-form').submit();
    	});

	 });

	$(".my-city-edit-btn").click(function() {

		var city_id = $(this).attr('city-id');
		var city_name = $(this).attr('city-name');
		var state = $(this).attr('state');

		var html_data = '<div class="modal-body"> <div class="form-group"> <label>City Name</label> <input type="hidden" class="form-control" name="city_id" value="'+city_id+'"> <input type="text" class="form-control" name="city_name" value="'+city_name+'"> <br> <label>State</label> <input type="text" class="form-control" name="state" value="'+state+'"> </div> </div> <div class="modal-footer"> <button type="button" class="btn btn-white" data-dismiss="modal">Close</button> <button type="submit" class="btn btn-primary">Edit</button> </div>';

		$(".place-modal-content-2").html(html_data);		
	});

	$(".my-wedding-type-edit-btn").click(function() {

		var wedding_type_id = $(this).attr('wedding-type-id');
		var wedding_type_name = $(this).attr('wedding-type-name');		

		var html_data = '<div class="modal-body"> <div class="form-group"> <label>Edit</label> <input type="hidden" class="form-control" name="wedding_type_id" value="'+wedding_type_id+'"> <input type="text" class="form-control" name="wedding_type_name" value="'+wedding_type_name+'"> </div> </div> <div class="modal-footer"> <button type="button" class="btn btn-white" data-dismiss="modal">Close</button> <button type="submit" class="btn btn-primary">Edit</button> </div>';

		$(".place-modal-content").html(html_data);

		// alert(business_id);
	});

	$(".my-file-chooser-2").change(function () {
		var files = this.files;
		var reader = new FileReader();
		name=this.value;
		var this_input=$(this);
		reader.onload = function (e) {
			$(".chooser-image").attr('src', e.target.result);
			//$('.def-logo').css('background-image', "url(" + e.target.result + ")");
		}
		reader.readAsDataURL(files[0]);
	});

	// Search Funtionality

	$('#search-vendor-listing').keypress(function (e) {
		var key = e.which;
		if(key == 13)  // the enter key code
		{
			var keyword = $(this).val();			
			if(keyword != "")
				var reload_path = base_path+"admin/vendor-listing/"+keyword+"/0";
			else
				var reload_path = base_path+"admin/vendor-listing/0/0";	        

	        window.location.href = reload_path;
		}
	}); 

	$('#search-wedding-listing').keypress(function (e) {
		var key = e.which;
		if(key == 13)  // the enter key code
		{
			var keyword = $(this).val();			
			if(keyword != "")
				var reload_path = base_path+"admin/wedding-listing/"+keyword+"/0";
			else
				var reload_path = base_path+"admin/wedding-listing/0/0";	        

	        window.location.href = reload_path;
		}
	}); 

	$('#search-user-listing').keypress(function (e) {
		var key = e.which;
		if(key == 13)  // the enter key code
		{
			var keyword = $(this).val();			
			if(keyword != "")
				var reload_path = base_path+"admin/user-listing/"+keyword+"/0";
			else
				var reload_path = base_path+"admin/user-listing/0/0";   	        

	        window.location.href = reload_path;
		}
	}); 

	$('#search-collaborator-listing').keypress(function (e) {
		var key = e.which;
		if(key == 13)  // the enter key code
		{
			var keyword = $(this).val();			
			if(keyword != "")
				var reload_path = base_path+"admin/collaborator-listing/"+keyword+"/0";
			else
				var reload_path = base_path+"admin/collaborator-listing/0/0";     

	        window.location.href = reload_path;
		}
	}); 

	$('#search-collaborator-group-listing').keypress(function (e) {
		var key = e.which;
		if(key == 13)  // the enter key code
		{
			var keyword = $(this).val();			

			if(keyword != "")
				var reload_path = base_path+"admin/collaborator-group-listing/"+keyword+"/0";
			else
				var reload_path = base_path+"admin/collaborator-group-listing/0/0";

	        window.location.href = reload_path;
		}
	}); 

	// Delete Sponsor Sweet Alert
	$('.btn-delete-sponsor').click(function(){	     

	    var vendor_id = $(this).attr("vendor-id");  	    

	    var base_path = window.location.protocol + "//" + window.location.host + "/";           
    	var post_path = base_path+"admin/delete-sponsor";

      	swal({   
    		title: "Are you sure?",   
    		text: "You want to remove this Sponsor!", 
    		type: "warning",   
    		showCancelButton: true,   
    		confirmButtonColor: "#DD6B55",   
    		confirmButtonText: "Yes, remove it!",   
    		closeOnConfirm: false 
    	}, function(){           		
    		$.post(post_path, {vendor_id: vendor_id}, function(result) {    			
	            location.reload();         
	        });  
    	});

	 });

	// Operator Edit Modal
	$(".operator-edit-btn").click(function() {

		var operator_id = $(this).attr('operator-id');
		var operator_username = $(this).attr('operator-username');		

		var html_data = '<div class="modal-body"> <div class="form-group"> <label>Operator Username</label> <input type="hidden" class="form-control" name="operator_id" value="'+operator_id+'"> <input type="text" class="form-control operator-username" name="operator_username" value="'+operator_username+'"> <br> <label>Password</label> <input type="password" class="form-control password" name="password" value=""> <br> <label>Confirm Password</label> <input type="password" class="form-control confirm-password" name="confirm_password" value=""> </div> </div> <div class="modal-footer"> <button type="button" class="btn btn-white" data-dismiss="modal">Close</button> <button type="submit" class="btn btn-primary">Edit</button> </div>';

		$(".place-modal-content-2").html(html_data);		
	});

	$(".edit-operator-form").submit(function(e) {
		
		var operator_username = $('.operator-username').val();
		operator_username = $.trim(operator_username);
		var password = $('.password').val();
		password = $.trim(password);
		var confirm_password = $('.confirm-password').val();
		confirm_password = $.trim(confirm_password);

		if(operator_username=="") {
			sweetAlert("Oops...", "Operator username cannot be empty", "error");		
			return false;
		}
		else {

			if(password != "" && confirm_password != "") {
				if(password == confirm_password) 
					return true;
				else {
					sweetAlert("Oops...", "Password do not match", "error");		
					return false;
				}
			}
			else {
				return true;
			}

		} 			

	});	

	// Remove Vendor Form
	$(".btn-remove-vendor").click(function() {	   	    

    	var user_id = $('.user_id').val();    	

		swal({
	        title: "Are you sure?",
	        text: "You want to remove this user!",
	        type: "warning",
	        showCancelButton: true,
	        confirmButtonColor: "#DD6B55",
	        confirmButtonText: "Remove!",
	        closeOnConfirm: false
	    }, function () {	    		    	
	    	
	    	var base_path = window.location.protocol + "//" + window.location.host + "/";           
	        var post_path = base_path+"admin/remove-vendor";
	        var redirect_path = base_path+"admin/vendor-listing/0/0";

	        $.post(post_path, {user_id: user_id}, function(result) {
	            window.location.href = redirect_path;       
	        });     			

	        swal("Approved!", "You successfully disapproved this user.", "success");	        
	    });	

  	});

});