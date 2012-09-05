<?php 

// dynamic_sidebar('sidebar-primary'); 

// display grandparent page title

// $grandparent = get_post(top_parent());

?>

<?php

	global $post;
	
	$depth = count(get_ancestors($post->ID, 'post'));
	$parent = $post->post_parent;
	$grandparent = get_post($parent);
	
	$location = ($depth == 3) ? $grandparent->post_parent : $parent;
	
	if ($location != top_parent() || stristr($post->post_title, "specials")) { ?>
	
		<h1><?php echo get_the_title($location); ?></h1>
	
		<nav id="nav-subsection">
			<ul>
				<?php 
				global $post; 
	$template = 'page-featured.php';
	$Pages = $wpdb->get_col("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_wp_page_template' AND meta_value = '$template'");
	foreach($Pages as $featured) {
		$exclude_featured .= $featured . ',';
	}
	
	$args = array(
    'child_of' => $location,
    'parent' => $location,
    'sort_column' => 'menu_order',
    'exclude' => $exclude_featured
    );
	
	$pages = get_pages( $args );
	
	foreach ( $pages as $page ) {
	
	
	if ( $post->ID == $page->ID ) {
		echo '<li class="current_page_item">';
	} else {
		echo '<li>';
	}
	
	$url = get_permalink( $page->ID );
	
	$children = get_pages('child_of='.$page->ID);
	
	//check for child pages
	if ( $children ) {
		echo '<a href="" class="no-pjax has_drop">'.$page->post_title.'</a>';

		//spit out dropdown menu
		echo '<ul class="dropdown">';
		foreach ($children as $child) {
			$childurl = get_permalink( $child->ID );
			if ( $post->ID == $child->ID ) {
				echo '<li class="current_page_item">';
			} else {
				echo '<li>';
			}
			echo '<a href="'.$childurl.'">'.$child->post_title.'</a></li>';
		}
		echo '</ul>';
		
	} else {
		echo '<a href="'.$url.'">'.$page->post_title.'</a>';
	}
	
	echo '</li>';
		
	}
				?>
			</ul>
			
			<?php //extra files ?>
			<?php
				
				$files = get_field('related-files', $post->post_parent);
			
				if ($files) { ?>
				
				<ul class="tertiary">
				
				<?php
					
					wp_list_pages ( array(
						'child_of' => $post->post_parent,
						'title_li' => '',
						'sort_column' => 'menu_order',
						'meta_key' => '_wp_page_template',
					    'meta_value' => 'page-featured.php'
					));
				
				?>
				
				<?php foreach ($files as $file) {
				
				$title = $file['title'];
				$src = $file['url'];
				
				echo '<li><a href="'.$src.'" target="_blank" class="no-pjax">'.$title.'</a></li>';
				
				} ?>
				
				</ul>
				
				<?php } ?>
		</nav>			

<?php }

// if this is a contact page, show the contact sidebar

if (stristr($post->post_title, "contact")) { ?>
	<h1><?php echo get_the_title($post->post_ID); ?></h1>
	<?php dynamic_sidebar('sidebar-contact');
}

if (is_home()) { ?>
	<!-- <h1><?php //echo get_the_title($post->post_parent); ?></h1> -->
	
	<nav id="nav-subsection">
			<ul>
				<?php 
				global $post; 
				wp_list_categories ( array(
								'depth' => 1,
								'hide_empty' => 0,
								'title_li' => ''
								));
				?>
			</ul>
		</nav>	
		
<?php } ?>