<?php /* If there are no posts to display, such as an empty archive page */ ?>
<?php if (!have_posts()) { ?>
  <div class="alert alert-block fade in">
    <a class="close" data-dismiss="alert">&times;</a>
    <p><?php _e('Sorry, no results were found.', 'roots'); ?></p>
  </div>
<?php } ?>

<?php /* Start loop */ ?>
<ul>
<?php while (have_posts()) : the_post(); ?>
  <?php roots_post_before(); ?>
    <li id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
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
    </li>
  <?php roots_post_after(); ?>
<?php endwhile; /* End loop */ ?>

</ul>