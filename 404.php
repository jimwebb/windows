<?php
// Look for a page with the slug/permalink "not-found"
query_posts("pagename=not-found&posts_per_page=1&post_type=page");

// If it was found, let's display it. 
if (have_posts()) {
	include_once(get_page_template());
	return;
}

<?php get_header(); ?>
  <?php roots_content_before(); ?>
    <div id="content" class="<?php echo CONTAINER_CLASSES; ?>">
    <?php roots_main_before(); ?>
      <div id="main" class="<?php echo FULLWIDTH_CLASSES; ?>" role="main">
        <div class="page-header">
          <h1><?php _e('File Not Found', 'roots'); ?></h1>
        </div>
        <div class="alert alert-block fade in">
          <a class="close" data-dismiss="alert">&times;</a>
          <p><?php _e('The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.', 'roots'); ?></p>
        </div>
        <p><?php _e('Please try the following:', 'roots'); ?></p>
        <ul>
          <li><?php _e('Check your spelling', 'roots'); ?></li>
          <li><?php printf(__('Return to the <a href="%s">home page</a>', 'roots'), home_url()); ?></li>
          <li><?php _e('Click the <a href="javascript:history.back()">Back</a> button', 'roots'); ?></li>
        </ul>
        <?php get_search_form(); ?>
      </div><!-- /#main -->
    <?php roots_main_after(); ?>
    </div><!-- /#content -->
  <?php roots_content_after(); ?>
<?php get_footer(); ?>