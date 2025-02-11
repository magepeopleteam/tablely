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
		}
		new MPTRS_CPT();
	}