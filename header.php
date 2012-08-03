<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->
<head>
  <meta charset="utf-8">

  <title><?php wp_title(''); ?></title>

  <?php if (current_theme_supports('bootstrap-responsive')) { ?><meta name="viewport" content="width=device-width, initial-scale=1.0"><?php } ?>

  <script src="<?php echo get_template_directory_uri(); ?>/js/vendor/modernizr-2.5.3.min.js"></script>

  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
  <script>window.jQuery || document.write('<script src="<?php echo get_template_directory_uri(); ?>/js/vendor/jquery-1.7.2.min.js"><\/script>')</script>

  <?php roots_head(); ?>
  <?php wp_head(); ?>

</head>

<body <?php body_class(); ?>>

  <!--[if lt IE 7]><div class="alert">Your browser is outdated! <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</div><![endif]-->

  <?php roots_header_before(); ?>
  
  <div id="wrap" class="<?php echo WRAP_CLASSES; ?>" role="document">

  <?php if (is_home() || is_front_page()) { ?>
  
	<header id="banner" role="banner">
	  <?php roots_header_inside(); ?>
	  <div class="<?php echo WRAP_CLASSES; ?>">
		<a class="brand" href="<?php echo home_url(); ?>/">
		  <?php bloginfo('name'); ?>
		</a>
		<nav id="nav-main" role="navigation">
		  <?php wp_nav_menu(array('theme_location' => 'primary_navigation', 'walker' => new Windows_Nav_Walker(), 'menu_class' => 'nav')); ?>
		</nav>
	
		<nav id="nav-utility" role="navigation">
		  <?php wp_nav_menu(array('theme_location' => 'utility_navigation', 'walker' => new Roots_Navbar_Nav_Walker(), 'menu_class' => 'nav')); ?>
		</nav>

	  </div>
	</header>
	
	<?php } else { ?>
	
	<header id="banner" role="banner">
	  <?php roots_header_inside(); ?>
	  <div class="<?php echo WRAP_CLASSES; ?>">
		<nav id="nav-interior" role="navigation">
		 <ul>
		<?php wp_list_pages ( array(
						'depth' => 2,
						'title_li' => '',
						'walker' => new Walker_Page_Classes(),
						'link_before' => '<span>',
						'link_after' => '</span>'
						));
		?>
		</ul>
		</nav>

	  </div>
	</header>
	
	<?php } ?>
	
	
  <?php roots_header_after(); ?>

  <?php roots_wrap_before(); ?>
  
 
	