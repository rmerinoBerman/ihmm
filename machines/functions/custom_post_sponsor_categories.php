<?php

// REGISTER CUSTOM POST TYPE
	add_action( 'init', 'register_post_type_sponsor_categories');
	function register_post_type_sponsor_categories(){

		$labels = array(
			'name' => 'Sponsor_categories',
			'singular_name' => 'Sponsor_category',
			'add_new' => 'Add New',
			'add_new_item' => 'Add New Sponsor_category',
			'edit_item' => 'Edit Sponsor_category',
			'new_item' => 'New Sponsor_category',
			'view_item' => 'View Sponsor_category',
			'search_items' => 'Search Sponsor_categories',
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

		register_post_type( 'sponsor_categories', $args);

	}

// DEFINE META BOXES
	$sponsor_categoriesMetaBoxArray = array(
	    "sponsor_categories_prive_meta" => array(
	    	"id" => "sponsor_categories_prive_meta",
	        "name" => "Price",
	        "post_type" => "sponsor_categories",
	        "position" => "side",
	        "priority" => "low",
	        "callback_args" => array(
	        	"input_type" => "input_text",
	        	"input_name" => "prive"
	        )
	    ),
	);

// ADD META BOXES
	add_action( "admin_init", "admin_init_sponsor_categories" );
	function admin_init_sponsor_categories(){
		global $sponsor_categoriesMetaBoxArray;
		generateMetaBoxes($sponsor_categoriesMetaBoxArray);
	}

// SAVE POST TO DATABASE
	add_action('save_post', 'save_sponsor_categories');
	function save_sponsor_categories(){
		global $sponsor_categoriesMetaBoxArray;
		savePostData($sponsor_categoriesMetaBoxArray, $post, $wpdb);
	}

// SORTING CUSTOM SUBMENU

	add_action('admin_menu', 'register_sortable_sponsor_categories_submenu');

	function register_sortable_sponsor_categories_submenu() {
		add_submenu_page('edit.php?post_type=sponsor_categories', 'Sort Sponsor_categories', 'Sort', 'edit_pages', 'sponsor_categories_sort', 'sort_sponsor_categories');
	}

	function sort_sponsor_categories() {
		
		echo '<div class="wrap"><div id="icon-tools" class="icon32"></div>';
			echo '<h2>Sort Sponsor_categories</h2>';
		echo '</div>';

		listSponsor_categories('sort');
	}

// CUSTOM COLUMNS

	// add_action("manage_posts_custom_column",  "sponsor_categories_custom_columns");
	// add_filter("manage_edit-sponsor_categories_columns", "sponsor_categories_edit_columns");

	// function sponsor_categories_edit_columns($columns){
	// 	$columns = array(
	// 		"full_name" => "Sponsor_category Name",
	// 	);

	// 	return $columns;
	// }
	// function sponsor_categories_custom_columns($column){
	// 	global $post;

	// 	switch ($column) {
	// 		case "full_name":
	// 			$custom = get_post_custom();
	// 			echo "<a href='post.php?post=" . $post->ID . "&action=edit'>" . $custom["first_name"][0] . " " . $custom["last_name"][0] . "</a>";
	// 		break;
	// 	}
	// }

// LISTING FUNCTION
	function listSponsor_categories($context, $idArray = null){
		global $post;
		global $sponsor_categoriesMetaBoxArray;
		
		switch ($context) {
			case 'sort':
				$args = array(
					'post_type'  => 'sponsor_categories',
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
					'post_type'  => 'sponsor_categories',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true
				);
				returnData($args, $sponsor_categoriesMetaBoxArray, 'json', 'sponsor_categories_data');
			break;

			case 'array':
				$args = array(
					'post_type'  => 'sponsor_categories',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true,
					'post__in' => $idArray
				);
				return returnData($args, $sponsor_categoriesMetaBoxArray, 'array');
			break;

			case 'rest':
				$args = array(
					'post_type'  => 'sponsor_categories',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true,
					'post__in' => $idArray
				);
				return returnData($args, $sponsor_categoriesMetaBoxArray, 'array');
			break;

			case 'checkbox':
				$args = array(
					'post_type'  => 'sponsor_categories',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true
				);

				$outputArray = returnData($args, $sponsor_categoriesMetaBoxArray, 'array');

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
					'post_type'  => 'sponsor_categories',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true
				);

				$outputArray = returnData($args, $sponsor_categoriesMetaBoxArray, 'array');

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
