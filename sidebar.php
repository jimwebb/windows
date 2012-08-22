<?php 

// dynamic_sidebar('sidebar-primary'); 

// display grandparent page title

// $grandparent = get_post(top_parent());

?>

<?php

	global $post;
	
	if ($post->post_parent != top_parent()) { ?>
	
		<h1><?php echo get_the_title($post->post_parent); ?></h1>
	
		<nav id="nav-subsection">
			<ul>
				<?php 
				global $post; 
				wp_list_pages ( array(
								'child_of' => $post->post_parent,
								'depth' => 1,
								'title_li' => ''
								));
				?>
			</ul>
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