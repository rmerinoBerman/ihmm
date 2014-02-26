<?php

// REGISTER CUSTOM POST TYPE
	add_action( 'init', 'register_post_type_sponsors');
	function register_post_type_sponsors(){

		$labels = array(
			'name' => 'Sponsors',
			'singular_name' => 'Sponsor',
			'add_new' => 'Add New',
			'add_new_item' => 'Add New Sponsor',
			'edit_item' => 'Edit Sponsor',
			'new_item' => 'New Sponsor',
			'view_item' => 'View Sponsor',
			'search_items' => 'Search Sponsors',
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

		register_post_type( 'sponsors', $args);

	}

// DEFINE META BOXES
	$sponsorsMetaBoxArray = array(
	    "sponsors_website" => array(
	    	"id" => "sponsors_website",
	        "name" => "Sponsor Website",
	        "post_type" => "sponsors",
	        "position" => "side",
	        "priority" => "low",
	        "callback_args" => array(
	        	"input_type" => "input_text",
	        	"input_name" => "sponsor_website"
	        )
	    ),
	    "sponsors_type" => array(
	    	"id" => "sponsors_type",
	        "name" => "Sponsor Type",
	        "post_type" => "sponsors",
	        "position" => "side",
	        "priority" => "low",
	        "callback_args" => array(
	        	"input_type" => "input_select",
	        	"input_source" => "listSponsor_categories",
	        	"input_name" => "sponsor_type"
	        )
	    ),
	);

// ADD META BOXES
	add_action( "admin_init", "admin_init_sponsors" );
	function admin_init_sponsors(){
		global $sponsorsMetaBoxArray;
		generateMetaBoxes($sponsorsMetaBoxArray);
	}

// SAVE POST TO DATABASE
	add_action('save_post', 'save_sponsors');
	function save_sponsors(){
		global $sponsorsMetaBoxArray;
		savePostData($sponsorsMetaBoxArray, $post, $wpdb);
	}

// SORTING CUSTOM SUBMENU

	add_action('admin_menu', 'register_sortable_sponsors_submenu');

	function register_sortable_sponsors_submenu() {
		add_submenu_page('edit.php?post_type=sponsors', 'Sort Sponsors', 'Sort', 'edit_pages', 'sponsors_sort', 'sort_sponsors');
	}

	function sort_sponsors() {
		
		echo '<div class="wrap"><div id="icon-tools" class="icon32"></div>';
			echo '<h2>Sort Sponsors</h2>';
		echo '</div>';

		listSponsors('sort');
	}

// CUSTOM COLUMNS

	// add_action("manage_posts_custom_column",  "sponsors_custom_columns");
	// add_filter("manage_edit-sponsors_columns", "sponsors_edit_columns");

	// function sponsors_edit_columns($columns){
	// 	$columns = array(
	// 		"full_name" => "Sponsor Name",
	// 	);

	// 	return $columns;
	// }
	// function sponsors_custom_columns($column){
	// 	global $post;

	// 	switch ($column) {
	// 		case "full_name":
	// 			$custom = get_post_custom();
	// 			echo "<a href='post.php?post=" . $post->ID . "&action=edit'>" . $custom["first_name"][0] . " " . $custom["last_name"][0] . "</a>";
	// 		break;
	// 	}
	// }

// LISTING FUNCTION
	function listSponsors($context, $idArray = null){
		global $post;
		global $sponsorsMetaBoxArray;
		
		switch ($context) {
			case 'sort':
				$args = array(
					'post_type'  => 'sponsors',
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
					'post_type'  => 'sponsors',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true
				);
				returnData($args, $sponsorsMetaBoxArray, 'json', 'sponsors_data');
			break;

			case 'array':
				$args = array(
					'post_type'  => 'sponsors',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true,
					'post__in' => $idArray
				);
				return returnData($args, $sponsorsMetaBoxArray, 'array');
			break;

			case 'rest':
				$args = array(
					'post_type'  => 'sponsors',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true,
					'post__in' => $idArray
				);
				return returnData($args, $sponsorsMetaBoxArray, 'array');
			break;

			case 'checkbox':
				$args = array(
					'post_type'  => 'sponsors',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true
				);

				$outputArray = returnData($args, $sponsorsMetaBoxArray, 'array');

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
					'post_type'  => 'sponsors',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true
				);

				$outputArray = returnData($args, $sponsorsMetaBoxArray, 'array');

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
