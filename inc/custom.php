<?php

// ------------------------------------------------------------------- 
// ---------------------- Disable admin bar -- messes with CSS.
// ------------------------------------------------------------------- 

// show_admin_bar(false);



// ------------------------------------------------------------------- 
// ----------------------  Add our javascript
// Enqueue it for better minification and updating
// ------------------------------------------------------------------- 

// Put jQuery and Selectivizr at the footer; Modernizr in the header.
// Use our, more recent, version of jQuery

function is_login_page() {
    return in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'));
}

function boilerplate_scripts() {

    if (!is_admin() && !is_login_page()) {
		
		wp_enqueue_script( 'jquery' );
		
		wp_register_script('roots_plugins', get_template_directory_uri() . '/js/plugins.js', array('jquery'), null, true);
		wp_register_script('roots_script', get_template_directory_uri() . '/js/script.js', array('jquery'), null, true);
		wp_enqueue_script('roots_plugins');
		wp_enqueue_script('roots_script');
		
		
		// Add our CSS. Do it here instead of in the header.php file so WordPress or plugins can combine & minify CSS.
		
		wp_register_style(
		'jscrollpane_css',
		get_bloginfo('template_directory') . '/js/libs/jquery.jscrollpane.css',
		array(), 1, "all");
		// wp_enqueue_style( 'jscrollpane_css' );

	}

}

add_action("init","boilerplate_scripts");



// ======================= LOGIN LOGO ======================== 

// custom admin login logo, 326 x 63
// your image should be in your theme folder/img/logo-login.png

function custom_login_logo() {
	echo '<style type="text/css">
	h1 a { background-image: url('.get_bloginfo('template_directory').'/img/logo-login.png) !important; }
	</style>';
}
// add_action('login_head', 'custom_login_logo');




// ======================= SIMPLIFY POSTS & PAGES ========================


add_action( 'add_meta_boxes', 'simplify_metaboxes' );

function simplify_metaboxes() {

	// Uncomment to remove meta-boxes from page and post entry screens

	// Tags
	remove_meta_box('tagsdiv-post_tag', 'post', 'side');

	// Categories
	remove_meta_box('categorydiv', 'post', 'side');

	// Page Attributes
	// remove_meta_box('pageparentdiv', 'page', 'side');
	
	// Excerpts
	remove_meta_box('postexcerpt', 'post', 'normal');
	
	// Trackbacks
	// remove_meta_box('trackbacksdiv', 'post', 'normal');
	// remove_meta_box('trackbacksdiv', 'page', 'normal');

	// Custom Fields
	remove_meta_box('postcustom', 'post', 'normal');
	remove_meta_box('postcustom', 'page', 'normal');

	// Comments
	// remove_meta_box('commentstatusdiv', 'post', 'normal');
	// remove_meta_box('commentsdiv', 'post', 'normal');
	// remove_meta_box('commentstatusdiv', 'page', 'normal');
	// remove_meta_box('commentsdiv', 'page', 'normal');

	// Revisions
	// remove_meta_box('revisionsdiv', 'post', 'normal');
	// remove_meta_box('revisionsdiv', 'page', 'normal');

	// Author
	remove_meta_box('authordiv', 'post', 'normal');
	remove_meta_box('authordiv', 'page', 'normal');

	// Featured Image (uncomment both)
	remove_meta_box('postimagediv', 'post', 'side');
	// remove_meta_box('postimagediv', 'page', 'side');

}


// ======================== RICH TEXT EDITOR (TinyMCE) ======================== 

// Change TinyMCE options to include HTML headers (h1,h2,h3,etc.)
// and reduce ability to change colors, etc. 

function change_mce_options( $init ) {

// Load our stylesheet 

$init['content_css'] = get_bloginfo('template_directory') . "/editor-style.css";
 
 /* Customize TinyMCE buttons 
    Complete list: http://tinymce.moxiecode.com/wiki.php/Buttons/controls 
	
	Wordpress defaults, FYI:
	
	 $init["theme_advanced_buttons1"] = "bold,italic,strikethrough,|,bullist,numlist,blockquote,|,justifyleft,justifycenter,justifyright,|,link,unlink,wp_more,|,spellchecker,fullscreen,wp_adv";
	 $init["theme_advanced_buttons2"] = "formatselect,underline,justifyfull,forecolor,|,pastetext,pasteword,removeformat,|,charmap,|,outdent,indent,|,undo,redo,wp_help";
	 $init["theme_advanced_buttons3"]="";
 */
 
 $pos = strpos($init['theme_advanced_buttons1'], "wp_adv,");
 
 if ($pos != false) {
 	$init['theme_advanced_buttons1'] = substr( $init['theme_advanced_buttons1'], ($pos+6)); 
 } else {
 	$init['theme_advanced_buttons1'] = "";
 }
 
 // The list of buttons that will appear in the WYSIWIG edit
 // Feel free to customize
 
 $init['theme_advanced_buttons1'] = "formatselect,|,bold,italic,|,bullist,numlist,blockquote,|,outdent,indent,|,justifyleft,justifycenter,justifyright,|,link,unlink,|,spellchecker". $init['theme_advanced_buttons1'];
 
 $init["theme_advanced_buttons2"] = "";
 $init['theme_advanced_blockformats'] = 'p,h1,h2,h3,h4';
 $init['theme_advanced_disable'] = 'forecolor';
 
 if (isset( $init['extended_valid_elements'] )) { 
 $init['extended_valid_elements'] .= ",";
 };
 
 $init ['extended_valid_elements'] .= "canvas[id|width|height],script[src|type]," .
"object[classid|codebase|width|height|align|name|id],param[name|value],embed[quality|type|pluginspage|width|height|src|align]," .
"iframe[src|frameborder|width|height|scrolling|name|id]," .
"video[src|audio|autoplay|controls|width|height|loop|preload|poster],audio[src|autoplay|loop|controls|preload],source[id|src|type],";



return $init;
 }
add_filter('tiny_mce_before_init', 'change_mce_options');




// ======================== REMOVE DASHBOARD WIDGETS ========================


/*
 * Remove senseless dashboard widgets for non-admins.
   (Un)Comment or delete as you wish.
 */
function remove_dashboard_widgets() {
	global $wp_meta_boxes;
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']); // Plugins widget
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']); // WordPress Blog widget
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']); // Other WordPress News widget
	//unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']); // Right Now widget
	//unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']); // Quick Press widget
	//unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']); // Incoming Links widget
	//unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_recent_drafts']); // Recent Drafts widget
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']); // Recent Comments widget
}
add_action('wp_dashboard_setup', 'remove_dashboard_widgets' );


/**
 *	Hide Menu Items
 */
 
function themename_configure_menu_page(){
	
	// remove_menu_page("link-manager.php"); //Hide Links
	// remove_menu_page("edit-comments.php"); //Hide Comments
	//remove_menu_page("tools.php"); //Hide Tools

}

// add_action("admin_menu","themename_configure_menu_page"); 



// ----------------------------------------------------------
// Add image_src link to head for social media sites
// ----------------------------------------------------------

function image_src_rel() {

	global $post;

	if ( !function_exists( 'has_post_thumbnail' ) ) { return; }

	if ( !is_singular() ) { return; }
	
	if (!has_post_thumbnail( $post->ID ) ) {
		$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
		$thumb = $matches[1][0];
	} else {
		$thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium' );
		$thumb = $thumb[0];
	}
	
	echo '<link rel="image_src" href="' . esc_attr( $thumb ) . '" />';

}

add_action( 'wp_head', 'image_src_rel' );

