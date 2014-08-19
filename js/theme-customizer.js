/**
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

( function( $ ) {
	function et_remove_element_class( prefix, el ) {
		var $element = typeof el === 'undefined' ? $( 'body' ) : $( el ),
			el_classes = $element.attr( 'class' ),
			el_class;

		regex = new RegExp( prefix + '[^\\s]+' );

		el_class = el_classes.replace( regex, '' );

		$element.attr( 'class', $.trim( el_class ) );
	}

	function et_fix_page_top_padding() {
		setTimeout( function() {
			var $body = $( 'body' ),
				secondary_nav_height = $body.find( '#top-header' ).length ? $body.find( '#top-header' ).innerHeight() : 0;

			if ( $body.hasClass( 'et_fixed_nav' ) ) {
				$body.find( '#page-container' ).css( 'paddingTop', $body.find( '#main-header' ).innerHeight() + secondary_nav_height );
			} else {
				$body.css( 'paddingTop', 0 );
			}

		}, 200 );
	}

	function et_maybe_create_secondary_nav() {
		if ( $( '#top-header' ).length ) {
			return;
		}

		$( 'body' )
			.addClass( 'et_secondary_nav_enabled' )
			.prepend( '<div id="top-header" class="et_nav_text_color_light"><div class="container"></div></div>' );

		et_fix_page_top_padding();
	}

	function et_maybe_remove_secondary_nav() {
		if ( ! $( '#top-header' ).length ) {
			return;
		}

		setTimeout( function() {
			if ( $( '#top-header .container' ).children().filter( ':visible' ).length ) {
				return;
			}

			$( 'body' )
				.removeClass( 'et_secondary_nav_enabled' )
				.removeClass( 'et_secondary_nav_two_panels' )
				.find( '#top-header' )
				.remove();

			et_fix_page_top_padding();
		}, 500 );
	}

	wp.customize( 'et_divi[link_color]', function( value ) {
		value.bind( function( to ) {
			$( 'article p:not(.post-meta) a, .comment-edit-link, .pinglist a, .pagination a' ).css( 'color', to );
		} );
	} );

	wp.customize( 'et_divi[font_color]', function( value ) {
		value.bind( function( to ) {
			$( 'body' ).css( 'color', to );
		} );
	} );

	wp.customize( 'et_divi[accent_color]', function( value ) {
		value.bind( function( to ) {
			$( '.et_pb_counter_amount, .et_pb_featured_table .et_pb_pricing_heading, .et_pb_pricing_table_button, .comment-reply-link, .form-submit input, .et_quote_content, .et_link_content, .et_audio_content' ).css( 'background-color', to );

			$( '#et_search_icon:hover, .mobile_menu_bar:before, .footer-widget h4, .et-social-icon a:hover, .et_pb_sum, .et_pb_pricing li a, .et_overlay:before, .et_pb_member_social_links a:hover, .et_pb_widget li a:hover, .et_pb_bg_layout_light .et_pb_promo_button, .et_pb_bg_layout_light .et_pb_more_button, .et_pb_filterable_portfolio .et_pb_portfolio_filters li a.active, .et_pb_filterable_portfolio .et_pb_portofolio_pagination ul li a.active, .et_pb_gallery .et_pb_gallery_pagination ul li a.active, .wp-pagenavi span.current, .wp-pagenavi a:hover, .et_pb_contact_submit, .et_pb_bg_layout_light .et_pb_newsletter_button, .nav-single a, .posted_in a' ).css( 'color', to );

			$( '.et-search-form, .nav li ul, .et_mobile_menu, .footer-widget li:before, .et_pb_pricing li:before' ).css( 'border-color', to );
		} );
	} );

	wp.customize( 'et_divi[primary_nav_bg]', function( value ) {
		value.bind( function( to ) {
			$( '#main-header, #main-header .nav li ul, .et-search-form, #main-header .et_mobile_menu' ).css( 'background-color', to );
		} );
	} );

	wp.customize( 'et_divi[secondary_nav_bg]', function( value ) {
		value.bind( function( to ) {
			$( '#top-header, #et-secondary-nav li ul' ).css( 'background-color', to );
		} );
	} );

	wp.customize( 'et_divi[footer_bg]', function( value ) {
		value.bind( function( to ) {
			$( '#main-footer' ).css( 'background-color', to );
		} );
	} );

	wp.customize( 'et_divi[menu_link]', function( value ) {
		value.bind( function( to ) {
			$( '#top-menu a' ).css( 'color', to );
		} );
	} );

	wp.customize( 'et_divi[menu_link_active]', function( value ) {
		value.bind( function( to ) {
			$( '#top-menu li.current-menu-ancestor > a, #top-menu li.current-menu-item > a, .bottom-nav li.current-menu-item > a' ).css( 'color', to );
		} );
	} );

	wp.customize( 'et_divi[boxed_layout]', function( value ) {
		value.bind( function( to ) {
			var $body = $('body');

			if ( to ) {
				$body.addClass( 'et_boxed_layout' );
			} else {
				$body.removeClass( 'et_boxed_layout' );
			}
		} );
	} );

	wp.customize( 'et_divi[cover_background]', function( value ) {
		value.bind( function( to ) {
			var $body = $('body');

			if ( to ) {
				$body.addClass( 'et_cover_background' );
			} else {
				$body.removeClass( 'et_cover_background' );
			}
		} );
	} );

	wp.customize( 'et_divi[show_header_social_icons]', function( value ) {
		value.bind( function( to ) {
			et_maybe_create_secondary_nav();

			var $social_icons = $('#top-header ul.et-social-icons');

			if ( to ) {
				$social_icons.show();
			} else {
				$social_icons.hide();
			}
		} );
	} );

	wp.customize( 'et_divi[show_search_icon]', function( value ) {
		value.bind( function( to ) {
			var $search = $('#et_top_search');

			if ( to ) {
				$search.show();
			} else {
				$search.hide();
			}
		} );
	} );

	wp.customize( 'et_divi[show_footer_social_icons]', function( value ) {
		value.bind( function( to ) {
			var $social_icons = $('#main-footer ul.et-social-icons');

			if ( to ) {
				$social_icons.show();
			} else {
				$social_icons.hide();
			}
		} );
	} );

	wp.customize( 'et_divi[header_style]', function( value ) {
		value.bind( function( to ) {
			var header_style_prefix = 'et_header_style_';

			et_remove_element_class( header_style_prefix );

			$( 'body' ).addClass( header_style_prefix + to );

			et_fix_page_top_padding();
		} );
	} );

	wp.customize( 'et_divi[phone_number]', function( value ) {
		value.bind( function( to ) {
			et_maybe_create_secondary_nav();

			var $phone_number = $( '#et-info-phone' );

			if ( ! $phone_number.length ) {
				if ( ! $( '#et-info' ).length ) {
					$( '#top-header .container' ).prepend( '<div id="et-info"></div>' );
				}

				$( '#et-info' ).prepend( '<span id="et-info-phone"></span>' );

				$phone_number = $( '#et-info-phone' );
			}

			if ( to !== '' ) {
				$phone_number.show().text( to );
			} else {
				$phone_number.hide();
				et_maybe_remove_secondary_nav();
			}
		} );
	} );

	wp.customize( 'et_divi[header_email]', function( value ) {
		value.bind( function( to ) {
			et_maybe_create_secondary_nav();

			var $email = $( '#et-info-email' );

			if ( ! $email.length ) {
				if ( ! $( '#et-info' ).length ) {
					$( '#top-header .container' ).append( '<div id="et-info"></div>' );
				}

				$( '#et-info' ).append( '<span id="et-info-email"></span>' );

				$email = $( '#et-info-email' );
			}

			if ( to !== '' ) {
				$email.show().text( to );
			} else {
				$email.hide();
			}
		} );
	} );

	wp.customize( 'et_divi[primary_nav_text_color]', function( value ) {
		value.bind( function( to ) {
			var nav_color_prefix = 'et_nav_text_color_',
				element = '#main-header';

			et_remove_element_class( nav_color_prefix, element );

			$( element ).addClass( nav_color_prefix + to );
		} );
	} );

	wp.customize( 'et_divi[secondary_nav_text_color]', function( value ) {
		value.bind( function( to ) {
			var nav_color_prefix = 'et_nav_text_color_',
				element = '#top-header';

			et_remove_element_class( nav_color_prefix, element );

			$( element ).addClass( nav_color_prefix + to );
		} );
	} );

	wp.customize( 'et_divi[vertical_nav]', function( value ) {
		value.bind( function( to ) {
			var $body = $('body');

			if ( to ) {
				$body.addClass( 'et_vertical_nav' );

				if ( $body.hasClass( 'et_fixed_nav' ) ) {
					$body.removeClass( 'et_fixed_nav' ).addClass( 'et_fixed_nav_temp' );
				}
			} else {
				$body.find( '#main-header' ).removeClass( '.et-fixed-header' );

				$body.removeClass( 'et_vertical_nav' );

				if ( $body.hasClass( 'et_fixed_nav_temp' ) ) {
					$body.removeClass( 'et_fixed_nav_temp' ).addClass( 'et_fixed_nav' );
				}

				et_fix_page_top_padding();
			}
		} );
	} );

	wp.customize( 'et_divi[color_schemes]', function( value ) {
		value.bind( function( to ) {
			var $body = $( 'body' ),
				body_classes = $body.attr( 'class' ),
				et_customizer_color_scheme_prefix = 'et_color_scheme_',
				body_class;

			body_class = body_classes.replace( /et_color_scheme_[^\s]+/, '' );
			$body.attr( 'class', $.trim( body_class ) );

			if ( 'none' !== to  )
				$body.addClass( et_customizer_color_scheme_prefix + to );
		} );
	} );
} )( jQuery );