<?php
// Look for a page with the slug/permalink "lookbook"
query_posts("pagename=lookbook&posts_per_page=1&post_type=page");

// If it was found, let's display it. 
if (have_posts()) {
	include_once(get_page_template());
	return;
}
