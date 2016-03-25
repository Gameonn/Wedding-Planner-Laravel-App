$(document).ready(function() {

	$(".chat-discussion").scrollTop($(".chat-discussion")[0].scrollHeight);	

	setInterval(function() { 

		var scroll_pos_2 = $(".chat-discussion").scrollTop();
		if(scroll_pos_2 > ($(".chat-discussion")[0].scrollHeight - 500)) {
		
			var user_id = $('#user_id').val();
			var user_id_2 = $('#user_id_2').val();        	

			var base_path = window.location.protocol + "//" + window.location.host + "/";	    	
			var post_path = base_path+"admin/view-current-conceirge-admin-messages";

		    $.post(post_path, {user_id: user_id, user_id_2: user_id_2}, function(result) {

				$(".dyna-chat-cont").html(result);		
				
				var scroll_pos = $(".chat-discussion").scrollTop();		

				if(scroll_pos > ($(".chat-discussion")[0].scrollHeight - 500)) {
					$(".chat-discussion").scrollTop($(".chat-discussion")[0].scrollHeight);			
				}			
		    			        
		    });	

	    }    

	}, 3000);			

	$(".send-chat-btn").click(function(event){   

    	var user_id = $('#user_id').val();
    	var user_id_2 = $('#user_id_2').val();
    	var message_type = $('#message_type').val();
    	var message = $('#send-message').val();    	

    	var base_path = window.location.protocol + "//" + window.location.host + "/";	    	
    	var post_path = base_path+"admin/send-conceirge-admin-message";

    	$('#send-message').val('');

        $.post(post_path, {user_id: user_id, user_id_2: user_id_2, message_type: message_type, message: message}, function(result){

			$(".dyna-chat-cont").html(result);				
			$(".chat-discussion").scrollTop($(".chat-discussion")[0].scrollHeight);
        			        
	    });
    
	});	

	$("#send-message").keyup(function(event){
	    if(event.keyCode == 13){

	    	var user_id = $('#user_id').val();
	    	var user_id_2 = $('#user_id_2').val();
	    	var message_type = $('#message_type').val();
	    	var message = $(this).val();

	    	var base_path = window.location.protocol + "//" + window.location.host + "/";	    	
	    	var post_path = base_path+"admin/send-conceirge-admin-message";

	    	$('#send-message').val('');

	        $.post(post_path, {user_id: user_id, user_id_2: user_id_2, message_type: message_type, message: message}, function(result){

				$(".dyna-chat-cont").html(result);				
				$(".chat-discussion").scrollTop($(".chat-discussion")[0].scrollHeight);
	        			        
		    });

	    }
	});

	$(".send-wedding-btn").click(function(event){   

    	var user_id = $('#user_id').val();
    	var user_id_2 = $('#user_id_2').val();
    	var message_type = '2';
    	var wedding_id = $('#select-wedding-id').val();    	    	

    	var base_path = window.location.protocol + "//" + window.location.host + "/";	    	
    	var post_path = base_path+"admin/send-conceirge-admin-message";

    	$('#send-message').val('');

        $.post(post_path, {user_id: user_id, user_id_2: user_id_2, message_type: message_type, wedding_id: wedding_id}, function(result) {        	

			$(".dyna-chat-cont").html(result);				
			$(".chat-discussion").scrollTop($(".chat-discussion")[0].scrollHeight);

			$('#myWeddingModal').modal('toggle');
        			        
	    });
    
	});

	$(".send-vendor-btn").click(function(event){   

    	var user_id = $('#user_id').val();
    	var user_id_2 = $('#user_id_2').val();
    	var message_type = '3';
    	var vendor_id = $('#select-vendor-id').val();    	    	

    	var base_path = window.location.protocol + "//" + window.location.host + "/";	    	
    	var post_path = base_path+"admin/send-conceirge-admin-message";

    	$('#send-message').val('');

        $.post(post_path, {user_id: user_id, user_id_2: user_id_2, message_type: message_type, vendor_id: vendor_id}, function(result) {        

			$(".dyna-chat-cont").html(result);				
			$(".chat-discussion").scrollTop($(".chat-discussion")[0].scrollHeight);

			$('#myVendorModal').modal('toggle');
        			        
	    });
    
	});	

	$(".prev-message-btn").click(function() {   

    	var is_more_chat = $('.is-more-chat').val();
    	var user_id = $('#user_id').val();
    	var user_id_2 = $('#user_id_2').val();

    	var base_path = window.location.protocol + "//" + window.location.host + "/";	    	
    	var post_path = base_path+"admin/view-previous-conceirge-admin-messages";
    	
        $.post(post_path, {user_id: user_id, user_id_2: user_id_2, last_message_id: is_more_chat}, function(result){

        	// alert(result);

			$(".dyna-chat-cont").html(result);		
			$(".prev-message-btn").hide();

			var scroll_pos_3 = $(".chat-discussion").scrollTop();	
			$(".chat-discussion").scrollTop(scroll_pos_3);	
        			        
	    });
    
	});	

});