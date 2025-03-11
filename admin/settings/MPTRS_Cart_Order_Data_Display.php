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

        public function add_order_item_meta($item, $cart_item_key, $values, $order) {
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