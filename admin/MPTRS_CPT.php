<?php
	/*
   * @Author 		engr.sumonazma@gmail.com
   * Copyright: 	mage-people.com
   */
	if (!defined('ABSPATH')) {
		die;
	} // Cannot access pages directly.
	if (!class_exists('MPTRS_CPT')) {
		class MPTRS_CPT {
			public function __construct() {
				add_action('init', [$this, 'add_cpt']);
                add_action('init', [$this, 'register_mptrs_table_reservations_cpt'] );
                add_action('init', [$this, 'create_mptrs_table_reservations_cpt'] );
                add_action( 'init', [ $this, 'register_taxonomy' ] );
                add_action( 'add_meta_boxes', [$this,'pa_add_seat_design_meta_box'] );
			}

            public function register_taxonomy() {
                $args = [
                    'hierarchical'      => false,
                    'show_ui'           => false,
                    'show_admin_column' => false,
                    'query_var'         => true,
                    'rewrite'           => [ 'slug' => 'restaurant-type' ],
                ];

                register_taxonomy( 'mptrs_restaurant_city', [ 'restaurant-city' ], $args );
            }
			public function add_cpt(): void {
				$cpt = MPTRS_Function::get_cpt();
				$label = MPTRS_Function::get_name();
				$slug = MPTRS_Function::get_slug();
				$icon = MPTRS_Function::get_icon();
				$labels = [
					'name' => $label,
					'singular_name' => $label,
					'menu_name' => $label,
					'name_admin_bar' => $label,
					'archives' => $label . ' ' . esc_html__(' List', 'tablely'),
					'attributes' => $label . ' ' . esc_html__(' List', 'tablely'),
					'parent_item_colon' => $label . ' ' . esc_html__(' Item:', 'tablely'),
					'all_items' => esc_html__('All ', 'tablely') . ' ' . $label,
					'add_new_item' => esc_html__('Add New ', 'tablely') . ' ' . $label,
					'add_new' => esc_html__('Add New ', 'tablely') . ' ' . $label,
					'new_item' => esc_html__('New ', 'tablely') . ' ' . $label,
					'edit_item' => esc_html__('Edit ', 'tablely') . ' ' . $label,
					'update_item' => esc_html__('Update ', 'tablely') . ' ' . $label,
					'view_item' => esc_html__('View ', 'tablely') . ' ' . $label,
					'view_items' => esc_html__('View ', 'tablely') . ' ' . $label,
					'search_items' => esc_html__('Search ', 'tablely') . ' ' . $label,
					'not_found' => $label . ' ' . esc_html__(' Not found', 'tablely'),
					'not_found_in_trash' => $label . ' ' . esc_html__(' Not found in Trash', 'tablely'),
					'featured_image' => $label . ' ' . esc_html__(' Feature Image', 'tablely'),
					'set_featured_image' => esc_html__('Set ', 'tablely') . ' ' . $label . ' ' . esc_html__(' featured image', 'tablely'),
					'remove_featured_image' => esc_html__('Remove ', 'tablely') . ' ' . $label . ' ' . esc_html__(' featured image', 'tablely'),
					'use_featured_image' => esc_html__('Use as', 'tablely') . ' ' . $label . ' ' . esc_html__(' featured image', 'tablely') . ' ' . $label . ' ' . esc_html__(' featured image', 'tablely'),
					'insert_into_item' => esc_html__('Insert into', 'tablely') . ' ' . $label,
					'uploaded_to_this_item' => esc_html__('Uploaded to this ', 'tablely') . ' ' . $label,
					'items_list' => $label . ' ' . esc_html__(' list', 'tablely'),
					'items_list_navigation' => $label . ' ' . esc_html__(' list navigation', 'tablely'),
					'filter_items_list' => esc_html__('Filter ', 'tablely') . ' ' . $label . ' ' . esc_html__(' list', 'tablely')
				];
				$args = [
					'public' => true,
					'labels' => $labels,
					'menu_icon' => $icon,
					'supports' => ['title', 'editor', 'thumbnail'],
					'show_in_rest' => true,
					'capability_type' => 'post',
					'publicly_queryable' => true,  // you should be able to query it
					'show_ui' => true,  // you should be able to edit it in wp-admin
					'exclude_from_search' => true,  // you should exclude it from search results
					'show_in_nav_menus' => false,  // you shouldn't be able to add it to menus
					'has_archive' => false,  // it shouldn't have archive page
					'rewrite' => ['slug' => $slug],
				];
				register_post_type($cpt, $args);
			}

            function register_mptrs_table_reservations_cpt() {
                register_post_type('mptrs_table_reserv', array(
                    'label' => 'Table Reservations',
                    'public' => true,
                    'supports' => ['title', 'custom-fields'],
                    'show_in_rest' => false,
                    'show_ui' => false,
                ));
            }
            function create_mptrs_table_reservations_cpt_aa() {
                register_post_type('mptrs_table_reserv', array(
                    'label' => 'Table Reservations',
                    'public' => true,
                    'supports' => ['title', 'custom-fields'],
                    'show_in_rest' => false,
                    'show_ui' => false,
                ));
            }

            // 1. Register Custom Post Type: mptrs_seat_reservation
            function create_mptrs_table_reservations_cpt() {
                $labels = array(
                    'name'               => 'Seat Reservations',
                    'singular_name'      => 'Seat Reservation',
                    'menu_name'          => 'Seat Reservations',
                    'name_admin_bar'     => 'Seat Reservation',
                    'add_new'            => 'Add New',
                    'add_new_item'       => 'Add New Seat Reservation',
                    'edit_item'          => 'Edit Seat Reservation',
                    'new_item'           => 'New Seat Reservation',
                    'view_item'          => 'View Seat Reservation',
                    'all_items'          => 'All Seat Reservations',
                    'search_items'       => 'Search Seat Reservations',
                    'not_found'          => 'No seat reservations found.',
                    'not_found_in_trash' => 'No seat reservations found in Trash.'
                );

                $args = array(
                    'labels'             => $labels,
                    'description'        => 'Manage seat reservations with design.',
                    'public'             => true,
                    'show_ui'            => true,
                    'show_in_menu'       => false,
                    'menu_position'      => 25,
                    'menu_icon'          => 'dashicons-tickets-alt',
                    'supports'           => array( 'title' ), // ✅ Only title field
                    'has_archive'        => true,
                    'rewrite'            => array( 'slug' => 'seat-reservation' ),
                );

                register_post_type( 'mptrs_seat_mapping', $args );
            }

            function pa_add_seat_design_meta_box() {
                add_meta_box(
                    'pa_seat_design_meta',
                    'Seat Design',
                    [$this,'pa_seat_design_meta_callback'],
                    'mptrs_seat_mapping',
                    'normal',
                    'default'
                );
            }

            function pa_seat_design_meta_callback( $post ) { ?>
                <div class="tabsItem" data-tabs="#mptrs_seat_mapping">
                <header>
                    <h2><?php esc_html_e('Seat Mapping', 'tablely'); ?></h2>
                    <span><?php esc_html_e('In this section you will make table and seat for reservation.', 'tablely'); ?></span>
                </header>
                <section class="mptrs-seat-mapping-section " id="mptrs-seat-mapping-section">
                <?php
                $seat_design = get_post_meta( $post->ID, '_pa_seat_design', true );
                    MPTRS_Seat_Mapping::render_seat_mapping_meta_box( $post->ID ); ?>
                </section>
                </div>
                <?php


            }


		}
		new MPTRS_CPT();
	}