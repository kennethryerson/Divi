<?php

if ( ! isset( $content_width ) ) $content_width = 1080;

function et_setup_theme() {
	global $themename, $shortname, $et_store_options_in_one_row, $default_colorscheme;
	$themename = 'Divi';
	$shortname = 'divi';
	$et_store_options_in_one_row = true;

	$default_colorscheme = "Default";

	$template_directory = get_template_directory();

	require_once( $template_directory . '/epanel/custom_functions.php' );

	require_once( $template_directory . '/includes/functions/comments.php' );

	require_once( $template_directory . '/includes/functions/sidebars.php' );

	load_theme_textdomain( 'Divi', $template_directory . '/lang' );

	require_once( $template_directory . '/epanel/core_functions.php' );

	require_once( $template_directory . '/epanel/post_thumbnails_divi.php' );

	include( $template_directory . '/includes/widgets.php' );

	register_nav_menus( array(
		'primary-menu'   => __( 'Primary Menu', 'Divi' ),
		'secondary-menu' => __( 'Secondary Menu', 'Divi' ),
		'footer-menu'    => __( 'Footer Menu', 'Divi' ),
	) );

	// don't display the empty title bar if the widget title is not set
	remove_filter( 'widget_title', 'et_widget_force_title' );

	add_action( 'wp_enqueue_scripts', 'et_add_responsive_shortcodes_css', 11 );

	add_theme_support( 'post-formats', array(
		'video', 'audio', 'quote', 'gallery', 'link'
	) );

	add_theme_support( 'woocommerce' );

	remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

	remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
	add_action( 'woocommerce_before_main_content', 'et_divi_output_content_wrapper', 10 );

	remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
	add_action( 'woocommerce_after_main_content', 'et_divi_output_content_wrapper_end', 10 );

	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

	// deactivate page templates and custom import functions
	remove_action( 'init', 'et_activate_features' );
}
add_action( 'after_setup_theme', 'et_setup_theme' );

if ( ! function_exists( 'et_divi_fonts_url' ) ) :
function et_divi_fonts_url() {
	$fonts_url = '';

	/* Translators: If there are characters in your language that are not
	 * supported by Open Sans, translate this to 'off'. Do not translate
	 * into your own language.
	 */
	$open_sans = _x( 'on', 'Open Sans font: on or off', 'Divi' );

	if ( 'off' !== $open_sans ) {
		$font_families = array();

		if ( 'off' !== $open_sans )
			$font_families[] = 'Open+Sans:300italic,400italic,700italic,800italic,400,300,700,800';

		$protocol = is_ssl() ? 'https' : 'http';
		$query_args = array(
			'family' => implode( '%7C', $font_families ),
			'subset' => 'latin,latin-ext',
		);
		$fonts_url = add_query_arg( $query_args, "$protocol://fonts.googleapis.com/css" );
	}

	return $fonts_url;
}
endif;

function et_divi_load_fonts() {
	$fonts_url = et_divi_fonts_url();
	if ( ! empty( $fonts_url ) )
		wp_enqueue_style( 'divi-fonts', esc_url_raw( $fonts_url ), array(), null );
}
add_action( 'wp_enqueue_scripts', 'et_divi_load_fonts' );

function et_add_home_link( $args ) {
	// add Home link to the custom menu WP-Admin page
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'et_add_home_link' );

function et_divi_load_scripts_styles(){
	global $wp_styles;

	$template_dir = get_template_directory_uri();

	$theme_version = et_get_theme_version();

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

	wp_register_script( 'google-maps-api', add_query_arg( array( 'v' => 3, 'sensor' => 'false' ), is_ssl() ? 'https://maps-api-ssl.google.com/maps/api/js' : 'http://maps.google.com/maps/api/js' ), array(), $theme_version, true );
	wp_enqueue_script( 'divi-fitvids', $template_dir . '/js/jquery.fitvids.js', array( 'jquery' ), $theme_version, true );
	wp_enqueue_script( 'waypoints', $template_dir . '/js/waypoints.min.js', array( 'jquery' ), $theme_version, true );
	wp_enqueue_script( 'magnific-popup', $template_dir . '/js/jquery.magnific-popup.js', array( 'jquery' ), $theme_version, true );
	wp_register_script( 'hashchange', $template_dir . '/js/jquery.hashchange.js', array( 'jquery' ), $theme_version, true );
	wp_register_script( 'imagesloaded', $template_dir . '/js/imagesloaded.js', array( 'jquery' ), $theme_version, true );
	wp_register_script( 'jquery-masonry-3', $template_dir . '/js/masonry.js', array( 'jquery', 'imagesloaded' ), $theme_version, true );
	wp_register_script( 'easypiechart', $template_dir . '/js/jquery.easypiechart.js', array( 'jquery' ), $theme_version, true );
	wp_enqueue_script( 'divi-custom-script', $template_dir . '/js/custom.js', array( 'jquery' ), $theme_version, true );
	wp_localize_script( 'divi-custom-script', 'et_custom', array(
		'ajaxurl'             => admin_url( 'admin-ajax.php' ),
		'images_uri'                    => get_template_directory_uri() . '/images',
		'et_load_nonce'       => wp_create_nonce( 'et_load_nonce' ),
		'subscription_failed' => __( 'Please, check the fields below to make sure you entered the correct information.', 'Divi' ),
		'fill'                => esc_html__( 'Fill', 'Divi' ),
		'field'               => esc_html__( 'field', 'Divi' ),
		'invalid'             => esc_html__( 'Invalid email', 'Divi' ),
		'captcha'             => esc_html__( 'Captcha', 'Divi' ),
		'prev'				  => esc_html__( 'Prev', 'Divi' ),
		'next'				  => esc_html__( 'Next', 'Divi' ),
	) );

	if ( 'on' === et_get_option( 'divi_smooth_scroll', false ) ) {
		wp_enqueue_script( 'smooth-scroll', $template_dir . '/js/smoothscroll.js', array( 'jquery' ), $theme_version, true );
	}

	$et_gf_enqueue_fonts = array();
	$et_gf_heading_font = sanitize_text_field( et_get_option( 'heading_font', 'none' ) );
	$et_gf_body_font = sanitize_text_field( et_get_option( 'body_font', 'none' ) );

	if ( 'none' != $et_gf_heading_font ) $et_gf_enqueue_fonts[] = $et_gf_heading_font;
	if ( 'none' != $et_gf_body_font ) $et_gf_enqueue_fonts[] = $et_gf_body_font;

	if ( ! empty( $et_gf_enqueue_fonts ) ) et_gf_enqueue_fonts( $et_gf_enqueue_fonts );

	/*
	 * Loads the main stylesheet.
	 */
	wp_enqueue_style( 'divi-style', get_stylesheet_uri(), array(), $theme_version );
}
add_action( 'wp_enqueue_scripts', 'et_divi_load_scripts_styles' );

function et_add_mobile_navigation(){
	printf(
		'<div id="et_mobile_nav_menu">
			<a href="#" class="mobile_nav closed">
				<span class="select_page">%1$s</span>
				<span class="mobile_menu_bar"></span>
			</a>
		</div>',
		esc_html__( 'Select Page', 'Divi' )
	);
}
add_action( 'et_header_top', 'et_add_mobile_navigation' );

function et_add_viewport_meta(){
	echo '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />';
}
add_action( 'wp_head', 'et_add_viewport_meta' );

function et_remove_additional_stylesheet( $stylesheet ){
	global $default_colorscheme;
	return $default_colorscheme;
}
add_filter( 'et_get_additional_color_scheme', 'et_remove_additional_stylesheet' );

if ( ! function_exists( 'et_list_pings' ) ) :
function et_list_pings($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment; ?>
	<li id="comment-<?php comment_ID(); ?>"><?php comment_author_link(); ?> - <?php comment_excerpt(); ?>
<?php }
endif;

if ( ! function_exists( 'et_get_theme_version' ) ) :
function et_get_theme_version() {
	$theme_info = wp_get_theme();

	if ( is_child_theme() ) {
		$theme_info = wp_get_theme( $theme_info->parent_theme );
	}

	$theme_version = $theme_info->display( 'Version' );

	return $theme_version;
}
endif;

if ( ! function_exists( 'et_get_the_author_posts_link' ) ) :
function et_get_the_author_posts_link(){
	global $authordata, $themename;

	$link = sprintf(
		'<a href="%1$s" title="%2$s" rel="author">%3$s</a>',
		esc_url( get_author_posts_url( $authordata->ID, $authordata->user_nicename ) ),
		esc_attr( sprintf( __( 'Posts by %s', $themename ), get_the_author() ) ),
		get_the_author()
	);
	return apply_filters( 'the_author_posts_link', $link );
}
endif;

if ( ! function_exists( 'et_get_comments_popup_link' ) ) :
function et_get_comments_popup_link( $zero = false, $one = false, $more = false ){
	global $themename;

	$id = get_the_ID();
	$number = get_comments_number( $id );

	if ( 0 == $number && !comments_open() && !pings_open() ) return;

	if ( $number > 1 )
		$output = str_replace('%', number_format_i18n($number), ( false === $more ) ? __('% Comments', $themename) : $more);
	elseif ( $number == 0 )
		$output = ( false === $zero ) ? __('No Comments',$themename) : $zero;
	else // must be one
		$output = ( false === $one ) ? __('1 Comment', $themename) : $one;

	return '<span class="comments-number">' . '<a href="' . esc_url( get_permalink() . '#respond' ) . '">' . apply_filters('comments_number', $output, $number) . '</a>' . '</span>';
}
endif;

if ( ! function_exists( 'et_postinfo_meta' ) ) :
function et_postinfo_meta( $postinfo, $date_format, $comment_zero, $comment_one, $comment_more ){
	global $themename;

	$postinfo_meta = '';

	if ( in_array( 'author', $postinfo ) )
		$postinfo_meta .= ' ' . esc_html__('by',$themename) . ' ' . et_get_the_author_posts_link();

	if ( in_array( 'date', $postinfo ) ) {
		if ( in_array( 'author', $postinfo ) ) $postinfo_meta .= ' | ';
		$postinfo_meta .= get_the_time( $date_format );
	}

	if ( in_array( 'categories', $postinfo ) ){
		if ( in_array( 'author', $postinfo ) || in_array( 'date', $postinfo ) ) $postinfo_meta .= ' | ';
		$postinfo_meta .= get_the_category_list(', ');
	}

	if ( in_array( 'comments', $postinfo ) ){
		if ( in_array( 'author', $postinfo ) || in_array( 'date', $postinfo ) || in_array( 'categories', $postinfo ) ) $postinfo_meta .= ' | ';
		$postinfo_meta .= et_get_comments_popup_link( $comment_zero, $comment_one, $comment_more );
	}

	echo $postinfo_meta;
}
endif;

function et_add_post_meta_box() {
	add_meta_box( 'et_settings_meta_box', __( 'ET Settings', 'Divi' ), 'et_single_settings_meta_box', 'page', 'side', 'high' );
	add_meta_box( 'et_settings_meta_box', __( 'ET Settings', 'Divi' ), 'et_single_settings_meta_box', 'post', 'side', 'high' );
	add_meta_box( 'et_settings_meta_box', __( 'ET Settings', 'Divi' ), 'et_single_settings_meta_box', 'product', 'side', 'high' );
	add_meta_box( 'et_settings_meta_box', __( 'ET Settings', 'Divi' ), 'et_single_settings_meta_box', 'project', 'side', 'high' );
}
add_action( 'add_meta_boxes', 'et_add_post_meta_box' );

function et_pb_register_posttypes() {
	$labels = array(
		'name'               => _x( 'Projects', 'project type general name', 'Divi' ),
		'singular_name'      => _x( 'Project', 'project type singular name', 'Divi' ),
		'add_new'            => _x( 'Add New', 'project item', 'Divi' ),
		'add_new_item'       => __( 'Add New Project', 'Divi' ),
		'edit_item'          => __( 'Edit Project', 'Divi' ),
		'new_item'           => __( 'New Project', 'Divi' ),
		'all_items'          => __( 'All Projects', 'Divi' ),
		'view_item'          => __( 'View Project', 'Divi' ),
		'search_items'       => __( 'Search Projects', 'Divi' ),
		'not_found'          => __( 'Nothing found', 'Divi' ),
		'not_found_in_trash' => __( 'Nothing found in Trash', 'Divi' ),
		'parent_item_colon'  => '',
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'can_export'         => true,
		'show_in_nav_menus'  => true,
		'query_var'          => true,
		'has_archive'        => true,
		'rewrite'            => apply_filters( 'et_project_posttype_rewrite_args', array(
			'feeds'      => true,
			'slug'       => 'project',
			'with_front' => false,
		) ),
		'capability_type'    => 'post',
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments', 'revisions', 'custom-fields' ),
	);

	register_post_type( 'project', apply_filters( 'et_project_posttype_args', $args ) );

	$labels = array(
		'name'              => _x( 'Categories', 'Project category name', 'Divi' ),
		'singular_name'     => _x( 'Category', 'Project category singular name', 'Divi' ),
		'search_items'      => __( 'Search Categories', 'Divi' ),
		'all_items'         => __( 'All Categories', 'Divi' ),
		'parent_item'       => __( 'Parent Category', 'Divi' ),
		'parent_item_colon' => __( 'Parent Category:', 'Divi' ),
		'edit_item'         => __( 'Edit Category', 'Divi' ),
		'update_item'       => __( 'Update Category', 'Divi' ),
		'add_new_item'      => __( 'Add New Category', 'Divi' ),
		'new_item_name'     => __( 'New Category Name', 'Divi' ),
		'menu_name'         => __( 'Categories', 'Divi' ),
	);

	register_taxonomy( 'project_category', array( 'project' ), array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
	) );

	$labels = array(
		'name'              => _x( 'Tags', 'Project Tag name', 'Divi' ),
		'singular_name'     => _x( 'Tag', 'Project tag singular name', 'Divi' ),
		'search_items'      => __( 'Search Tags', 'Divi' ),
		'all_items'         => __( 'All Tags', 'Divi' ),
		'parent_item'       => __( 'Parent Tag', 'Divi' ),
		'parent_item_colon' => __( 'Parent Tag:', 'Divi' ),
		'edit_item'         => __( 'Edit Tag', 'Divi' ),
		'update_item'       => __( 'Update Tag', 'Divi' ),
		'add_new_item'      => __( 'Add New Tag', 'Divi' ),
		'new_item_name'     => __( 'New Tag Name', 'Divi' ),
		'menu_name'         => __( 'Tags', 'Divi' ),
	);

	register_taxonomy( 'project_tag', array( 'project' ), array(
		'hierarchical'      => false,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
	) );


	$labels = array(
		'name'               => _x( 'Layouts', 'Layout type general name', 'Divi' ),
		'singular_name'      => _x( 'Layout', 'Layout type singular name', 'Divi' ),
		'add_new'            => _x( 'Add New', 'Layout item', 'Divi' ),
		'add_new_item'       => __( 'Add New Layout', 'Divi' ),
		'edit_item'          => __( 'Edit Layout', 'Divi' ),
		'new_item'           => __( 'New Layout', 'Divi' ),
		'all_items'          => __( 'All Layouts', 'Divi' ),
		'view_item'          => __( 'View Layout', 'Divi' ),
		'search_items'       => __( 'Search Layouts', 'Divi' ),
		'not_found'          => __( 'Nothing found', 'Divi' ),
		'not_found_in_trash' => __( 'Nothing found in Trash', 'Divi' ),
		'parent_item_colon'  => '',
	);

	$args = array(
		'labels'             => $labels,
		'public'             => false,
		'can_export'         => true,
		'query_var'          => false,
		'has_archive'        => false,
		'capability_type'    => 'post',
		'hierarchical'       => false,
		'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments', 'revisions', 'custom-fields' ),
	);

	register_post_type( 'et_pb_layout', apply_filters( 'et_pb_layout_args', $args ) );
}
add_action( 'init', 'et_pb_register_posttypes', 0 );

if ( ! function_exists( 'et_pb_portfolio_meta_box' ) ) :
function et_pb_portfolio_meta_box() { ?>
	<div class="et_project_meta">
		<strong class="et_project_meta_title"><?php echo esc_html__( 'Skills', 'Divi' ); ?></strong>
		<p><?php echo get_the_term_list( get_the_ID(), 'project_tag', '', ', ' ); ?></p>

		<strong class="et_project_meta_title"><?php echo esc_html__( 'Posted on', 'Divi' ); ?></strong>
		<p><?php echo get_the_date(); ?></p>
	</div>
<?php }
endif;

if ( ! function_exists( 'et_single_settings_meta_box' ) ) :
function et_single_settings_meta_box( $post ) {
	$post_id = get_the_ID();

	wp_nonce_field( basename( __FILE__ ), 'et_settings_nonce' );

	$page_layout = get_post_meta( $post_id, '_et_pb_page_layout', true );

	$side_nav = get_post_meta( $post_id, '_et_pb_side_nav', true );

	$page_layouts = array(
		'et_right_sidebar'   => __( 'Right Sidebar', 'Divi' ),
   		'et_left_sidebar'    => __( 'Left Sidebar', 'Divi' ),
   		'et_full_width_page' => __( 'Full Width', 'Divi' ),
	);

	$layouts        = array(
		'light' => __( 'Light', 'Divi' ),
		'dark'  => __( 'Dark', 'Divi' ),
	);
	$post_bg_color  = ( $bg_color = get_post_meta( $post_id, '_et_post_bg_color', true ) ) && '' !== $bg_color
		? $bg_color
		: '#ffffff';
	$post_use_bg_color = get_post_meta( $post_id, '_et_post_use_bg_color', true )
		? true
		: false;
	$post_bg_layout = ( $layout = get_post_meta( $post_id, '_et_post_bg_layout', true ) ) && '' !== $layout
		? $layout
		: 'light'; ?>

	<p class="et_pb_page_settings et_pb_page_layout_settings">
		<label for="et_pb_page_layout" style="display: block; font-weight: bold; margin-bottom: 5px;"><?php esc_html_e( 'Page Layout', 'Divi' ); ?>: </label>

		<select id="et_pb_page_layout" name="et_pb_page_layout">
		<?php
		foreach ( $page_layouts as $layout_value => $layout_name ) {
			printf( '<option value="%2$s"%3$s>%1$s</option>',
				esc_html( $layout_name ),
				esc_attr( $layout_value ),
				selected( $layout_value, $page_layout )
			);
		} ?>
		</select>
	</p>
	<p class="et_pb_page_settings et_pb_side_nav_settings" style="display: none;">
		<label for="et_pb_side_nav" style="display: block; font-weight: bold; margin-bottom: 5px;"><?php esc_html_e( 'Side Navigation', 'Divi' ); ?>: </label>

		<select id="et_pb_side_nav" name="et_pb_side_nav">
			<option value="off"<?php selected( 'off', $side_nav ); ?> >Off</option>
			<option value="on" <?php selected( 'on', $side_nav ); ?> >On</option>
		</select>
	</p>
<?php if ( in_array( $post->post_type, array( 'page', 'project' ) ) ) : ?>
	<p class="et_pb_page_settings" style="display: none;">
		<input type="hidden" id="et_pb_use_builder" name="et_pb_use_builder" value="<?php echo esc_attr( get_post_meta( $post_id, '_et_pb_use_builder', true ) ); ?>" />
		<textarea id="et_pb_old_content" name="et_pb_old_content"><?php echo esc_attr( get_post_meta( $post_id, '_et_pb_old_content', true ) ); ?></textarea>
	</p>
<?php endif; ?>

<?php if ( 'post' === $post->post_type ) : ?>
	<p class="et_divi_quote_settings et_divi_audio_settings et_divi_link_settings et_divi_format_setting">
		<label for="et_post_use_bg_color" style="display: block; font-weight: bold; margin-bottom: 5px;">
			<input name="et_post_use_bg_color" type="checkbox" id="et_post_use_bg_color" <?php checked( $post_use_bg_color ); ?> />
			<?php esc_html_e( 'Use Background Color', 'Divi' ); ?></label>
	</p>

	<p class="et_post_bg_color_setting et_divi_format_setting">
		<label for="et_post_bg_color" style="display: block; font-weight: bold; margin-bottom: 5px;"><?php esc_html_e( 'Background Color', 'Divi' ); ?>: </label>
		<input id="et_post_bg_color" name="et_post_bg_color" class="color-picker-hex" type="text" maxlength="7" placeholder="<?php esc_attr_e( 'Hex Value', 'Divi' ); ?>" value="<?php echo esc_attr( $post_bg_color ); ?>" data-default-color="#ffffff" />
	</p>

	<p class="et_divi_quote_settings et_divi_audio_settings et_divi_link_settings et_divi_format_setting">
		<label for="et_post_bg_layout" style="font-weight: bold; margin-bottom: 5px;"><?php esc_html_e( 'Text Color', 'Divi' ); ?>: </label>
		<select id="et_post_bg_layout" name="et_post_bg_layout">
	<?php
		foreach ( $layouts as $layout_name => $layout_title )
			printf( '<option value="%s"%s>%s</option>',
				esc_attr( $layout_name ),
				selected( $layout_name, $post_bg_layout, false ),
				esc_html( $layout_title )
			);
	?>
		</select>
	</p>
<?php endif;

}
endif;

function et_metabox_settings_save_details( $post_id, $post ){
	global $pagenow;

	if ( 'post.php' != $pagenow ) return $post_id;

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return $post_id;

	$post_type = get_post_type_object( $post->post_type );
	if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) )
		return $post_id;

	if ( ! isset( $_POST['et_settings_nonce'] ) || ! wp_verify_nonce( $_POST['et_settings_nonce'], basename( __FILE__ ) ) )
		return $post_id;

	if ( isset( $_POST['et_pb_page_layout'] ) ) {
		update_post_meta( $post_id, '_et_pb_page_layout', sanitize_text_field( $_POST['et_pb_page_layout'] ) );
	} else {
		delete_post_meta( $post_id, '_et_pb_page_layout' );
	}

	if ( isset( $_POST['et_pb_side_nav'] ) ) {
		update_post_meta( $post_id, '_et_pb_side_nav', sanitize_text_field( $_POST['et_pb_side_nav'] ) );
	} else {
		delete_post_meta( $post_id, '_et_pb_side_nav' );
	}

	if ( isset( $_POST['et_pb_use_builder'] ) ) {
		update_post_meta( $post_id, '_et_pb_use_builder', sanitize_text_field( $_POST['et_pb_use_builder'] ) );
	} else {
		delete_post_meta( $post_id, '_et_pb_use_builder' );
	}

	if ( isset( $_POST['et_pb_old_content'] ) ) {
		update_post_meta( $post_id, '_et_pb_old_content', $_POST['et_pb_old_content'] );
	} else {
		delete_post_meta( $post_id, '_et_pb_old_content' );
	}

	if ( isset( $_POST['et_post_use_bg_color'] ) )
		update_post_meta( $post_id, '_et_post_use_bg_color', true );
	else
		delete_post_meta( $post_id, '_et_post_use_bg_color' );

	if ( isset( $_POST['et_post_bg_color'] ) )
		update_post_meta( $post_id, '_et_post_bg_color', sanitize_text_field( $_POST['et_post_bg_color'] ) );
	else
		delete_post_meta( $post_id, '_et_post_bg_color' );

	if ( isset( $_POST['et_post_bg_layout'] ) )
		update_post_meta( $post_id, '_et_post_bg_layout', sanitize_text_field( $_POST['et_post_bg_layout'] ) );
	else
		delete_post_meta( $post_id, '_et_post_bg_layout' );
}
add_action( 'save_post', 'et_metabox_settings_save_details', 10, 2 );

function et_divi_customize_register( $wp_customize ) {
	$google_fonts = et_get_google_fonts();

	$font_choices = array();
	$font_choices['none'] = 'Default Theme Font';
	foreach ( $google_fonts as $google_font_name => $google_font_properties ) {
		$font_choices[ $google_font_name ] = $google_font_name;
	}

	$wp_customize->remove_section( 'title_tagline' );

	$wp_customize->add_section( 'et_divi_settings' , array(
		'title'		=> __( 'Theme Settings', 'Divi' ),
		'priority'	=> 40,
	) );

	$wp_customize->add_section( 'et_google_fonts' , array(
		'title'		=> __( 'Fonts', 'Divi' ),
		'priority'	=> 50,
	) );

	$wp_customize->add_section( 'et_color_schemes' , array(
		'title'       => __( 'Schemes', 'Divi' ),
		'priority'    => 60,
		'description' => __( 'Note: Color settings set above should be applied to the Default color scheme.', 'Divi' ),
	) );

	$wp_customize->add_setting( 'et_divi[link_color]', array(
		'default'		=> '#2EA3F2',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage'
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_divi[link_color]', array(
		'label'		=> __( 'Link Color', 'Divi' ),
		'section'	=> 'colors',
		'settings'	=> 'et_divi[link_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[font_color]', array(
		'default'		=> '#666666',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage'
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_divi[font_color]', array(
		'label'		=> __( 'Main Font Color', 'Divi' ),
		'section'	=> 'colors',
		'settings'	=> 'et_divi[font_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[accent_color]', array(
		'default'		=> '#2EA3F2',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage'
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_divi[accent_color]', array(
		'label'		=> __( 'Accent Color', 'Divi' ),
		'section'	=> 'colors',
		'settings'	=> 'et_divi[accent_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[footer_bg]', array(
		'default'		=> '#222222',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage'
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_divi[footer_bg]', array(
		'label'		=> __( 'Footer Background Color', 'Divi' ),
		'section'	=> 'colors',
		'settings'	=> 'et_divi[footer_bg]',
	) ) );

	$wp_customize->add_setting( 'et_divi[menu_link]', array(
		'default'		=> '#666666',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage'
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_divi[menu_link]', array(
		'label'		=> __( 'Menu Links Color', 'Divi' ),
		'section'	=> 'colors',
		'settings'	=> 'et_divi[menu_link]',
	) ) );

	$wp_customize->add_setting( 'et_divi[menu_link_active]', array(
		'default'		=> '#2EA3F2',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage'
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_divi[menu_link_active]', array(
		'label'		=> __( 'Active Menu Link Color', 'Divi' ),
		'section'	=> 'colors',
		'settings'	=> 'et_divi[menu_link_active]',
	) ) );

	$wp_customize->add_setting( 'et_divi[boxed_layout]', array(
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[boxed_layout]', array(
		'label'		=> __( 'Boxed Layout', 'Divi' ),
		'section'	=> 'et_divi_settings',
		'type'      => 'checkbox',
		'priority'  => 10,
	) );

	$wp_customize->add_setting( 'et_divi[cover_background]', array(
		'default'       => 'on',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[cover_background]', array(
		'label'		=> __( 'Stretch Background Image', 'Divi' ),
		'section'	=> 'et_divi_settings',
		'type'      => 'checkbox',
		'priority'  => 10,
	) );

	$wp_customize->add_setting( 'et_divi[vertical_nav]', array(
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[vertical_nav]', array(
		'label'		=> __( 'Vertical Navigation', 'Divi' ),
		'section'	=> 'et_divi_settings',
		'type'      => 'checkbox',
		'priority'  => 20,
	) );

	$wp_customize->add_setting( 'et_divi[show_header_social_icons]', array(
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[show_header_social_icons]', array(
		'label'		=> __( 'Show Social Icons in Header', 'Divi' ),
		'section'	=> 'et_divi_settings',
		'type'      => 'checkbox',
		'priority'  => 30,
	) );

	$wp_customize->add_setting( 'et_divi[show_footer_social_icons]', array(
		'default'       => 'on',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[show_footer_social_icons]', array(
		'label'		=> __( 'Show Social Icons in Footer', 'Divi' ),
		'section'	=> 'et_divi_settings',
		'type'      => 'checkbox',
		'priority'  => 40,
	) );

	$wp_customize->add_setting( 'et_divi[show_search_icon]', array(
		'default'       => 'on',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[show_search_icon]', array(
		'label'		=> __( 'Show Search Icon', 'Divi' ),
		'section'	=> 'et_divi_settings',
		'type'      => 'checkbox',
		'priority'  => 50,
	) );

	$wp_customize->add_setting( 'et_divi[header_style]', array(
		'default'       => 'left',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[header_style]', array(
		'label'		=> __( 'Header Style', 'Divi' ),
		'section'	=> 'et_divi_settings',
		'type'      => 'select',
		'choices'	=> array(
			'left'     => __( 'Default', 'Divi' ),
			'centered' => __( 'Centered', 'Divi' ),
		),
		'priority'  => 55,
	) );

	$wp_customize->add_setting( 'et_divi[phone_number]', array(
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[phone_number]', array(
		'label'		=> __( 'Phone Number', 'Divi' ),
		'section'	=> 'et_divi_settings',
		'type'      => 'text',
		'priority'  => 60,
	) );

	$wp_customize->add_setting( 'et_divi[header_email]', array(
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[header_email]', array(
		'label'		=> __( 'Email', 'Divi' ),
		'section'	=> 'et_divi_settings',
		'type'      => 'text',
		'priority'  => 70,
	) );

	$wp_customize->add_setting( 'et_divi[primary_nav_bg]', array(
		'default'		=> '#ffffff',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage'
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_divi[primary_nav_bg]', array(
		'label'		=> __( 'Primary Navigation Background', 'Divi' ),
		'section'	=> 'colors',
		'settings'	=> 'et_divi[primary_nav_bg]',
		'priority'  => 80,
	) ) );

	$wp_customize->add_setting( 'et_divi[primary_nav_text_color]', array(
		'default'       => 'dark',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[primary_nav_text_color]', array(
		'label'		=> __( 'Primary Navigation Text Color', 'Divi' ),
		'section'	=> 'et_divi_settings',
		'type'      => 'select',
		'choices'	=> array(
			'dark'  => __( 'Dark', 'Divi' ),
			'light' => __( 'Light', 'Divi' ),
		),
		'priority'  => 90,
	) );

	$wp_customize->add_setting( 'et_divi[secondary_nav_bg]', array(
		'default'		=> et_get_option( 'accent_color', '#2EA3F2' ),
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage'
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_divi[secondary_nav_bg]', array(
		'label'		=> __( 'Secondary Navigation Background', 'Divi' ),
		'section'	=> 'colors',
		'settings'	=> 'et_divi[secondary_nav_bg]',
		'priority'  => 100,
	) ) );

	$wp_customize->add_setting( 'et_divi[secondary_nav_text_color]', array(
		'default'       => 'light',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[secondary_nav_text_color]', array(
		'label'		=> __( 'Secondary Navigation Text Color', 'Divi' ),
		'section'	=> 'et_divi_settings',
		'type'      => 'select',
		'choices'	=> array(
			'dark'  => __( 'Dark', 'Divi' ),
			'light' => __( 'Light', 'Divi' ),
		),
		'priority'  => 110,
	) );

	$wp_customize->add_setting( 'et_divi[heading_font]', array(
		'default'		=> 'none',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage'
	) );

	$wp_customize->add_control( 'et_divi[heading_font]', array(
		'label'		=> __( 'Header Font', 'Divi' ),
		'section'	=> 'et_google_fonts',
		'settings'	=> 'et_divi[heading_font]',
		'type'		=> 'select',
		'choices'	=> $font_choices
	) );

	$wp_customize->add_setting( 'et_divi[body_font]', array(
		'default'		=> 'none',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage'
	) );

	$wp_customize->add_control( 'et_divi[body_font]', array(
		'label'		=> __( 'Body Font', 'Divi' ),
		'section'	=> 'et_google_fonts',
		'settings'	=> 'et_divi[body_font]',
		'type'		=> 'select',
		'choices'	=> $font_choices
	) );

	$wp_customize->add_setting( 'et_divi[color_schemes]', array(
		'default'		=> 'none',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage'
	) );

	$wp_customize->add_control( 'et_divi[color_schemes]', array(
		'label'		=> __( 'Color Schemes', 'Divi' ),
		'section'	=> 'et_color_schemes',
		'settings'	=> 'et_divi[color_schemes]',
		'type'		=> 'select',
		'choices'	=> array(
			'none'   => __( 'Default', 'Divi' ),
			'green'  => __( 'Green', 'Divi' ),
			'orange' => __( 'Orange', 'Divi' ),
			'pink'   => __( 'Pink', 'Divi' ),
			'red'    => __( 'Red', 'Divi' ),
		),
	) );
}
add_action( 'customize_register', 'et_divi_customize_register' );

function et_divi_customize_preview_js() {
	wp_enqueue_script( 'divi-customizer', get_template_directory_uri() . '/js/theme-customizer.js', array( 'customize-preview' ), false, true );
}
add_action( 'customize_preview_init', 'et_divi_customize_preview_js' );

function et_divi_add_customizer_css(){ ?>
	<style>
		a { color: <?php echo esc_html( et_get_option( 'link_color', '#2EA3F2' ) ); ?>; }

		body { color: <?php echo esc_html( et_get_option( 'font_color', '#666666' ) ); ?>; }

		.et_pb_counter_amount, .et_pb_featured_table .et_pb_pricing_heading, .et_quote_content, .et_link_content, .et_audio_content { background-color: <?php echo esc_html( et_get_option( 'accent_color', '#2EA3F2' ) ); ?>; }

		#main-header, #main-header .nav li ul, .et-search-form, #main-header .et_mobile_menu { background-color: <?php echo esc_html( et_get_option( 'primary_nav_bg', '#ffffff' ) ); ?>; }

		#top-header, #et-secondary-nav li ul { background-color: <?php echo esc_html( et_get_option( 'secondary_nav_bg', et_get_option( 'accent_color', '#2EA3F2' ) ) ); ?>; }

		.woocommerce a.button.alt, .woocommerce-page a.button.alt, .woocommerce button.button.alt, .woocommerce-page button.button.alt, .woocommerce input.button.alt, .woocommerce-page input.button.alt, .woocommerce #respond input#submit.alt, .woocommerce-page #respond input#submit.alt, .woocommerce #content input.button.alt, .woocommerce-page #content input.button.alt, .woocommerce a.button, .woocommerce-page a.button, .woocommerce button.button, .woocommerce-page button.button, .woocommerce input.button, .woocommerce-page input.button, .woocommerce #respond input#submit, .woocommerce-page #respond input#submit, .woocommerce #content input.button, .woocommerce-page #content input.button, .woocommerce-message, .woocommerce-error, .woocommerce-info { background: <?php echo esc_html( et_get_option( 'accent_color', '#2EA3F2' ) ); ?> !important; }

		#et_search_icon:hover, .mobile_menu_bar:before, .footer-widget h4, .et-social-icon a:hover, .comment-reply-link, .form-submit input, .et_pb_sum, .et_pb_pricing li a, .et_pb_pricing_table_button, .et_overlay:before, .entry-summary p.price ins, .woocommerce div.product span.price, .woocommerce-page div.product span.price, .woocommerce #content div.product span.price, .woocommerce-page #content div.product span.price, .woocommerce div.product p.price, .woocommerce-page div.product p.price, .woocommerce #content div.product p.price, .woocommerce-page #content div.product p.price, .et_pb_member_social_links a:hover { color: <?php echo esc_html( et_get_option( 'accent_color', '#2EA3F2' ) ); ?> !important; }

		.woocommerce .star-rating span:before, .woocommerce-page .star-rating span:before, .et_pb_widget li a:hover, .et_pb_bg_layout_light .et_pb_promo_button, .et_pb_bg_layout_light .et_pb_more_button, .et_pb_filterable_portfolio .et_pb_portfolio_filters li a.active, .et_pb_filterable_portfolio .et_pb_portofolio_pagination ul li a.active, .et_pb_gallery .et_pb_gallery_pagination ul li a.active, .wp-pagenavi span.current, .wp-pagenavi a:hover, .et_pb_contact_submit, .et_pb_bg_layout_light .et_pb_newsletter_button, .nav-single a, .posted_in a { color: <?php echo esc_html( et_get_option( 'accent_color', '#2EA3F2' ) ); ?> !important; }

		.et-search-form, .nav li ul, .et_mobile_menu, .footer-widget li:before, .et_pb_pricing li:before, blockquote { border-color: <?php echo esc_html( et_get_option( 'accent_color', '#2EA3F2' ) ); ?>; }

		#main-footer { background-color: <?php echo esc_html( et_get_option( 'footer_bg', '#222222' ) ); ?>; }

		#top-menu a { color: <?php echo esc_html( et_get_option( 'menu_link', '#666666' ) ); ?>; }

		#top-menu li.current-menu-ancestor > a, #top-menu li.current-menu-item > a, .bottom-nav li.current-menu-item > a { color: <?php echo esc_html( et_get_option( 'menu_link_active', '#2EA3F2' ) ); ?>; }

	<?php
		$et_gf_heading_font = sanitize_text_field( et_get_option( 'heading_font', 'none' ) );
		$et_gf_body_font = sanitize_text_field( et_get_option( 'body_font', 'none' ) );

		if ( 'none' != $et_gf_heading_font || 'none' != $et_gf_body_font ) :

			if ( 'none' != $et_gf_heading_font )
				et_gf_attach_font( $et_gf_heading_font, 'h1, h2, h3, h4, h5, h6' );

			if ( 'none' != $et_gf_body_font )
				et_gf_attach_font( $et_gf_body_font, 'body, input, textarea, select' );

		endif;
	?>
	</style>
<?php }
add_action( 'wp_head', 'et_divi_add_customizer_css' );
add_action( 'customize_controls_print_styles', 'et_divi_add_customizer_css' );

/*
 * Adds color scheme class to the body tag
 */
function et_customizer_color_scheme_class( $body_class ) {
	$color_scheme        = et_get_option( 'color_schemes', 'none' );
	$color_scheme_prefix = 'et_color_scheme_';

	if ( 'none' !== $color_scheme ) $body_class[] = $color_scheme_prefix . $color_scheme;

	return $body_class;
}
add_filter( 'body_class', 'et_customizer_color_scheme_class' );

function et_load_google_fonts_scripts() {
	wp_enqueue_script( 'et_google_fonts', get_template_directory_uri() . '/epanel/google-fonts/et_google_fonts.js', array( 'jquery' ), '1.0', true );
}
add_action( 'customize_controls_print_footer_scripts', 'et_load_google_fonts_scripts' );

function et_load_google_fonts_styles() {
	wp_enqueue_style( 'et_google_fonts_style', get_template_directory_uri() . '/epanel/google-fonts/et_google_fonts.css', array(), null );
}
add_action( 'customize_controls_print_styles', 'et_load_google_fonts_styles' );

if ( ! function_exists( 'et_divi_post_meta' ) ) :
function et_divi_post_meta() {
	$postinfo = is_single() ? et_get_option( 'divi_postinfo2' ) : et_get_option( 'divi_postinfo1' );

	if ( $postinfo ) :
		echo '<p class="post-meta">';
		et_postinfo_meta( $postinfo, et_get_option( 'divi_date_format', 'M j, Y' ), esc_html__( '0 comments', 'Divi' ), esc_html__( '1 comment', 'Divi' ), '% ' . esc_html__( 'comments', 'Divi' ) );
		echo '</p>';
	endif;
}
endif;

/**
 * Extract and return the first blockquote from content.
 */
if ( ! function_exists( 'et_get_blockquote_in_content' ) ) :
function et_get_blockquote_in_content() {
	global $more;
	$more_default = $more;
	$more = 1;

	remove_filter( 'the_content', 'et_remove_blockquote_from_content' );

	$content = apply_filters( 'the_content', get_the_content() );

	add_filter( 'the_content', 'et_remove_blockquote_from_content' );

	$more = $more_default;

	if ( preg_match( '/<blockquote>(.+?)<\/blockquote>/is', $content, $matches ) ) {
		return $matches[0];
	} else {
		return false;
	}
}
endif;

function et_remove_blockquote_from_content( $content ) {
	if ( 'quote' !== get_post_format() ) {
		return $content;
	}

	$content = preg_replace( '/<blockquote>(.+?)<\/blockquote>/is', '', $content, 1 );

	return $content;
}
add_filter( 'the_content', 'et_remove_blockquote_from_content' );

if ( ! function_exists( 'et_get_link_url' ) ) :
function et_get_link_url() {
	if ( '' !== ( $link_url = get_post_meta( get_the_ID(), '_format_link_url', true ) ) ) {
		return $link_url;
	}

	$content = get_the_content();
	$has_url = get_url_in_content( $content );

	return ( $has_url ) ? $has_url : apply_filters( 'the_permalink', get_permalink() );
}
endif;

function et_video_embed_html( $video ) {
	if ( is_single() && 'video' === get_post_format() ) {
		static $post_video_num = 0;

		$post_video_num++;

		// Hide first video in the post content on single video post page
		if ( 1 === $post_video_num ) {
			return '';
		}
	}

	return "<div class='et_post_video'>{$video}</div>";
}
add_filter( 'embed_oembed_html', 'et_video_embed_html' );

/**
 * Removes galleries on single gallery posts, since we display images from all
 * galleries on top of the page
 */
function et_delete_post_gallery( $content ) {
	if ( is_single() && is_main_query() && has_post_format( 'gallery' ) ) :
		$regex = get_shortcode_regex();
		preg_match_all( "/{$regex}/s", $content, $matches );

		// $matches[2] holds an array of shortcodes names in the post
		foreach ( $matches[2] as $key => $shortcode_match ) {
			if ( 'gallery' === $shortcode_match )
				$content = str_replace( $matches[0][$key], '', $content );
		}
	endif;

	return $content;
}
add_filter( 'the_content', 'et_delete_post_gallery' );

if ( ! function_exists( 'et_gallery_images' ) ) :
function et_gallery_images() {
	$output = $images_ids = '';

	if ( function_exists( 'get_post_galleries' ) ) {
		$galleries = get_post_galleries( get_the_ID(), false );

		if ( empty( $galleries ) ) return false;

		foreach ( $galleries as $gallery ) {
			// Grabs all attachments ids from one or multiple galleries in the post
			$images_ids .= ( '' !== $images_ids ? ',' : '' ) . $gallery['ids'];
		}

		$attachments_ids = explode( ',', $images_ids );
		// Removes duplicate attachments ids
		$attachments_ids = array_unique( $attachments_ids );
	} else {
		$pattern = get_shortcode_regex();
		preg_match( "/$pattern/s", get_the_content(), $match );
		$atts = shortcode_parse_atts( $match[3] );

		if ( isset( $atts['ids'] ) )
			$attachments_ids = explode( ',', $atts['ids'] );
		else
			return false;
	}

	$slides = '';

	foreach ( $attachments_ids as $attachment_id ) {
		$attachment_attributes = wp_get_attachment_image_src( $attachment_id, 'et-pb-post-main-image-fullwidth' );
		$attachment_image = ! is_single() ? $attachment_attributes[0] : wp_get_attachment_image( $attachment_id, 'et-pb-portfolio-image' );

		if ( ! is_single() ) {
			$slides .= sprintf(
				'<div class="et_pb_slide" style="background: url(%1$s);"></div>',
				esc_attr( $attachment_image )
			);
		} else {
			$full_image = wp_get_attachment_image_src( $attachment_id, 'full' );
			$full_image_url = $full_image[0];
			$attachment = get_post( $attachment_id );

			$slides .= sprintf(
				'<li class="et_gallery_item">
					<a href="%1$s" title="%3$s">
						<span class="et_portfolio_image">
							%2$s
							<span class="et_overlay"></span>
						</span>
					</a>
				</li>',
				esc_url( $full_image_url ),
				$attachment_image,
				esc_attr( $attachment->post_title )
			);
		}
	}

	if ( ! is_single() ) {
		$output =
			'<div class="et_pb_slider et_pb_slider_fullwidth_off">
				<div class="et_pb_slides">
					%1$s
				</div>
			</div>';
	} else {
		$output =
			'<ul class="et_post_gallery clearfix">
				%1$s
			</ul>';
	}

	printf( $output, $slides );
}
endif;

if ( ! function_exists( 'et_get_first_video' ) ) :
function et_get_first_video() {
	$first_oembed  = '';
	$custom_fields = get_post_custom();

	foreach ( $custom_fields as $key => $custom_field ) {
		if ( 0 !== strpos( $key, '_oembed_' ) ) {
			continue;
		}

		$first_oembed = $custom_field[0];

		$video_width  = (int) apply_filters( 'et_blog_video_width', 1080 );
		$video_height = (int) apply_filters( 'et_blog_video_height', 630 );

		$first_oembed = preg_replace( '/<embed /', '<embed wmode="transparent" ', $first_oembed );
		$first_oembed = preg_replace( '/<\/object>/','<param name="wmode" value="transparent" /></object>', $first_oembed );

		$first_oembed = preg_replace( "/width=\"[0-9]*\"/", "width={$video_width}", $first_oembed );
		$first_oembed = preg_replace( "/height=\"[0-9]*\"/", "height={$video_height}", $first_oembed );

		break;
	}

	return ( '' !== $first_oembed ) ? $first_oembed : false;
}
endif;

function et_divi_post_admin_scripts_styles( $hook ) {
	global $typenow;

	if ( ! in_array( $hook, array( 'post-new.php', 'post.php' ) ) ) return;

	if ( ! isset( $typenow ) ) return;

	if ( in_array( $typenow, array( 'post' ) ) ) {
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'et-admin-post-script', get_template_directory_uri() . '/js/admin_post_settings.js', array( 'jquery' ) );
	}
}
add_action( 'admin_enqueue_scripts', 'et_divi_post_admin_scripts_styles' );

function et_add_wp_version( $classes ) {
	global $wp_version;

	// add 'et-wp-pre-3_8' class if the current WordPress version is less than 3.8
	if ( version_compare( $wp_version, '3.7.2', '<=' ) ) {
		if ( 'body_class' === current_filter() )
			$classes[] = 'et-wp-pre-3_8';
		else
			$classes = 'et-wp-pre-3_8';
	} else {
		if ( 'admin_body_class' === current_filter() )
			$classes = 'et-wp-after-3_8';
	}

	return $classes;
}
add_filter( 'body_class', 'et_add_wp_version' );
add_filter( 'admin_body_class', 'et_add_wp_version' );

function et_layout_body_class( $classes ) {
	if ( true === et_get_option( 'vertical_nav', false ) ) {
		$classes[] = 'et_vertical_nav';
	} else if ( 'on' === et_get_option( 'divi_fixed_nav', 'on' ) ) {
		$classes[] = 'et_fixed_nav';
	}

	if ( true === et_get_option( 'boxed_layout', false ) ) {
		$classes[] = 'et_boxed_layout';
	}

	if ( true === et_get_option( 'cover_background', true ) ) {
		$classes[] = 'et_cover_background';
	}

	$et_secondary_nav_items = et_divi_get_top_nav_items();

	if ( $et_secondary_nav_items->top_info_defined ) {
		$classes[] = 'et_secondary_nav_enabled';
	}

	if ( $et_secondary_nav_items->two_info_panels ) {
		$classes[] = 'et_secondary_nav_two_panels';
	}

	if ( $et_secondary_nav_items->secondary_nav && ! ( $et_secondary_nav_items->contact_info_defined || $et_secondary_nav_items->show_header_social_icons ) ) {
		$classes[] = 'et_secondary_nav_only_menu';
	}

	if ( 'left' !== ( $header_style = et_get_option( 'header_style', 'left' ) ) ) {
		$classes[] = esc_attr( "et_header_style_{$header_style}" );
	}

	if ( ( is_page() || is_singular( 'project' ) ) && 'on' == get_post_meta( get_the_ID(), '_et_pb_side_nav', true ) && et_pb_is_pagebuilder_used( get_the_ID() ) ) {
		$classes[] = 'et_pb_side_nav_page';
	}

	if ( stristr( $_SERVER['HTTP_USER_AGENT'],"mac") ) {
	    $classes[] = 'osx';
	} elseif ( stristr( $_SERVER['HTTP_USER_AGENT'],"linux") ) {
	    $classes[] = 'linux';
	} elseif ( stristr( $_SERVER['HTTP_USER_AGENT'],"windows") ) {
	    $classes[] = 'windows';
	}

	return $classes;
}
add_filter( 'body_class', 'et_layout_body_class' );

if ( ! function_exists( 'et_show_cart_total' ) ) {
	function et_show_cart_total( $args = array() ) {
		if ( ! class_exists( 'woocommerce' ) ) {
			return;
		}

		$defaults = array(
			'no_text' => false,
		);

		$args = wp_parse_args( $args, $defaults );

		printf(
			'<a href="%1$s" class="et-cart-info">
				<span>%2$s</span>
			</a>',
			esc_url( WC()->cart->get_cart_url() ),
			( ! $args['no_text']
				? sprintf(
				__( '%1$s %2$s' ),
				esc_html( WC()->cart->get_cart_contents_count() ),
				( 1 === WC()->cart->get_cart_contents_count() ? __( 'Item', 'Divi' ) : __( 'Items', 'Divi' ) )
				)
				: ''
			)
		);
	}
}

if ( ! function_exists( 'et_divi_get_top_nav_items' ) ) {
	function et_divi_get_top_nav_items() {
		$items = new stdClass;

		$items->phone_number = et_get_option( 'phone_number' );

		$items->email = et_get_option( 'header_email' );

		$items->contact_info_defined = $items->phone_number || $items->email;

		$items->show_header_social_icons = et_get_option( 'show_header_social_icons', false );

		$items->secondary_nav = wp_nav_menu( array(
			'theme_location' => 'secondary-menu',
			'container'      => '',
			'fallback_cb'    => '',
			'menu_id'        => 'et-secondary-nav',
			'echo'           => false,
		) );

		$items->top_info_defined = $items->contact_info_defined || $items->show_header_social_icons || $items->secondary_nav;

		$items->two_info_panels = $items->contact_info_defined && ( $items->show_header_social_icons || $items->secondary_nav );

		return $items;
	}
}

function et_divi_activate_features(){
	/* activate shortcodes */
	require_once( get_template_directory() . '/epanel/shortcodes/shortcodes.php' );
}
add_action( 'init', 'et_divi_activate_features' );

require_once( get_template_directory() . '/et-pagebuilder/et-pagebuilder.php' );

function et_divi_sidebar_class( $classes ) {
	if ( ( is_page() || is_singular( 'project' ) ) && et_pb_is_pagebuilder_used( get_the_ID() ) )
		$classes[] = 'et_pb_pagebuilder_layout';

	if ( is_single() || is_page() || ( class_exists( 'woocommerce' ) && is_product() ) )
		$page_layout = '' !== ( $layout = get_post_meta( get_the_ID(), '_et_pb_page_layout', true ) )
			? $layout
			: 'et_right_sidebar';

	if ( class_exists( 'woocommerce' ) && ( is_shop() || is_product() || is_product_category() || is_product_tag() ) ) {
		if ( is_shop() || is_tax() )
			$classes[] = et_get_option( 'divi_shop_page_sidebar', 'et_right_sidebar' );
		if ( is_product() )
			$classes[] = $page_layout;
	}

	else if ( is_archive() || is_home() || is_search() || is_404() ) {
		$classes[] = 'et_right_sidebar';
	}

	else if ( is_singular( 'project' ) ) {
		if ( 'et_full_width_page' === $page_layout )
			$page_layout = 'et_right_sidebar et_full_width_portfolio_page';

		$classes[] = $page_layout;
	}

	else if ( is_single() || is_page() ) {
		$classes[] = $page_layout;
	}

	return $classes;
}
add_filter( 'body_class', 'et_divi_sidebar_class' );

function et_modify_shop_page_columns_num( $columns_num ) {
	if ( class_exists( 'woocommerce' ) && is_shop() ) {
		$columns_num = 'et_full_width_page' !== et_get_option( 'divi_shop_page_sidebar', 'et_right_sidebar' )
			? 3
			: 4;
	}

	return $columns_num;
}
add_filter( 'loop_shop_columns', 'et_modify_shop_page_columns_num' );

// WooCommerce

global $pagenow;
if ( is_admin() && isset( $_GET['activated'] ) && $pagenow == 'themes.php' )
	add_action( 'init', 'et_divi_woocommerce_image_dimensions', 1 );

/**
 * Default values for WooCommerce images changed in version 1.3
 * Checks if WooCommerce image dimensions have been updated already.
 */
function et_divi_check_woocommerce_images() {
	if ( 'checked' === et_get_option( 'divi_1_3_images' ) ) return;

	et_divi_woocommerce_image_dimensions();
	et_update_option( 'divi_1_3_images', 'checked' );
}
add_action( 'admin_init', 'et_divi_check_woocommerce_images' );

function et_divi_woocommerce_image_dimensions() {
  	$catalog = array(
		'width' 	=> '400',
		'height'	=> '400',
		'crop'		=> 1,
	);

	$single = array(
		'width' 	=> '510',
		'height'	=> '9999',
		'crop'		=> 0,
	);

	$thumbnail = array(
		'width' 	=> '157',
		'height'	=> '157',
		'crop'		=> 1,
	);

	update_option( 'shop_catalog_image_size', $catalog );
	update_option( 'shop_single_image_size', $single );
	update_option( 'shop_thumbnail_image_size', $thumbnail );
}

function woocommerce_template_loop_product_thumbnail() {
	printf( '<span class="et_shop_image">%1$s<span class="et_overlay"></span></span>',
		woocommerce_get_product_thumbnail()
	);
}

function et_review_gravatar_size( $size ) {
	return '80';
}
add_filter( 'woocommerce_review_gravatar_size', 'et_review_gravatar_size' );


function et_divi_output_content_wrapper() {
	echo '
		<div id="main-content">
			<div class="container">
				<div id="content-area" class="clearfix">
					<div id="left-area">';
}

function et_divi_output_content_wrapper_end() {
	echo '</div> <!-- #left-area -->';

	if (
		( is_product() && 'et_full_width_page' !== get_post_meta( get_the_ID(), '_et_pb_page_layout', true ) )
		||
		( ( is_shop() || is_product_category() || is_product_tag() ) && 'et_full_width_page' !== et_get_option( 'divi_shop_page_sidebar', 'et_right_sidebar' ) )
	) {
		woocommerce_get_sidebar();
	}

	echo '
				</div> <!-- #content-area -->
			</div> <!-- .container -->
		</div> <!-- #main-content -->';
}

function et_aweber_authorization_option() {
	wp_enqueue_script( 'divi-advanced-options', get_template_directory_uri() . '/js/advanced_options.js', array( 'jquery' ), '1.0', true );
	wp_localize_script( 'divi-advanced-options', 'et_advanced_options', array(
		'et_load_nonce'            => wp_create_nonce( 'et_load_nonce' ),
		'aweber_connecting'        => __( 'Connecting...', 'Divi' ),
		'aweber_failed'            => __( 'Connection failed', 'Divi' ),
		'aweber_remove_connection' => __( 'Removing connection...', 'Divi' ),
		'aweber_done'              => __( 'Done', 'Divi' ),
	) );
	wp_enqueue_style( 'divi-advanced-options', get_template_directory_uri() . '/css/advanced_options.css' );

	$app_id = 'b17f3351';

	$aweber_auth_endpoint = 'https://auth.aweber.com/1.0/oauth/authorize_app/' . $app_id;

	$hide_style = ' style="display: none;"';

	$aweber_connection_established = et_get_option( 'divi_aweber_consumer_key', false ) && et_get_option( 'divi_aweber_consumer_secret', false ) && et_get_option( 'divi_aweber_access_key', false ) && et_get_option( 'divi_aweber_access_secret', false );

	$output = sprintf(
		'<div id="et_aweber_connection">
			<ul id="et_aweber_authorization"%4$s>
				<li>%1$s</li>
				<li>
					<p>%2$s</p>
					<p><textarea id="et_aweber_authentication_code" name="et_aweber_authentication_code"></textarea></p>

					<p><button class="et_make_connection button button-primary button-large">%3$s</button></p>
				</li>
			</ul>

			<div id="et_aweber_remove_connection"%5$s>
				<p>%6$s</p>
				<p><button class="et_remove_connection button button-primary button-large">%7$s</button></p>
			</div>
		</div>',
		sprintf( __( 'Step 1: <a href="%1$s" target="_blank">Generate authorization code</a>', 'Divi' ), esc_url( $aweber_auth_endpoint ) ),
		__( 'Step 2: Paste in the authorization code and click "Make a connection" button: ', 'Divi' ),
		__( 'Make a connection', 'Divi' ),
		( $aweber_connection_established ? $hide_style : ''  ),
		( ! $aweber_connection_established ? $hide_style : ''  ),
		__( 'Aweber is set up properly. You can remove connection here if you wish.', 'Divi' ),
		__( 'Remove the connection', 'Divi' )
	);

	echo $output;
}

function et_aweber_submit_authorization_code() {
	if ( ! wp_verify_nonce( $_POST['et_load_nonce'], 'et_load_nonce' ) ) {
		die( __( 'Nonce failed.', 'Divi' ) );
	}

	$et_authorization_code = $_POST['et_authorization_code'];

	if ( '' === $et_authorization_code ) {
		die( __( 'Authorization code is empty.', 'Divi' ) );
	}

	if ( ! class_exists( 'AWeberAPI' ) ) {
		require_once( get_template_directory() . '/includes/subscription/aweber/aweber_api.php' );
	}

	try {
		$auth = AWeberAPI::getDataFromAweberID( $et_authorization_code );

		if ( ! ( is_array( $auth ) && 4 === count( $auth ) ) ) {
			die ( __( 'Authorization code is invalid. Try regenerating it and paste in the new code.', 'Divi' ) );
		}

		list( $consumer_key, $consumer_secret, $access_key, $access_secret ) = $auth;

		et_update_option( 'divi_aweber_consumer_key', $consumer_key );
		et_update_option( 'divi_aweber_consumer_secret', $consumer_secret );
		et_update_option( 'divi_aweber_access_key', $access_key );
		et_update_option( 'divi_aweber_access_secret', $access_secret );

		die( 'success' );
	} catch ( AWeberAPIException $exc ) {
		printf(
			'<p>AWeberAPIException.</p>
			<ul>
				<li>Type: %1$s</li>
				<li>Message: %2$s</li>
				<li>Documentation: %3$s</li>
			</ul>',
			esc_html( $exc->type ),
			esc_html( $exc->message ),
			esc_html( $exc->documentation_url )
		);
	}

	die();
}
add_action( 'wp_ajax_et_aweber_submit_authorization_code', 'et_aweber_submit_authorization_code' );

function et_aweber_remove_connection() {
	if ( ! wp_verify_nonce( $_POST['et_load_nonce'], 'et_load_nonce' ) ) {
		die( __( 'Nonce failed', 'Divi' ) );
	}

	et_delete_option( 'divi_aweber_consumer_key' );
	et_delete_option( 'divi_aweber_consumer_secret' );
	et_delete_option( 'divi_aweber_access_key' );
	et_delete_option( 'divi_aweber_access_secret' );

	die( 'success' );
}
add_action( 'wp_ajax_et_aweber_remove_connection', 'et_aweber_remove_connection' );

if ( ! function_exists( 'et_pb_get_audio_player' ) ){
	function et_pb_get_audio_player(){
		$output = sprintf(
			'<div class="et_audio_container">
				%1$s
			</div> <!-- .et_audio_container -->',
			do_shortcode( '[audio]' )
		);

		return $output;
	}
}

if ( ! function_exists( 'et_divi_get_post_text_color' ) ) {
	function et_divi_get_post_text_color() {
		$text_color_class = '';

		$post_format = get_post_format();

		if ( in_array( $post_format, array( 'audio', 'link', 'quote' ) ) ) {
			$text_color_class = ( $text_color = get_post_meta( get_the_ID(), '_et_post_bg_layout', true ) ) ? $text_color : 'light';
			$text_color_class = ' et_pb_text_color_' . $text_color_class;
		}

		return $text_color_class;
	}
}

if ( ! function_exists( 'et_divi_get_post_bg_inline_style' ) ) {
	function et_divi_get_post_bg_inline_style() {
		$inline_style = '';

		$post_id = get_the_ID();

		$post_use_bg_color = get_post_meta( $post_id, '_et_post_use_bg_color', true )
			? true
			: false;
		$post_bg_color  = ( $bg_color = get_post_meta( $post_id, '_et_post_bg_color', true ) ) && '' !== $bg_color
			? $bg_color
			: '#ffffff';

		if ( $post_use_bg_color ) {
			$inline_style = sprintf( ' style="background-color: %1$s;"', esc_html( $post_bg_color ) );
		}

		return $inline_style;
	}
}

/*
 * Displays post audio, quote and link post formats content
 */
if ( ! function_exists( 'et_divi_post_format_content' ) ){
	function et_divi_post_format_content(){
		$post_format = get_post_format();

		$text_color_class = et_divi_get_post_text_color();

		$inline_style = et_divi_get_post_bg_inline_style();

		switch ( $post_format ) {
			case 'audio' :
				printf(
					'<div class="et_audio_content%4$s"%5$s>
						<h2><a href="%3$s">%1$s</a></h2>
						%2$s
					</div> <!-- .et_audio_content -->',
					get_the_title(),
					et_pb_get_audio_player(),
					esc_url( get_permalink() ),
					esc_attr( $text_color_class ),
					$inline_style
				);

				break;
			case 'quote' :
				printf(
					'<div class="et_quote_content%4$s"%5$s>
						%1$s
						<a href="%2$s" class="et_quote_main_link">%3$s</a>
					</div> <!-- .et_quote_content -->',
					et_get_blockquote_in_content(),
					esc_url( get_permalink() ),
					__( 'Read more', 'Divi' ),
					esc_attr( $text_color_class ),
					$inline_style
				);

				break;
			case 'link' :
				printf(
					'<div class="et_link_content%5$s"%6$s>
						<h2><a href="%2$s">%1$s</a></h2>
						<a href="%3$s" class="et_link_main_url">%4$s</a>
					</div> <!-- .et_link_content -->',
					get_the_title(),
					esc_url( get_permalink() ),
					esc_url( et_get_link_url() ),
					esc_html( et_get_link_url() ),
					esc_attr( $text_color_class ),
					$inline_style
				);

				break;
		}
	}
}


// Shortcodes

if ( ! function_exists( 'et_pb_fix_shortcodes' ) ){
	function et_pb_fix_shortcodes( $content ){
		$replace_tags_from_to = array (
			'<p>[' => '[',
			']</p>' => ']',
			']<br />' => ']',
			"<br />\n[" => '[',
		);

		return strtr( $content, $replace_tags_from_to );
	}
}

add_shortcode( 'et_pb_slider', 'et_pb_slider' );
add_shortcode( 'et_pb_fullwidth_slider', 'et_pb_slider' );
function et_pb_slider( $atts, $content = '', $function_name ) {
	extract( shortcode_atts( array(
			'module_id' => '',
			'module_class' => '',
			'show_arrows' => 'on',
			'show_pagination' => 'on',
			'parallax' => 'off',
			'parallax_method' => 'off',
			'auto' => 'off',
			'auto_speed' => '7000',
		), $atts
	) );

	global $et_pb_slider_has_video, $et_pb_slider_parallax, $et_pb_slider_parallax_method;

	$et_pb_slider_has_video = false;

	$et_pb_slider_parallax = $parallax;

	$et_pb_slider_parallax_method = $parallax_method;

	$fullwidth = 'et_pb_fullwidth_slider' === $function_name ? 'on' : 'off';

	$class  = '';
	$class .= 'off' === $fullwidth ? ' et_pb_slider_fullwidth_off' : '';
	$class .= 'off' === $show_arrows ? ' et_pb_slider_no_arrows' : '';
	$class .= 'off' === $show_pagination ? ' et_pb_slider_no_pagination' : '';
	$class .= 'on' === $parallax ? ' et_pb_slider_parallax' : '';
	$class .= 'on' === $auto ? ' et_slider_auto et_slider_speed_' . esc_attr( $auto_speed ) : '';

	$content = do_shortcode( et_pb_fix_shortcodes( $content ) );

	$output = sprintf(
		'<div%4$s class="et_pb_slider%1$s%3$s%5$s">
			<div class="et_pb_slides">
				%2$s
			</div> <!-- .et_pb_slides -->
		</div> <!-- .et_pb_slider -->
		',
		$class,
		$content,
		( $et_pb_slider_has_video ? ' et_pb_preload' : '' ),
		( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
		( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' )
	);

	return $output;
}

add_shortcode( 'et_pb_slide', 'et_pb_slide' );
function et_pb_slide( $atts, $content = '' ) {
	extract( shortcode_atts( array(
			'alignment' => 'center',
			'heading' => '',
			'button_text' => '',
			'button_link' => '#',
			'background_color' => '',
			'background_image' => '',
			'image' => '',
			'image_alt' => '',
			'background_layout' => 'dark',
			'video_bg_webm' => '',
			'video_bg_mp4' => '',
			'video_bg_width' => '',
			'video_bg_height' => '',
			'video_url' => '',
		), $atts
	) );

	global $et_pb_slider_has_video, $et_pb_slider_parallax, $et_pb_slider_parallax_method;

	$background_video = '';

	$first_video = false;

	if ( '' !== $video_bg_mp4 || '' !== $video_bg_webm ) {
		if ( ! $et_pb_slider_has_video )
			$first_video = true;

		$background_video = sprintf(
			'<div class="et_pb_section_video_bg%2$s">
				%1$s
			</div>',
			do_shortcode( sprintf( '
				<video loop="loop" autoplay="autoplay"%3$s%4$s>
					%1$s
					%2$s
				</video>',
				( '' !== $video_bg_mp4 ? sprintf( '<source type="video/mp4" src="%s" />', esc_attr( $video_bg_mp4 ) ) : '' ),
				( '' !== $video_bg_webm ? sprintf( '<source type="video/webm" src="%s" />', esc_attr( $video_bg_webm ) ) : '' ),
				( '' !== $video_bg_width ? sprintf( ' width="%s"', esc_attr( $video_bg_width ) ) : '' ),
				( '' !== $video_bg_height ? sprintf( ' height="%s"', esc_attr( $video_bg_height ) ) : '' ),
				( '' !== $background_image ? sprintf( ' poster="%s"', esc_attr( $background_image ) ) : '' )
			) ),
			( $first_video ? ' et_pb_first_video' : '' )
		);

		$et_pb_slider_has_video = true;

		wp_enqueue_style( 'wp-mediaelement' );
		wp_enqueue_script( 'wp-mediaelement' );
	}

	if ( '' !== $heading ) {
		if ( '#' !== $button_link ) {
			$heading = sprintf( '<a href="%1$s">%2$s</a>',
				esc_url( $button_link ),
				$heading
			);
		}

		$heading = '<h2>' . $heading . '</h2>';
	}

	$button = '';
	if ( '' !== $button_text )
		$button = sprintf( '<a href="%1$s" class="et_pb_more_button">%2$s</a>',
			esc_attr( $button_link ),
			esc_html( $button_text )
		);

	$style = $class = '';

	if ( '' !== $background_color )
		$style .= sprintf( 'background-color:%s;',
			esc_attr( $background_color )
		);

	if ( '' !== $background_image && 'on' !== $et_pb_slider_parallax )
		$style .= sprintf( 'background-image:url(%s);',
			esc_attr( $background_image )
		);

	$style = '' !== $style ? " style='{$style}'" : '';

	$image = '' !== $image
		? sprintf( '<div class="et_pb_slide_image"><img src="%1$s" alt="%2$s" /></div>',
			esc_attr( $image ),
			esc_attr( $image_alt )
		)
		: '';

	if ( '' !== $video_url ) {
		global $wp_embed;

		$video_embed = apply_filters( 'the_content', $wp_embed->shortcode( '', esc_url( $video_url ) ) );

		$video_embed = preg_replace('/<embed /','<embed wmode="transparent" ',$video_embed);
		$video_embed = preg_replace('/<\/object>/','<param name="wmode" value="transparent" /></object>',$video_embed);

		$image = sprintf( '<div class="et_pb_slide_video">%1$s</div>',
			$video_embed
		);
	}

	if ( '' !== $image ) $class = ' et_pb_slide_with_image';

	if ( '' !== $video_url ) $class .= ' et_pb_slide_with_video';

	$class .= " et_pb_bg_layout_{$background_layout}";

	if ( 'bottom' !== $alignment ) {
		$class .= " et_pb_media_alignment_{$alignment}";
	}

	$output = sprintf(
		'<div class="et_pb_slide%6$s"%4$s>
			%8$s
			<div class="et_pb_container clearfix">
				%5$s
				<div class="et_pb_slide_description">
					%1$s
					<div class="et_pb_slide_content">%2$s</div>
					%3$s
				</div> <!-- .et_pb_slide_description -->
			</div> <!-- .et_pb_container -->
			%7$s
		</div> <!-- .et_pb_slide -->
		',
		$heading,
		do_shortcode( et_pb_fix_shortcodes( $content ) ),
		$button,
		$style,
		$image,
		esc_attr( $class ),
		( '' !== $background_video ? $background_video : '' ),
		( '' !== $background_image && 'on' === $et_pb_slider_parallax ? sprintf( '<div class="et_parallax_bg%2$s" style="background-image: url(%1$s);"></div>', esc_attr( $background_image ), ( 'off' === $et_pb_slider_parallax_method ? ' et_pb_parallax_css' : '' ) ) : '' )
	);

	return $output;
}

add_shortcode( 'et_pb_section', 'et_pb_section' );
function et_pb_section( $atts, $content = '' ) {
	extract( shortcode_atts( array(
			'module_id' => '',
			'module_class' => '',
			'background_image' => '',
			'background_color' => '',
			'background_video_mp4' => '',
			'background_video_webm' => '',
			'background_video_width' => '',
			'background_video_height' => '',
			'inner_shadow' => 'off',
			'parallax' => 'off',
			'parallax_method' => 'off',
			'fullwidth' => 'off',
			'specialty' => 'off',
			'transparent_background' => 'off',
		), $atts
	) );

	$style = $background_video = '';

	if ( '' !== $background_video_mp4 || '' !== $background_video_webm ) {
		$background_video = sprintf(
			'<div class="et_pb_section_video_bg">
				%1$s
			</div>',
			do_shortcode( sprintf( '
				<video loop="loop" autoplay="autoplay"%3$s%4$s>
					%1$s
					%2$s
				</video>',
				( '' !== $background_video_mp4 ? sprintf( '<source type="video/mp4" src="%s" />', esc_attr( $background_video_mp4 ) ) : '' ),
				( '' !== $background_video_webm ? sprintf( '<source type="video/webm" src="%s" />', esc_attr( $background_video_webm ) ) : '' ),
				( '' !== $background_video_width ? sprintf( ' width="%s"', esc_attr( $background_video_width ) ) : '' ),
				( '' !== $background_video_height ? sprintf( ' height="%s"', esc_attr( $background_video_height ) ) : '' )
			) )
		);

		wp_enqueue_style( 'wp-mediaelement' );
		wp_enqueue_script( 'wp-mediaelement' );
	}


	if ( '' !== $background_color && 'off' === $transparent_background )
		$style .= sprintf( 'background-color:%s;',
			esc_attr( $background_color )
		);

	if ( '' !== $background_image && 'on' !== $parallax ) {
		$style .= sprintf( 'background-image:url(%s);',
			esc_attr( $background_image )
		);
	}

	$style = '' !== $style ? " style='{$style}'" : '';

	$output = sprintf(
		'<div%8$s class="et_pb_section%4$s%5$s%6$s%7$s%9$s%13$s%14$s"%2$s>
			%12$s
			%10$s
				%3$s
				%1$s
			%11$s
		</div> <!-- .et_pb_section -->',
		do_shortcode( et_pb_fix_shortcodes( $content ) ),
		$style,
		$background_video,
		( '' !== $background_video ? ' et_pb_section_video et_pb_preload' : '' ),
		( 'off' !== $inner_shadow ? ' et_pb_inner_shadow' : '' ),
		( 'on' === $parallax ? ' et_pb_section_parallax' : '' ),
		( 'off' !== $fullwidth ? ' et_pb_fullwidth_section' : '' ),
		( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
		( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
		( 'on' === $specialty ? '<div class="et_pb_row">' : '' ),
		( 'on' === $specialty ? '</div> <!-- .et_pb_row -->' : '' ),
		( '' !== $background_image && 'on' === $parallax
			? sprintf(
				'<div class="et_parallax_bg%2$s" style="background-image: url(%1$s);"></div>',
				esc_attr( $background_image ),
				( 'off' === $parallax_method ? ' et_pb_parallax_css' : '' )
			)
			: ''
		),
		( 'on' === $specialty ? ' et_section_specialty' : ' et_section_regular' ),
		( 'on' === $transparent_background ? ' et_section_transparent' : '' )
	);

	return $output;
}

add_shortcode( 'et_pb_row', 'et_pb_row' );
add_shortcode( 'et_pb_row_inner', 'et_pb_row' );
function et_pb_row( $atts, $content = '', $shortcode_name ) {
	extract( shortcode_atts( array(
			'margin' => '',
		), $atts
	) );

	$class = 'et_pb_row_inner' !== $shortcode_name ? 'et_pb_row' : 'et_pb_row_inner';

	$inner_content = do_shortcode( et_pb_fix_shortcodes( $content ) );
	$class .= '' == trim( $inner_content ) ? ' et_pb_row_empty' : '';

	$output = sprintf(
		'<div class="%2$s">
			%1$s
		</div> <!-- .%3$s -->',
		$inner_content,
		esc_attr( $class ),
		esc_html( $class )
	);

	return $output;
}

add_shortcode( 'et_pb_column', 'et_pb_column' );
add_shortcode( 'et_pb_column_inner', 'et_pb_column' );
function et_pb_column( $atts, $content = '', $shortcode_name ) {
	extract( shortcode_atts( array(
			'type' => '4_4',
			'specialty_columns' => '',
		), $atts
	) );

	global $et_specialty_column_type;

	if ( 'et_pb_column_inner' !== $shortcode_name ) {
		$et_specialty_column_type = $type;
	} else {
		switch ( $et_specialty_column_type ) {
			case '1_2':
				if ( '1_2' === $type ) {
					$type = '1_4';
				}

				break;
			case '2_3':
				if ( '1_2' === $type ) {
					$type = '1_3';
				}

				break;
			case '3_4':
				if ( '1_2' === $type ) {
					$type = '3_8';
				} else if ( '1_3' === $type ) {
					$type = '1_4';
				}

				break;
		}
	}

	$inner_class = 'et_pb_column_inner' === $shortcode_name ? ' et_pb_column_inner' : '';

	$class = 'et_pb_column_' . $type . $inner_class;

	$inner_content = do_shortcode( et_pb_fix_shortcodes( $content ) );
	$class .= '' == trim( $inner_content ) ? ' et_pb_column_empty' : '';

	$output = sprintf(
		'<div class="et_pb_column %1$s">
			%2$s
		</div> <!-- .et_pb_column -->',
		esc_attr( $class ),
		$inner_content
	);

	return $output;
}

add_shortcode( 'et_pb_image', 'et_pb_image' );
function et_pb_image( $atts ) {
	extract( shortcode_atts( array(
			'module_id' => '',
			'module_class' => '',
			'src' => '',
			'alt' => '',
			'title_text' => '',
			'animation' => 'left',
			'url' => '',
			'url_new_window' => 'off',
			'show_in_lightbox' => 'off',
			'sticky' => 'off',
		), $atts
	) );

	$output = sprintf(
		'<img%4$s src="%1$s" alt="%2$s"%6$s class="et-waypoint et_pb_image%3$s%5$s%7$s" />',
		esc_attr( $src ),
		esc_attr( $alt ),
		esc_attr( " et_pb_animation_{$animation}" ),
		( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
		( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
		( '' !== $title_text ? sprintf( ' title="%1$s"', esc_attr( $title_text ) ) : '' ),
		( 'on' === $sticky ? esc_attr( ' et_pb_image_sticky' ) : '' )
	);

	if ( 'on' === $show_in_lightbox ) {
		$output = sprintf( '<a href="%1$s" class="et_pb_lightbox_image" title="%3$s">%2$s</a>',
			esc_url( $src ),
			$output,
			esc_attr( $alt )
		);
	} else if ( '' !== $url ) {
		$output = sprintf( '<a href="%1$s"%3$s>%2$s</a>',
			esc_url( $url ),
			$output,
			( 'on' === $url_new_window ? ' target="_blank"' : '' )
		);
	}

	return $output;
}

add_shortcode( 'et_pb_testimonial', 'et_pb_testimonial' );
function et_pb_testimonial( $atts, $content = '' ) {
	extract( shortcode_atts( array(
			'module_id' => '',
			'module_class' => '',
			'author' => '',
			'job_title' => '',
			'portrait_url' => '',
			'company_name' => '',
			'url' => '',
			'quote_icon' => 'on',
			'url_new_window' => 'off',
			'use_background_color' => 'on',
			'background_color' => '#f5f5f5',
			'background_layout' => 'dark',
			'text_orientation'  => 'left',
		), $atts
	) );

	$portrait_image = '';

	$class = " et_pb_bg_layout_{$background_layout} et_pb_text_align_{$text_orientation}";

	if ( ! isset( $atts['quote_icon'] ) ) {
		$class .= "	et_pb_testimonial_old_layout";
	}

	if ( '' !== $portrait_url ) {
		$portrait_image = sprintf(
			'<div class="et_pb_testimonial_portrait" style="background-image: url(%1$s);">
			</div>',
			esc_attr( $portrait_url )
		);
	}

	if ( '' !== $url && ( '' !== $company_name || '' !== $author ) ) {
		$link_output = sprintf( '<a href="%1$s"%3$s>%2$s</a>',
			esc_url( $url ),
			( '' !== $company_name ? esc_html( $company_name ) : esc_html( $author ) ),
			( 'on' === $url_new_window ? ' target="_blank"' : '' )
		);

		if ( '' !== $company_name ) {
			$company_name = $link_output;
		} else {
			$author = $link_output;
		}
	}

	$output = sprintf(
		'<div%3$s class="et_pb_testimonial%4$s%5$s%9$s%10$s%12$s clearfix"%11$s>
			%8$s
			<div class="et_pb_testimonial_description">
				%1$s
				<strong class="et_pb_testimonial_author">%2$s</strong>
				<p class="et_pb_testimonial_meta">%6$s%7$s</p>
			</div> <!-- .et_pb_testimonial_description -->
		</div> <!-- .et_pb_testimonial -->',
		do_shortcode( et_pb_fix_shortcodes( $content ) ),
		$author,
		( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
		( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
		( 'off' === $quote_icon ? ' et_pb_icon_off' : '' ),
		( '' !== $job_title ? esc_html( $job_title ) : '' ),
		( '' !== $company_name
			? sprintf( '%2$s%1$s',
				$company_name,
				( '' !== $job_title ? ', ' : '' )
			)
			: ''
		),
		( '' !== $portrait_image ? $portrait_image : '' ),
		( '' === $portrait_image ? ' et_pb_testimonial_no_image' : '' ),
		esc_attr( $class ),
		( 'on' === $use_background_color
			? sprintf( ' style="background-color: %1$s;"', esc_attr( $background_color ) )
			: ''
		),
		( 'off' === $use_background_color ? ' et_pb_testimonial_no_bg' : '' )
	);

	return $output;
}

add_shortcode( 'et_pb_team_member', 'et_pb_team_member' );
function et_pb_team_member( $atts, $content = '' ) {
	extract( shortcode_atts( array(
			'module_id' => '',
			'module_class' => '',
			'name' => '',
			'position' => '',
			'image_url' => '',
			'animation' => 'off',
			'facebook_url' => '',
			'twitter_url' => '',
			'google_url' => '',
			'linkedin_url' => '',
			'background_layout' => 'light',
		), $atts
	) );

	$image = $social_links = '';

	if ( '' !== $facebook_url ) {
		$social_links .= sprintf(
			'<li><a href="%1$s" class="et_pb_font_icon et_pb_facebook_icon"><span>%2$s</span></a></li>',
			esc_url( $facebook_url ),
			esc_html__( 'Facebook', 'Divi' )
		);
	}

	if ( '' !== $twitter_url ) {
		$social_links .= sprintf(
			'<li><a href="%1$s" class="et_pb_font_icon et_pb_twitter_icon"><span>%2$s</span></a></li>',
			esc_url( $twitter_url ),
			esc_html__( 'Twitter', 'Divi' )
		);
	}

	if ( '' !== $google_url ) {
		$social_links .= sprintf(
			'<li><a href="%1$s" class="et_pb_font_icon et_pb_google_icon"><span>%2$s</span></a></li>',
			esc_url( $google_url ),
			esc_html__( 'Google+', 'Divi' )
		);
	}

	if ( '' !== $linkedin_url ) {
		$social_links .= sprintf(
			'<li><a href="%1$s" class="et_pb_font_icon et_pb_linkedin_icon"><span>%2$s</span></a></li>',
			esc_url( $linkedin_url ),
			esc_html__( 'LinkedIn', 'Divi' )
		);
	}

	if ( '' !== $social_links ) {
		$social_links = sprintf( '<ul class="et_pb_member_social_links">%1$s</ul>', $social_links );
	}

	if ( '' !== $image_url ) {
		$image = sprintf(
			'<div class="et_pb_team_member_image et-waypoint%3$s">
				<img src="%1$s" alt="%2$s" />
			</div>',
			esc_attr( $image_url ),
			esc_attr( $name ),
			esc_attr( " et_pb_animation_{$animation}" )
		);
	}

	$output = sprintf(
		'<div%3$s class="et_pb_team_member%4$s%9$s et_pb_bg_layout_%8$s clearfix">
			%2$s
			<div class="et_pb_team_member_description">
				%5$s
				%6$s
				%1$s
				%7$s
			</div> <!-- .et_pb_team_member_description -->
		</div> <!-- .et_pb_team_member -->',
		do_shortcode( et_pb_fix_shortcodes( $content ) ),
		( '' !== $image ? $image : '' ),
		( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
		( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
		( '' !== $name ? sprintf( '<h4>%1$s</h4>', esc_html( $name ) ) : '' ),
		( '' !== $position ? sprintf( '<p class="et_pb_member_position">%1$s</p>', esc_html( $position ) ) : '' ),
		$social_links,
		$background_layout,
		( '' === $image ? ' et_pb_team_member_no_image' : '' )
	);

	return $output;
}

add_shortcode( 'et_pb_blurb', 'et_pb_blurb' );
function et_pb_blurb( $atts, $content = '' ) {
	$et_accent_color = et_get_option( 'accent_color', '#7EBEC5' );

	extract( shortcode_atts( array(
			'module_id' => '',
			'module_class' => '',
			'title' => '',
			'url' => '',
			'image' => '',
			'url_new_window' => 'off',
			'alt' => '',
			'background_layout' => 'light',
			'text_orientation' => 'center',
			'animation' => 'top',
			'icon_placement' => 'top',
			'font_icon' => '',
			'use_icon' => 'off',
			'use_circle' => 'off',
			'use_circle_border' => 'off',
			'icon_color' => $et_accent_color,
			'circle_color' => $et_accent_color,
			'circle_border_color' => $et_accent_color,
		), $atts
	) );

	if ( '' !== $title && '' !== $url )
		$title = sprintf( '<a href="%1$s"%3$s>%2$s</a>',
			esc_url( $url ),
			esc_html( $title ),
			( 'on' === $url_new_window ? ' target="_blank"' : '' )
		);

	if ( '' !== $title )
		$title = "<h4>{$title}</h4>";

	if ( '' !== $image || '' !== $font_icon ) {
		if ( 'off' === $use_icon ) {
			$image = sprintf(
				'<img src="%1$s" alt="%2$s" class="et-waypoint%3$s" />',
				esc_attr( $image ),
				esc_attr( $alt ),
				esc_attr( " et_pb_animation_{$animation}" )
			);
		} else {
			$icon_style = sprintf( 'color: %1$s;', esc_attr( $icon_color ) );

			if ( 'on' === $use_circle ) {
				$icon_style .= sprintf( ' background-color: %1$s;', esc_attr( $circle_color ) );

				if ( 'on' === $use_circle_border ) {
					$icon_style .= sprintf( ' border-color: %1$s;', esc_attr( $circle_border_color ) );
				}
			}

			$image = sprintf(
				'<span class="et-pb-icon et-waypoint%2$s%3$s%4$s" style="%5$s">%1$s</span>',
				esc_attr( $font_icon ),
				esc_attr( " et_pb_animation_{$animation}" ),
				( 'on' === $use_circle ? ' et-pb-icon-circle' : '' ),
				( 'on' === $use_circle && 'on' === $use_circle_border ? ' et-pb-icon-circle-border' : '' ),
				$icon_style
			);
		}

		$image = sprintf(
			'<div class="et_pb_main_blurb_image">%1$s</div>',
			( '' !== $url
				? sprintf(
					'<a href="%1$s"%3$s>%2$s</a>',
					esc_url( $url ),
					$image,
					( 'on' === $url_new_window ? ' target="_blank"' : '' )
				)
				: $image
			)
		);
	}

	$class = " et_pb_bg_layout_{$background_layout} et_pb_text_align_{$text_orientation}";

	$output = sprintf(
		'<div%5$s class="et_pb_blurb%4$s%6$s%7$s">
			<div class="et_pb_blurb_content">
				%2$s
				%3$s
				%1$s
			</div> <!-- .et_pb_blurb_content -->
		</div> <!-- .et_pb_blurb -->',
		do_shortcode( et_pb_fix_shortcodes( $content ) ),
		$image,
		$title,
		esc_attr( $class ),
		( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
		( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
		sprintf( ' et_pb_blurb_position_%1$s', esc_attr( $icon_placement ) )
	);

	return $output;
}

add_shortcode( 'et_pb_text', 'et_pb_text' );
function et_pb_text( $atts, $content = '' ) {
	extract( shortcode_atts( array(
			'module_id' => '',
			'module_class' => '',
			'background_layout' => 'light',
			'text_orientation' => 'left',
		), $atts
	) );

	$class = " et_pb_bg_layout_{$background_layout} et_pb_text_align_{$text_orientation}";

	$output = sprintf(
		'<div%3$s class="et_pb_text%2$s%4$s">
			%1$s
		</div> <!-- .et_pb_text -->',
		do_shortcode( et_pb_fix_shortcodes( $content ) ),
		esc_attr( $class ),
		( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
		( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' )
	);

	return $output;
}

add_shortcode( 'et_pb_tabs', 'et_pb_tabs' );
function et_pb_tabs( $atts, $content = '' ) {
	extract( shortcode_atts( array(
			'module_id' => '',
			'module_class' => '',
		), $atts
	) );

	global $et_pb_tab_titles;

	$et_pb_tab_titles = array();
	$tabs = '';
	$all_tabs_content = do_shortcode( et_pb_fix_shortcodes( $content ) );

	$i = 0;
	foreach ( $et_pb_tab_titles as $tab_title ){
		++$i;
		$tabs .= sprintf( '<li%1$s><a href="#">%2$s</a></li>',
			( 1 == $i ? ' class="et_pb_tab_active"' : '' ),
			esc_html( $tab_title )
		);
	}

	$output = sprintf(
		'<div%3$s class="et_pb_tabs%4$s">
			<ul class="et_pb_tabs_controls clearfix">
				%1$s
			</ul>
			<div class="et_pb_all_tabs">
				%2$s
			</div> <!-- .et_pb_all_tabs -->
		</div> <!-- .et_pb_tabs -->',
		$tabs,
		$all_tabs_content,
		( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
		( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' )
	);

	return $output;
}

add_shortcode( 'et_pb_tab', 'et_pb_tab' );
function et_pb_tab( $atts, $content = null ) {
	global $et_pb_tab_titles;

	extract( shortcode_atts( array(
			'title' => '',
		), $atts
	) );

	$et_pb_tab_titles[] = '' !== $title ? $title : __( 'Tab', 'Divi' );

	$output = sprintf(
		'<div class="et_pb_tab clearfix%2$s">
			%1$s
		</div> <!-- .et_pb_tab -->',
		do_shortcode( et_pb_fix_shortcodes( $content ) ),
		( 1 === count( $et_pb_tab_titles ) ? ' et_pb_active_content' : '' )
	);

	return $output;
}

add_shortcode( 'et_pb_toggle', 'et_pb_toggle' );
add_shortcode( 'et_pb_accordion_item', 'et_pb_toggle' );
function et_pb_toggle( $atts, $content = null, $function_name ) {
	extract( shortcode_atts( array(
			'module_id' => '',
			'module_class' => '',
			'title' => '',
			'open' => 'off',
		), $atts
	) );

	if ( 'et_pb_accordion_item' === $function_name ) {
		global $et_pb_accordion_item_number;

		$open = 1 === $et_pb_accordion_item_number ? 'on' : 'off';

		$et_pb_accordion_item_number++;
	}

	$output = sprintf(
		'<div%4$s class="et_pb_toggle %2$s%5$s">
			<h5 class="et_pb_toggle_title">%1$s</h5>
			<div class="et_pb_toggle_content clearfix">
				%3$s
			</div> <!-- .et_pb_toggle_content -->
		</div> <!-- .et_pb_toggle -->',
		esc_html( $title ),
		( 'on' === $open ? 'et_pb_toggle_open' : 'et_pb_toggle_close' ),
		do_shortcode( et_pb_fix_shortcodes( $content ) ),
		( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
		( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' )
	);

	return $output;
}

add_shortcode( 'et_pb_counters', 'et_pb_counters' );
function et_pb_counters( $atts, $content = null ) {
	global $et_pb_counters_colors;

	extract( shortcode_atts( array(
			'module_id' => '',
			'module_class' => '',
			'background_layout' => 'light',
			'background_color' => '#ddd',
			'bar_bg_color' => et_get_option( 'accent_color', '#7EBEC5' ),
		), $atts
	) );

	$et_pb_counters_colors = array(
		'background_color' => $background_color,
		'bar_bg_color' => $bar_bg_color,
	);

	$class = " et_pb_bg_layout_{$background_layout}";

	$output = sprintf(
		'<ul%3$s class="et_pb_counters et-waypoint%2$s%4$s">
			%1$s
		</ul> <!-- .et_pb_counters -->',
		do_shortcode( et_pb_fix_shortcodes( $content ) ),
		esc_attr( $class ),
		( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
		( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' )
	);

	return $output;
}

add_shortcode( 'et_pb_counter', 'et_pb_counter' );
function et_pb_counter( $atts, $content = null ) {
	global $et_pb_counters_colors;

	extract( shortcode_atts( array(
			'percent' => '0',
		), $atts
	) );

	$percent .= '%';

	$background_color_style = $bar_bg_color_style = '';

	if ( isset( $et_pb_counters_colors['background_color'] ) && '' !== $et_pb_counters_colors['background_color'] )
		$background_color_style = sprintf( ' style="background-color: %1$s;"', esc_attr( $et_pb_counters_colors['background_color'] ) );

	if ( isset( $et_pb_counters_colors['bar_bg_color'] ) && '' !== $et_pb_counters_colors['bar_bg_color'] )
		$bar_bg_color_style = sprintf( ' background-color: %1$s;', esc_attr( $et_pb_counters_colors['bar_bg_color'] ) );

	$output = sprintf(
		'<li>
			<span class="et_pb_counter_title">%1$s</span>
			<span class="et_pb_counter_container"%4$s>
				<span class="et_pb_counter_amount" style="width: %3$s;%5$s">%2$s</span>
			</span>
		</li>',
		sanitize_text_field( $content ),
		esc_html( $percent ),
		esc_attr( $percent ),
		$background_color_style,
		$bar_bg_color_style
	);

	return $output;
}

add_shortcode( 'et_pb_accordion', 'et_pb_accordion' );
function et_pb_accordion( $atts, $content = null ) {
	extract( shortcode_atts( array(
			'module_id' => '',
			'module_class' => '',
		), $atts
	) );

	global $et_pb_accordion_item_number;

	$et_pb_accordion_item_number = 1;

	$output = sprintf(
		'<div%3$s class="et_pb_accordion%2$s">
			%1$s
		</div> <!-- .et_pb_accordion -->',
		do_shortcode( et_pb_fix_shortcodes( $content ) ),
		( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
		( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' )
	);

	return $output;
}

add_shortcode( 'et_pb_social_media_follow', 'et_pb_social_media_follow' );
function et_pb_social_media_follow( $atts, $content = null ) {
	global $et_pb_social_media_follow_link;

	extract( shortcode_atts( array(
			'module_id' => '',
			'module_class' => '',
			'background_layout' => 'light',
			'link_shape' => 'rounded_rectangle',
			'url_new_window' => 'on',
			'follow_button' => 'off'
		), $atts
	) );

	$et_pb_social_media_follow_link = array();
	$et_pb_social_media_follow_link['url_new_window'] = $url_new_window;
	$et_pb_social_media_follow_link['shape'] = $link_shape;
	$et_pb_social_media_follow_link['follow_button'] = $follow_button;

	$class = " et_pb_bg_layout_{$background_layout}";

	$output = sprintf(
		'<ul%3$s class="et_pb_social_media_follow%2$s%4$s%5$s clearfix">
			%1$s
		</ul> <!-- .et_pb_counters -->',
		do_shortcode( et_pb_fix_shortcodes( $content ) ),
		esc_attr( $class ),
		( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
		( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
		( 'on' === $follow_button ? ' has_follow_button' : '' )
	);

	return $output;
}

add_shortcode( 'et_pb_social_media_follow_network', 'et_pb_social_media_follow_network' );
function et_pb_social_media_follow_network ( $atts, $content = null ) {
	global $et_pb_social_media_follow_link;

	extract( shortcode_atts( array(
			'social_network' => '',
			'url' => '#',
			'bg_color' => '#666666',
			'follow_button' => '' // this is set in parent shortcode, this is just setting the initial var
		), $atts
	) );

	if ( isset( $bg_color ) && '' !== $bg_color ) {
		$bg_color_style = sprintf( 'background-color: %1$s;', esc_attr( $bg_color ) );
	}

	if ( 'on' === $et_pb_social_media_follow_link['follow_button'] ) {
		$follow_button = sprintf(
			'<a href="%1$s" class="follow_button" title="%2$s"%3$s>%4$s</a>',
			esc_url_raw( $url ),
			esc_attr( $content ),
			( 'on' === $et_pb_social_media_follow_link['url_new_window'] ? ' target="_blank"' : '' ),
			esc_html__( 'Follow', 'Divi' )
		);
	}

	$output = sprintf(
		'<li class="et_pb_social_icon et_pb_social_network_link%1$s">
			<a href="%4$s" class="icon%2$s" title="%5$s"%7$s style="%3$s"><span>%6$s</span></a>
			%8$s
		</li>',
		( '' !== $social_network ? sprintf( ' et-social-%s', esc_attr( $social_network ) ) : '' ),
		( '' !== $et_pb_social_media_follow_link['shape'] ? sprintf( ' %s', esc_attr( $et_pb_social_media_follow_link['shape'] ) ) : '' ),
		$bg_color_style,
		esc_url_raw( $url ),
		esc_attr( $content ),
		sanitize_text_field( $content ),
		( 'on' === $et_pb_social_media_follow_link['url_new_window'] ? ' target="_blank"' : '' ),
		$follow_button
	);

	return $output;
}

add_shortcode( 'et_pb_countdown_timer', 'et_pb_countdown_timer' );
function et_pb_countdown_timer( $atts ) {
	extract( shortcode_atts( array(
			'module_id' => '',
			'module_class' => '',
			'title' => '',
			'date_time' => '',
			'background_layout' => 'dark',
			'background_color' => et_get_option( 'accent_color', '#7EBEC5' ),
			'use_background_color' => 'on',
		), $atts
	) );

	$module_id = '' !== $module_id ? sprintf( ' id="%s"', esc_attr( $module_id ) ) : '';
	$module_class = '' !== $module_class ? sprintf( ' %s', esc_attr( $module_class ) ) : '';

	$background_layout = sprintf( ' et_pb_bg_layout_%s', esc_attr( $background_layout ) );

	$end_date = gmdate( 'M d, Y H:i:s', strtotime( $date_time ) );
	$gmt_offset = get_option( 'gmt_offset' );

	if ( '' !== $title ) {
		$title = sprintf( '<h4 class="title">%s</h4>', esc_html( $title ) );
	}

	$background_color_style = '';
	if ( ! empty( $background_color ) && 'on' == $use_background_color ) {
		$background_color_style = sprintf( ' style="background-color: %1$s;"', esc_attr( $background_color ) );
	}

	$output = sprintf(
		'<div%1$s class="et_pb_countdown_timer%2$s%3$s"%4$s data-end-date="%5$s" data-gmt-offset="%6$s">
			<div class="et_pb_countdown_timer_container clearfix">
				%7$s
				<div class="days section values">
					<p class="value"></p>
					<p class="label">%8$s</p>
				</div>
				<div class="sep section"><p>:</p></div>
				<div class="hours section values" data-short="%10$s">
					<p class="value"></p>
					<p class="label">%9$s</p>
				</div>
				<div class="sep section"><p>:</p></div>
				<div class="minutes section values" data-short="%12$s">
					<p class="value"></p>
					<p class="label">%11$s</p>
				</div>
				<div class="sep section"><p>:</p></div>
				<div class="seconds section values" data-short="%14$s">
					<p class="value"></p>
					<p class="label">%13$s</p>
				</div>
			</div>
		</div>',
		$module_id,
		$background_layout,
		$module_class,
		$background_color_style,
		esc_attr( $end_date ),
		esc_attr( $gmt_offset ),
		$title,
		esc_html__( 'Days', 'Divi' ),
		esc_html__( 'Hours', 'Divi' ),
		esc_attr__( 'Hrs', 'Divi' ),
		esc_html__( 'Minutes', 'Divi' ),
		esc_attr__( 'Min', 'Divi' ),
		esc_html__( 'Seconds', 'Divi' ),
		esc_attr__( 'Sec', 'Divi' )
	);

	return $output;
}

add_shortcode( 'et_pb_circle_counter', 'et_pb_circle_counter' );
function et_pb_circle_counter( $atts, $content = null ) {
	wp_enqueue_script( 'easypiechart' );
	extract( shortcode_atts( array(
			'number' => '0',
			'percent_sign' => 'on',
			'title' => '',
			'module_id' => '',
			'module_class' => '',
			'background_layout' => 'light',
			'bar_bg_color' => et_get_option( 'accent_color', '#7EBEC5' ),
		), $atts
	) );

	$number = str_ireplace( '%', '', $number );

	$class = " et_pb_bg_layout_{$background_layout}";

	$output = sprintf(
		'<div%1$s class="et_pb_circle_counter container-width-change-notify%2$s%3$s" data-number-value="%4$s" data-bar-bg-color="%5$s">
				<div class="percent"><p><span class="percent-value"></span>%6$s</p></div>
				%7$s
		</div><!-- .et_pb_circle_counter -->',
		( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
		esc_attr( $class ),
		( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
		esc_attr( $number ),
		esc_attr( $bar_bg_color ),
		( 'on' == $percent_sign ? '%' : ''),
		( '' !== $title ? '<h3>' . esc_html( $title ) . '</h3>' : '' )
	 );

	return $output;
}

add_shortcode( 'et_pb_number_counter', 'et_pb_number_counter' );
function et_pb_number_counter( $atts, $content = null ) {
	wp_enqueue_script( 'easypiechart' );
	extract( shortcode_atts( array(
			'number' => '0',
			'percent_sign' => 'on',
			'title' => '',
			'module_id' => '',
			'module_class' => '',
			'counter_color' => et_get_option( 'accent_color', '#7EBEC5' ),
			'background_layout' => 'light'
		), $atts
	) );

	$number = str_ireplace( '%', '', $number );

	$class = " et_pb_bg_layout_{$background_layout}";

	$output = sprintf(
		'<div%1$s class="et_pb_number_counter%2$s%3$s" data-number-value="%4$s">
			<div class="percent" style="%5$s"><p><span class="percent-value"></span>%6$s</p></div>
			%7$s
		</div><!-- .et_pb_number_counter -->',
		( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
		esc_attr( $class ),
		( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
		esc_attr( $number ),
		sprintf( 'color:%s', esc_attr( $counter_color ) ),
		( 'on' == $percent_sign ? '%' : ''),
		( '' !== $title ? '<h3>' . esc_html( $title ) . '</h3>' : '' )
	 );

	return $output;
}

add_shortcode( 'et_pb_cta', 'et_pb_cta' );
function et_pb_cta( $atts, $content = null ) {
	extract( shortcode_atts( array(
			'module_id' => '',
			'module_class' => '',
			'title' => '',
			'button_url' => '',
			'button_text' => '',
			'background_color' => et_get_option( 'accent_color', '#7EBEC5' ),
			'background_layout' => 'dark',
			'text_orientation' => 'center',
			'use_background_color' => 'on',
		), $atts
	) );

	$class = " et_pb_bg_layout_{$background_layout} et_pb_text_align_{$text_orientation}";

	$output = sprintf(
		'<div%6$s class="et_pb_promo%4$s%7$s%8$s"%5$s>
			<div class="et_pb_promo_description">
				%1$s
				%2$s
			</div>
			%3$s
		</div>',
		( '' !== $title ? '<h2>' . esc_html( $title ) . '</h2>' : '' ),
		do_shortcode( et_pb_fix_shortcodes( $content ) ),
		(
			'' !== $button_url && '' !== $button_text
				? sprintf( '<a class="et_pb_promo_button" href="%1$s">%2$s</a>',
					esc_url( $button_url ),
					esc_html( $button_text )
				)
				: ''
		),
		esc_attr( $class ),
		( 'on' === $use_background_color
			? sprintf( ' style="background-color: %1$s;"', esc_attr( $background_color ) )
			: ''
		),
		( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
		( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
		( 'on' !== $use_background_color ? ' et_pb_no_bg' : '' )
	);

	return $output;
}

add_shortcode( 'et_pb_audio', 'et_pb_audio' );
function et_pb_audio( $atts, $content = null ) {
	extract( shortcode_atts( array(
			'module_id' => '',
			'module_class' => '',
			'audio' => '',
			'title' => '',
			'artist_name' => '',
			'album_name' => '',
			'image_url' => '',
			'button_text' => '',
			'background_color' => et_get_option( 'accent_color', '#7EBEC5' ),
			'background_layout' => 'dark',
		), $atts
	) );

	$meta = $cover_art = '';
	$class = " et_pb_bg_layout_{$background_layout}";

	if ( 'light' === $background_layout ) {
		$class .= " et_pb_text_color_dark";
	}

	if ( '' !== $artist_name || '' !== $album_name ) {
		if ( '' !== $artist_name && '' !== $album_name ) {
			$album_name = ' | ' . $album_name;
		}

		if ( '' !== $artist_name ) {
			$artist_name = sprintf( _x( 'by <strong>%1$s</strong>', 'Audio Module meta information', 'Divi' ),
				esc_html( $artist_name )
			);
		}

		$meta = sprintf( '%1$s%2$s',
			$artist_name,
			esc_html( $album_name )
		);

		$meta = sprintf( '<p class="et_audio_module_meta">%1$s</p>', $meta );
	}

	if ( '' !== $image_url ) {
		$cover_art = sprintf(
			'<div class="et_pb_audio_cover_art" style="background-image: url(%1$s);">
			</div>',
			esc_attr( $image_url )
		);
	}

	$output = sprintf(
		'<div%8$s class="et_pb_audio_module clearfix%4$s%7$s%9$s"%5$s>
			%6$s

			<div class="et_pb_audio_module_content et_audio_container">
				%1$s
				%2$s
				%3$s
			</div>
		</div>',
		( '' !== $title ? '<h2>' . esc_html( $title ) . '</h2>' : '' ),
		$meta,
		do_shortcode(
			sprintf( '[audio src="%1$s" /]', esc_attr( $audio ) )
		),
		esc_attr( $class ),
		sprintf( ' style="background-color: %1$s;"', esc_attr( $background_color ) ),
		$cover_art,
		( '' === $image_url ? ' et_pb_audio_no_image' : '' ),
		( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
		( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' )
	);

	return $output;
}

if ( ! function_exists( 'et_pb_get_mailchimp_lists' ) ) :
function et_pb_get_mailchimp_lists() {
	$lists = array();

	if ( 'on' === et_get_option( 'divi_regenerate_mailchimp_lists', 'false' ) || false === ( $et_pb_mailchimp_lists = get_transient( 'et_pb_mailchimp_lists' ) ) ) {
		if ( ! class_exists( 'Mailchimp' ) )
			require_once( get_template_directory() . '/includes/subscription/mailchimp/Mailchimp.php' );

		$mailchimp_api_key = et_get_option( 'divi_mailchimp_api_key' );

		if ( '' === $mailchimp_api_key ) return false;

		try {
			$mailchimp = new Mailchimp( $mailchimp_api_key );
			$mailchimp_lists = new Mailchimp_Lists( $mailchimp );

			$retval = $mailchimp_lists->getlist();

			foreach ( $retval['data'] as $list ) {
				$lists[$list['id']] = $list['name'];
			}

			set_transient( 'et_pb_mailchimp_lists', $lists, 60*60*24 );
		} catch ( Exception $exc ) {
			$lists = $et_pb_mailchimp_lists;
		}

	return $lists;
	}
}
endif;

if ( ! function_exists( 'et_pb_get_aweber_account' ) ) :
function et_pb_get_aweber_account() {
	if ( ! class_exists( 'AWeberAPI' ) ) {
		require_once( get_template_directory() . '/includes/subscription/aweber/aweber_api.php' );
	}

	$consumer_key = et_get_option( 'divi_aweber_consumer_key' );
	$consumer_secret = et_get_option( 'divi_aweber_consumer_secret' );
	$access_key = et_get_option( 'divi_aweber_access_key' );
	$access_secret = et_get_option( 'divi_aweber_access_secret' );

	try {
		// Aweber requires curl extension to be enabled
		if ( ! function_exists( 'curl_init' ) ) {
			return false;
		}

		$aweber = new AWeberAPI( $consumer_key, $consumer_secret );

		if ( ! $aweber ) {
			return false;
		}

		$account = $aweber->getAccount( $access_key, $access_secret );
	} catch ( Exception $exc ) {
		return false;
	}

	return $account;
}
endif;

if ( ! function_exists( 'et_pb_get_aweber_lists' ) ) :
function et_pb_get_aweber_lists() {
	$lists = array();

	if ( 'on' === et_get_option( 'divi_regenerate_aweber_lists', 'false' ) || false === ( $et_pb_aweber_lists = get_transient( 'et_pb_aweber_lists' ) ) ) {

		if ( ! class_exists( 'AWeberAPI' ) ) {
			require_once( get_template_directory() . '/includes/subscription/aweber/aweber_api.php' );
		}

		$account = et_pb_get_aweber_account();
		$aweber_lists = $account->lists;

		if ( isset( $aweber_lists ) ) {
			foreach ( $aweber_lists as $list ) {
				$lists[$list->id] = $list->name;
			}
		}

		set_transient( 'et_pb_aweber_lists', $lists, 60*60*24 );
	} else {
		$lists = $et_pb_aweber_lists;
	}

	return $lists;
}
endif;

function et_pb_submit_subscribe_form(  ) {
	if ( ! wp_verify_nonce( $_POST['et_load_nonce'], 'et_load_nonce' ) ) die( json_encode( array( 'error' => __( 'Configuration error', 'Divi' ) ) ) );

	$service = sanitize_text_field( $_POST['et_service'] );

	$list_id = sanitize_text_field( $_POST['et_list_id'] );

	$email = array( 'email' => sanitize_email( $_POST['et_email'] ) );

	$firstname = sanitize_text_field( $_POST['et_firstname'] );

	if ( '' === $firstname ) die( json_encode( array( 'error' => __( 'Please enter first name', 'Divi' ) ) ) );

	if ( ! is_email( sanitize_email( $_POST['et_email'] ) ) ) die( json_encode( array( 'error' => __( 'Incorrect email', 'Divi' ) ) ) );

	if ( '' == $list_id ) die( json_encode( array( 'error' => __( 'Configuration error: List is not defined', 'Divi' ) ) ) );

	$success_message = __( '<h2 class="et_pb_subscribed">Subscribed - look for the confirmation email!</h2>', 'Divi' );

	switch ( $service ) {
		case 'mailchimp' :
			$lastname = sanitize_text_field( $_POST['et_lastname'] );

			if ( ! class_exists( 'Mailchimp' ) )
				require_once( get_template_directory() . '/includes/subscription/mailchimp/Mailchimp.php' );

			$mailchimp_api_key = et_get_option( 'divi_mailchimp_api_key' );

			if ( '' === $mailchimp_api_key ) die( json_encode( array( 'error' => __( 'Configuration error: api key is not defined', 'Divi' ) ) ) );

			try {
				$mailchimp = new Mailchimp( $mailchimp_api_key );
				$mailchimp_lists = new Mailchimp_Lists( $mailchimp );

	 			$merge_vars = array(
					'FNAME' => $firstname,
					'LNAME' => $lastname,
				);

				$retval = $mailchimp_lists->subscribe( $list_id, $email, $merge_vars );

				$result = json_encode( array( 'success' => $success_message ) );
			} catch ( Exception $e ) {
				 if ( $e == 'List_AlreadySubscribed' ) {
                    $error_code = $e->code;
                }
                    if ( $error_code = '214' ) {
                        $error_message = str_replace( 'Click here to update your profile.', '', $e->getMessage() );
                        $result = json_encode( array( 'success' => $error_message ) );
                    } else {
                    	$result = json_encode( array( 'success' => $e->getMessage() ) );
                    }
                }

            die( $result );
			break;
		case 'aweber' :
			if ( ! class_exists( 'AWeberAPI' ) ) {
				require_once( get_template_directory() . '/includes/subscription/aweber/aweber_api.php' );
			}

			$account = et_pb_get_aweber_account();

			try {
				$list_url = "/accounts/{$account->id}/lists/{$list_id}";
				$list = $account->loadFromUrl( $list_url );

				$new_subscriber = $list->subscribers->create(
					array(
						'email' => $email,
						'name'  => $firstname,
					)
				);

				echo $success_message;
			} catch ( Exception $exc ) {
				die( $exc );
			}

			break;
	}

	die();
}
add_action( 'wp_ajax_et_pb_submit_subscribe_form', 'et_pb_submit_subscribe_form' );
add_action( 'wp_ajax_nopriv_et_pb_submit_subscribe_form', 'et_pb_submit_subscribe_form' );

add_shortcode( 'et_pb_signup', 'et_pb_signup' );
function et_pb_signup( $atts, $content = null ) {
	extract( shortcode_atts( array(
			'module_id' => '',
			'module_class' => '',
			'title' => '',
			'button_text' => __( 'Subscribe', 'Divi' ),
			'background_color' => et_get_option( 'accent_color', '#7EBEC5' ),
			'background_layout' => 'dark',
			'mailchimp_list' => '',
			'aweber_list' => '',
			'text_orientation' => 'left',
			'use_background_color' => 'on',
			'provider' => 'mailchimp',
			'feedburner_uri' => '',
		), $atts
	) );

	$class = " et_pb_bg_layout_{$background_layout} et_pb_text_align_{$text_orientation}";

	$form = '';

	$firstname     = __( 'First Name', 'Divi' );
	$lastname      = __( 'Last Name', 'Divi' );
	$email_address = __( 'Email Address', 'Divi' );

	switch ( $provider ) {
		case 'mailchimp' :
			if ( ! in_array( $mailchimp_list, array( '', 'none' ) ) ) {
				$form = sprintf( '
					<div class="et_pb_newsletter_form">
						<div class="et_pb_newsletter_result"></div>
						<p>
							<label class="et_pb_contact_form_label" for="et_pb_signup_firstname" style="display: none;">%3$s</label>
							<input id="et_pb_signup_firstname" class="input" type="text" value="%4$s" name="et_pb_signup_firstname">
						</p>
						<p>
							<label class="et_pb_contact_form_label" for="et_pb_signup_lastname" style="display: none;">%5$s</label>
							<input id="et_pb_signup_lastname" class="input" type="text" value="%6$s" name="et_pb_signup_lastname">
						</p>
						<p>
							<label class="et_pb_contact_form_label" for="et_pb_signup_email" style="display: none;">%7$s</label>
							<input id="et_pb_signup_email" class="input" type="text" value="%8$s" name="et_pb_signup_email">
						</p>
						<p><a class="et_pb_newsletter_button" href="#">%1$s</a></p>
						<input type="hidden" value="%2$s" name="et_pb_signup_list_id" />
					</div>',
					esc_html( $button_text ),
					( ! in_array( $mailchimp_list, array( '', 'none' ) ) ? esc_attr( $mailchimp_list ) : '' ),
					esc_html( $firstname ),
					esc_attr( $firstname ),
					esc_html( $lastname ),
					esc_attr( $lastname ),
					esc_html( $email_address ),
					esc_attr( $email_address )
				);
			}

			break;
		case 'feedburner':
			$form = sprintf( '
				<div class="et_pb_newsletter_form et_pb_feedburner_form">
					<form action="http://feedburner.google.com/fb/a/mailverify" method="post" target="popupwindow" onsubmit="window.open(\'http://feedburner.google.com/fb/a/mailverify?uri=%4$s\', \'popupwindow\', \'scrollbars=yes,width=550,height=520\'); return true">
					<p>
						<label class="et_pb_contact_form_label" for="email" style="display: none;">%2$s</label>
						<input id="email" class="input" type="text" value="%3$s" name="email">
					</p>
					<p><button class="et_pb_newsletter_button" type="submit">%1$s</button></p>
					<input type="hidden" value="%4$s" name="uri" />
					<input type="hidden" name="loc" value="%5$s" />
				</div>',
				esc_html( $button_text ),
				esc_html( $email_address ),
				esc_attr( $email_address ),
				esc_attr( $feedburner_uri ),
				esc_attr( get_locale() )
			);

			break;
		case 'aweber' :
			$firstname = __( 'Name', 'Divi' );

			if ( ! in_array( $aweber_list, array( '', 'none' ) ) ) {
				$form = sprintf( '
					<div class="et_pb_newsletter_form" data-service="aweber">
						<div class="et_pb_newsletter_result"></div>
						<p>
							<label class="et_pb_contact_form_label" for="et_pb_signup_firstname" style="display: none;">%3$s</label>
							<input id="et_pb_signup_firstname" class="input" type="text" value="%4$s" name="et_pb_signup_firstname">
						</p>
						<p>
							<label class="et_pb_contact_form_label" for="et_pb_signup_email" style="display: none;">%5$s</label>
							<input id="et_pb_signup_email" class="input" type="text" value="%6$s" name="et_pb_signup_email">
						</p>
						<p><a class="et_pb_newsletter_button" href="#">%1$s</a></p>
						<input type="hidden" value="%2$s" name="et_pb_signup_list_id" />
					</div>',
					esc_html( $button_text ),
					( ! in_array( $aweber_list, array( '', 'none' ) ) ? esc_attr( $aweber_list ) : '' ),
					esc_html( $firstname ),
					esc_attr( $firstname ),
					esc_html( $email_address ),
					esc_attr( $email_address )
				);
			}

			break;
	}

	$output = sprintf(
		'<div%6$s class="et_pb_newsletter clearfix%4$s%7$s%8$s"%5$s>
			<div class="et_pb_newsletter_description">
				%1$s
				%2$s
			</div>
			%3$s
		</div>',
		( '' !== $title ? '<h2>' . esc_html( $title ) . '</h2>' : '' ),
		do_shortcode( et_pb_fix_shortcodes( $content ) ),
		$form,
		esc_attr( $class ),
		( 'on' === $use_background_color
			? sprintf( ' style="background-color: %1$s;"', esc_attr( $background_color ) )
			: ''
		),
		( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
		( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
		( 'on' !== $use_background_color ? ' et_pb_no_bg' : '' )
	);

	return $output;
}

add_shortcode( 'et_pb_login', 'et_pb_login' );
function et_pb_login( $atts, $content = null ) {
	extract( shortcode_atts( array(
			'module_id' => '',
			'module_class' => '',
			'title' => '',
			'background_color' => et_get_option( 'accent_color', '#7EBEC5' ),
			'background_layout' => 'dark',
			'text_orientation' => 'left',
			'use_background_color' => 'on',
			'current_page_redirect' => 'off',
		), $atts
	) );

	$redirect_url = 'on' === $current_page_redirect
		? ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
		: '';


	if ( is_user_logged_in() ) {
		global $current_user;
     	get_currentuserinfo();

		$content .= sprintf( '<br/>%1$s <a href="%2$s">%3$s</a>',
				sprintf( __( 'Logged in as %1$s', 'Divi' ), esc_html( $current_user->display_name ) ),
				esc_url( wp_logout_url( $redirect_url ) ),
				esc_html__( 'Log out', 'Divi' )
			);
	}

	$class = " et_pb_bg_layout_{$background_layout} et_pb_text_align_{$text_orientation}";

	$form = '';

	if ( !is_user_logged_in() ) {
		$username = __( 'Username', 'Divi' );
		$password = __( 'Password', 'Divi' );

		$form = sprintf( '
			<div class="et_pb_newsletter_form et_pb_login_form">
				<form action="%7$s" method="post">
					<p>
						<label class="et_pb_contact_form_label" for="user_login" style="display: none;">%3$s</label>
						<input id="user_login" placeholder="%4$s" class="input" type="text" value="" name="log" />
					</p>
					<p>
						<label class="et_pb_contact_form_label" for="user_pass" style="display: none;">%5$s</label>
						<input id="user_pass" placeholder="%6$s" class="input" type="password" value="" name="pwd" />
					</p>
					<p class="et_pb_forgot_password"><a href="%2$s">%1$s</a></p>
					<p>
						<button type="submit" class="et_pb_newsletter_button">%8$s</button>
						%9$s
					</p>
				</form>
			</div>',
			__( 'Forgot your password?', 'Divi' ),
			esc_url( wp_lostpassword_url() ),
			esc_html( $username ),
			esc_attr( $username ),
			esc_html( $password ),
			esc_attr( $password ),
			esc_url( site_url( 'wp-login.php' ) ),
			__( 'Login', 'Divi' ),
			( 'on' === $current_page_redirect
				? sprintf( '<input type="hidden" name="redirect_to" value="%1$s" />',  $redirect_url )
				: ''
			)
		);
	}

	$output = sprintf(
		'<div%6$s class="et_pb_newsletter et_pb_login clearfix%4$s%7$s"%5$s>
			<div class="et_pb_newsletter_description">
				%1$s
				%2$s
			</div>
			%3$s
		</div>',
		( '' !== $title ? '<h2>' . esc_html( $title ) . '</h2>' : '' ),
		do_shortcode( et_pb_fix_shortcodes( $content ) ),
		$form,
		esc_attr( $class ),
		( 'on' === $use_background_color
			? sprintf( ' style="background-color: %1$s;"', esc_attr( $background_color ) )
			: ''
		),
		( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
		( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' )
	);

	return $output;
}

add_shortcode( 'et_pb_sidebar', 'et_pb_sidebar' );
function et_pb_sidebar( $atts ) {
	extract( shortcode_atts( array(
			'module_id' => '',
			'module_class' => '',
			'orientation' => 'left',
			'area' => '',
			'background_layout' => 'light',
		), $atts
	) );

	$widgets = '';

	ob_start();

	if ( is_active_sidebar( $area ) )
		dynamic_sidebar( $area );

	$widgets = ob_get_contents();

	ob_end_clean();

	$class = " et_pb_bg_layout_{$background_layout}";

	$output = sprintf(
		'<div%4$s class="et_pb_widget_area %2$s clearfix%3$s%5$s">
			%1$s
		</div> <!-- .et_pb_widget_area -->',
		$widgets,
		esc_attr( "et_pb_widget_area_{$orientation}" ),
		esc_attr( $class ),
		( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
		( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' )
	);

	return $output;
}

add_shortcode( 'et_pb_blog', 'et_pb_blog' );
function et_pb_blog( $atts ) {
	extract( shortcode_atts( array(
			'module_id' => '',
			'module_class' => '',
			'fullwidth' => 'on',
			'posts_number' => 10,
			'include_categories' => '',
			'meta_date' => 'M j, Y',
			'show_thumbnail' => 'on',
			'show_content' => 'off',
			'show_author' => 'on',
			'show_date' => 'on',
			'show_categories' => 'on',
			'show_pagination' => 'on',
			'background_layout' => 'light',
			'show_more' => 'off',
		), $atts
	) );

	global $paged;

	$container_is_closed = false;

	if ( 'on' !== $fullwidth ){
		wp_enqueue_script( 'jquery-masonry-3' );
	}

	$args = array( 'posts_per_page' => (int) $posts_number );

	$et_paged = is_front_page() ? get_query_var( 'page' ) : get_query_var( 'paged' );

	if ( is_front_page() ) {
		$paged = $et_paged;
	}

	if ( '' !== $include_categories )
		$args['cat'] = $include_categories;

	if ( ! is_search() ) {
		$args['paged'] = $et_paged;
	}

	ob_start();

	query_posts( $args );

	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();

			$post_format = get_post_format();

			$thumb = '';

			$width = 'on' === $fullwidth ? 1080 : 400;
			$width = (int) apply_filters( 'et_pb_blog_image_width', $width );

			$height = 'on' === $fullwidth ? 675 : 250;
			$height = (int) apply_filters( 'et_pb_blog_image_height', $height );
			$classtext = 'on' === $fullwidth ? 'et_pb_post_main_image' : '';
			$titletext = get_the_title();
			$thumbnail = get_thumbnail( $width, $height, $classtext, $titletext, $titletext, false, 'Blogimage' );
			$thumb = $thumbnail["thumb"];

			$no_thumb_class = '' === $thumb || 'off' === $show_thumbnail ? ' et_pb_no_thumb' : '';

			if ( in_array( $post_format, array( 'video', 'gallery' ) ) ) {
				$no_thumb_class = '';
			} ?>

		<article id="post-<?php the_ID(); ?>" <?php post_class( 'et_pb_post' . $no_thumb_class ); ?>>

		<?php
			et_divi_post_format_content();

			if ( ! in_array( $post_format, array( 'link', 'audio', 'quote' ) ) ) {
				if ( 'video' === $post_format && false !== ( $first_video = et_get_first_video() ) ) :
					printf(
						'<div class="et_main_video_container">
							%1$s
						</div>',
						$first_video
					);
				elseif ( 'gallery' === $post_format ) :
					et_gallery_images();
				elseif ( '' !== $thumb && 'on' === $show_thumbnail ) :
					if ( 'on' !== $fullwidth ) echo '<div class="et_pb_image_container">'; ?>
						<a href="<?php the_permalink(); ?>">
							<?php print_thumbnail( $thumb, $thumbnail["use_timthumb"], $titletext, $width, $height ); ?>
						</a>
				<?php
					if ( 'on' !== $fullwidth ) echo '</div> <!-- .et_pb_image_container -->';
				endif;
			} ?>

		<?php if ( 'off' === $fullwidth || ! in_array( $post_format, array( 'link', 'audio', 'quote', 'gallery' ) ) ) { ?>
			<?php if ( ! in_array( $post_format, array( 'link', 'audio' ) ) ) { ?>
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			<?php } ?>

			<?php
				if ( 'on' === $show_author || 'on' === $show_date || 'on' === $show_categories ) {
					printf( '<p class="post-meta">%1$s %2$s %3$s %4$s %5$s</p>',
						(
							'on' === $show_author
								? sprintf( __( 'by %s', 'Divi' ), et_get_the_author_posts_link() )
								: ''
						),
						(
							( 'on' === $show_author && 'on' === $show_date )
								? ' | '
								: ''
						),
						(
							'on' === $show_date
								? sprintf( __( '%s', 'Divi' ), get_the_date( $meta_date ) )
								: ''
						),
						(
							(( 'on' === $show_author || 'on' === $show_date ) && 'on' === $show_categories)
								? ' | '
								: ''
						),
						(
							'on' === $show_categories
								? get_the_category_list(', ')
								: ''
						)
					);
				}

				if ( 'on' === $show_content ) {
					global $more;
					$more = null;

					the_content( __( 'read more...', 'Divi' ) );
				} else {
					if ( has_excerpt() ) {
						the_excerpt();
					} else {
						truncate_post( 270 );
					}
					$more = 'on' == $show_more ? sprintf( ' <a href="%1$s" class="more-link" >%2$s</a>' , esc_url( get_permalink() ), __( 'read more', 'Divi' ) )  : '';
					echo $more;
				} ?>
		<?php } // 'off' === $fullwidth || ! in_array( $post_format, array( 'link', 'audio', 'quote', 'gallery' ?>

		</article> <!-- .et_pb_post -->
<?php
		} // endwhile

		if ( 'on' === $show_pagination && ! is_search() ) {
			echo '</div> <!-- .et_pb_posts -->';

			$container_is_closed = true;

			if ( function_exists( 'wp_pagenavi' ) )
				wp_pagenavi();
			else
				get_template_part( 'includes/navigation', 'index' );
		}

		wp_reset_query();
	} else {
		get_template_part( 'includes/no-results', 'index' );
	}

	$posts = ob_get_contents();

	ob_end_clean();

	$class = " et_pb_bg_layout_{$background_layout}";

	$output = sprintf(
		'<div%5$s class="%1$s%3$s%6$s">
			%2$s
		%4$s',
		( 'on' === $fullwidth ? 'et_pb_posts' : 'et_pb_blog_grid clearfix' ),
		$posts,
		esc_attr( $class ),
		( ! $container_is_closed ? '</div> <!-- .et_pb_posts -->' : '' ),
		( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
		( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' )
	);

	if ( 'on' !== $fullwidth )
		$output = sprintf( '<div class="et_pb_blog_grid_wrapper">%1$s</div>', $output );

	return $output;
}

add_shortcode( 'et_pb_gallery', 'et_pb_gallery' );
function et_pb_gallery( $atts ) {

	extract(shortcode_atts(array(
		'module_id' => '',
		'module_class' => '',
		'gallery_ids'    => '',
		'fullwidth'  => 'off',
		'show_title_and_caption'    => 'on',
		'background_layout' => 'light',
		'posts_number' => 4,
		'show_pagination' => 'on',
		'gallery_orderby' => '',
	), $atts ));

	$attachments = array();
	if ( !empty($gallery_ids) ) {
		$attachments_args = array(
			'include'        => $gallery_ids,
			'post_status'    => 'inherit',
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'order'          => 'ASC',
			'orderby'        => 'post__in',
		);

		if ( 'rand' === $gallery_orderby ) {
			$attachments_args['orderby'] = 'rand';
		}

		$_attachments = get_posts( $attachments_args );

		foreach ( $_attachments as $key => $val ) {
			$attachments[$val->ID] = $_attachments[$key];
		}
	}

	if ( empty($attachments) )
		return '';

	wp_enqueue_script( 'jquery-masonry-3' );
	wp_enqueue_script( 'hashchange' );

	$fullwidth_class = 'on' === $fullwidth ?  ' et_pb_slider et_pb_gallery_fullwidth' : ' et_pb_gallery_grid';
	$background_class = " et_pb_bg_layout_{$background_layout}";

	$output = sprintf(
		'<div%1$s class="et_pb_gallery%2$s%3$s%4$s clearfix">
			%5$s
			<div class="et_pb_gallery_items et_post_gallery" data-per_page="%6$d">',
		( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
		( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
		esc_attr( $fullwidth_class ),
		esc_attr( $background_class ),
		( 'on' !== $fullwidth ? '<div class="column_width"></div><div class="gutter_width"></div>' : '' ),
		esc_attr( $posts_number )
	);

	$i = 0;
	foreach ( $attachments as $id => $attachment ) {

		$width = 'on' === $fullwidth ?  1080 : 400;
		$width = (int) apply_filters( 'et_pb_gallery_image_width', $width );

		$height = 'on' === $fullwidth ?  9999 : 284;
		$height = (int) apply_filters( 'et_pb_gallery_image_height', $height );

		list($full_src, $full_width, $full_height) = wp_get_attachment_image_src( $id, 'full' );
		list($thumb_src, $thumb_width, $thumb_height) = wp_get_attachment_image_src( $id, array( $width, $height ) );

		$image_output = sprintf(
			'<a href="%1$s" title="%2$s">
				<img src="%3$s" alt="%2$s" /><span class="et_overlay"></span>
			</a>',
			esc_attr( $full_src ),
			esc_attr( $attachment->post_title ),
			esc_attr( $thumb_src )
		);

		$orientation = ( $thumb_height > $thumb_width ) ? 'portrait' : 'landscape';

		$output .= sprintf(
			'<div class="et_pb_gallery_item%1$s">',
			esc_attr( $background_class )
		);
		$output .= "
			<div class='et_pb_gallery_image {$orientation}'>
				$image_output
			</div>";

		if ( 'on' !== $fullwidth && 'on' === $show_title_and_caption ) {
			if ( trim($attachment->post_title) ) {
				$output .= "
					<h3 class='et_pb_gallery_title'>
					" . wptexturize($attachment->post_title) . "
					</h3>";
			}
			if ( trim($attachment->post_excerpt) ) {
			$output .= "
					<p class='et_pb_gallery_caption'>
					" . wptexturize($attachment->post_excerpt) . "
					</p>";
			}
		}
		$output .= "</div>";
	}

	$output .= "</div><!-- .et_pb_gallery_items -->";

	if ( 'on' !== $fullwidth && 'on' === $show_pagination ) {
		$output .= "<div class='et_pb_gallery_pagination'></div>";
	}

	$output .= "</div><!-- .et_pb_gallery -->";

	return $output;
}

add_shortcode( 'et_pb_portfolio', 'et_pb_portfolio' );
function et_pb_portfolio( $atts ) {
	extract( shortcode_atts( array(
			'module_id' => '',
			'module_class' => '',
			'fullwidth' => 'on',
			'posts_number' => 10,
			'include_categories' => '',
			'show_title' => 'on',
			'show_categories' => 'on',
			'show_pagination' => 'on',
			'background_layout' => 'light',
		), $atts
	) );

	global $paged;

	$container_is_closed = false;

	$args = array(
		'posts_per_page' => (int) $posts_number,
		'post_type'      => 'project',
	);

	$et_paged = is_front_page() ? get_query_var( 'page' ) : get_query_var( 'paged' );

	if ( is_front_page() ) {
		$paged = $et_paged;
	}

	if ( '' !== $include_categories )
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'project_category',
				'field' => 'id',
				'terms' => explode( ',', $include_categories ),
				'operator' => 'IN',
			)
		);

	if ( ! is_search() ) {
		$args['paged'] = $et_paged;
	}

	ob_start();

	query_posts( $args );

	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post(); ?>

			<div id="post-<?php the_ID(); ?>" <?php post_class( 'et_pb_portfolio_item' ); ?>>

		<?php
			$thumb = '';

			$width = 'on' === $fullwidth ?  1080 : 400;
			$width = (int) apply_filters( 'et_pb_portfolio_image_width', $width );

			$height = 'on' === $fullwidth ?  9999 : 284;
			$height = (int) apply_filters( 'et_pb_portfolio_image_height', $height );
			$classtext = 'on' === $fullwidth ? 'et_pb_post_main_image' : '';
			$titletext = get_the_title();
			$thumbnail = get_thumbnail( $width, $height, $classtext, $titletext, $titletext, false, 'Blogimage' );
			$thumb = $thumbnail["thumb"];

			if ( '' !== $thumb ) : ?>
				<a href="<?php the_permalink(); ?>">
				<?php if ( 'on' !== $fullwidth ) : ?>
					<span class="et_portfolio_image">
				<?php endif; ?>
						<?php print_thumbnail( $thumb, $thumbnail["use_timthumb"], $titletext, $width, $height ); ?>
				<?php if ( 'on' !== $fullwidth ) : ?>
						<span class="et_overlay"></span>
					</span>
				<?php endif; ?>
				</a>
		<?php
			endif;
		?>

			<?php if ( 'on' === $show_title ) : ?>
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			<?php endif; ?>

			<?php if ( 'on' === $show_categories ) : ?>
				<p class="post-meta"><?php echo get_the_term_list( get_the_ID(), 'project_category', '', ', ' ); ?></p>
			<?php endif; ?>

			</div> <!-- .et_pb_portfolio_item -->
<?php	}

		if ( 'on' === $show_pagination && ! is_search() ) {
			echo '</div> <!-- .et_pb_portfolio -->';

			$container_is_closed = true;

			if ( function_exists( 'wp_pagenavi' ) )
				wp_pagenavi();
			else
				get_template_part( 'includes/navigation', 'index' );
		}

		wp_reset_query();
	} else {
		get_template_part( 'includes/no-results', 'index' );
	}

	$posts = ob_get_contents();

	ob_end_clean();

	$class = " et_pb_bg_layout_{$background_layout}";

	$output = sprintf(
		'<div%5$s class="%1$s%3$s%6$s">
			%2$s
		%4$s',
		( 'on' === $fullwidth ? 'et_pb_portfolio' : 'et_pb_portfolio_grid clearfix' ),
		$posts,
		esc_attr( $class ),
		( ! $container_is_closed ? '</div> <!-- .et_pb_portfolio -->' : '' ),
		( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
		( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' )
	);

	return $output;
}

function get_portfolio_projects( $args = array() ) {

	$default_args = array(
		'post_type' => 'project',
	);

	$args = wp_parse_args( $args, $default_args );

	return new WP_Query( $args );

}

add_shortcode( 'et_pb_filterable_portfolio', 'et_pb_filterable_portfolio' );
function et_pb_filterable_portfolio( $atts ) {
	extract( shortcode_atts( array(
			'module_id' => '',
			'module_class' => '',
			'fullwidth' => 'on',
			'posts_number' => 10,
			'include_categories' => '',
			'show_title' => 'on',
			'show_categories' => 'on',
			'show_pagination' => 'on',
			'background_layout' => 'light',
		), $atts
	) );

	wp_enqueue_script( 'jquery-masonry-3' );
	wp_enqueue_script( 'hashchange' );

	$args = array();

	if( 'on' === $show_pagination ) {
		$args['nopaging'] = true;
	} else {
		$args['posts_per_page'] = (int) $posts_number;
	}

	if ( '' !== $include_categories ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'project_category',
				'field' => 'id',
				'terms' => explode( ',', $include_categories ),
				'operator' => 'IN',
			)
		);
	}

	$projects = get_portfolio_projects( $args );

	$categories_included = array();
	ob_start();
	if( $projects->post_count > 0 ) {
		while ( $projects->have_posts() ) {
			$projects->the_post();

			$category_classes = array();
			$categories = get_the_terms( get_the_ID(), 'project_category' );
			if ( $categories ) {
				foreach ( $categories as $category ) {
					$category_classes[] = 'project_category_' . $category->slug;
					$categories_included[] = $category->term_id;
				}
			}

			$category_classes = implode( ' ', $category_classes );

			?>
			<div id="post-<?php the_ID(); ?>" <?php post_class( 'et_pb_portfolio_item ' . $category_classes ); ?>>
			<?php
				$thumb = '';

				$width = 'on' === $fullwidth ?  1080 : 400;
				$width = (int) apply_filters( 'et_pb_portfolio_image_width', $width );

				$height = 'on' === $fullwidth ?  9999 : 284;
				$height = (int) apply_filters( 'et_pb_portfolio_image_height', $height );
				$classtext = 'on' === $fullwidth ? 'et_pb_post_main_image' : '';
				$titletext = get_the_title();
				$thumbnail = get_thumbnail( $width, $height, $classtext, $titletext, $titletext, false, 'Blogimage' );
				$thumb = $thumbnail["thumb"];

				if ( '' !== $thumb ) : ?>
					<a href="<?php the_permalink(); ?>">
					<?php if ( 'on' !== $fullwidth ) : ?>
						<span class="et_portfolio_image">
					<?php endif; ?>
							<?php print_thumbnail( $thumb, $thumbnail["use_timthumb"], $titletext, $width, $height ); ?>
					<?php if ( 'on' !== $fullwidth ) : ?>
							<span class="et_overlay"></span>
						</span>
					<?php endif; ?>
					</a>
			<?php
				endif;
			?>

			<?php if ( 'on' === $show_title ) : ?>
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			<?php endif; ?>

			<?php if ( 'on' === $show_categories ) : ?>
				<p class="post-meta"><?php echo get_the_term_list( get_the_ID(), 'project_category', '', ', ' ); ?></p>
			<?php endif; ?>

			</div><!-- .et_pb_portfolio_item -->
			<?php
		}
	}

	$posts = ob_get_clean();

	$categories_included = explode ( ',', $include_categories );
	$terms_args = array(
		'include' => $categories_included,
		'orderby' => 'name',
		'order' => 'ASC',
	);
	$terms = get_terms( 'project_category', $terms_args );

	$category_filters = '<ul class="clearfix">';
	$category_filters .= sprintf( '<li class="et_pb_portfolio_filter et_pb_portfolio_filter_all"><a href="#" class="active" data-category-slug="all">%1$s</a></li>',
		esc_html__( 'All', 'Divi' )
	);
	foreach ( $terms as $term  ) {
		$category_filters .= sprintf( '<li class="et_pb_portfolio_filter"><a href="#" data-category-slug="%1$s">%2$s</a></li>',
			esc_attr( $term->slug ),
			esc_html( $term->name )
		);
	}
	$category_filters .= '</ul>';

	$class = " et_pb_bg_layout_{$background_layout}";

	$output = sprintf(
		'<div%5$s class="et_pb_filterable_portfolio %1$s%4$s%6$s" data-posts-number="%7$d">
			<div class="et_pb_portfolio_filters clearfix">%2$s</div><!-- .et_pb_portfolio_filters -->

			<div class="et_pb_portfolio_items_wrapper %8$s">
				<div class="column_width"></div>
				<div class="gutter_width"></div>
				<div class="et_pb_portfolio_items">%3$s</div><!-- .et_pb_portfolio_items -->
			</div>
			%9$s
		</div> <!-- .et_pb_filterable_portfolio -->',
		( 'on' === $fullwidth ? 'et_pb_filterable_portfolio_fullwidth' : 'et_pb_filterable_portfolio_grid clearfix' ),
		$category_filters,
		$posts,
		esc_attr( $class ),
		( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
		( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
		esc_attr( $posts_number),
		('on' === $show_pagination ? '' : 'no_pagination' ),
		('on' === $show_pagination ? '<div class="et_pb_portofolio_pagination"></div>' : '' )
	);

	return $output;
}

add_shortcode( 'et_pb_fullwidth_portfolio', 'et_pb_fullwidth_portfolio' );
function et_pb_fullwidth_portfolio( $atts ) {
	extract( shortcode_atts( array(
			'title' => '',
			'module_id' => '',
			'module_class' => '',
			'fullwidth' => 'on',
			'include_categories' => '',
			'posts_number' => '',
			'show_title' => 'on',
			'show_date' => 'on',
			'background_layout' => 'light',
			'auto' => 'off',
			'auto_speed' => 7000,
		), $atts
	) );

	$args = array();
	if ( is_numeric( $posts_number ) && $posts_number > 0 ) {
		$args['posts_per_page'] = $posts_number;
	} else {
		$args['nopaging'] = true;
	}

	if ( '' !== $include_categories ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'project_category',
				'field' => 'id',
				'terms' => explode( ',', $include_categories ),
				'operator' => 'IN'
			)
		);
	}

	$projects = get_portfolio_projects( $args );

	ob_start();
	if( $projects->post_count > 0 ) {
		while ( $projects->have_posts() ) {
			$projects->the_post();
			?>
			<div id="post-<?php the_ID(); ?>" <?php post_class( 'et_pb_portfolio_item ' ); ?>>
			<?php
				$thumb = '';

				$width = 320;
				$width = (int) apply_filters( 'et_pb_portfolio_image_width', $width );

				$height = 241;
				$height = (int) apply_filters( 'et_pb_portfolio_image_height', $height );

				list($thumb_src, $thumb_width, $thumb_height) = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), array( $width, $height ) );

				$orientation = ( $thumb_height > $thumb_width ) ? 'portrait' : 'landscape';

				if ( '' !== $thumb_src ) : ?>
					<div class="et_pb_portfolio_image <?php esc_attr_e( $orientation ); ?>">
						<a href="<?php the_permalink(); ?>">
							<img src="<?php esc_attr_e( $thumb_src); ?>" alt="<?php esc_attr_e( get_the_title() ); ?>"/>
							<div class="meta">
								<span class="et_overlay"></span>

								<?php if ( 'on' === $show_title ) : ?>
									<h3><?php the_title(); ?></h3>
								<?php endif; ?>

								<?php if ( 'on' === $show_date ) : ?>
									<p class="post-meta"><?php echo get_the_date(); ?></p>
								<?php endif; ?>
							</div>
						</a>
					</div>
			<?php endif; ?>
			</div>
			<?php
		}
	}

	$posts = ob_get_clean();

	$class = " et_pb_bg_layout_{$background_layout}";

	$output = sprintf(
		'<div%4$s class="et_pb_fullwidth_portfolio %1$s%3$s%5$s" data-auto-rotate="%6$s" data-auto-rotate-speed="%7$s">
			%8$s
			<div class="et_pb_portfolio_items clearfix" data-columns="">
				%2$s
			</div><!-- .et_pb_portfolio_items -->
		</div> <!-- .et_pb_fullwidth_portfolio -->',
		( 'on' === $fullwidth ? 'et_pb_fullwidth_portfolio_carousel' : 'et_pb_fullwidth_portfolio_grid clearfix' ),
		$posts,
		esc_attr( $class ),
		( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
		( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
		( '' !== $auto && in_array( $auto, array('on', 'off') ) ? esc_attr( $auto ) : 'off' ),
		( '' !== $auto_speed && is_numeric( $auto_speed ) ? esc_attr( $auto_speed ) : '7000' ),
		( '' !== $title ? sprintf( '<h2>%s</h2>', esc_html( $title ) ) : '' )
	);

	return $output;
}

add_shortcode( 'et_pb_pricing_tables', 'et_pb_pricing_tables' );
function et_pb_pricing_tables( $atts, $content = null ) {
	extract( shortcode_atts( array(
			'module_id' => '',
			'module_class' => '',
		), $atts
	) );

	global $et_pb_pricing_tables_num;

	$et_pb_pricing_tables_num = 0;

	$content = do_shortcode( et_pb_fix_shortcodes( $content ) );

	$output = sprintf(
		'<div%3$s class="et_pb_pricing clearfix%2$s%4$s">
			%1$s
		</div>',
		$content,
		esc_attr( " et_pb_pricing_{$et_pb_pricing_tables_num}" ),
		( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
		( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' )
	);

	return $output;
}

add_shortcode( 'et_pb_pricing_table', 'et_pb_pricing_table' );
function et_pb_pricing_table( $atts, $content = null ) {
	global $et_pb_pricing_tables_num;

	extract( shortcode_atts( array(
			'featured' => 'off',
			'title' => '',
			'subtitle' => '',
			'currency' => '',
			'per' => '',
			'sum' => '',
			'button_url' => '',
			'button_text' => '',
		), $atts
	) );

	$et_pb_pricing_tables_num++;

	if ( '' !== $button_url && '' !== $button_text )
		$button_text = sprintf( '<a class="et_pb_pricing_table_button" href="%1$s">%2$s</a>',
			esc_url( $button_url ),
			esc_html( $button_text )
		);

	$output = sprintf(
		'<div class="et_pb_pricing_table%1$s">
			<div class="et_pb_pricing_heading">
				%2$s
				%3$s
			</div> <!-- .et_pb_pricing_heading -->
			<div class="et_pb_pricing_content_top">
				<span class="et_pb_et_price">%6$s%7$s%8$s</span>
			</div> <!-- .et_pb_pricing_content_top -->
			<div class="et_pb_pricing_content">
				<ul class="et_pb_pricing">
					%4$s
				</ul>
			</div> <!-- .et_pb_pricing_content -->
			%5$s
		</div>',
		( 'off' !== $featured ? ' et_pb_featured_table' : '' ),
		( '' !== $title ? sprintf( '<h2 class="et_pb_pricing_title">%1$s</h2>', esc_html( $title ) ) : '' ),
		( '' !== $subtitle ? sprintf( '<span class="et_pb_best_value">%1$s</span>', esc_html( $subtitle ) ) : '' ),
		do_shortcode( et_pb_fix_shortcodes( et_pb_extract_items( $content ) ) ),
		$button_text,
		( '' !== $currency ? sprintf( '<span class="et_pb_dollar_sign">%1$s</span>', esc_html( $currency ) ) : '' ),
		( '' !== $sum ? sprintf( '<span class="et_pb_sum">%1$s</span>', esc_html( $sum ) ) : '' ),
		( '' !== $per ? sprintf( '/%1$s', esc_html( $per ) ) : '' )
	);

	return $output;
}

function et_pb_extract_items( $content ) {
	$output = $first_character = '';

	$lines = explode( "\n", str_replace( array( '<p>', '</p>', '<br />' ), '', $content ) );

	foreach ( $lines as $line ) {
		$line = trim( $line );

		if ( '' === $line ) continue;

		$first_character = $line[0];

		if ( in_array( $first_character, array( '-', '+' ) ) )
			$line = trim( substr( $line, 1 ) );

		$output .= sprintf( '[et_pb_pricing_item available="%2$s"]%1$s[/et_pb_pricing_item]',
			$line,
			( '-' === $first_character ? 'off' : 'on' )
		);
	}

	return $output;
}

add_shortcode( 'et_pb_pricing_item', 'et_pb_pricing_item' );
function et_pb_pricing_item( $atts, $content = null ) {
	extract( shortcode_atts( array(
			'available' => 'on',
		), $atts
	) );

	$output = sprintf( '<li%2$s>%1$s</li>',
		do_shortcode( et_pb_fix_shortcodes( $content ) ),
		( 'on' !== $available ? ' class="et_pb_not_available"' : '' )
	);

	return $output;
}

add_shortcode( 'et_pb_contact_form', 'et_pb_contact_form' );
function et_pb_contact_form( $atts, $content = null ) {
	global $et_pb_contact_form_num;

	extract( shortcode_atts( array(
			'module_id' => '',
			'module_class' => '',
			'captcha' => 'on',
			'email' => '',
			'title' => '',
		), $atts
	) );

	$et_error_message = '';
	$et_contact_error = false;

	if ( isset( $_POST['et_pb_contactform_submit'] ) ) {
		if ( 'on' === $captcha && ( ! isset( $_POST['et_pb_contact_captcha'] ) || empty( $_POST['et_pb_contact_captcha'] ) ) ) {
			$et_error_message .= sprintf( '<p>%1$s</p>', esc_html__( 'Make sure you entered the captcha.', 'Divi' ) );
			$et_contact_error = true;
		} else if ( 'on' === $captcha && ( $_POST['et_pb_contact_captcha'] <> ( $_SESSION['et_pb_first_digit'] + $_SESSION['et_pb_second_digit'] ) ) ) {
			$et_error_message .= sprintf( '<p>%1$s</p>', esc_html__( 'You entered the wrong number in captcha.', 'Divi' ) );

			unset( $_SESSION['et_pb_first_digit'] );
			unset( $_SESSION['et_pb_second_digit'] );

			$et_contact_error = true;
		} else if ( empty( $_POST['et_pb_contact_name'] ) || empty( $_POST['et_pb_contact_email'] ) || empty( $_POST['et_pb_contact_message'] ) ) {
			$et_error_message .= sprintf( '<p>%1$s</p>', esc_html__( 'Make sure you fill all fields.', 'Divi' ) );
			$et_contact_error = true;
		}

		if ( ! is_email( $_POST['et_pb_contact_email'] ) ) {
			$et_error_message .= sprintf( '<p>%1$s</p>', esc_html__( 'Invalid Email.', 'Divi' ) );
			$et_contact_error = true;
		}
	} else {
		$et_contact_error = true;
		if ( isset( $_SESSION['et_pb_first_digit'] ) )
			unset( $_SESSION['et_pb_first_digit'] );
		if ( isset( $_SESSION['et_pb_second_digit'] ) )
			unset( $_SESSION['et_pb_second_digit'] );
	}

	if ( ! isset( $_SESSION['et_pb_first_digit'] ) )
		$_SESSION['et_pb_first_digit'] = $et_pb_first_digit = rand(1, 15);
	else
		$et_pb_first_digit = $_SESSION['et_pb_first_digit'];

	if ( ! isset( $_SESSION['et_pb_second_digit'] ) )
		$_SESSION['et_pb_second_digit'] = $et_pb_second_digit = rand(1, 15);
	else
		$et_pb_second_digit = $_SESSION['et_pb_second_digit'];

	if ( ! $et_contact_error && isset( $_POST['_wpnonce-et-pb-contact-form-submitted'] ) && wp_verify_nonce( $_POST['_wpnonce-et-pb-contact-form-submitted'], 'et-pb-contact-form-submit' ) ) {
		$et_email_to = '' !== $email
			? $email
			: get_site_option( 'admin_email' );

		$et_site_name = get_option( 'blogname' );

		$contact_name 	= stripslashes( sanitize_text_field( $_POST['et_pb_contact_name'] ) );
		$contact_email 	= sanitize_email( $_POST['et_pb_contact_email'] );

		$headers  = 'From: ' . $contact_name . ' <' . $contact_email . '>' . "\r\n";
		$headers .= 'Reply-To: ' . $contact_name . ' <' . $contact_email . '>';

		wp_mail( apply_filters( 'et_contact_page_email_to', $et_email_to ),
			sprintf( __( 'New Message From %1$s%2$s', 'Divi' ),
				sanitize_text_field( $et_site_name ),
				( '' !== $title ? sprintf( _x( ' - %s', 'contact form title separator', 'Divi' ), sanitize_text_field( $title ) ) : '' )
			), stripslashes( wp_strip_all_tags( $_POST['et_pb_contact_message'] ) ), apply_filters( 'et_contact_page_headers', $headers, $contact_name, $contact_email ) );

		$et_error_message = sprintf( '<p>%1$s</p>', esc_html__( 'Thanks for contacting us', 'Divi' ) );
	}

	$form = '';

	$name_label = __( 'Name', 'Divi' );
	$email_label = __( 'Email Address', 'Divi' );
	$message_label = __( 'Message', 'Divi' );

	$et_pb_contact_form_num = ! isset( $et_pb_contact_form_num ) ? 1 : $et_pb_contact_form_num++;

	$et_pb_captcha = sprintf( '
		<div class="et_pb_contact_right">
			<p class="clearfix">
				%1$s = <input type="text" size="2" class="input et_pb_contact_captcha" value="" name="et_pb_contact_captcha">
			</p>
		</div> <!-- .et_pb_contact_right -->',
		sprintf( '%1$s + %2$s', esc_html( $et_pb_first_digit ), esc_html( $et_pb_second_digit ) )
	);

	if ( $et_contact_error )
		$form = sprintf( '
			<div class="et_pb_contact">
				<div class="et-pb-contact-message">%11$s</div>

				<form class="et_pb_contact_form clearfix" method="post" action="%1$s">
					<div class="et_pb_contact_left">
						<p class="clearfix">
							<label class="et_pb_contact_form_label">%2$s</label>
							<input type="text" class="input et_pb_contact_name" value="%3$s" name="et_pb_contact_name">
						</p>
						<p class="clearfix">
							<label class="et_pb_contact_form_label">%4$s</label>
							<input type="text" class="input et_pb_contact_email" value="%5$s" name="et_pb_contact_email">
						</p>
					</div> <!-- .et_pb_contact_left -->

					<div class="clear"></div>
					<p class="clearfix">
						<label class="et_pb_contact_form_label">%7$s</label>
						<textarea name="et_pb_contact_message" class="et_pb_contact_message input">%8$s</textarea>
					</p>

					<input type="hidden" value="et_contact_proccess" name="et_pb_contactform_submit">

					<input type="submit" value="%9$s" class="et_pb_contact_submit">

					%6$s

					%10$s
				</form>
			</div> <!-- .et_pb_contact -->',
			esc_url( get_permalink( get_the_ID() ) ),
			$name_label,
			( isset( $_POST['et_pb_contact_name'] ) ? esc_attr( $_POST['et_pb_contact_name'] ) : $name_label ),
			$email_label,
			( isset( $_POST['et_pb_contact_email'] ) ? esc_attr( $_POST['et_pb_contact_email'] ) : $email_label ),
			(  'on' === $captcha ? $et_pb_captcha : '' ),
			$message_label,
			( isset( $_POST['et_pb_contact_message'] ) ? esc_attr( $_POST['et_pb_contact_message'] ) : $message_label ),
			__( 'Submit', 'Divi' ),
			wp_nonce_field( 'et-pb-contact-form-submit', '_wpnonce-et-pb-contact-form-submitted', true, false ),
			$et_error_message
		);

	$output = sprintf( '
		<div id="%4$s" class="et_pb_contact_form_container clearfix%5$s">
			%1$s
			%2$s
			%3$s
		</div> <!-- .et_pb_contact_form_container -->
		',
		( '' !== $title ? sprintf( '<h1 class="et_pb_contact_main_title">%1$s</h1>', esc_html( $title ) ) : '' ),
		( '' !== $et_error_message ? sprintf( '<div class="et-pb-contact-message">%1$s</div>', $et_error_message ) : '' ),
		$form,
		( '' !== $module_id
			? esc_attr( $module_id )
			: esc_attr( 'et_pb_contact_form_' . $et_pb_contact_form_num )
		),
		( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' )
	);

	return $output;
}

add_shortcode( 'et_pb_divider', 'et_pb_divider' );
function et_pb_divider( $atts, $content = null ) {
	extract( shortcode_atts( array(
			'module_id' => '',
			'module_class' => '',
			'color' => '',
			'show_divider' => 'off',
			'height' => '',
		), $atts
	) );

	$style = '';

	if ( '' !== $color && 'on' === $show_divider )
		$style .= sprintf( 'border-color: %s;',
			esc_attr( $color )
		);

	if ( '' !== $height )
		$style .= sprintf( 'height:%spx;',
			esc_attr( $height )
		);

	$style = '' !== $style ? " style='{$style}'" : '';

	$output = sprintf(
		'<hr%3$s class="et_pb_space%1$s%4$s"%2$s />',
		( 'on' === $show_divider ? ' et_pb_divider' : '' ),
		$style,
		( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
		( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' )
	);

	return $output;
}

add_shortcode( 'et_pb_shop', 'et_pb_shop' );
function et_pb_shop( $atts, $content = null ) {
	extract( shortcode_atts( array(
			'module_id' => '',
			'module_class' => '',
			'type' => 'recent',
			'posts_number' => '12',
			'orderby' => 'menu_order',
			'columns' => '4',
		), $atts
	) );

	$woocommerce_shortcodes_types = array(
		'recent'       => 'recent_products',
		'featured'     => 'featured_products',
		'sale'         => 'sale_products',
		'best_selling' => 'best_selling_products',
		'top_rated'    => 'top_rated_products',
	);

	$output = sprintf(
		'<div%2$s class="et_pb_shop%3$s">
			%1$s
		</div>',
		do_shortcode(
			sprintf( '[%1$s per_page="%2$s" orderby="%3$s" columns="%4$s"]',
				esc_html( $woocommerce_shortcodes_types[$type] ),
				esc_attr( $posts_number ),
				esc_attr( $orderby ),
				esc_attr( $columns )
			)
		),
		( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
		( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' )
	);

	return $output;
}

add_shortcode( 'et_pb_fullwidth_header', 'et_pb_fullwidth_header' );
function et_pb_fullwidth_header( $atts, $content = null ) {
	extract( shortcode_atts( array(
			'module_id'         => '',
			'module_class'      => '',
			'title'             => '',
			'subhead'           => '',
			'background_layout' => 'light',
			'text_orientation'  => 'left',
		), $atts
	) );

	$class = " et_pb_bg_layout_{$background_layout} et_pb_text_align_{$text_orientation}";

	$output = sprintf(
		'<section%4$s class="et_pb_fullwidth_header%3$s%5$s">
			<div class="et_pb_row">
				<h1>%1$s</h1>
				%2$s
			</div>
		</section>',
		$title,
		( $subhead ? sprintf( '<p class="et_pb_fullwidth_header_subhead">%1$s</p>', $subhead ) : '' ),
		esc_attr( $class ),
		( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
		( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' )
	);

	return $output;
}

add_shortcode( 'et_pb_fullwidth_menu', 'et_pb_fullwidth_menu' );
function et_pb_fullwidth_menu( $atts, $content = null ) {
	extract( shortcode_atts( array(
			'module_id'         => '',
			'module_class'      => '',
			'background_color'  => '',
			'background_layout' => 'light',
			'text_orientation'  => 'left',
			'menu_id'           => '',
		), $atts
	) );

	$style = '';

	if ( '' !== $background_color ) {
		$style .= sprintf( ' style="background-color: %s;"',
			esc_attr( $background_color )
		);
	}

	$class = " et_pb_bg_layout_{$background_layout} et_pb_text_align_{$text_orientation}";

	$menu = '<nav id="top-menu-nav">';
	$menuClass = 'nav';
	if ( 'on' == et_get_option( 'divi_disable_toptier' ) ) {
		$menuClass .= ' et_disable_top_tier';
	}

	$primaryNav = '';

	$menu_args = array(
		'theme_location' => 'primary-menu',
		'container'      => '',
		'fallback_cb'    => '',
		'menu_class'     => $menuClass,
		'menu_id'        => 'top-menu',
		'echo'           => false,
	);

	if ( '' !== $menu_id ) {
		$menu_args['menu'] = (int) $menu_id;
	}

	$primaryNav = wp_nav_menu( apply_filters( 'et_fullwidth_menu_args', $menu_args ) );

	if ( '' == $primaryNav ) {
		$menu .= sprintf(
			'<ul id="top-menu" class="%1$s">
				%2$s
			</ul>',
			esc_attr( $menuClass ),
			( 'on' === et_get_option( 'divi_home_link' )
				? sprintf( '<li%1$s><a href="%2$s">%3$s</a></li>',
					( is_home() ? ' class="current_page_item"' : '' ),
					esc_url( home_url( '/' ) ),
					esc_html_e( 'Home', 'Divi' )
				)
				: ''
			)
		);

		ob_start();

		show_page_menu( $menuClass, false, false );
		show_categories_menu( $menuClass, false );

		$menu .= ob_get_contents();

		ob_end_clean();
	} else {
		$menu .= $primaryNav;
	}

	$menu .= '</nav>';

	$output = sprintf(
		'<div%4$s class="et_pb_fullwidth_menu%3$s%5$s"%2$s>
			<div class="et_pb_row clearfix">
				%1$s
				<div id="et_mobile_nav_menu">
					<a href="#" class="mobile_nav closed">
						<span class="mobile_menu_bar"></span>
					</a>
				</div>
			</div>
		</div>',
		$menu,
		$style,
		esc_attr( $class ),
		( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
		( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' )
	);

	return $output;
}

add_shortcode( 'et_pb_fullwidth_map', 'et_pb_map' );
add_shortcode( 'et_pb_map', 'et_pb_map' );
function et_pb_map( $atts, $content = '' ) {
	extract( shortcode_atts( array(
			'module_id' => '',
			'module_class' => '',
			'address_lat' => '',
			'address_lng' => '',
			'zoom_level' => 18,
			'mouse_wheel' => 'on',
		), $atts
	) );

	wp_enqueue_script( 'google-maps-api' );

	$all_pins_content = do_shortcode( et_pb_fix_shortcodes( $content ) );

	$output = sprintf(
		'<div%5$s class="et_pb_map_container%6$s">
			<div class="et_pb_map" data-center-lat="%1$s" data-center-lng="%2$s" data-zoom="%3$d" data-mouse-wheel="%7$s"></div>
			%4$s
		</div>',
		esc_attr( $address_lat ),
		esc_attr( $address_lng ),
		esc_attr( $zoom_level ),
		$all_pins_content,
		( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
		( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
		esc_attr( $mouse_wheel )
	);

	return $output;
}

add_shortcode( 'et_pb_map_pin', 'et_pb_map_pin' );
function et_pb_map_pin( $atts, $content = null ) {
	global $et_pb_tab_titles;

	extract( shortcode_atts( array(
			'title' => '',
			'pin_address_lat' => '',
			'pin_address_lng' => '',
		), $atts
	) );

	$content = do_shortcode( et_pb_fix_shortcodes( $content ) );

	$output = sprintf(
		'<div class="et_pb_map_pin" data-lat="%1$s" data-lng="%2$s" data-title="%3$s">
			%4$s
		</div>',

		esc_attr( $pin_address_lat ),
		esc_attr( $pin_address_lng ),
		esc_html( $title ),
		( '' != $content ? sprintf( '<div class="infowindow">%1$s</div>', $content ) : '' )
	);

	return $output;
}