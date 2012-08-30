<?php
/*
Template Name: Lookbook
*/

// Add specific CSS class by filter
add_filter('body_class','custom_classname');
function custom_classname($classes) {
	$classes[] = 'lookbook';
	return $classes;
}

$bgImage = false;

get_header(); ?>
  <?php roots_content_before(); ?>
    <div id="content-wrapper"> 
    <div id="content" class="<?php echo CONTAINER_CLASSES; ?>">
    <?php roots_main_before(); ?>
      <div id="main" class="<?php echo MAIN_CLASSES; ?> isotope-item" role="main">
        <?php roots_loop_before(); ?>
		<?php /* Start loop */ ?>
		
		<?php while (have_posts()) : the_post(); ?>
		  <?php roots_post_before(); ?>
			<?php roots_post_inside_before(); ?>
			  <?php the_content(); ?>
			<?php roots_post_inside_after(); ?>
		  <?php roots_post_after(); ?>
		<?php endwhile; /* End loop */ ?>
		
		</div><!-- /#main -->
	
		
		<?php /* Start lookbook loop */ 
		query_posts("post_type=lookbook&posts_per_page=-1&orderby=rand");
		?>
		<?php while (have_posts()) : the_post(); ?>
		  <?php roots_post_before(); ?>
			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php // roots_post_inside_before();
			?>
			<?php		
				$image = wp_get_attachment_image_src(get_field('image'), 'thumbnail');
				$large = wp_get_attachment_image_src(get_field('image'), 'large');
			?>
			<a class="fancybox" rel="lookbook" href="<?php echo $large[0]; ?>">
				<img src="<?php echo $image[0]; ?>" alt="<?php echo get_the_title(get_field('image_test')); ?>" />
			</a>
			<div class="caption"><?php echo get_field('caption'); ?></div>
			<?php // roots_post_inside_after();
			?>
			</div>
		  <?php roots_post_after(); ?>
		<?php endwhile; /* End lookbook loop */ ?>
		</ul>

        <?php roots_loop_after(); ?>
    <?php roots_main_after(); ?>
    </div><!-- /#content -->
</div><!-- /#content-wrapper -->
  <?php roots_content_after(); ?>
<?php get_footer(); ?>