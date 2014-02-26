<?php

// REGISTER CUSTOM POST TYPE
	add_action( 'init', 'register_post_type_people');
	function register_post_type_people(){

		$labels = array(
			'name' => 'People',
			'singular_name' => 'Person',
			'add_new' => 'Add New',
			'add_new_item' => 'Add New Person',
			'edit_item' => 'Edit Person',
			'new_item' => 'New Person',
			'view_item' => 'View Person',
			'search_items' => 'Search People',
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

		register_post_type( 'people', $args);

	}

// DEFINE META BOXES
	$peopleMetaBoxArray = array(
	    "people_first_name" => array(
	    	"id" => "people_first_name",
	        "name" => "First Name",
	        "post_type" => "people",
	        "position" => "side",
	        "priority" => "low",
	        "callback_args" => array(
	        "input_type" => "input_text",
	        "input_name" => "first_name"
	        )
	    ),
	   		"people_last_name" => array(
	    	"id" => "people_last_name",
	        "name" => "Last Name",
	        "post_type" => "people",
	        "position" => "side",
	        "priority" => "low",
	        "callback_args" => array(
	        "input_type" => "input_text",
	        "input_name" => "last_name"
	        )
	    ),
	   		"people_work_title" => array(
	    	"id" => "people_work_title",
	        "name" => "Work Title",
	        "post_type" => "people",
	        "position" => "side",
	        "priority" => "low",
	        "callback_args" => array(
	        "input_type" => "input_text",
	        "input_name" => "work_title"
	        )
	    ),
	   		"people_association_title" => array(
	    	"id" => "people_association_title",
	        "name" => "Association Title",
	        "post_type" => "people",
	        "position" => "side",
	        "priority" => "low",
	        "callback_args" => array(
	        "input_type" => "input_text",
	        "input_name" => "association_title"
	        )
	    ),
	   		"people_company" => array(
	    	"id" => "people_company",
	        "name" => "Company",
	        "post_type" => "people",
	        "position" => "side",
	        "priority" => "low",
	        "callback_args" => array(
	        "input_type" => "input_text",
	        "input_name" => "company"
	        )
	    ),
	   		"people_category_meta" => array(
	    	"id" => "people_category_meta",
	        "name" => "Category",
	        "post_type" => "people",
	        "position" => "side",
	        "priority" => "low",
	        "callback_args" => array(
	        	"input_type" => "input_select",
	        	"input_source" => "listPeople_category",
	        	"input_name" => "category"
	        )
	    ),

	);

// ADD META BOXES
	add_action( "admin_init", "admin_init_people" );
	function admin_init_people(){
		global $peopleMetaBoxArray;
		generateMetaBoxes($peopleMetaBoxArray);
	}

// SAVE POST TO DATABASE
	add_action('save_post', 'save_people');
	function save_people(){
		global $peopleMetaBoxArray;
		savePostData($peopleMetaBoxArray, $post, $wpdb);
	}

// SORTING CUSTOM SUBMENU

	add_action('admin_menu', 'register_sortable_people_submenu');

	function register_sortable_people_submenu() {
		add_submenu_page('edit.php?post_type=people', 'Sort People', 'Sort', 'edit_pages', 'people_sort', 'sort_people');
	}

	function sort_people() {
		
		echo '<div class="wrap"><div id="icon-tools" class="icon32"></div>';
			echo '<h2>Sort People</h2>';
		echo '</div>';

		listPeople('sort');
	}

// CUSTOM COLUMNS

	// add_action("manage_posts_custom_column",  "people_custom_columns");
	// add_filter("manage_edit-people_columns", "people_edit_columns");

	// function people_edit_columns($columns){
	// 	$columns = array(
	// 		"full_name" => "Person Name",
	// 	);

	// 	return $columns;
	// }
	// function people_custom_columns($column){
	// 	global $post;

	// 	switch ($column) {
	// 		case "full_name":
	// 			$custom = get_post_custom();
	// 			echo "<a href='post.php?post=" . $post->ID . "&action=edit'>" . $custom["first_name"][0] . " " . $custom["last_name"][0] . "</a>";
	// 		break;
	// 	}
	// }

// LISTING FUNCTION
	function listPeople($context, $idArray = null){
		global $post;
		global $peopleMetaBoxArray;
		
		switch ($context) {
			case 'sort':
				$args = array(
					'post_type'  => 'people',
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
					'post_type'  => 'people',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true
				);
				returnData($args, $peopleMetaBoxArray, 'json', 'people_data');
			break;

			case 'array':
				$args = array(
					'post_type'  => 'people',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true,
					'post__in' => $idArray
				);
				return returnData($args, $peopleMetaBoxArray, 'array');
			break;

			case 'rest':
				$args = array(
					'post_type'  => 'people',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true,
					'post__in' => $idArray
				);
				return returnData($args, $peopleMetaBoxArray, 'array');
			break;

			case 'checkbox':
				$args = array(
					'post_type'  => 'people',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true
				);

				$outputArray = returnData($args, $peopleMetaBoxArray, 'array');

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
					'post_type'  => 'people',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true
				);

				$outputArray = returnData($args, $peopleMetaBoxArray, 'array');

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
