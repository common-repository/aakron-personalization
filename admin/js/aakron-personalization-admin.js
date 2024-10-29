(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	$(function() {
		$('#js_all').on('click',function () {
			$('#sku_list').hide();
		});
		$('#js_sku').on('click',function () {
			$('#sku_list').show();
		});
		$('.sync_now').on('click',function () {
			var sku_list;
			var sku_list = $('#sku_list').val();
			if( $('#js_sku input[type="radio"]').is(':checked') ){
				if( sku_list == '' ){
					$('#sync-error').html('<p style="font-size:14px;line-height:1;"> Please enter atleast one Sku Id.</p>').css('display','block');
					setTimeout(function(){ 
						$('#sync-error').css('display','none');
					}, 3000);
					return false;
				}else{
					syncProducts(sku_list);
				}
			}else{
				syncProducts(sku_list);
			}
			
		});

		function syncProducts(sku_list){
			$('.sync-header').html('');
			$(".sync_now").attr("disabled", true);
			$(".sync_now_offer").attr("disabled", true);
			jQuery.ajax({
				type : "post",
				url : aakron_design_admin_obj.ajax_url,
				data : {
					action: "aakron_design_sync_product",
					sku_list: sku_list
				},
				success: function(response) {
					var resultObj = JSON.parse(response);
					console.log(resultObj);
					if(resultObj.type == "success") {
						$('.sync_now').attr("disabled", false);
						$('.sync_now').removeAttr("disabled");
						$('.sync-log').html(resultObj.data);
					} else {
						$('.sync_now').attr("disabled", false);
						$('.sync_now').removeAttr("disabled");
						$('.sync-log').html("");
						$('#sync-error').html(resultObj.data).css('display','block');
					}
					setTimeout(function () { window.location.reload(); }, 2500);      
				}
			});	
		}
		
		$(document).ajaxStart(function(){
			$('#js_overlay_spinner').show();
		});
		$(document).ajaxComplete(function(){
			$('#js_overlay_spinner').hide();
		});
		
		// user access token validation
		$('#user_verify').on('click',function () {
			var userAccessToken = $('input#js_accesstoken').val();
			if( userAccessToken !== '' ){
				$("#js_design_tool_status").find('#js_verify_spinner').show();
				jQuery.ajax({
					type : "post",
					dataType: 'json',
					url : aakron_design_admin_obj.ajax_url,
					data : {
						action: "aakron_design_tool_verify_user",
						userAccessToken : userAccessToken,
					},
					success: function(response) {
						console.log(response);
						if( response.type == 'success'){
							$('.design_tool_status').find('#regSuccess').css('display','block');
						}else{
							$('.design_tool_status').find('#regError').css('display','block');
						}
						$("#js_design_tool_status").find('#js_verify_spinner').hide();
						setTimeout(function () { window.location.reload(); }, 2500);
					}
				});
			}else{
				$('.design_tool_status').find('#regImpError').css('display','block');
				setTimeout(function(){ 
					$('.design_tool_status').find('#regImpError').css('display','none');
				}, 3000);
			}
		});

		// user token remove
		$('#user_token_remove').on('click',function () {
			var userAccessToken = $('input#js_accesstoken').val();
			if( userAccessToken !== '' ){
				$("#js_design_tool_status").find('#js_verify_spinner').show();
				jQuery.ajax({
					type : "post",
					dataType: 'json',
					url : aakron_design_admin_obj.ajax_url,
					data : {
						action: "aakron_design_tool_remove_user_token",
						userAccessToken : userAccessToken,
					},
					success: function(response) {
						if( response.type == 'success'){
							$('.design_tool_status').find('#regSuccess').html(response.data).css('display','block');
						}
						$("#js_design_tool_status").find('#js_verify_spinner').hide();
						setTimeout(function () { window.location.reload(); }, 2500); 
					}
				});
			}else{
				$('.design_tool_status').find('#regImpError').css('display','block');
				setTimeout(function(){ 
					$('.design_tool_status').find('#regImpError').css('display','none');
				}, 3000);
			}
		});

		// user email validation 
		//on keyup, start the countdown
		$('#js_user_first_name').focus();
		
		$('#js_user_email').on('blur', function () {
		  emailValidate();
		});

		function ValidateEmail(email) {
	        var expr = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
	        return expr.test(email);
	    };

		//user is "finished typing," do something
		function emailValidate () {
			var emailValue  = $('#js_user_email').val();
			if (!ValidateEmail(emailValue)) {
	            return false;
	        }else{
	        	if( emailValue !== '' ){
					$("#user_registration-form").find('#js_verify_spinner').show();
					jQuery.ajax({
						type : "post",
						dataType: 'json',
						url : aakron_design_admin_obj.ajax_url,
						data : {
							action: "aakron_design_tool_user_email_validate",
							emailValue : emailValue,
						},
						success: function(response) {
							$("#user_registration-form").find('#js_verify_spinner').hide();
							if( response.type == 'success'){
								$('#user_registration-form').find('.email-validate').html(response.data).css('display','block');
								setTimeout(function(){ 
									$('.email-validate').css('display','none');
								}, 3000);
							}else{
								$('#user_registration-form').find('.email-validate').html(response.data).css('display','block');
								setTimeout(function(){ 
									$('.email-validate').css('display','none');
								}, 3000);
							}
						}
					});
				}
	        }
		}
	});

})( jQuery );