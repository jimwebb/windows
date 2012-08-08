  <div id="footer-push"></div>
  </div><!-- /#wrap -->

  <?php roots_footer_before(); ?>
  <footer id="content-info" class="<?php echo WRAP_CLASSES; ?>" role="contentinfo">
    <?php roots_footer_inside(); ?>
    <div class="footer-wrap">
	    <div class="footer-content">
	    <?php dynamic_sidebar('sidebar-footer'); ?>
	    </div>
	    <ul id="social">
	    	<li class="fb"><a href="http://www.facebook.com/WindowsCateringCompany" target="_blank">Facebook</a></li>
	    	<li class="tw"><a href="http://twitter.com/WindowsCatering" target="_blank">Twitter</a></li>
	    	<li class="pin"><a href="http://pinterest.com/windowsCatering" target="_blank">Pinterest</a></li>
	    </ul>
    </div>
  </footer>
  <?php roots_footer_after(); ?>

  <?php wp_footer(); ?>
  <?php roots_footer(); ?>

</body>
</html>