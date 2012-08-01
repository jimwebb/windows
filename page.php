<?php get_header(); ?>
 <?php   
  if (!is_front_page() && !is_home()) {
  
  	// echo the background image
    $grandparent = top_parent_with_thumbnail(); ?>
    
    <?php if (has_post_thumbnail( $grandparent )) { 
		$bgImage = wp_get_attachment_image_src( get_post_thumbnail_id( $grandparent ), 'background' );
		$bgImage = $bgImage[0];
 	
	} ?>
	 
	<div id="page-header" <?php if ($bgImage) echo 'data-url="' . $bgImage . '" class="background"' ; ?>>

    <!-- <section id="nav-section">
		<nav>
		<ul>
		<?php wp_list_pages ( array(
						'child_of' => $grandparent,
						'depth' => 1,
						'title_li' => ''
						));
		?>
		</ul>
		</nav>			
	</section>
	-->
	
	</div>
	<?php } ?>
  <?php roots_content_before(); ?>
  
   <div id="content" class="<?php echo CONTAINER_CLASSES; ?>">
    
     <?php roots_main_before(); ?>
      <div id="main" class="<?php echo MAIN_CLASSES; ?>" role="main">
        <?php roots_loop_before(); ?>
        <?php get_template_part('loop', 'page'); ?>
        <?php roots_loop_after(); ?>
        <?php edit_post_link('Edit this page', '<p>', '</p>'); ?>
      </div><!-- /#main -->
    <?php roots_main_after(); ?>
    <?php roots_sidebar_before(); ?>
      <aside id="sidebar" class="<?php echo SIDEBAR_CLASSES; ?>" role="complementary">
      <?php roots_sidebar_inside_before(); ?>
        <?php get_sidebar(); ?>
      <?php roots_sidebar_inside_after(); ?>
      </aside><!-- /#sidebar -->
    <?php roots_sidebar_after(); ?>
    </div><!-- /#content -->
  <?php roots_content_after(); ?>
<?php get_footer(); ?>