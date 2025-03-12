<?php

/**
 * @author rubel mia <rubelcuet10@gmail.com>
 * @license mage-people.com
 * @var 1.0.0
 */

namespace settings;
if (!defined('ABSPATH'))
    die;
if (!class_exists('MPTRS_Cart_Order_Data_Display')) {
    class MPTRS_Cart_Order_Data_Display{

        public function __construct() {
            add_filter('woocommerce_add_cart_item_data', [$this, 'set_custom_price_cart_item'], 10, 2);
            add_action('woocommerce_before_calculate_totals', [$this, 'update_cart_item_price'], 10, 1);
            add_filter('woocommerce_get_item_data', [$this, 'display_custom_cart_item_data'], 10, 2);
            add_action('woocommerce_checkout_create_order_line_item', [$this, 'add_order_item_meta'], 10, 4);
            add_action('woocommerce_order_item_meta_end', [$this, 'display_order_meta'], 10, 3);
            add_action('woocommerce_new_order', [$this, 'mptrs_woocommerce_new_order'], 10, 1);
//            add_action( 'woocommerce_order_status_changed', 'custom_function_on_order_status_change', 10, 4 );

        }

        function custom_function_on_order_status_change( $order_id, $old_status, $new_status, $order ) {
            if ( $new_status === 'processing' ) {
                error_log( print_r( [ 'place' => 'order', '$product_id' => $order_id ], true ) );
            }
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

        public function mptrs_woocommerce_new_order( $order_id ) {
            $order = wc_get_order( $order_id );
            $orderPostId = '';
            foreach ( $order->get_items() as $item_id => $item ) {
                $product = $item->get_product();
                $orderPostId = $product->get_id();
            }

            $mptrs_booking_data = maybe_unserialize( get_post_meta( $orderPostId, '_mptrs_booking_data', true ) );

            $booked_seat_ids = isset( $mptrs_booking_data['selected_seat_ids'] ) ? $mptrs_booking_data['selected_seat_ids'] : array();
            $order_date = isset( $mptrs_booking_data['ordered_date'] ) ? $mptrs_booking_data['ordered_date'] : '';
            $order_time = isset( $mptrs_booking_data['ordered_time'] ) ? $mptrs_booking_data['ordered_time'] : '';

            if( is_array( $booked_seat_ids ) && !empty( $booked_seat_ids ) && !empty( $orderPostId ) && !empty( $order_date ) && !empty( $order_time ) ){
                $seat_booking_data = get_post_meta( $orderPostId, '_mptrs_seat_booking', true );
                if( !is_array( $seat_booking_data ) && empty( $seat_booking_data ) ){
                    $seat_booking_data = [];
                }

                $orderDateFormatted = date('d_m_y', strtotime( $order_date ) );
                foreach ( $booked_seat_ids as $seat_index ){
                    $seat_booking_data = self::set_seat_bookig( $seat_booking_data, $seat_index, $orderDateFormatted, $order_time );
                }
                update_post_meta( $orderPostId, '_mptrs_seat_booking', $seat_booking_data );
            }

        }

        public function set_custom_price_cart_item( $cart_item_data, $product_id) {
            if (!empty($_POST['price'])) {
                $cart_item_data['custom_price'] = floatval($_POST['price']);
            }
            return $cart_item_data;
        }

        public function update_cart_item_price($cart) {
            if (is_admin() && !defined('DOING_AJAX')) return;

            foreach ($cart->get_cart() as $cart_item) {
                if (!empty($cart_item['custom_price'])) {
                    $cart_item['data']->set_price($cart_item['custom_price']);
                }
            }
        }

        public function display_custom_cart_item_data( $item_data, $cart_item ) {
            $fields = [
                'food_menu' => 'Food Menu',
                'booking_seats' => 'Seats',
                'mptrs_order_date' => 'Order Date',
                'mptrs_order_time' => 'Order Time'
            ];

            foreach ($fields as $key => $label) {
                if (!empty($cart_item[$key])) {
                    $value = is_array($cart_item[$key]) ? implode(', ', $cart_item[$key]) : $cart_item[$key];
                    $item_data[] = [
                        'name'  => $label,
                        'value' => esc_html($value)
                    ];
                }
            }
            return $item_data;
        }

        public function add_order_item_meta( $item, $cart_item_key, $values, $order ) {

            $meta_fields = [
                'food_menu' => 'Food Menu',
                'booking_seats' => 'Seats',
                'mptrs_order_date' => 'Order Date',
                'mptrs_order_time' => 'Order Time'
            ];

            foreach ($meta_fields as $key => $label) {
                if (!empty($values[$key])) {
                    $value = is_array($values[$key]) ? implode(', ', $values[$key]) : $values[$key];
                    $item->add_meta_data($label, $value, true);
                }
            }

            $customer_name = trim($order->get_billing_first_name() . ' ' . $order->get_billing_last_name());
            if (!empty($customer_name)) {
                $item->add_meta_data('Customer Name', $customer_name, true);
            }

            $selected_seat_ids = isset( $values['booking_seat_ids'] ) ? $values['booking_seat_ids'] : [];
            if( !empty( $selected_seat_ids ) ){

                $order_data = array(
                    'selected_seat_ids' => $selected_seat_ids,
                    'ordered_date' => $values['mptrs_order_date'],
                    'ordered_time' => $values['mptrs_order_time'],
                );

                $order_data = maybe_serialize( $order_data );

                $product_id = isset( $values['mptrs_item_id'] ) ? $values['mptrs_item_id'] : '';
                if( !empty( $product_id ) ){
                    update_post_meta( $product_id, '_mptrs_booking_data', $order_data );
                }

            }
//            error_log( print_r( [ 'selected_seat_ids' => $selected_seat_ids ], true ) );

        }

        public function display_order_meta( $item_id, $item, $order ) {
//            error_log( print_r( [ '$item' => $item], true ) );
            $meta_fields = [
                'Food Menu' => '🍽 Food Menu',
                'Seats' => '💺 Seats',
                'Customer Name' => '👤 Customer',
                'Order Date' => '📅 Date',
                'Order Time' => '⏰ Time'
            ];

            echo '<div class="mptrs-order-meta"><h4 class="mptrs-meta-title">Order Details</h4>';
            foreach ($meta_fields as $key => $label) {
                $value = $item->get_meta($key, true);
                if (!empty($value)) {
                    echo '<p><strong>' . esc_html($label) . ':</strong> ' . esc_html($value) . '</p>';
                }
            }
            echo '</div>';
        }

    }

    new MPTRS_Cart_Order_Data_Display();
}