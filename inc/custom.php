<?php

// ------------------------------------------------------------------- 
// ---------------------- Disable admin bar -- messes with CSS.
// ------------------------------------------------------------------- 

show_admin_bar(false);

// ------------------------------------------------------------------- 
// ---------------------- Contact Page Sidebar
// ------------------------------------------------------------------- 

register_sidebar(array(
    'name' => 'Contact Form Sidebar',
    'id' => 'sidebar-contact',
    'before_widget' => '<section>',
    'after_widget' => '</section>',
    'before_title' => '<h2>',
    'after_title' => '</h2>',
));


// ------------------------------------------------------------------- 
// ---------------------- Background image size
// ------------------------------------------------------------------- 

add_image_size( 'background', 2000, 1000, false );


// ------------------------------------------------------------------- 
// ---------------------- Always show all lookbook images
// ------------------------------------------------------------------- 

function lookbook_pagesize( $query ) {
    if ( is_post_type_archive('lookbook-images') ){
        $query->query_vars['posts_per_page'] = 9999;
        return;
    }
}
add_action('pre_get_posts', 'lookbook_pagesize', 1);


// ------------------------------------------------------------------- 
// - Utility Function: Translate page headings into styles for Windows
// ------------------------------------------------------------------- 

function translateWindowsItem($string) {
	
	$string = strtolower($string);
	
	if (strstr($string, "corp")) $theid = "corporate";
	if (strstr($string, "wedd")) $theid = "weddings";
	if (strstr($string, "spec")) $theid = "events";
	if (strstr($string, "comp")) $theid = "company";	
	
	if ($theid) return $theid;
	
	return $string;

}


// ------------------------------------------------------------------- 
// ---------------------- Add top-level page slug as a body class
// ------------------------------------------------------------------- 


add_filter('body_class','top_level_page_class');

function top_level_page_class($classes) {
	global $post;
	
	$parent = top_parent($post->ID);
	
	// get the slug of that page (this is a shortcut)
	$slug = basename(get_permalink($parent));
	
	$slug = translateWindowsItem($slug);
	
	// add slug to the $classes array
	$classes[] = $slug;
	
	// return the $classes array
	return $classes;
}



// ------------------------------------------------------------------- 
// ---------------------- Nav menu
// ------------------------------------------------------------------- 


// This theme uses wp_nav_menu()
function register_my_menus() {
	register_nav_menus(
		array(
			'utility_navigation' => __( 'Utility menu' )
		)
	);
}
add_action( 'init', 'register_my_menus' );


// ------------------------------------------------------------------- 
// ---------------------- top parent -- get top-most parent ID
// ------------------------------------------------------------------- 

function top_parent($id = null) {

	if ($id == null) {
		global $post;
	} else {
		$post = get_post($id);
	}
	
	// get the topmost parent
	if ($post->post_parent)	{
		$ancestors=get_post_ancestors($post->ID);
		$root=count($ancestors)-1;
		$parent = $ancestors[$root];
	} else {
		$parent = 0;
	}

	return $parent;	
}


// ------------------------------------------------------------------- 
// --------- top parent with thumbnail
// --------- get top-most parent that has a featured image
// ------------------------------------------------------------------- 

function top_parent_with_thumbnail($id = null) {

	if ($id == null) {
		global $post;
	} else {
		$post = get_post($id);
	}
	
	if (has_post_thumbnail($post)) return $post->ID;
	
	// get the topmost parent
	if ($post->post_parent)	{
		$ancestors=get_post_ancestors($post->ID);
		foreach ($ancestors as $ancestor) {
			if (has_post_thumbnail($ancestor)) return $ancestor;
			}
	} else {
		return 0;
	}

	return false;	
}




// ------------------------------------------------------------------- 
// ---------------------- Login Logo
// ------------------------------------------------------------------- 

// custom admin login logo, 326 x 63
// your image should be in your theme folder/img/logo-login.png

function custom_login_logo() {
	echo '<style type="text/css">
	.login h1 a { background-size: auto; background-image: url('.get_bloginfo('template_directory').'/img/logo-login.png) !important; height: 150px !important; }
	</style>';
}
add_action('login_head', 'custom_login_logo');



// ------------------------------------------------------------------- 
// ---------------------- Vendor JS
// ------------------------------------------------------------------- 

function our_scripts() {

if (!is_admin()) {
		
		wp_register_script('backstretch', get_template_directory_uri() . '/js/vendor/jquery.backstretch.min.js', array('jquery'), null, true);
		wp_enqueue_script('backstretch');
		
		wp_register_script('pjax', get_template_directory_uri() . '/js/vendor/jquery.pjax.js', array('jquery'), null, true);
		wp_enqueue_script('pjax');
		
		wp_register_script('isotope', get_template_directory_uri() . '/js/vendor/jquery.isotope.min.js', array('jquery'), null, true);
		wp_enqueue_script('isotope');

		wp_register_script('fancybox', get_template_directory_uri() . '/js/vendor/jquery.fancybox.pack.js', array('jquery'), null, true);
		wp_enqueue_script('fancybox');
		
		wp_register_script('inflickity', get_template_directory_uri() . '/js/vendor/inflickity.js', array('jquery'), null, true);
		wp_enqueue_script('inflickity');

		wp_register_script('queue', get_template_directory_uri() . '/js/vendor/Queue.js', array('jquery'), null, true);
		wp_enqueue_script('queue');

		wp_register_script('pjax', get_template_directory_uri() . '/js/vendor/jquery.pjax.js', array('jquery'), null, true);
		wp_enqueue_script('pjax');
		
		wp_register_script('cycle', get_template_directory_uri() . '/js/vendor/jquery.cycle.all.js', array('jquery'), null, true);
		wp_enqueue_script('cycle');

		wp_register_script('requestanimationframe', get_template_directory_uri() . '/js/vendor/requestanimationframe.js', array('jquery'), null, true);
		wp_enqueue_script('requestanimationframe');


		// Add our CSS. Do it here instead of in the header.php file so WordPress or plugins can combine & minify CSS.
		
		// wp_register_style('fancybox_css', get_bloginfo('template_directory') . '/js/vendor/fancybox/jquery.fancybox.css', array(), 1, "all");
		// wp_enqueue_style( 'fancybox_css' );
	}
}

add_action("init","our_scripts");	


// ------------------------------------------------------------------- 
// ------- Custom Nav Walker (for home page menu)
// ------- adds markup we need on that menu
// ------------------------------------------------------------------- 

class Windows_Nav_Walker extends Walker_Nav_Menu {
  function check_current($classes) {
    return preg_match('/(current[-_])/', $classes);
  }

  function start_el(&$output, $item, $depth, $args) {
    global $wp_query;
    $indent = ($depth) ? str_repeat("\t", $depth) : '';

    $slug = sanitize_title($item->title);
    $id = 'menu-' . $slug;

    $class_names = $value = '';
    $classes = empty($item->classes) ? array() : (array) $item->classes;

    $classes = array_filter($classes, array(&$this, 'check_current'));

    if ($custom_classes = get_post_meta($item->ID, '_menu_item_classes', true)) {
      foreach ($custom_classes as $custom_class) {
        $classes[] = $custom_class;
      }
    }

    $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
    $class_names = $class_names ? ' class="' . $id . ' ' . esc_attr($class_names) . '"' : ' class="' . $id . '"';


	// let's reset the ID
	
	$theid = translateWindowsItem($id);
	
	
    $output .= $indent . '<li' . $class_names . ($theid ? ' id="nav-'.$theid.'"' : '') . '>';

    $attributes  = ! empty($item->attr_title) ? ' title="'  . esc_attr($item->attr_title) .'"' : '';
    $attributes .= ! empty($item->target)     ? ' target="' . esc_attr($item->target    ) .'"' : '';
    $attributes .= ! empty($item->xfn)        ? ' rel="'    . esc_attr($item->xfn       ) .'"' : '';
    $attributes .= ! empty($item->url)        ? ' href="'   . esc_attr($item->url       ) .'"' : '';

    $item_output  = $args->before;
    $item_output .= '<a'. $attributes .'>';
 	/* 
	$item_output .= '<table class="link">';  
    $item_output .= '<tr><td>'; 
    */
	$item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
 	
	/*
	$item_output .= '</td></tr>';  
 	$item_output .= '</table>';  
    */
	$item_output .= '</a>';
    $item_output .= $args->after;

    $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
  }
}


// ------------------------------------------------------------------- 
// ------- Custom Nav Walker (for interior navigation bar)
// ------- adds markup we need on that menu
// ------------------------------------------------------------------- 

class Walker_Page_Classes extends Walker_Page {

	/**
	 * @see Walker::start_el()
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $page Page data object.
	 * @param int $depth Depth of page. Used for padding.
	 * @param int $current_page Page ID.
	 * @param array $args
	 */
	function start_el( &$output, $page, $depth, $args, $current_page = 0 ) {
		if ( $depth )
			$indent = str_repeat("\t", $depth);
		else
			$indent = '';

		extract($args, EXTR_SKIP);
		$css_class = array('page_item', 'page-item-'.$page->ID);
		if ( !empty($current_page) ) {
			$_current_page = get_page( $current_page );
			_get_post_ancestors($_current_page);
			if ( isset($_current_page->ancestors) && in_array($page->ID, (array) $_current_page->ancestors) )
				$css_class[] = 'current_page_ancestor';
			if ( $page->ID == $current_page )
				$css_class[] = 'current_page_item';
			elseif ( $_current_page && $page->ID == $_current_page->post_parent )
				$css_class[] = 'current_page_parent';
		} elseif ( $page->ID == get_option('page_for_posts') ) {
			$css_class[] = 'current_page_parent';
		}
		
		$css_class[] = 'menu-'.sanitize_title(get_the_title($page->ID));
		
		if (!$page->post_parent) {
			$css_class[] = 'nav-'.translateWindowsItem(get_the_title($page->ID));
		}
		
		$css_class = implode( ' ', apply_filters( 'page_css_class', $css_class, $page, $depth, $args, $current_page ) );

		$output .= $indent . '<li class="' . $css_class . '"><a href="' . get_permalink($page->ID) . '">' . $link_before . apply_filters( 'the_title', $page->post_title, $page->ID ) . $link_after . '</a>';

		if ( !empty($show_date) ) {
			if ( 'modified' == $show_date )
				$time = $page->post_modified;
			else
				$time = $page->post_date;

			$output .= " " . mysql2date($date_format, $time);
		}
	}


}



// ------------------------------------------------------------------- 
// ---------------------- Simplify Posts and Pages
// ------------------------------------------------------------------- 

add_action( 'add_meta_boxes', 'simplify_metaboxes' );

function simplify_metaboxes() {

	// Uncomment to remove meta-boxes from page and post entry screens

	// Tags
	remove_meta_box('tagsdiv-post_tag', 'post', 'side');

	// Categories
	// remove_meta_box('categorydiv', 'post', 'side');

	// Page Attributes
	// remove_meta_box('pageparentdiv', 'page', 'side');
	
	// Excerpts
	remove_meta_box('postexcerpt', 'post', 'normal');
	
	// Trackbacks
	remove_meta_box('trackbacksdiv', 'post', 'normal');
	remove_meta_box('trackbacksdiv', 'page', 'normal');

	// Custom Fields
	remove_meta_box('postcustom', 'post', 'normal');
	remove_meta_box('postcustom', 'page', 'normal');

	// Comments
	remove_meta_box('commentstatusdiv', 'post', 'normal');
	remove_meta_box('commentsdiv', 'post', 'normal');
	remove_meta_box('commentstatusdiv', 'page', 'normal');
	remove_meta_box('commentsdiv', 'page', 'normal');

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


// ------------------------------------------------------------------- 
// ---------------------- Rich Text Editor (TinyMCE)
// ------------------------------------------------------------------- 

// Change TinyMCE options to include HTML headers (h1,h2,h3,etc.)
// and reduce ability to change colors, etc. 

function change_mce_options( $init ) {

// Load our stylesheet 

// $init['content_css'] = get_bloginfo('template_directory') . "/editor-style.css";
 
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
 
 
 $init['theme_advanced_buttons1'] = "formatselect,fontsizeselect,|,bold,italic,|,bullist,numlist,blockquote,|,outdent,indent,|,justifyleft,justifycenter,justifyright,|,link,unlink,|,spellchecker". $init['theme_advanced_buttons1'];
 
 // $init["theme_advanced_buttons2"] = "tablecontrols";
//  $init["theme_advanced_buttons3"] = "";
 
 $init['wordpress_adv_hidden'] = false; // show the second and third row
 $init['theme_advanced_font_sizes'] = "small";
 
 $init['theme_advanced_blockformats'] = 'p,h1,h2,h3';
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


// add_filter( 'mce_external_plugins', 'mce_external_plugins' );

function mce_external_plugins( $plugin_array ) {
	$plugin_array['table'] = get_template_directory_uri() . '/js/vendor/table/editor_plugin.js';
	return $plugin_array;
}
	


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




// ----------------------------------------------------------
// Custom Post Type: Lookbook Images
// ----------------------------------------------------------

add_action('init', 'custom_lookbook_init');

function custom_lookbook_init() 
{
  $labels = array(
    'name' => __('Lookbook Images'),
    'singular_name' => __('Image'),
    'add_new' => __('Add Image'),
    'add_new_item' => __('Add New Image'),
    'all_items' => __('Lookbook Images'),
    'edit_item' => __('Edit Image'),
    'new_item' => __('New Image'),
    'view_item' => __('View Image'),
    'search_items' => __('Search Images'),
    'not_found' =>  __('No images found'),
    'not_found_in_trash' => __('No images found in Trash'), 
    'parent_item_colon' => '',
    'menu_name' => 'Lookbook'

  );
  $args = array(
    'labels' => $labels,
    '_builtin' => false,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true, 
    'show_in_menu' => true, 
    'query_var' => true,
    'rewrite' => array("slug" => "lookbook"),
    'capability_type' => 'post',
    'has_archive' => true, 
    'hierarchical' => false,
    'menu_position' => null,
    'taxonomies' => array('post_tag'),
    'supports' => array('title')
  ); 
  register_post_type('lookbook',$args);

}


//add filter to ensure the text is displayed when user updates a gallery

add_filter('post_updated_messages', 'lookbook_updated_messages');
function lookbook_updated_messages( $messages ) {
  global $post, $post_ID;

  $messages['lookbook'] = array(
    0 => '', // Unused. Messages start at index 1.
    1 => sprintf( __('Lookbook image updated. <a href="%s">View image</a>'), esc_url( get_permalink($post_ID) ) ),
    2 => __('Custom field updated.'),
    3 => __('Custom field deleted.'),
    4 => __('Image updated.'),
    // translators: %s: date and time of the revision 
    5 => isset($_GET['revision']) ? sprintf( __('Image restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
    6 => sprintf( __('Image published. <a href="%s">View image</a>'), esc_url( get_permalink($post_ID) ) ),
    7 => __('Image saved.'),
    8 => sprintf( __('Image submitted. <a target="_blank" href="%s">Preview image</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
    9 => sprintf( __('Image scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Image</a>'),
      // translators: Publish box date format, see http://php.net/date
      date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
    10 => sprintf( __('Image draft updated. <a target="_blank" href="%s">Preview image</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
  );

  return $messages;
}





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


// ------------------------------------------------------------------- 
// ---------------------- BWP Minify
// ------------------------------------------------------------------- 
// if BWP minify is installed, fix the file paths


// add_filter('bwp_minify_script_header','minify_scripts');
// add_action('bwp_minify_before_header_scripts','minify_scripts');



function minify_scripts($content) {
	global $bwp_minify;
	print_r($bwp_minify);
	// exit();
}


