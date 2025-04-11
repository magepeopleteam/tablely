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

            add_action('wp_ajax_mptrs_save_service_status_update', [$this, 'mptrs_save_service_status_update'] );
            add_action('wp_ajax_nopriv_mptrs_save_service_status_update', [$this, 'mptrs_save_service_status_update'] );

            add_action('wp_ajax_mptrs_save_table_reserved_status_update', [$this, 'mptrs_save_table_reserved_status_update'] );
            add_action('wp_ajax_nopriv_mptrs_save_table_reserved_status_update', [$this, 'mptrs_save_table_reserved_status_update'] );

            add_action('wp_ajax_mptrs_table_reservations', [$this, 'mptrs_table_reservations'] );
            add_action('wp_ajax_nopriv_mptrs_table_reservations', [$this, 'mptrs_table_reservations'] );

            add_action('wp_ajax_mptrs_order_details_display', [$this, 'mptrs_order_details_display'] );
            add_action('wp_ajax_nopriv_mptrs_order_details_display', [$this, 'mptrs_order_details_display'] );

            add_action('wp_ajax_mptrs_set_food_menu_display_limit', [$this, 'mptrs_set_food_menu_display_limit'] );
            add_action('wp_ajax_nopriv_mptrs_set_food_menu_display_limit', [$this, 'mptrs_set_food_menu_display_limit'] );


        }

        function mptrs_set_food_menu_display_limit() {

            $result = false;
            $message = 'Something Wrong';

            if (isset($_POST['nonce']) && wp_verify_nonce(sanitize_text_field($_POST['nonce']), 'mptrs_admin_nonce')) {
                $newLimit = isset( $_POST['newLimit'] ) ? sanitize_file_name( $_POST['newLimit'] ) : '';
                $limitKey = isset( $_POST['limitKey'] ) ? sanitize_file_name( $_POST['limitKey'] ) : '';

                if( $limitKey ){
                    $result = update_option( $limitKey, $newLimit );
                    $message = 'Display Limit Successfully Updated';
                }

            }

            wp_send_json_success([
                'message' => $message,
                'success' => $result,
            ]);


        }

        function mptrs_order_details_display() {

            $result = false;
            $all_order_meta = [];

            if (isset($_POST['nonce']) && wp_verify_nonce(sanitize_text_field($_POST['nonce']), 'mptrs_admin_nonce')) {
                $order_id = isset( $_POST['orderId'] ) ? absint( $_POST['orderId'] ) : '';

                if( $order_id ){
                    $order = wc_get_order( $order_id );

                    if ( $order ) {
                        $all_order_meta['billing_email'] = $order->get_billing_email();
                        foreach ( $order->get_items() as $item_id => $item ) {
                            $item_meta = [];

                            foreach ( $item->get_meta_data() as $meta ) {
                                $item_meta[ $meta->key ] = $meta->value;
                            }

                            if( !empty( $item_meta )){
                                $result = true;
                                $all_order_meta[ 'order_info' ] = $item_meta;
                            }

                        }
                    }

                }

            }

            wp_send_json_success([
                'message' => 'order Details Displayed',
                'success' => $result,
                'order_data' => $all_order_meta,
            ]);


        }

        function mptrs_add_food_items_to_cart() {

            if ( !isset($_POST['post_id'], $_POST['mptrs_orderType'], $_POST['menu'], $_POST['price'], $_POST['quantity'])) {
                wp_send_json_error('Missing required data.');
            }
            $original_post_id = intval( sanitize_text_field( $_POST['post_id'] ) );
            $post_id = get_post_meta( $original_post_id, 'link_wc_product', true ) ;
            $get_food_menu = get_option( '_mptrs_food_menu' );
            $ordered_menu_key = sanitize_text_field( $_POST['menu'] );
            $ordered_menu_key = json_decode( stripslashes( $ordered_menu_key ), true);

            $orderVarDetailsAry =  isset( $_POST['orderVarDetailsStr'] ) ? (array) json_decode( stripslashes( $_POST['orderVarDetailsStr'] ) ) : [];

            $menu = '<ul class="mptrs_orderDetailListsHolder">';
            foreach ($ordered_menu_key as $key => $value) {
                $var_details = '';
                if( isset( $get_food_menu[ $key ] ) ) {
                    if (isset( $orderVarDetailsAry[ $key] ) ){
                        $var_details = $orderVarDetailsAry[ $key];
                    }
                    if( $var_details ){
                        $menu .= '<li class="mptrs_orderDetailList">
                                     <strong>Item Name:</strong> '.$get_food_menu[ $key ]['menuName'].'<br>
                                     <strong>Person:<strong>'.$get_food_menu[ $key ]['numPersons'].'<br>
                                     <strong>Quantity:<strong>'.$value.'<br>
                                     <strong>Details:<strong>'.$var_details.'<br>
                                    </li>';
                    }else{
//                        $menu .= '<li class="mptrs_orderDetailList">Item Name: '.$get_food_menu[ $key ]['menuName']. ' Person:'.$get_food_menu[ $key ]['numPersons'].' Quantity:'.$value.'</li>';
                        $menu .= '<li class="mptrs_orderDetailList">
                                     <strong>Item Name:</strong> '.$get_food_menu[ $key ]['menuName'].'<br>
                                     <strong>Person:<strong>'.$get_food_menu[ $key ]['numPersons'].'<br>
                                     <strong>Quantity:<strong>'.$value.'<br>
                                   </li>';
                    }
                    $menu .= '';
                }
            }
            $menu .= '</ul>';

            $mptrs_order_date = isset( $_POST['mptrs_order_date'] ) ?  sanitize_text_field( $_POST['mptrs_order_date'] ) : '';
            $mptrs_order_time = isset( $_POST['mptrs_order_time'] ) ? sanitize_text_field( $_POST['mptrs_order_time'] ) : '';

            $price = floatval( sanitize_text_field($_POST['price'] ) );
            $quantity = intval( sanitize_text_field($_POST['quantity'] ) );
            $mptrs_user_details = '';
            $mptrs_locations = isset( $_POST['mptrs_locations'] ) ?  sanitize_text_field( $_POST['mptrs_locations'] ) : '';

            $mptrs_orderType_text = sanitize_text_field( $_POST['mptrs_orderType'] );

            if( $mptrs_orderType_text === 'Delivery' ) {
                $mptrs_orderType = 'delivery';
            }elseif( $mptrs_orderType_text === 'Takeaway' ) {
                $mptrs_orderType = 'take_away';
            }elseif( $mptrs_orderType_text === 'Dine-In' ){
                $mptrs_orderType = 'dine_in';
            }else{
                $mptrs_orderType = '';
            }

            /*if( $mptrs_orderType === 'dine_in' ){
                $seats = json_decode( stripslashes( sanitize_text_field( $_POST['seats'] ) ), true);
                $bookedSeatName = json_decode( stripslashes( sanitize_text_field( $_POST['bookedSeatName'] ) ), true);
                $cart_item_data = [
                    'mptrs_original_post_id' => $original_post_id,
                    'mptrs_item_id' => $post_id,
                    'food_menu' => $menu,
                    'mptrs_order_type' => $mptrs_orderType,
                    'booking_seat_ids' => $seats,
                    'booking_seats' => $bookedSeatName,
                    'price' => $price,
                    'mptrs_order_date' => $mptrs_order_date,
                    'mptrs_order_time' => $mptrs_order_time,
                    'mptrs_user_details' => $mptrs_user_details,
                ];
            }
            else if( $mptrs_orderType === 'delivery' ){
                $mptrs_locations = json_decode( stripslashes( sanitize_text_field( $_POST['mptrs_locations'] ) ), true);
                $cart_item_data = [
                    'mptrs_original_post_id' => $original_post_id,
                    'mptrs_item_id' => $post_id,
                    'food_menu' => $menu,
                    'mptrs_order_type' => $mptrs_orderType,
                    'mptrs_locations' => $mptrs_locations,
                    'price' => $price,
                    'mptrs_order_date' => $mptrs_order_date,
                    'mptrs_order_time' => $mptrs_order_time,
                    'mptrs_user_details' => $mptrs_user_details,
                ];
            }
            else{
                $mptrs_locations = '';
                $cart_item_data = [
                    'mptrs_original_post_id' => $original_post_id,
                    'mptrs_item_id' => $post_id,
                    'food_menu' => $menu,
                    'mptrs_order_type' => $mptrs_orderType,
                    'mptrs_locations' => $mptrs_locations,
                    'price' => $price,
                    'mptrs_order_date' => $mptrs_order_date,
                    'mptrs_order_time' => $mptrs_order_time,
                    'mptrs_user_details' => $mptrs_user_details,
                ];
            }*/

            $cart_item_data = [
                'mptrs_original_post_id' => $original_post_id,
                'mptrs_item_id' => $post_id,
                'food_menu' => $menu,
                'mptrs_order_type' => $mptrs_orderType,
                'price' => $price,
                'mptrs_locations' => $mptrs_locations,
                'mptrs_order_date' => $mptrs_order_date,
                'mptrs_order_time' => $mptrs_order_time,
                'mptrs_user_details' => $mptrs_user_details,
            ];

            if (!class_exists('WC_Cart')) {
                wp_send_json_error('WooCommerce is not active.');
            }

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

        function mptrs_table_reservations_old(){

            if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field($_POST['nonce']), 'mptrs_admin_nonce')) {

                $get_time = isset( $_POST['get_time'] ) ? $_POST['get_time'] : '' ;
                $get_date = isset( $_POST['get_date'] ) ? $_POST['get_date'] : '' ;
                $seatIds = isset( $_POST['seatIds'] ) ? $_POST['seatIds'] : '' ;
                $seatNames = isset( $_POST['seatNames'] ) ? $_POST['seatNames'] : '' ;
                $occasion = isset( $_POST['occasion'] ) ? $_POST['occasion'] : '' ;
                $guests = isset( $_POST['guests'] ) ? $_POST['guests'] : '' ;

                $userName = isset( $_POST['userName'] ) ? $_POST['userName'] : '' ;
                $userPhoneNum = isset( $_POST['userPhoneNum'] ) ? $_POST['userPhoneNum'] : '' ;
                $userEmailId = isset( $_POST['userEmailId'] ) ? $_POST['userEmailId'] : '' ;
                $userAdvice = isset( $_POST['userAdvice'] ) ? $_POST['userAdvice'] : '' ;

                $reservation_table_info = array(
                    'get_time' => $get_time,
                    'get_date' => $get_date,
                    'seatIds' => $seatIds,
                    'seatNames' => $seatNames,
                    'occasion' => $occasion,
                    'guests' =>  $guests,
                );

                $reservation_persion_info = array(
                    'userName' => $userName,
                    'userPhoneNum' => $userPhoneNum,
                    'userEmailId' => $userEmailId,
                    'userAdvice' => $userAdvice,
                );

                $order_title = $userName.' table reservations';
                $custom_order_id = wp_insert_post(array(
                    'post_title'    => $order_title,
                    'post_type'     => 'mptrs_table_reservations',
                    'post_status'   => 'publish',
                    'post_author'   => 1,
                ));

                if ( $custom_order_id ) {
                    // Store meta data in the custom post
                    update_post_meta( $custom_order_id, '_mptrs_table_reservation_table_info', $reservation_table_info );
                    update_post_meta( $custom_order_id, '_mptrs_table_reservation_persion_info', $reservation_persion_info );
                    update_post_meta( $custom_order_id, '_mptrs_table_reservation_status', 0 );
                }
            }
        }

        public static function  mptrs_seats_reserved( $booked_seat_ids_str, $orderPostId, $order_date, $order_time ){

            $result = 0;
            if( $booked_seat_ids_str ){
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

            return $result;
         }
        function mptrs_table_reservations() {

            $result = 0;
            $message = 'Table Reserved Failed';
            if (isset($_POST['nonce']) && wp_verify_nonce(sanitize_text_field($_POST['nonce']), 'mptrs_nonce')) {

                $seatNames = [];
                $get_time   = sanitize_text_field($_POST['get_time'] ?? '');
                $get_date   = sanitize_text_field($_POST['get_date'] ?? '');
                $booked_seat_ids_str    = sanitize_text_field($_POST['seatIds'] ?? '');
                $seatNames_str  = sanitize_text_field($_POST['seatNames'] ?? '');
                $occasion   = sanitize_text_field($_POST['occasion'] ?? '');
                $guests     = sanitize_text_field($_POST['guests'] ?? '');

                $userName      = sanitize_text_field($_POST['userName'] ?? '');
                $userPhoneNum  = sanitize_text_field($_POST['userPhoneNum'] ?? '');
                $userEmailId   = sanitize_email($_POST['userEmailId'] ?? '');
                $userAdvice    = sanitize_text_field($_POST['userAdvice'] ?? '');
                $orderPostId         = sanitize_text_field($_POST['postId'] ?? '');

                if( $seatNames_str ){
                    $seatNames_ary = json_decode( stripslashes( $seatNames_str ), true );
                    if( is_array( $seatNames_ary ) && !empty( $seatNames_ary ) ){
                        $seatNames = $seatNames_ary;
                    }

                }

                $reservation_table_info = [
                    'reserve_time' => $get_time,
                    'reserve_date' => $get_date,
                    'seatNames' => $seatNames,
                    'occasion' => $occasion,
                    'guests' =>  $guests,
                    'userName' => $userName,
                    'userPhoneNum' => $userPhoneNum,
                    'userEmailId' => $userEmailId,
                    'userAdvice' => $userAdvice,
                ];

                /*$reservation_persion_info = [
                    'userName' => $userName,
                    'userPhoneNum' => $userPhoneNum,
                    'userEmailId' => $userEmailId,
                    'userAdvice' => $userAdvice,
                ];*/

                $order_title = $userName . ' table reservation';

                $custom_order_id = wp_insert_post([
                    'post_title'    => $order_title,
                    'post_type'     => 'mptrs_table_reserve',
                    'post_status'   => 'publish',
                    'post_author'   => get_current_user_id(),
                ]);

                if ( $custom_order_id && !is_wp_error($custom_order_id ) ) {

                    $reservation_table_info = maybe_serialize( $reservation_table_info );

                    $result = self::mptrs_seats_reserved( $booked_seat_ids_str, $orderPostId, $get_date, $get_time );
                    update_post_meta($custom_order_id, '_mptrs_table_reservation_info', $reservation_table_info);
//                    update_post_meta($custom_order_id, '_mptrs_table_reservation_persion_info', $reservation_persion_info);
                    update_post_meta($custom_order_id, '_mptrs_table_reservation_status', 0);

                    $message = 'Successfully table reserved';
                }

            } else {
               $message='Nonce failed in mptrs_table_reservations';
            }

            wp_send_json_success([
                'message' => $message,
                'result' => $result,
            ]);

        }


        function mptrs_get_available_seats_for_reservations(){

            $seat_map = '';
            $get_food_menu = [];

            if (!isset($_POST['nonce']) || !wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'mptrs_admin_nonce')) {
                $orderPostId = isset( $_POST['post_id']) ? sanitize_text_field( $_POST['post_id'] ) : '';
                $search_time = isset( $_POST['get_time']) ? sanitize_text_field( $_POST['get_time'] ) : '';
                $get_date = isset( $_POST['get_date']) ? sanitize_text_field( $_POST['get_date'] ) : '';
                $orderDateFormatted = date('d_m_y', strtotime( $get_date ) );

//                $orderPostId = get_post_meta( $orderPostId, 'link_wc_product', true ) ;
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
                if( $post_id ){
                    $seat_map = MPTRS_Details_Layout::display_seat_mapping( $post_id, $not_available );
                }

            }

            /*$get_food_menu = get_option( '_mptrs_food_menu' );
            if( !is_array( $get_food_menu ) && empty( $get_food_menu ) ){
                $get_food_menu = [];
            }*/

            wp_send_json_success([
                'message' => 'Categories Data getting successfully.!',
                'mptrs_seat_maps' => $seat_map,
                'get_food_menu' => $get_food_menu,
            ]);

        }

        public function mptrs_price_change_food_menu_restaurant(){

            if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field($_POST['nonce']), 'mptrs_admin_nonce')) {
                wp_send_json_error( [ 'message' => 'Security check failed.' ], 403 );
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
            $key = isset( $_POST['menuKey'] ) ? sanitize_text_field( $_POST['menuKey'] ) : '';
            $result = 0;
            $categories = [];
            $edited_menu = [];
            $message = 'Categories Data getting successfully.!';

            $mptrs_categories = get_option( 'mptrs_categories' );

            if( !empty( $key ) ){
                $existing_menus = get_option( '_mptrs_food_menu' );
                $edited_menu = isset( $existing_menus[$key] ) ? $existing_menus[$key] : [];
                $result = 1;
            }
            if( is_array( $mptrs_categories ) && !empty( $mptrs_categories ) ) {
                $result = 1;
                $categories = $mptrs_categories;
            }

            wp_send_json_success([
                'message' => $message,
                'success' => $result,
                'mptrs_categories' => $categories,
                'mptrs_edited_menu' => $edited_menu,
            ]);
        }

        public function mptrs_save_service_status_update(){
            $result = 0;

            if ( isset($_POST['nonce']) && wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'mptrs_admin_nonce')) {
                $post_id = isset( $_POST['post_id'] ) ? sanitize_text_field($_POST['post_id']) : '';
                $service_status = isset( $_POST['selectedVal'] ) ? sanitize_text_field($_POST['selectedVal']) : '';
                if(  $post_id !== '' && $service_status !== '' ){
                    $result = update_post_meta( $post_id, '_mptrs_service_status', $service_status );
                }
            }
            wp_send_json_success([
                'message' => 'Service Status updated successfully.!',
                'success' => $result,
            ]);
        }

        public function mptrs_send_reservation_completed_email( $post_id ) {


            $reservation_info = maybe_unserialize( get_post_meta( $post_id, '_mptrs_table_reservation_info', true ) );
            $user_email = $reservation_info['userEmailId'];
            $user_name = $reservation_info['userName'];
            $reservation_date = $reservation_info['reserve_date'];
            $reserve_time = $reservation_info['reserve_time'];
            $seatNames = $reservation_info['seatNames'];
            $table_seats_number = '';
            if( is_array( $seatNames ) && !empty( $seatNames ) ){
                foreach( $seatNames as $seat ){
                    $table_seats_number .= $seat.', ';
                }
            }

//            $headers[] = "From: $form_name <$form_email>";
            $headers[] = "";

            if ( ! empty( $user_email ) ) {
                $subject = 'Your Reservation is Completed';
                $message = "Dear Customer,\n\nYour table reservation has been successfully completed.\n\n";
                $message .= "Table: {$table_seats_number}\nDate: {$reservation_date}\n\n";
                $message .= "Thank you for choosing us!\n\nBest regards,\nYour Restaurant Team";

                wp_mail( $user_email, $subject, $message, $headers );
            }
        }
        public function mptrs_save_table_reserved_status_update(){
            $result = 0;

            if ( isset($_POST['nonce']) && wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'mptrs_admin_nonce')) {
                $post_id = isset( $_POST['post_id'] ) ? sanitize_text_field($_POST['post_id']) : '';
                $service_status = isset( $_POST['selectedVal'] ) ? sanitize_text_field($_POST['selectedVal']) : '';
                if(  $post_id !== '' && $service_status !== '' ){
                    $result = update_post_meta( $post_id, '_mptrs_table_reservation_status', $service_status );
                }

                if ( $service_status == 1 ) {
                    $this->mptrs_send_reservation_completed_email( $post_id );
                }
                $message = 'Service Status updated successfully.!';
            }else{
                $message = 'Service Status updated Failed.!';
            }
            wp_send_json_success([
                'message' => $message,
                'success' => $result,
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