<?php

// if this is an attachment, let's redirect to the originating post

if (is_attachment()) {
	while (have_posts()) : the_post();
	
		wp_redirect(wp_get_attachment_url($post->ID));
		exit;
	
	endwhile;
	
}

