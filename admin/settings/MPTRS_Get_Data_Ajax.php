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

            add_action('wp_ajax_mptrs_get_available_seats_for_reservations', [$this, 'mptrs_get_available_seats_for_reservations']);
            add_action('wp_ajax_nopriv_mptrs_get_available_seats_for_reservations', [$this, 'mptrs_get_available_seats_for_reservations']);

            add_action('wp_ajax_mptrs_set_order', [$this, 'mptrs_set_order']);
            add_action('wp_ajax_nopriv_mptrs_set_order', [$this, 'mptrs_set_order']);

            add_action('wp_ajax_mptrs_add_food_items_to_cart', [$this, 'mptrs_add_food_items_to_cart'] );
            add_action('wp_ajax_nopriv_mptrs_add_food_items_to_cart', [$this, 'mptrs_add_food_items_to_cart'] );

            add_filter('woocommerce_add_cart_item_data', [$this, 'mptrs_set_custom_price_cart_item'], 10, 2);
            add_action('woocommerce_before_calculate_totals', [$this, 'mptrs_update_cart_item_price'], 10, 1);

            add_filter('woocommerce_get_item_data', [$this,'mptrs_display_custom_cart_item_data'], 10, 2);
            add_action('woocommerce_checkout_create_order_line_item', [$this,'mptrs_add_order_item_meta'], 10, 4);


        }

        function mptrs_add_order_item_meta($item, $cart_item_key, $values, $order) {
//            error_log( print_r($values, true) );
            if (isset($values['food_menu'])) {
                $item->add_meta_data('Food Menu', $values['food_menu'], true);
            }
            if (isset($values['booking_seats'])) {
                $item->add_meta_data('Seats', is_array($values['booking_seats']) ? implode(', ', $values['booking_seats']) : $values['booking_seats'], true);
            }
        }
        function mptrs_display_custom_cart_item_data($item_data, $cart_item) {
            /*if (isset($cart_item['food_menu'])) {
                $item_data[] = [
                    'name'  => 'Food Menu',
                    'value' => wc_clean($cart_item['food_menu']),
                ];
            }*/
            if (isset($cart_item['booking_seats'])) {
                $item_data[] = [
                    'name'  => 'Seats',
                    'value' => is_array($cart_item['booking_seats']) ? implode(', ', $cart_item['booking_seats']) : $cart_item['booking_seats'],
                ];
            }
            return $item_data;
        }
        function mptrs_update_cart_item_price($cart) {
            if (is_admin() && !defined('DOING_AJAX')) return;

            foreach ($cart->get_cart() as $cart_item) {
                if (isset($cart_item['custom_price'])) {
                    $cart_item['data']->set_price($cart_item['custom_price']); // Set custom price
                }
            }
        }
        function mptrs_set_custom_price_cart_item($cart_item_data, $product_id) {
            if (isset($_POST['price'])) {
                $cart_item_data['custom_price'] = floatval($_POST['price']); // Store custom price
            }
            return $cart_item_data;
        }

        function mptrs_add_food_items_to_cart() {

            error_log( print_r( [ '$_POST' => $_POST ], true ) );
            if (!isset($_POST['post_id'], $_POST['menu'], $_POST['seats'], $_POST['price'], $_POST['quantity'])) {
                wp_send_json_error('Missing required data.');
            }

            $post_id = intval($_POST['post_id']);
            $post_id = get_post_meta( $post_id, 'link_wc_product', true ) ;

            $menu = $_POST['menu'];
//            $seats = json_decode(stripslashes($_POST['seats']), true);
            $seats = $_POST['seats'];
            $price = floatval($_POST['price']);
            $quantity = intval($_POST['quantity']);

            // Check if WooCommerce is active
            if (!class_exists('WC_Cart')) {
                wp_send_json_error('WooCommerce is not active.');
            }

            // Create cart item data
            $cart_item_data = [
                'mptrs_item_id' => $post_id,
                'food_menu' => $menu,
                'booking_seats' => $seats,
                'price' => $price,
            ];
            WC()->cart->empty_cart();

            $cart_item_key = WC()->cart->add_to_cart( $post_id, $quantity, 0, [], $cart_item_data );
            if ($cart_item_key) {
                wp_send_json_success('Item added to cart.');
            } else {
                wp_send_json_error('Failed to add to cart.');
            }

        }


        public function mptrs_set_order(){
            $result = 0;
//            $seat_booking_data = $seat_booking_data = array();
            if ( !isset( $_POST['nonce']) || !wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'mptrs_admin_nonce' ) ) {
//                wp_send_json_error(['message' => 'Security check failed.'], 403);
                $booked_seat_ids_str = isset( $_POST['bookedSeats'] ) ? sanitize_text_field( $_POST['bookedSeats'] ) : '';
                $orderPostId = isset( $_POST['orderPostId'] ) ? sanitize_text_field( $_POST['orderPostId'] ) : '';
                $order_date = isset( $_POST['order_date'] ) ? sanitize_text_field( $_POST['order_date'] ) : '';
                $order_time = isset( $_POST['order_time'] ) ? sanitize_text_field( $_POST['order_time'] ) : '';
                $booked_seat_ids = json_decode(stripslashes( $booked_seat_ids_str ), true);


                if( !empty( $booked_seat_ids ) && !empty( $orderPostId ) && !empty( $order_date ) && !empty( $order_time ) ){

                    $seat_booking_data = get_post_meta( $orderPostId, '_mptrs_seat_booking', true );
                    if( !is_array( $seat_booking_data ) && empty( $seat_booking_data ) ){
                        $seat_booking_data = [];
                    }

                    $booked_seat_ids = json_decode(stripslashes( $booked_seat_ids_str ), true);
                    $orderDateFormatted = date('d_m_y', strtotime( $order_date ) );

                    foreach ( $booked_seat_ids as $seat_index ){
                        $seat_booking_data = self::set_seat_bookig( $seat_booking_data, $seat_index, $orderDateFormatted, $order_time );
                    }
                    $result = update_post_meta( $orderPostId, '_mptrs_seat_booking', $seat_booking_data );
                }

            }

            wp_send_json_success([
                'message' => 'Seat Successfully Reserved!',
                'success' => $result,
            ]);
        }

        function mptrs_get_available_seats_for_reservations(){

            $seat_map = '';
            if (!isset($_POST['nonce']) || !wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'mptrs_admin_nonce')) {
//                wp_send_json_error(['message' => 'Security check failed.'], 403);
                $orderPostId = isset( $_POST['post_id']) ? sanitize_text_field( $_POST['post_id'] ) : '';
                $search_time = isset( $_POST['get_time']) ? sanitize_text_field( $_POST['get_time'] ) : '';
                $get_date = isset( $_POST['get_date']) ? sanitize_text_field( $_POST['get_date'] ) : '';
                $orderDateFormatted = date('d_m_y', strtotime( $get_date ) );

                $seat_booking_data = get_post_meta( $orderPostId, '_mptrs_seat_booking', true );
                if( !is_array( $seat_booking_data ) && empty( $seat_booking_data ) ){
                    $seat_booking_data = [];
                }

                if( count( $seat_booking_data ) > 0 ){
                    $not_available = self::getAvailableSeats( $seat_booking_data, $orderDateFormatted, $search_time);
                }else{
                    $not_available = [];
                }

                $post_id = isset( $_POST['post_id'] ) ? sanitize_text_field($_POST['post_id']) : '';
                $seat_map = MPTRS_Details_Layout::display_seat_mapping( $post_id, $not_available );
            }

            $get_food_menu = get_option( '_mptrs_food_menu' );
            if( !is_array( $get_food_menu ) && empty( $get_food_menu ) ){
                $get_food_menu = [];
            }

            /*$seat_booking_data = array(
                'seat-2-4' => array(
                    '25_03_25' => array('11', '12', '13'),
                ),
                'A2' => array(
                    '25_03_25' => array('11', '12', '13'),
                    '26_05_25' => array('18', '12', '13'),
                ),
                'A3' => array(
                    '24_03_25' => array('11', '12', '13'),
                ),
                'A4' => array(
                    '26_03_25' => array('11', '12', '13'),
                ),
                'A5' => array(
                    '26_03_25' => array('11'),
                ),
            );*/

            wp_send_json_success([
                'message' => 'Categories Data getting successfully.!',
                'mptrs_seat_maps' => $seat_map,
                'get_food_menu' => $get_food_menu,
            ]);

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

            $existing_edited_prices = get_post_meta($post_id, '_mptrs_food_menu_edited_prices', true);
            if (!is_array($existing_edited_prices)) {
                $existing_edited_prices = [];
            }
            $existing_edited_prices[$editKey] = $price;

            $update = update_post_meta( $post_id, '_mptrs_food_menu_edited_prices', $existing_edited_prices );
//            $existing_edited_pr = get_post_meta($post_id, '_mptrs_food_menu_edited_prices', true);

            wp_send_json_success([
                'message' => 'Price successfully Updated!',
                'success' => $update,
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

        public static function set_seat_bookig( $seat_booking_data, $seat_index, $date_key, $search_time ) {
            if (isset($seat_booking_data[$seat_index])) {
                if (isset($seat_booking_data[$seat_index][$date_key])) {
                    if ( !in_array($search_time, $seat_booking_data[$seat_index][$date_key])) {
                        $seat_booking_data[$seat_index][$date_key][] = $search_time;
                    }
                } else {
                    $seat_booking_data[$seat_index][$date_key] = array($search_time);
                }
            }else{
                $seat_booking_data[$seat_index][$date_key] = array($search_time);
            }

            return $seat_booking_data;
        }
        public static function getAvailableSeats( $seatData, $dateKey, $searchTime ) {
            $not_available = [];
            foreach ($seatData as $seat => $dates ) {
                if( isset( $dates[$dateKey] ) ) {
                    if( in_array( $searchTime, $dates[$dateKey] ) ) {
                        $not_available[] = $seat;
                    }
                }
            }

            return $not_available;
        }

    }

    new MPTRS_Get_Data_Ajax();
}