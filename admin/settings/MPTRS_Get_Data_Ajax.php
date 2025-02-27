<?php

/**
 * @author rubel mia <rubelcuet10@gmail.com>
 * @license mage-people.com
 * @var 1.0.0
 */
if (!defined('ABSPATH'))
    die;
if (!class_exists('MPTRS_Get_Data_Ajax')) {
    class MPTRS_Get_Data_Ajax{
        public function __construct() {
            add_action('wp_ajax_mptrs_get_categories', [$this, 'mptrs_get_categories']);
            add_action('wp_ajax_nopriv_mptrs_get_categories', [$this, 'mptrs_get_categories']);

            add_action('wp_ajax_mptrs_price_change_food_menu_restaurant', [$this, 'mptrs_price_change_food_menu_restaurant']);
            add_action('wp_ajax_nopriv_mptrs_price_change_food_menu_restaurant', [$this, 'mptrs_price_change_food_menu_restaurant']);

        }

        public function mptrs_price_change_food_menu_restaurant(){

            if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field($_POST['nonce']), 'mptrs_admin_nonce')) {
                wp_send_json_error(['message' => 'Security check failed.'], 403);
            }

            if (!current_user_can('manage_options')) {
                wp_send_json_error(['message' => 'Permission denied']);
            }

            $editKey = isset( $_POST['editKey'] ) ? sanitize_text_field($_POST['editKey']) : '';
            $price = isset( $_POST['editKey'] ) ? sanitize_text_field($_POST['price']) : '';
            $post_id = isset( $_POST['editKey'] ) ? sanitize_text_field($_POST['postId']) : '';

            $existing_menuItems = get_post_meta( $post_id, '_mptrs_food_menu_items', true );
            error_log( print_r( [ '$existing_menuItems' => $existing_menuItems ], true ) );

            wp_send_json_success([
                'message' => 'Price successfully Updated!',
                'success' => 1,
            ]);
        }

        public function mptrs_get_categories(){

            if (!isset($_POST['nonce']) || !wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'mptrs_admin_nonce')) {
                wp_send_json_error(['message' => 'Security check failed.'], 403);
            }

            if( !current_user_can('manage_options')) {
                wp_send_json_error(['message' => 'Permission denied']);
            }

            $result = 0;
            $categories = [];
            $mptrs_categories = get_option( 'mptrs_categories' );

            if( is_array( $mptrs_categories ) && !empty( $mptrs_categories ) ) {
                $result = 1;
                $categories = $mptrs_categories;
            }

            wp_send_json_success([
                'message' => 'Categories Data getting successfully.!',
                'success' => $result,
                'mptrs_categories' => $categories,
            ]);
        }

    }

    new MPTRS_Get_Data_Ajax();
}