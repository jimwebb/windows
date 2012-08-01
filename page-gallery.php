<?php
/*
Template Name: Photo Gallery

*/

// Add specific CSS class by filter
add_filter('body_class','custom_classname');
function custom_classname($classes) {
	$classes[] = 'gallery';
	return $classes;
}

$bgImage = false;


get_header(); ?>
  <?php roots_content_before(); ?>
    <div id="content" class="<?php echo CONTAINER_CLASSES; ?>">
    <?php roots_main_before(); ?>
      <div id="main" class="<?php echo FULLWIDTH_CLASSES; ?>" role="main">
        <?php roots_loop_before(); ?>
		<?php /* Start loop */ ?>
		<?php while (have_posts()) : the_post(); ?>
		  <?php roots_post_before(); ?>
			<?php roots_post_inside_before(); ?>
			
			
			<?php // Get photo gallery images
			
			$gallery = get_field('gallery');
			
			if ($gallery) { ?>
			<div id="gallery">
			<ul>
			
			<?php 
			
			foreach ($gallery as $galleryitem) {
			
				$image = wp_get_attachment_image_src($galleryitem['image'], 'thumbnail');
				$large = wp_get_attachment_image_src($galleryitem['image'], 'background');
			
				$caption = $galleryitem['caption'];
				$credit = $galleryitem['credit'];
			
			?>
			<li><a href="<?php echo $large[0]; ?>">
				<img src="<?php echo $image[0]; ?>" alt="<?php echo esc_attr(strip_tags($caption));?>" />
				</a>
				<div class="caption"><?php echo $caption; ?> <span class="credit"><?php echo $credit; ?></span></div>
			</li>
			
			<?php } ?>
			
			</ul>
			</div>			
			
			<?php } ?>
						
			  <?php // the_content(); ?>
			<?php roots_post_inside_after(); ?>
		  <?php roots_post_after(); ?>
		<?php endwhile; /* End loop */ ?>
        <?php roots_loop_after(); ?>
      </div><!-- /#main -->
    <?php roots_main_after(); ?>
    </div><!-- /#content -->
  <?php roots_content_after(); ?>
<?php get_footer(); ?>