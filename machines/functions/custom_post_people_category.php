<?php

// REGISTER CUSTOM POST TYPE
	add_action( 'init', 'register_post_type_people_category');
	function register_post_type_people_category(){

		$labels = array(
			'name' => 'People_category',
			'singular_name' => 'Person_category',
			'add_new' => 'Add New',
			'add_new_item' => 'Add New Person_category',
			'edit_item' => 'Edit Person_category',
			'new_item' => 'New Person_category',
			'view_item' => 'View Person_category',
			'search_items' => 'Search People_category',
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

		register_post_type( 'people_category', $args);

	}

// DEFINE META BOXES
	$people_categoryMetaBoxArray = array();

// ADD META BOXES
	add_action( "admin_init", "admin_init_people_category" );
	function admin_init_people_category(){
		global $people_categoryMetaBoxArray;
		generateMetaBoxes($people_categoryMetaBoxArray);
	}

// SAVE POST TO DATABASE
	add_action('save_post', 'save_people_category');
	function save_people_category(){
		global $people_categoryMetaBoxArray;
		savePostData($people_categoryMetaBoxArray, $post, $wpdb);
	}

// SORTING CUSTOM SUBMENU

	add_action('admin_menu', 'register_sortable_people_category_submenu');

	function register_sortable_people_category_submenu() {
		add_submenu_page('edit.php?post_type=people_category', 'Sort People_category', 'Sort', 'edit_pages', 'people_category_sort', 'sort_people_category');
	}

	function sort_people_category() {
		
		echo '<div class="wrap"><div id="icon-tools" class="icon32"></div>';
			echo '<h2>Sort People_category</h2>';
		echo '</div>';

		listPeople_category('sort');
	}

// CUSTOM COLUMNS

	// add_action("manage_posts_custom_column",  "people_category_custom_columns");
	// add_filter("manage_edit-people_category_columns", "people_category_edit_columns");

	// function people_category_edit_columns($columns){
	// 	$columns = array(
	// 		"full_name" => "Person_category Name",
	// 	);

	// 	return $columns;
	// }
	// function people_category_custom_columns($column){
	// 	global $post;

	// 	switch ($column) {
	// 		case "full_name":
	// 			$custom = get_post_custom();
	// 			echo "<a href='post.php?post=" . $post->ID . "&action=edit'>" . $custom["first_name"][0] . " " . $custom["last_name"][0] . "</a>";
	// 		break;
	// 	}
	// }

// LISTING FUNCTION
	function listPeople_category($context, $idArray = null){
		global $post;
		global $people_categoryMetaBoxArray;
		
		switch ($context) {
			case 'sort':
				$args = array(
					'post_type'  => 'people_category',
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
					'post_type'  => 'people_category',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true
				);
				returnData($args, $people_categoryMetaBoxArray, 'json', 'people_category_data');
			break;

			case 'array':
				$args = array(
					'post_type'  => 'people_category',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true,
					'post__in' => $idArray
				);
				return returnData($args, $people_categoryMetaBoxArray, 'array');
			break;

			case 'rest':
				$args = array(
					'post_type'  => 'people_category',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true,
					'post__in' => $idArray
				);
				return returnData($args, $people_categoryMetaBoxArray, 'array');
			break;

			case 'checkbox':
				$args = array(
					'post_type'  => 'people_category',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true
				);

				$outputArray = returnData($args, $people_categoryMetaBoxArray, 'array');

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
					'post_type'  => 'people_category',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true
				);

				$outputArray = returnData($args, $people_categoryMetaBoxArray, 'array');

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
