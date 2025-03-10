<?php

/**
 * @author rubel mia <rubelcuet10@gmail.com>
 * @license mage-people.com
 * @var 1.0.0
 */

namespace settings;
if (!defined('ABSPATH'))
    die;
if (!class_exists('settings\MPTRS_Cart_Order_Data_Display')) {
    class MPTRS_Cart_Order_Data_Display
    {
        public function __construct()
        {
            add_filter('woocommerce_add_cart_item_data', [$this, 'mptrs_set_custom_price_cart_item'], 10, 2);
            add_action('woocommerce_before_calculate_totals', [$this, 'mptrs_update_cart_item_price'], 10, 1);

            add_filter('woocommerce_get_item_data', [$this, 'mptrs_display_custom_cart_item_data'], 10, 2);
            add_action('woocommerce_checkout_create_order_line_item', [$this, 'mptrs_add_order_item_meta'], 10, 4);
        }

        public function mptrs_add_order_item_meta($item, $cart_item_key, $values, $order)
        {
            error_log(print_r($values, true));
            if (isset($values['food_menu'])) {
                $item->add_meta_data('Food Menu', $values['food_menu'], true);
            }
            if (isset($values['booking_seats'])) {
                $item->add_meta_data('Seats', is_array($values['booking_seats']) ? implode(', ', $values['booking_seats']) : $values['booking_seats'], true);
            }
        }

        public function mptrs_display_custom_cart_item_data($item_data, $cart_item)
        {
            if (isset($cart_item['food_menu'])) {
                $item_data[] = [
                    'name' => 'Food Menu',
                    'value' => wc_clean($cart_item['food_menu']),
                ];
            }
            if (isset($cart_item['booking_seats'])) {
                $item_data[] = [
                    'name' => 'Seats',
                    'value' => is_array($cart_item['booking_seats']) ? implode(', ', $cart_item['booking_seats']) : $cart_item['booking_seats'],
                ];
            }
            return $item_data;
        }

        public function mptrs_update_cart_item_price($cart)
        {
            if (is_admin() && !defined('DOING_AJAX')) return;

            foreach ($cart->get_cart() as $cart_item) {
                if (isset($cart_item['custom_price'])) {
                    $cart_item['data']->set_price($cart_item['custom_price']); // Set custom price
                }
            }
        }

        public function mptrs_set_custom_price_cart_item($cart_item_data, $product_id)
        {
            if (isset($_POST['price'])) {
                $cart_item_data['custom_price'] = floatval($_POST['price']); // Store custom price
            }
            return $cart_item_data;
        }


    }
}