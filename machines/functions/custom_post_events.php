<?php

// REGISTER CUSTOM POST TYPE
	add_action( 'init', 'register_post_type_events');
	function register_post_type_events(){

		$labels = array(
			'name' => 'Events',
			'singular_name' => 'Event',
			'add_new' => 'Add New',
			'add_new_item' => 'Add New Event',
			'edit_item' => 'Edit Event',
			'new_item' => 'New Event',
			'view_item' => 'View Event',
			'search_items' => 'Search Events',
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

		register_post_type( 'events', $args);

	}

// DEFINE META BOXES
	$eventsMetaBoxArray = array(
	    "events_location" => array(
	    	"id" => "events_location",
	        "name" => "Event Location",
	        "post_type" => "events",
	        "position" => "side",
	        "priority" => "low",
	        "callback_args" => array(
	        	"input_type" => "input_text",
	        	"input_name" => "event_location"
	        )
	    ),
	    "events_start_date" => array(
	    	"id" => "events_start_date",
	        "name" => "Start Date",
	        "post_type" => "events",
	        "position" => "side",
	        "priority" => "low",
	        "callback_args" => array(
	        	"input_type" => "input_date",
	        	"input_name" => "start_date"
	        )
	    ),
	    "events_end_date" => array(
	    	"id" => "events_end_date",
	        "name" => "End Date",
	        "post_type" => "events",
	        "position" => "side",
	        "priority" => "low",
	        "callback_args" => array(
	        	"input_type" => "input_date",
	        	"input_name" => "end_date"
	        )
	    ),
	    "speakers_checkbox" => array(
	    	"id" => "speakers_checkbox",
	        "name" => "Speaker(s)",
	        "post_type" => "events",
	        "position" => "side",
	        "priority" => "low",
	        "callback_args" => array(
	        	"input_type" => "input_checkbox_multi",
	        	"input_source" => "listPeople",
	        	"input_name" => "speakers"
	        )
	    ),
	    // "events_sample_color_meta" => array(
	    // 	"id" => "events_sample_color_meta",
	    //     "name" => "Sample Color",
	    //     "post_type" => "events",
	    //     "position" => "side",
	    //     "priority" => "low",
	    //     "callback_args" => array(
	    //     	"input_type" => "input_colorpicker",
	    //     	"input_name" => "sample_color",
	    //     	"input_palette" => array(
	    //     		'rgb(0, 59, 168);',
	    //     		'rgb(102, 153, 51);',
					// 'rgb(53, 109, 211);',
					// 'rgb(95, 136, 211);',
	    //     	)
	    //     )
	    // ),
	    // "events_sample_editor_meta" => array(
	    // 	"id" => "events_sample_editor_meta",
	    //     "name" => "Sample Editor",
	    //     "post_type" => "events",
	    //     "position" => "side",
	    //     "priority" => "low",
	    //     "callback_args" => array(
	    //     	"input_type" => "input_editor",
	    //     	"input_name" => "sample_editor"
	    //     )
	    // ),
	    // "events_sample_select_meta" => array(
	    // 	"id" => "events_sample_select_meta",
	    //     "name" => "Sample Select",
	    //     "post_type" => "events",
	    //     "position" => "side",
	    //     "priority" => "low",
	    //     "callback_args" => array(
	    //     	"input_type" => "input_select",
	    //     	"input_source" => "listEvents",
	    //     	"input_name" => "sample_select"
	    //     )
	    // ),
	    // "events_sample_checkbox_single_meta" => array(
	    // 	"id" => "events_sample_checkbox_single_meta",
	    //     "name" => "Sample Checkbox Single",
	    //     "post_type" => "events",
	    //     "position" => "side",
	    //     "priority" => "low",
	    //     "callback_args" => array(
	    //     	"input_type" => "input_checkbox_single",
	    //     	"input_name" => "events_sample_checkbox_single",
	    //     	"input_text" => "Sample Option"
	    //     )
	    // ),
	    // "events_sample_hidden_meta" => array(
	    // 	"id" => "events_sample_hidden_meta",
	    //     "name" => "Sample Hidden",
	    //     "post_type" => "events",
	    //     "position" => "side",
	    //     "priority" => "low",
	    //     "callback_args" => array(
	    //     	"input_type" => "input_hidden",
	    //     	"input_name" => "events_sample_hidden"
	    //     )
	    // ),


	);

// ADD META BOXES
	add_action( "admin_init", "admin_init_events" );
	function admin_init_events(){
		global $eventsMetaBoxArray;
		generateMetaBoxes($eventsMetaBoxArray);
	}

// SAVE POST TO DATABASE
	add_action('save_post', 'save_events');
	function save_events(){
		global $eventsMetaBoxArray;
		savePostData($eventsMetaBoxArray, $post, $wpdb);
	}

// SORTING CUSTOM SUBMENU

	add_action('admin_menu', 'register_sortable_events_submenu');

	function register_sortable_events_submenu() {
		add_submenu_page('edit.php?post_type=events', 'Sort Events', 'Sort', 'edit_pages', 'events_sort', 'sort_events');
	}

	function sort_events() {
		
		echo '<div class="wrap"><div id="icon-tools" class="icon32"></div>';
			echo '<h2>Sort Events</h2>';
		echo '</div>';

		listEvents('sort');
	}

// CUSTOM COLUMNS

	// add_action("manage_posts_custom_column",  "events_custom_columns");
	// add_filter("manage_edit-events_columns", "events_edit_columns");

	// function events_edit_columns($columns){
	// 	$columns = array(
	// 		"full_name" => "Event Name",
	// 	);

	// 	return $columns;
	// }
	// function events_custom_columns($column){
	// 	global $post;

	// 	switch ($column) {
	// 		case "full_name":
	// 			$custom = get_post_custom();
	// 			echo "<a href='post.php?post=" . $post->ID . "&action=edit'>" . $custom["first_name"][0] . " " . $custom["last_name"][0] . "</a>";
	// 		break;
	// 	}
	// }

// LISTING FUNCTION
	function listEvents($context, $idArray = null){
		global $post;
		global $eventsMetaBoxArray;
		
		switch ($context) {
			case 'sort':
				$args = array(
					'post_type'  => 'events',
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
					'post_type'  => 'events',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true
				);
				returnData($args, $eventsMetaBoxArray, 'json', 'events_data');
			break;

			case 'array':
				$args = array(
					'post_type'  => 'events',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true,
					'post__in' => $idArray
				);
				return returnData($args, $eventsMetaBoxArray, 'array');
			break;

			case 'rest':
				$args = array(
					'post_type'  => 'events',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true,
					'post__in' => $idArray
				);
				return returnData($args, $eventsMetaBoxArray, 'array');
			break;

			case 'checkbox':
				$args = array(
					'post_type'  => 'events',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true
				);

				$outputArray = returnData($args, $eventsMetaBoxArray, 'array');

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
					'post_type'  => 'events',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true
				);

				$outputArray = returnData($args, $eventsMetaBoxArray, 'array');

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
