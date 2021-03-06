<?php

// since we're here, we've already done redirects. So send a header that allows Pjax to know what our URL is.

header('X-PJAX-URL: '. $_SERVER['REQUEST_URI']);

?><!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->
<head>
  <meta charset="utf-8">

  <title><?php if (is_front_page()) { bloginfo('name'); } else { wp_title('|', true, 'right'); bloginfo('name'); } ?></title>
  
  <meta name="description" content="For 25 years Windows Catering Company has established a national reputation for exceptional food, creativity and presentation combined with outstanding service for galas, fundraisers, corporate meetings, weddings, mitzvahs, and special events.">
  
  <meta name="keywords" content="Catering Washington dc, catering VA, Catering MD, full-service catering, wedding planner, event planner, corporate catering, galas, fundraisers, bar mitzvahs, bat mitzvahs, breakfast, luncheons, business meetings, receptions, weddings, wedding cakes, social events, certified green catering, national pastry champion, sustainable catering, Washington DC, Maryland, Virginia">
  

  <?php if (current_theme_supports('bootstrap-responsive')) { ?><meta name="viewport" content="width=device-width, initial-scale=1.0"><?php } ?>

  <script src="<?php echo get_template_directory_uri(); ?>/js/vendor/modernizr-2.5.3.min.js"></script>

  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
  <script>window.jQuery || document.write('<script src="<?php echo get_template_directory_uri(); ?>/js/vendor/jquery-1.7.2.min.js"><\/script>')</script>

  <?php roots_head(); ?>
  <?php wp_head(); ?>

</head>

<?php 
	add_filter('body_class','home_custom_classname');
	function home_custom_classname($classes) {
		if (!is_front_page() && !is_home()) {
			$classes[] = 'not-home';
		}
		return $classes;
	}
?>

<body <?php body_class(); ?>>

  <!--[if lt IE 7]><div class="alert">Your browser is outdated! <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</div><![endif]-->

  <?php roots_header_before(); ?>
  
  <div id="wrap" class="<?php echo WRAP_CLASSES; ?>" role="document">

  
	<!--
<header id="banner" role="banner">
	  <?php //roots_header_inside(); ?>
	  <div class="<?php //echo WRAP_CLASSES; ?>">
		<a class="brand" href="<?php //echo home_url(); ?>/">
		  <?php //bloginfo('name'); ?>
		</a>
		<nav id="nav-main" role="navigation">
		  <?php //wp_nav_menu(array('theme_location' => 'primary_navigation', 'walker' => new Windows_Nav_Walker(), 'menu_class' => 'nav')); ?>
		</nav>

	  </div>
	</header>
-->

<?php

$menu_name = 'primary_navigation';

    if ( ( $locations = get_nav_menu_locations() ) && isset( $locations[ $menu_name ] ) ) {
	$menu = wp_get_nav_menu_object( $locations[ $menu_name ] );

	$menu_items = wp_get_nav_menu_items($menu->term_id);

	$menu_list = '';

	foreach ( (array) $menu_items as $key => $menu_item ) {
	    $title = $menu_item->title;
	    $slug = $menu_item->attr_title;
	    $url = $menu_item->url;
	    $menu_list .= '<li class="' . $slug . '"><a href="' . $url . '">' . $title . '';
	    $menu_list .= '<span class="'.$slug.' home-slideshow">';
	    $menu_list .= '<img src="" data-src="/img/'.$slug.'3.jpg" />';
	    $menu_list .= '<img src="" data-src="/img/'.$slug.'4.jpg" />';
	    $menu_list .= '<img src="" data-src="/img/'.$slug.'5.jpg" />';
	    $menu_list .= '<img src="" data-src="/img/'.$slug.'6.jpg" />';
	    $menu_list .= '<img src="" data-src="/img/'.$slug.'7.jpg" />';
	    $menu_list .= '<img src="" data-src="/img/'.$slug.'8.jpg" />';
	    $menu_list .= '<img src="" data-src="/img/'.$slug.'9.jpg" />';
	    $menu_list .= '</span></a></li>';
	}
    }
?>
	
	
	<header id="banner">

		<div class="logo"><a class="brand" href="<?php echo home_url(); ?>/"><?php bloginfo('name'); ?></a></div>
		<nav id="nav-utility" role="navigation">
		<?php wp_nav_menu(array('theme_location' => 'utility_navigation', 'walker' => new Roots_Navbar_Nav_Walker(), 'menu_class' => 'nav')); ?>
		</nav>
		
		<nav id="nav-main">
			<ul>
				<?php echo $menu_list; ?>
			</ul>
		</nav>
		
	  <?php roots_header_inside(); ?>

	  <div class="<?php echo WRAP_CLASSES; ?> nav-interior-container">
		<nav id="nav-interior" role="navigation">
		 <ul>
		<?php			
			wp_list_pages ( array(
						'depth' => 2,
						'title_li' => '',
						'walker' => new Walker_Page_Classes(),
						'link_before' => '<span>',
						'link_after' => '</span>',
						'exclude' => $exclude_featured
						));
		?>
		</ul>
		</nav>

	  </div>
	</header>
	

	
  <?php roots_header_after(); ?>

  <?php roots_wrap_before(); ?>
  
 
	