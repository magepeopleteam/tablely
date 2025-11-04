<?php
/**
 * Template for restaurant Tablely
 * @author Shahadat Hossain <raselsha@gmail.com>
 * @version 1.0.0
 */

if (!defined('ABSPATH')) die;

if (!class_exists('MPTRS_Template')) {
    class MPTRS_Template
    {
        public function __construct()
        {
            add_action('mptrs_template_header',[$this, 'template_header']);
            add_action('mptrs_template_header',[$this, 'template_popup_tablely']);
            add_action('mptrs_template_header',[$this, 'template_popup_reviews']);
            add_action('mptrs_template_header',[$this, 'template_popup_restaurant']);

            add_action('mptrs_template_logo',[$this, 'template_logo']);
            add_action('mptrs_restaurant_info',[$this, 'restaurant_info']);
            add_action('mptrs_template_menus',[$this, 'display_restaurant_content']);
            add_action('mptrs_template_basket',[$this, 'display_restaurant_basket']);
            // add_action('mptrs_sidebar_content',[$this, 'display_sidebar_content']);
            add_action('mptrs_time_schedule',[$this, 'display_time_schedule']);
        }

        public function template_header()
        {
            $post_id = get_the_id();
            $thumbnail_url = get_the_post_thumbnail_url($post_id, 'full');
            if ( has_post_thumbnail() ) : ?>
                <header class="mptrs-header-baner"> 
                    <img alt="<?php echo esc_attr( get_the_title() );?>" src="<?php echo esc_attr( $thumbnail_url );?>">
                </header>
            <?php endif; ?>
            <?php
        }

        public function template_popup_tablely(){
            ?>
            <div id="seatPopup" class="mptrs-popup">
                <div class="mptrs-popup-content">
                    <span class="close-btn">&times;</span>
                    <div class="mptrs-popup-header">
                        <span class="mptrs-popup-close" data-popup-close="yes"><i class="fas fa-times"></i></span>
                    </div>
                    <div class="mptrs-popup-body">
                        <div class="mptrs_seatMappedHolder">
                            <span class="mptrs_selectSeatText"><?php esc_html_e( 'Choose a Seat, What\'s Your Choice?', 'tablely' ); ?></span>
                            <div class="mptrs_popUpInfoHolder">
                                <div class="mptrs_seatMapDisplay" id="mptrs_seatMapDisplay"></div>
                                <div class="mptrs_orderInfoHolder">
                                    <div class="mptrs_orderDetailsPopup" id="mptrs_orderDetailsPopup">
                                        <table class="mptrs_orderAddedTable" id="mptrs_orderAddedTable">
                                            <thead>
                                            <tr>
                                                <th><?php esc_html_e( 'Menu Item', 'tablely' )?></th>
                                                <th><?php esc_html_e( 'Quantity', 'tablely' )?></th>
                                            </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                        <table class="mptrs_orderAddedTable" id="mptrs_orderAddedDetails">
                                            <thead>
                                            <tr>
                                                <th><?php esc_html_e( 'Order Date', 'tablely' )?></th>
                                                <th><?php esc_html_e( 'Ordered Time', 'tablely' )?></th>
                                                <th><?php esc_html_e( 'Total Price', 'tablely' )?></th>
                                            </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="mptrs_dineInOrderPlaceBtn" id="mptrs_dineInOrderPlaceBtn"><?php esc_html_e( 'Process Checkout', 'tablely' )?></div>
                    </div>
                </div>
            </div>
            <?php
        }

        public function template_popup_restaurant(){
            ?>
            <div id="mptrs-restaurant-popup" class="mptrs-popup">
                <div class="mptrs-popup-content">
                    <div class="mptrs-popup-header">
                        <span class="mptrs-popup-close" data-popup-close="yes"><i class="fas fa-times"></i></span>
                    </div>
                    <div class="mptrs-popup-body">
                        <?php do_action('mptrs_time_schedule'); ?>
                        <?php the_content(); ?>
                    </div>
                </div>
            </div>
            <?php
        }
        public function template_popup_reviews(){
            ?>
            <div id="mptrs-reviews-popup" class="mptrs-popup">
                <div class="mptrs-popup-content mptrs-popup-medium">
                    <div class="mptrs-popup-header">
                        <h2><?php esc_html_e('Reviews','tablely') ?></h2>
                        <span class="mptrs-popup-close" data-popup-close="yes"><i class="fas fa-times"></i></span>
                    </div>
                    <div class="mptrs-popup-body">
                        
                    </div>
                </div>
            </div>
            <?php
        }

        public function template_logo()
        {
            $post_id = get_the_id();
            $image_id = get_post_meta($post_id, 'mptrs_restaurant_logo', true);
			$image_url = $image_id ? wp_get_attachment_image_src($image_id, 'full')[0] : '';
            ?>
            <?php if ($image_url): ?>
                <div class="mptrs-logo">
                    <img class="mptrs_logo_image" alt="<?php esc_attr( get_the_title() );?>" src="<?php echo esc_attr($image_url); ?>">

                </div>
            <?php endif; ?>
            <?php
        }

        public function restaurant_info()
        {
            $post_id = get_the_id();
            ?>
            <h1 class="mptrs-restaurant-name"><?php the_title();?></h1>
            <p class="mptrs-location"><i class="fas fa-map-marker-alt"></i> <?php esc_html_e( '5th floor, Concord MK Heritage, Dhaka, Dhanmondi Dhaka', 'tablely' ); ?></p>
            <div class="mptrs-schedule-review">
<p class="mptrs-time-schedule">
                <span class="open-now"></span><?php esc_html_e( 'Open Now', 'tablely' ); ?></span>
                <?php esc_html_e( '11:00 AM - 11:00 PM', 'tablely' ); ?>
            </p>
            <p class="mptrs-reviews">
                <i class="fas fa-star"></i>
                <span><?php echo esc_html__('4.8/5','tablely'); ?></span>
                <button class="reviews-button mptrs-data" data-popup-target="#mptrs-reviews-popup"><?php echo esc_html__('See Reviews','tablely'); ?></button>
               
            </p>
            <p class="mptrs-more-info">
		 <button class="more-info-button mptrs-data" data-popup-target="#mptrs-restaurant-popup"><i class="fas fa-info"></i> <?php echo esc_html__('More Info','tablely'); ?></button>    
	    </p>
		    
	</div>
            <?php
        }

        public function display_time_schedule(){
            ?>
            <div class="mptrs_openingHours">
                <h3 class="mptrs_openingTitle"><?php esc_html_e( 'Opening Hours', 'tablely' ); ?></h3>
                <ul class="mptrs_openingList">
                    <li><span class="mptrs_day"><?php esc_html_e( 'Saturday', 'tablely' ); ?></span> <span class="mptrs_time"><?php esc_html_e( '11:00 AM - 11:00 PM', 'tablely' ); ?></span></li>
                    <li><span class="mptrs_day"><?php esc_html_e( 'Sunday', 'tablely' ); ?></span> <span class="mptrs_time"><?php esc_html_e( '11:00 AM - 11:00 PM', 'tablely' ); ?></span></li>
                    <li><span class="mptrs_day"><?php esc_html_e( 'Monday', 'tablely' ); ?></span> <span class="mptrs_time"><?php esc_html_e( '11:00 AM - 11:00 PM', 'tablely' ); ?></span></li>
                    <li><span class="mptrs_day"><?php esc_html_e( 'Tuesday', 'tablely' ); ?></span> <span class="mptrs_time"><?php esc_html_e( '11:00 AM - 11:00 PM', 'tablely' ); ?></span></li>
                    <li><span class="mptrs_day"><?php esc_html_e( 'Wednesday', 'tablely' ); ?></span> <span class="mptrs_time"><?php esc_html_e( '11:00 AM - 11:00 PM', 'tablely' ); ?></span></li>
                    <li><span class="mptrs_day"><?php esc_html_e( 'Thursday', 'tablely' ); ?></span> <span class="mptrs_time"><?php esc_html_e( '11:00 AM - 11:00 PM', 'tablely' ); ?></span></li>
                    <li><span class="mptrs_day"><?php esc_html_e( 'Friday', 'tablely' ); ?></span> <span class="mptrs_time"><?php esc_html_e( '11:00 AM - 11:00 PM', 'tablely' ); ?></span></li>
                </ul>
            </div>
            <?php
        }
        public function display_sidebar_content(){
            ?>
            <div class="mptrs_rightSidebar">
                <div class="mptrs_rightSidebarItem">
                    <h4><?php esc_html_e( 'Dress Code', 'tablely' ); ?></h4>
                    <p><?php esc_html_e( 'Casual, Business Casual, Semi-Formal, Western, Formal', 'tablely' ); ?></p>
                </div>

                <div class="mptrs_rightSidebarItem">
                    <h4><?php esc_html_e( 'Noise', 'tablely' ); ?></h4>
                    <p><?php esc_html_e( 'Silence, Party, Normal', 'tablely' ); ?></p>
                </div>

                <div class="mptrs_rightSidebarItem">
                    <h4><?php esc_html_e( 'Dining Style', 'tablely' ); ?></h4>
                    <p><?php esc_html_e( 'Casual Dining, Family', 'tablely' ); ?></p>
                </div>

                <div class="mptrs_rightSidebarItem">
                    <h4><?php esc_html_e( 'Cuisine', 'tablely' ); ?></h4>
                    <p><?php esc_html_e( 'International, Local', 'tablely' ); ?></p>
                </div>

                <div class="mptrs_rightSidebarItem">
                    <h4><?php esc_html_e( 'Address', 'tablely' ); ?></h4>
                    <p class="mptrs_address">
                        <?php esc_html_e( '5th floor, Concord MK Heritage, Dhaka, Dhanmondi Dhaka', 'tablely' ); ?>
                    </p>
                    <a href="#" class="mptrs_openMap"><?php esc_html_e( 'Open in Map', 'tablely' ); ?></a>
                </div>

                <div class="mptrs_rightSidebarItem">
                    <a href="#" class="mptrs_socialMedia"><?php esc_html_e( 'Find on Social Media', 'tablely' ); ?></a>
                </div>
            </div>
            <?php
        }

        public static function display_cart_data_from_cookie( $post_id ){
            $html = '';
            $total_price = 0;

            if ( isset( $_COOKIE['mptrs_cart_cookie_items'] ) ) {
                $cookie_data = $_COOKIE['mptrs_cart_cookie_items'];
                $item_keys = (array) json_decode( wp_unslash( $cookie_data, true ) );
                $existing_menu_by_id = array_keys( $item_keys );

                $existing_edited_price = get_post_meta( $post_id, '_mptrs_food_menu_edited_prices', true );

                $existing_menus = [];
                $all_food_menus = get_option('_mptrs_food_menu', true);

                $menu_category_count = [];
                if (is_array($existing_menu_by_id) && !empty($existing_menu_by_id)) {
                    foreach ($existing_menu_by_id as $item) {
                        if (isset($all_food_menus[$item])) {
                            $existing_menus[$item] = $all_food_menus[$item];
                        }
                    }
                }

                $orderVarDetails = '';
                ob_start();

                foreach ( $existing_menus as $key => $item ){
                    $price = $item['menuPrice'];
                    $count = isset( $item_keys[ $key ] ) ? $item_keys[ $key ] : 1;
                    if ( !empty($existing_edited_price) && isset($existing_edited_price[$key]  )) {
                        $price = $existing_edited_price[$key];
                    }

                    $total_price += $price*$count;
                    ?>
                    <div class="mptrs_menuAddedCartItem"
                     id="mptrs_menuAddedCartItem-<?php echo esc_attr( $key ); ?>"
                     data-id="<?php echo esc_attr( $key ); ?>"
                     data-price="<?php echo esc_attr( $price ); ?>">

                    <img class="mptrs_menuImg"
                         src="<?php echo esc_url( $item['menuImgUrl'] ); ?>"
                         alt="<?php echo esc_attr( $item['menuName'] ); ?>">

                    <div class="mptrs_menuDetails">
                        <div class="mptrs_addedMenuName"><?php echo esc_html( $item['menuName'] ); ?></div>
                        <div class="mptrs_addedMenuOrderDetails"><?php echo esc_html( $orderVarDetails ); ?></div>
                        <div class="mptrs_menuPrice">
<!--                            --><?php //echo esc_html( $item['mptrs_CurrencySymbol'] ); ?>
                            <?php echo esc_html( $price ); ?>
                        </div>
                    </div>

                    <div class="mptrs_quantityControls"
                         id="mptrs_quantityControls-<?php echo esc_attr( $key ); ?>">
                            <?php if( $count > 1 ){?>
                                <span class="mptrs_decrease">-</span>
                                <?php }else{?>
                                <span class="mptrs_decrease">
                                    <i class="fas fa-trash" style="font-size: 16px"></i>
                                </span>
                            <?php }?>
                            <span class="mptrs_quantity"
                              id="mptrs_quantity-<?php echo esc_attr( $key ); ?>"> <?php echo esc_attr( $count );?>
                            </span>
                            <span class="mptrs_increase">+</span>
                    </div>
                </div>
                    <?php
                }

                $html = ob_get_clean();
            }

            return array(
                    'html' => $html,
                    'total_price' => $total_price,
            );
        }

        public function display_restaurant_basket( $post_id ){

            $cookie_data = self::display_cart_data_from_cookie( $post_id );

            $cart_details_cookie_data = [];
            $order_details = '';
            $details_display = 'none';
            if ( isset( $_COOKIE['mptrs_cart_details_cookie'] ) ) {
                $cart_details_cookie = wp_unslash( $_COOKIE['mptrs_cart_details_cookie'] );
                $cart_details_cookie_data = json_decode( $cart_details_cookie, true );
                $details_display = 'block';

                $location = '';
                if( $cart_details_cookie_data['mptrs_orderType'] === 'Delivery' ){
                    $location = isset( $cart_details_cookie_data['mptrs_locations'] )
                        ? ', Location: ' . $cart_details_cookie_data['mptrs_locations']
                        : '';
                }

                $order_details = ' '.$cart_details_cookie_data['mptrs_orderType'].', Order Date: '.$cart_details_cookie_data['mptrs_orderDate'].', Time: '.$cart_details_cookie_data['mptrs_orderTime'].' '.$location;
            }


            ?>
            <div class="mptrs-ordered-basket" id="mptrs_orderedFoodMenuInfoHolder">
                <div class="mptrs-basket-top">
                    <div class="mptrs_orderTypeDatesDisplay">
                        <span class="mptrs_orderTypeDates" id="mptrs_orderTypeDates">
                            <?php echo wp_kses_post( $order_details );?>
                        </span>
                        <span class="mptrs_orderTypeDatesChange" id="mptrs_orderTypeDatesChange" style="display: <?php echo esc_html( $details_display );?>">
                            <?php esc_html_e( 'change', 'tablely' );?>
                        </span>
                    </div>
                    <div class="basket-header">
                        <h6><?php esc_html_e( 'Your Orders', 'tablely' ); ?></h6>
                        <div class="mptrs_orderedMenuHolder">
                            <span class="mptrs_clearOrder" style="display: <?php echo esc_html( $details_display );?>"><?php esc_html_e( 'Clear Order', 'tablely' );?></span>
                        </div>
                    </div>
                    <div class="mptrs_orderedFoodMenuHolder" id="mptrs_orderedFoodMenuHolder">
                        <?php
//                        $cookie_data['html'] = '';
                        echo wp_kses_post( $cookie_data['html'] );
                        ?>
                    </div>
                </div>
                <?php if( $cookie_data['html'] === '' ){?>
                <div class="mptrs-basket-middle" id="mptrs-basket-middle">
                    <img src="<?php echo MPTRS_Plan_URL; ?>/assets/images/dish.png" alt="">
                    <p><?php esc_html_e('Please Add menu to busket','tablely'); ?></p>
                </div>
                <?php }

//                $cookie_data['total_price'] = 0;
                $formatted_price = wc_price( $cookie_data['total_price'] );
                ?>
                <div class="mptrs-basket-bottom">
                    <div class="mptrs_totalPriceHolder" id="mptrs_totalPriceHolder">
                        <div class="mptrs_totalPricetext"><?php esc_html_e( 'Total', 'tablely' ); ?></div>
                        <!--  <span class="mptrs_sitePriceSymble" id="mptrs_sitePriceSymble"></span>-->
                        <input class="mptrs_totalPrice" data-mptrs-total-price="<?php echo esc_attr( $cookie_data['total_price'] );?>" id="mptrs_totalPrice" name="mptrs_totalPrice" value="<?php echo esc_html( strip_tags( $formatted_price ) );?>" readonly placeholder="$0" disabled>
                    </div>
                    <div class="mptrs_dineInOrderPlaceBtn" id="mptrs_dineInOrderPlaceBtn"><?php esc_html_e( 'Process Checkout', 'tablely' )?></div>
                </div>
            </div>
            <?php
        }
        public function display_restaurant_content( $post_id ) {

            $existing_menu_cookie = $item_keys = [];
            if ( isset( $_COOKIE['mptrs_cart_cookie_items'] ) ) {
                $cookie_data = $_COOKIE['mptrs_cart_cookie_items'];
                $item_keys = (array)json_decode(wp_unslash($cookie_data, true));
                $existing_menu_cookie = array_keys($item_keys);
            }


//            $post_id = get_the_id();
            $categories = get_option('mptrs_categories');

            $enable_location_autocomplete = get_option( 'mptrs_enable_location_autocomplete' );

            $existing_menu_by_id = get_post_meta( $post_id, '_mptrs_food_menu_items', true );

            $existing_edited_price = get_post_meta( $post_id, '_mptrs_food_menu_edited_prices', true );
    
            $existing_menus = [];
            $all_food_menus = get_option('_mptrs_food_menu', true);

            $menu_category_count = [];
            if (is_array($existing_menu_by_id) && !empty($existing_menu_by_id)) {
                foreach ($existing_menu_by_id as $item) {
                    if (isset($all_food_menus[$item])) {
                        $existing_menus[$item] = $all_food_menus[$item];
                    }
                }
            }

            $total_menus = 0;
            $menu_display_limit = get_option( 'mptrs_menu_display_limit' );
            $menu_display_limit = !empty($menu_display_limit) ? $menu_display_limit : 20;

            if( is_array( $existing_menus ) ){
                $total_menus = count( $existing_menus );
                foreach ($existing_menus as $menu) {
                    if (!empty($menu['menuCategory'])) {
                        $category = $menu['menuCategory'];
                        if (!isset($menu_category_count[$category])) {
                            $menu_category_count[$category] = 0;
                        }
                        $menu_category_count[$category]++;
                    }
                }
            }
            ?>
            <?php if (!empty($existing_menus)) { ?>
                <div class="mptrs-top-menu-tabs">
                    <h4><?php esc_html_e('Menu', 'tablely'); ?> (<?php echo esc_html(count($existing_menus)); ?>)</h4>
                    <div class="mptrs-category-container">
                        <input type="hidden" id="mptrs_location_autocomplete" value="<?php echo esc_attr( $enable_location_autocomplete );?>">
                        <?php if (!empty($categories)) { ?>
                            <div class="mptrs-category-item  mptrs-active" data-filter="all"><?php echo esc_html__('All ', 'tablely').'('.count($existing_menus).')'; ?></div>
                            <?php foreach ($categories as $key => $category) {
                                $category_count = isset( $menu_category_count[$key]) ? $menu_category_count[$key] : 0;
                                if( $category_count > 0){
                                    $category_count_str = '( '.$category_count.' )';
                                    ?>
                                    <div class="mptrs-category-item" data-filter="<?php echo esc_attr($key); ?>"><?php echo esc_html($category); ?><?php echo esc_attr( $category_count_str ) ?></div>

                                    <?php
                                }
                                ?>
                            <?php } ?>
                        <?php } ?>
                        <div class="mptrs-more-button">...</div>

                    </div>
                    <div class="mptrs_hidden_items" style="display: none;"></div>
                </div>
                <div class="mptrs-food-menu-container">
                        <?php
                        $fallbackImgUrl = get_site_url() . '/wp-content/uploads/2025/02/fallbackimage.webp';
                        foreach ($existing_menus as $key => $existing_menu) {
                            $img = empty($existing_menu['menuImgUrl']) ? $fallbackImgUrl : $existing_menu['menuImgUrl'];
                            $price = $existing_menu['menuPrice'];
                            if (!empty($existing_edited_price) && isset($existing_edited_price[$key])) {
                                $price = $existing_edited_price[$key];
                            }
                            ?>
                            <div class="mptrs-food-menu" id="mptrs_foodMenuContent-<?php echo esc_attr($key); ?>" data-category="<?php echo esc_attr($existing_menu['menuCategory']); ?>" style="display: none">
                                <div class="mptrs-menu-item-info">
                                    <div class="mptrs_menuInfoHolder">
                                        <div class="mptrs_topMenuInFo">
                                            <div class="mptrs_menuName"><?php echo esc_html($existing_menu['menuName']); ?></div>
                                        </div>
                                        <div class="mptrs_BottomMenuInFo">
                                            <div class="mptrs_menuPrice"><?php echo wc_price($price); ?></div>
                                        </div>
                                        <div class="mptrs_menuDescription"><?php echo esc_html($existing_menu['menuDescription']); ?></div>
                                    </div>
                                </div>
                                <div class="mptrs-menu-item-thumbnail">
                                    <div class="mptrs_menuImageHolder">
                                        <img class="mptrs_menuImage" src="<?php echo esc_attr($img); ?>" >
                                        <div class="mptrs_menuPersion"><i class='fas fa-user-alt' style='font-size:10px'></i><span class="mptrs_numberOfPerson"><?php echo esc_html($existing_menu['numPersons']); ?></span></div>
                                    </div>
                                    <div class="mptrs_addedMenuordered"
                                         data-menuCategory="<?php echo esc_attr($existing_menu['menuCategory']); ?>"
                                         data-menuName="<?php echo esc_attr($existing_menu['menuName']); ?>"
                                         data-menuImgUrl="<?php echo esc_attr($img); ?>"
                                         data-menuPrice="<?php echo esc_attr(wc_price($price)); ?>"
                                         data-numOfPerson="<?php echo esc_attr($existing_menu['numPersons']); ?>">
                                        <?php
                                        if( in_array( $key, $existing_menu_cookie ) ){?>

                                            <button class="mptrs_addBtn" id="mptrs_addBtn-<?php echo esc_attr( $key );?>" style="display: none;"><i class="fas fa-plus"></i></button>

                                            <div class="mptrs_addedQuantityControls" id="mptrs_addedQuantityControls-<?php echo esc_attr( $key );?>">
                                                <span class="mptrs_decrease">
                                                    <?php if( $item_keys[ $key ] > 1 ){?>
                                                        -
                                                    <?php }else{?>
                                                        <i class="fas fa-trash" style="font-size: 16px"></i>
                                                    <?php }?>
                                                </span>
                                                <span class="mptrs_quantity" id="mptrs_menuAddedQuantity-<?php echo esc_attr( $key );?>"><?php echo esc_attr( $item_keys[ $key ] );?></span>
                                                <span class="mptrs_increase">+</span>
                                            </div>
                                        <?php }else{?>
                                            <button class="mptrs_addBtn" id="mptrs_addBtn-<?php echo esc_attr($key); ?>"><i class="fas fa-plus"></i></button>
                                        <?php }?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>

                <?php if( $total_menus > $menu_display_limit ){?>
                    <div class="mptrs_loadMoreMenuBtnHolder" id="mptrs_loadMoreMenuBtnHolder" style="display: none">
                        <div class="mptrs_loadMoreMenuBtn">Load More Menu</div>
                    </div>
                <?php } } ?>
                <input type="hidden" id="mptrs_getPost" value="<?php echo esc_attr( $post_id )?>">
                <input type="hidden" id="mptrs_menu_display_limit" value="<?php echo esc_attr( $menu_display_limit )?>">
            <?php
        }
    }
    new MPTRS_Template();
}
