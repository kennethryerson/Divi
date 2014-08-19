(function($){
	$(document).ready( function(){
		var $save_message = $("#epanel-ajax-saving"),
			$save_message_spinner = $save_message.children("img"),
			$save_message_description = $save_message.children("span");

		$("#et_aweber_connection .et_make_connection").on( "click", function( event ) {
			event.preventDefault();

			$.ajax({
				type: "POST",
				url: ajaxurl,
				data: {
					action : "et_aweber_submit_authorization_code",
					et_load_nonce : et_advanced_options.et_load_nonce,
					et_authorization_code : $("#et_aweber_authorization #et_aweber_authentication_code").val()
				},
				beforeSend: function ( xhr ){
					$( '#et_aweber_connection .et_result_error' ).remove();

					$save_message_spinner.css("display","block");
					$save_message_description.css("margin","6px 0px 0px").html( et_advanced_options.aweber_connecting );
					$save_message.fadeIn('fast');
				},
				success: function( response ){
					hide_ajax_popup( response );

					if ( response === 'success' ) {
						$( '#et_aweber_authorization' ).hide();

						$( '#et_aweber_remove_connection' ).show();
					} else {
						aweber_show_error_message( response );
					}
				}
			});
		});

		$("#et_aweber_connection .et_remove_connection").on( "click", function( event ) {
			event.preventDefault();

			$.ajax({
				type: "POST",
				url: ajaxurl,
				data: {
					action : "et_aweber_remove_connection",
					et_load_nonce : et_advanced_options.et_load_nonce
				},
				beforeSend: function ( xhr ){
					$save_message.css( { 'width' : '228px' } );
					$save_message_spinner.css("display","block");
					$save_message_description.css("margin","6px 0px 0px").html( et_advanced_options.aweber_remove_connection );
					$save_message.fadeIn('fast');
				},
				success: function( response ){
					hide_ajax_popup( response );

					if ( response === 'success' ) {
						$( '#et_aweber_remove_connection' ).hide();

						$("#et_aweber_authorization #et_aweber_authentication_code").val( '' );

						$( '#et_aweber_authorization' ).show();
					} else {
						aweber_show_error_message( response );
					}
				}
			});
		});

		function aweber_show_error_message( response ) {
			var error_html = '<div class="et_result_error">';

			error_html += '<p><strong>' + et_advanced_options.aweber_failed + '</strong>.</p>';
			error_html += '<p>' + response + '</p>';

			error_html += '</div> <!-- .et_result_error -->';

			$( '#et_aweber_authorization' ).after( error_html );
		}

		function hide_ajax_popup( response ) {
			var error_message = response !== 'success' ? ' with errors' : '';

			$save_message.addClass( 'et_aweber_connect_done' );
			$save_message_spinner.css("display","none");
			$save_message_description.css("margin","0px").html( et_advanced_options.aweber_done + error_message );

			setTimeout( function() {
				$save_message.fadeOut( "slow", function() {
					$(this).removeClass( 'et_aweber_connect_done' );

					$save_message.css( { 'width' : '142px' } );
				} );
			}, 500 );
		}
	});
})(jQuery)