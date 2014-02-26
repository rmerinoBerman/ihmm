<?php

// REGISTER CUSTOM POST TYPE
	add_action( 'init', 'register_post_type_guestposts');
	function register_post_type_guestposts(){

		$labels = array(
			'name' => 'Guestposts',
			'singular_name' => 'Guestpost',
			'add_new' => 'Add New',
			'add_new_item' => 'Add New Guestpost',
			'edit_item' => 'Edit Guestpost',
			'new_item' => 'New Guestpost',
			'view_item' => 'View Guestpost',
			'search_items' => 'Search Guestposts',
			'not_found' => 'Nothing found',
			'not_found_in_trash' => 'Nothing found in trash',
			'parent_item_colon' => ''
		);

		$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => true,
			'capability_type' => 'post',
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array('title', 'editor', 'thumbnail')
		);

		register_post_type( 'guestposts', $args);

	}

// DEFINE META BOXES
	$guestpostsMetaBoxArray = array(
	    "guestposts_company_meta" => array(
	    	"id" => "guestposts_company_meta",
	        "name" => "Company",
	        "post_type" => "guestposts",
	        "position" => "side",
	        "priority" => "low",
	        "callback_args" => array(
	        	"input_type" => "input_text",
	        	"input_name" => "company"
	        )
	    ),
	);

// ADD META BOXES
	add_action( "admin_init", "admin_init_guestposts" );
	function admin_init_guestposts(){
		global $guestpostsMetaBoxArray;
		generateMetaBoxes($guestpostsMetaBoxArray);
	}

// SAVE POST TO DATABASE
	add_action('save_post', 'save_guestposts');
	function save_guestposts(){
		global $guestpostsMetaBoxArray;
		savePostData($guestpostsMetaBoxArray, $post, $wpdb);
	}

// SORTING CUSTOM SUBMENU

	add_action('admin_menu', 'register_sortable_guestposts_submenu');

	function register_sortable_guestposts_submenu() {
		add_submenu_page('edit.php?post_type=guestposts', 'Sort Guestposts', 'Sort', 'edit_pages', 'guestposts_sort', 'sort_guestposts');
	}

	function sort_guestposts() {
		
		echo '<div class="wrap"><div id="icon-tools" class="icon32"></div>';
			echo '<h2>Sort Guestposts</h2>';
		echo '</div>';

		listGuestposts('sort');
	}

// CUSTOM COLUMNS

	// add_action("manage_posts_custom_column",  "guestposts_custom_columns");
	// add_filter("manage_edit-guestposts_columns", "guestposts_edit_columns");

	// function guestposts_edit_columns($columns){
	// 	$columns = array(
	// 		"full_name" => "Guestpost Name",
	// 	);

	// 	return $columns;
	// }
	// function guestposts_custom_columns($column){
	// 	global $post;

	// 	switch ($column) {
	// 		case "full_name":
	// 			$custom = get_post_custom();
	// 			echo "<a href='post.php?post=" . $post->ID . "&action=edit'>" . $custom["first_name"][0] . " " . $custom["last_name"][0] . "</a>";
	// 		break;
	// 	}
	// }

// LISTING FUNCTION
	function listGuestposts($context, $idArray = null){
		global $post;
		global $guestpostsMetaBoxArray;
		
		switch ($context) {
			case 'sort':
				$args = array(
					'post_type'  => 'guestposts',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true
				);
				$loop = new WP_Query($args);

				echo '<ul class="sortable">';
				while ($loop->have_posts()) : $loop->the_post(); 
					$output = get_post_meta($post->ID, 'first_name', true) . " " . get_post_meta($post->ID, 'last_name', true);
					include(get_template_directory() . '/views/item_sortable.php');
				endwhile;
				echo '</ul>';
			break;
			
			case 'json':
				$args = array(
					'post_type'  => 'guestposts',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true
				);
				returnData($args, $guestpostsMetaBoxArray, 'json', 'guestposts_data');
			break;

			case 'array':
				$args = array(
					'post_type'  => 'guestposts',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true,
					'post__in' => $idArray
				);
				return returnData($args, $guestpostsMetaBoxArray, 'array');
			break;

			case 'rest':
				$args = array(
					'post_type'  => 'guestposts',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true,
					'post__in' => $idArray
				);
				return returnData($args, $guestpostsMetaBoxArray, 'array');
			break;

			case 'checkbox':
				$args = array(
					'post_type'  => 'guestposts',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true
				);

				$outputArray = returnData($args, $guestpostsMetaBoxArray, 'array');

				$field_options = array();
				foreach ($outputArray as $key => $value) {
					$checkBoxOption = array(
						"id" => $value['post_id'],
						"name" => $value['the_title'],
					);
					$field_options[] = $checkBoxOption;
				}

				return $field_options;

			break;

			case 'select':
				$args = array(
					'post_type'  => 'guestposts',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true
				);

				$outputArray = returnData($args, $guestpostsMetaBoxArray, 'array');

				$field_options = array();
				foreach ($outputArray as $key => $value) {
					$checkBoxOption = array(
						"id" => $value['post_id'],
						"name" => html_entity_decode($value['the_title'])
					);
					$field_options[] = $checkBoxOption;
				}

				return $field_options;

			break;
		}
	}

?>
