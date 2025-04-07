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
            add_action( 'woocommerce_order_status_changed', [$this, 'custom_function_on_order_status_change'], 10, 4 );

            add_filter('woocommerce_get_item_data', [$this,'pa_modify_order_summary_description'], 10, 2);


        }
        function pa_modify_order_summary_description( $item_data, $cart_item) {
            // Modify the product description

            foreach ( $item_data as $k => $v ) {
                if( $v['name'] === 'Food Menu' ){
                    $decoded_text = html_entity_decode( $v['value'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $clean_text = strip_tags($decoded_text);
                    $item_data[$k]['value'] = $clean_text;
                }
            }

            return $item_data;
        }


        function custom_function_on_order_status_change( $order_id, $old_status, $new_status, $order ) {
            if ( $new_status === 'processing' ) {

                $orderPostId = '';
                foreach ( $order->get_items() as $item_id => $item ) {
                    $product = $item->get_product();
                    $orderPostId = $product->get_id();
                }
                $mptrs_booking_data = maybe_unserialize( get_post_meta( $orderPostId, '_mptrs_booking_data', true ) );

                $order_locations =isset( $mptrs_booking_data['order_locations'] ) ? $mptrs_booking_data['order_locations'] : '';
                $booked_seat_ids = isset( $mptrs_booking_data['selected_seat_ids'] ) ? $mptrs_booking_data['selected_seat_ids'] : array();
                $order_date = isset( $mptrs_booking_data['ordered_date'] ) ? $mptrs_booking_data['ordered_date'] : '';
                $order_time = isset( $mptrs_booking_data['ordered_time'] ) ? $mptrs_booking_data['ordered_time'] : '';
                $mptrs_food_menu = isset( $mptrs_booking_data['mptrs_food_menu'] ) ? $mptrs_booking_data['mptrs_food_menu'] : '';
                $mptrs_order_type = isset( $mptrs_booking_data['mptrs_order_type'] ) ? $mptrs_booking_data['mptrs_order_type'] : '';
                $mptrs_service_status = 'In Progress';


                $order_title = 'Custom Order #' . $order_id;
                $order_created_date = $order->get_date_created()->date('Y-m-d H:i:s');
                $order_total = $order->get_total();
                $order_status = $new_status;
                $customer_id = $order->get_customer_id();
                $customer_name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
                $customer_email = $order->get_billing_email();

                $custom_order_id = wp_insert_post(array(
                    'post_title'    => $order_title,
                    'post_type'     => 'mptrs_order',
                    'post_status'   => 'publish',
                    'post_author'   => 1,
                ));

                if ($custom_order_id) {
                    // Store meta data in the custom post
                    update_post_meta($custom_order_id, '_mptrs_order_id', $order_id);
                    update_post_meta($custom_order_id, '_mptrs_created_order_date', $order_created_date);
                    update_post_meta($custom_order_id, '_mptrs_order_total', $order_total);
                    update_post_meta($custom_order_id, '_mptrs_order_status', $order_status);
                    update_post_meta($custom_order_id, '$order_locations', $order_locations);
                    update_post_meta($custom_order_id, '_mptrs_customer_id', $customer_id);
                    update_post_meta($custom_order_id, '_mptrs_customer_name', $customer_name);
                    update_post_meta($custom_order_id, '_mptrs_customer_email', $customer_email);
                    update_post_meta($custom_order_id, '_mptrs_order_date', $order_date);
                    update_post_meta($custom_order_id, '_mptrs_order_time', $order_time);
                    update_post_meta($custom_order_id, '_mptrs_booked_seats', $booked_seat_ids);
                    update_post_meta($custom_order_id, '_mptrs_ordered_food_menu', $mptrs_food_menu);
                    update_post_meta($custom_order_id, '_mptrs_order_type', $mptrs_order_type);
                    update_post_meta($custom_order_id, '_mptrs_service_status', $mptrs_service_status);
                }
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

            if( isset( $mptrs_booking_data['mptrs_order_type'] ) && $mptrs_booking_data['mptrs_order_type'] === 'dine_in' ){
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
            if( $cart_item['mptrs_order_type'] === '' ){
                $fields = [
                    'food_menu' => 'Food Menu',
                    'booking_seats' => 'Seats',
                    'mptrs_order_date' => 'Order Date',
                    'mptrs_order_time' => 'Order Time',
                ];
            }else{
                $fields = [
                    'food_menu' => 'Food Menu',
                    'mptrs_locations' => 'Delivery Locations',
                    'mptrs_order_date' => 'Order Date',
                    'mptrs_order_time' => 'Order Time',
                ];
            }


            foreach ($fields as $key => $label) {
                if (!empty($cart_item[$key])) {
                    $value = is_array($cart_item[$key]) ? implode(', ', $cart_item[$key]) : $cart_item[$key];
                    $item_data[] = [
                        'name'  => $label,
                        'value' => esc_html(esc_html( $value ) )
                    ];
                }
            }
            return $item_data;
        }

        public function add_order_item_meta( $item, $cart_item_key, $values, $order ) {

            /*if( $values['mptrs_order_type'] === 'dine_in' ) {
                $meta_fields = [
                    'food_menu' => 'Food Menu',
                    'booking_seats' => 'Seats',
                    'mptrs_order_date' => 'Order Date',
                    'mptrs_order_time' => 'Order Time',
                    'mptrs_original_post_id' => '_mptrs_id',
                ];
            }else{*/
                $meta_fields = [
                    'food_menu' => 'Food Menu',
                    'mptrs_locations' => 'Locations',
                    'mptrs_order_date' => 'Order Date',
                    'mptrs_order_time' => 'Order Time',
                    'mptrs_original_post_id' => '_mptrs_id',
                ];
//            }

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

            if( isset( $values['mptrs_order_type'] ) ){
                $order_data = [];
                $product_id = isset( $values['mptrs_item_id'] ) ? $values['mptrs_item_id'] : '';
                $order_data = array(
                    'order_locations' => $values['mptrs_locations'],
                    'ordered_date' => $values['mptrs_order_date'],
                    'ordered_time' => $values['mptrs_order_time'],
                    'mptrs_food_menu' => $values['food_menu'],
                    'mptrs_order_type' => $values['mptrs_order_type'],
                );

                $order_data = maybe_serialize( $order_data );
                if( !empty( $product_id ) ){
                    update_post_meta( $product_id, '_mptrs_booking_data', $order_data );
                }

            }


        }

        public function display_order_meta( $item_id, $item, $order ) {
            $meta_fields = [
                'Food Menu' => '🍽 Food Menu',
                'Seats' => '💺 Seats',
                'Customer Name' => '👤 Customer',
                'Order Date' => '📅 Date',
                'Order Time' => '⏰ Time',
                'Locations' => '⏰ Delivery Location'
            ];

            echo '<div class="mptrs-order-meta"><h4 class="mptrs-meta-title">Order Details</h4>';
            foreach ($meta_fields as $key => $label) {
                $value = $item->get_meta($key, true);
                if (!empty($value)) {

                    $value = preg_replace('/<\/li>,/', '</li>', $value);
                    echo '<p><strong>' . esc_attr($label) . ':</strong> ' . wp_kses_post($value) . '</p>';
                }
            }
            echo '</div>';
        }

    }

    new MPTRS_Cart_Order_Data_Display();
}