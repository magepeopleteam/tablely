<?php
	/*
   * @Author 		engr.sumonazma@gmail.com
   * Copyright: 	mage-people.com
   */
	if ( ! defined( 'ABSPATH' ) ) {
		die;
	} // Cannot access pages directly.
	if ( ! class_exists( 'MPTRS_Shortcodes' ) ) {
		class MPTRS_Shortcodes {
			public function __construct() {
                add_shortcode( 'mptrs_display_food_menu', array( $this, 'display_restaurant_menu_content_shortcode' ) );
                add_shortcode( 'mptrs_reserve_table', array( $this, 'display_seat_mapping_shortcode' ) );
                add_shortcode( 'mptrs_food_menu_list', array( $this, 'display_food_menu_list' ) );
			}

            public static function mptrs_food_menu_data( $attrs )
            {
                $post_id = isset( $attrs['post_id'] ) ? $attrs['post_id'] : '';
                $found_category = isset( $attrs['category'] ) ? $attrs['category'] : '';
                $existing_menus = $food_menus = $get_category = [];

                if( $post_id ){
                    $categories = get_option('mptrs_categories');
                    $existing_menu_by_id = get_post_meta($post_id, '_mptrs_food_menu_items', true);
                    $existing_edited_price = get_post_meta($post_id, '_mptrs_food_menu_edited_prices', true);

                    $all_food_menus = get_option('_mptrs_food_menu', true);
                    if (is_array($existing_menu_by_id) && !empty($existing_menu_by_id)) {
                        foreach ($existing_menu_by_id as $item) {
                            if (isset($all_food_menus[$item])) {
                                $existing_menus[$item] = $all_food_menus[$item];
                            }
                        }
                    }

                    $fallbackImgUrl = get_site_url() . '/wp-content/uploads/2025/02/fallbackimage.webp';

                    if( !empty( $existing_menus ) ){
                        foreach ( $existing_menus as $key => $existing_menu ) {
                            if ( $found_category && $found_category === $existing_menu['menuCategory']) {
                                $get_category[] = $found_category;
                                $found_category = true; // Mark category found
                                $img = empty($existing_menu['menuImgUrl']) ? $fallbackImgUrl : $existing_menu['menuImgUrl'];
                                $price = $existing_menu['menuPrice'];

                                if (!empty( $existing_edited_price) && isset($existing_edited_price[$key] ) ) {
                                    $price = $existing_edited_price[$key];
                                }

                                $food_menus[$key] = [
                                    'menu_name'        => $existing_menu['menuName'],
                                    'menu_category'    => $existing_menu['menuCategory'],
                                    'menu_description' => $existing_menu['menuDescription'],
                                    'menu_image'       => $img,
                                    'menu_price'       => $price,
                                ];
                            }
                        }

                        if (!$found_category) {
                            foreach ($existing_menus as $key => $existing_menu) {
                                $img = empty($existing_menu['menuImgUrl']) ? $fallbackImgUrl : $existing_menu['menuImgUrl'];
                                $price = $existing_menu['menuPrice'];

                                if (!empty($existing_menu['menuCategory'])) {
                                    $get_category[] = $existing_menu['menuCategory'];
                                }

                                if (!empty($existing_edited_price) && isset($existing_edited_price[$key])) {
                                    $price = $existing_edited_price[$key];
                                }

                                $food_menus[$key] = [
                                    'menu_name'        => $existing_menu['menuName'],
                                    'menu_category'    => $existing_menu['menuCategory'],
                                    'menu_description' => $existing_menu['menuDescription'],
                                    'menu_image'       => $img,
                                    'menu_price'       => $price,
                                ];
                            }
                        }
                    }


                }

                $get_category = array_unique($get_category);

                return  array(
                        'food_menus' => $food_menus,
                        'category' => $get_category,
                );
            }
            public function display_food_menu_list( $attrs ){

                $attrs = shortcode_atts( [
                    'category'          => '',
                    'post_id'           => 61,
                    'per_page'          => 20,
                    'column'            => 3,
                    'style'             => 'grid',
                    'category_filter'   => 'no',
                ], $attrs, 'mptrs_food_list' );

                $food_menu_data = self::mptrs_food_menu_data( $attrs );
                $food_menus = $food_menu_data['food_menus'];
                $categories = $food_menu_data['category'];

                $default_columns = isset( $attrs['columns'] ) ? $attrs['columns'] : 3;
                $style = isset( $attrs['style'] ) ? $attrs['style'] : '';
                $category_filter = isset( $attrs['category_filter'] ) ? $attrs['category_filter'] : 'no';
                if( $style === 'list' ){
                    $list_grid_class = 'list-view';
                }else{
                    $list_grid_class = 'grid-view';
                }

                ob_start();
                ?>

                <div class="mptrs_food_menu_wrapper" data-columns="<?php echo esc_attr($default_columns); ?>">

                    <div class="mptrs_food_menu_toolbar" style="display: none">
                        <button class="mptrs_food_menu_btn_grid active">Grid View</button>
                        <button class="mptrs_food_menu_btn_list">List View</button>
                    </div>

                    <?php if( $category_filter === 'yes' ){?>
                        <div class="mptrs_food_category_holder">
                            <div class="mptrs_food_category active" data-category="all">All</div>
                            <?php  if( !empty( $categories ) ){
                                foreach ( $categories as $key => $food_category) : ?>

                                <div class="mptrs_food_category" data-category="<?php echo esc_html( $food_category )?>"><?php echo  esc_html( ucfirst( $food_category ) );?></div>
                            <?php
                            endforeach;
                            }
                            ?>
                        </div>
                    <?php }?>

                    <div class="mptrs_food_menu_container <?php echo esc_html( $list_grid_class );?>">
                        <?php
                        if( !empty( $food_menus ) ){
                            foreach ($food_menus as $key => $menu) : ?>
                                <div class="mptrs_food_menu_card" data-category-filter="<?php echo esc_html($menu['menu_category']); ?>">
                                    <div class="mptrs_food_menu_img_wrap">
                                        <img src="<?php echo esc_url($menu['menu_image']); ?>" alt="<?php echo esc_attr($menu['menu_name']); ?>">
                                    </div>
                                    <div class="mptrs_food_menu_content">
                                        <h3 class="mptrs_food_menu_name"><?php echo esc_html($menu['menu_name']); ?></h3>
                                        <p class="mptrs_food_menu_cat"><?php echo esc_html($menu['menu_category']); ?></p>
                                        <p class="mptrs_food_menu_desc"><?php echo esc_html($menu['menu_description']); ?></p>
                                        <span class="mptrs_food_menu_price">$<?php echo esc_html($menu['menu_price']); ?></span>
                                    </div>
                                </div>
                            <?php endforeach;
                        }
                        ?>
                    </div>
                </div>

            <?php

            return ob_get_clean();
            }
            public function display_seat_mapping_shortcode( $atts ){
                $post_id = isset( $atts['post_id'] ) ? $atts['post_id'] : '';
                $table_reserve = '';
                if( $post_id ){
                    $not_available = [];
//                    $seat_map = MPTRS_Details_Layout::display_seat_mapping( $post_id, $not_available );
                    $table_reserve = MPTRS_Details_Layout::reserve_table( $post_id, $atts );
                }

                return $table_reserve;
            }
            public function display_restaurant_menu_content_shortcode_old( $atts ) {
                $post_id = isset( $atts['post_id'] ) ? $atts['post_id'] : '';
                if( $post_id ){
                    $post = get_post($post_id);
                    if ($post) {
                        $content = apply_filters('the_content', $post->post_content);
                    } else {
                        $content = '';
                    }

                    $categories = get_option('mptrs_categories');
                    $existing_menu_by_id = get_post_meta($post_id, '_mptrs_food_menu_items', true);
                    $existing_edited_price = get_post_meta($post_id, '_mptrs_food_menu_edited_prices', true);
                    $existing_menus = [];
                    $all_food_menus = get_option('_mptrs_food_menu', true);
                    if (is_array($existing_menu_by_id) && !empty($existing_menu_by_id)) {
                        foreach ($existing_menu_by_id as $item) {
                            if (isset($all_food_menus[$item])) {
                                $existing_menus[$item] = $all_food_menus[$item];
                            }
                        }
                    }

                    ob_start();
                    ?>
                    <div class="mptrs_contentDataHolder">
                        <div class="mptrs_restaurantLeftSide">

                            <!--<div class="mptrs_restaurantDesHolder">
                                <div class="mptrs_restaurantDes">
                                    <?php /*echo esc_attr( $content ); */?>
                                </div>
                                <button class="mptrs_toggleBtn"><?php /*esc_html_e('See More', 'tablely'); */?></button>
                            </div>-->

                            <?php if (!empty($existing_menus)) { ?>
                                <div class="mptrs_FoodMenuHolder">
                                    <h3 class="mptrs_FoodMenuHolderTitle"><?php esc_html_e('Menu', 'tablely'); ?> (<?php echo esc_html(count($existing_menus)); ?>)</h3>
                                    <div class="mptrs_category_container">
                                        <?php if (!empty($categories)) { ?>
                                            <div class="mptrs_category_item mptrs_active" data-filter="all"><?php esc_html_e('All', 'tablely'); ?></div>
                                            <?php foreach ($categories as $key => $category) { ?>
                                                <div class="mptrs_category_item" data-filter="<?php echo esc_attr($key); ?>"><?php echo esc_html($category); ?></div>
                                            <?php } ?>
                                        <?php } ?>
                                        <div class="mptrs_more_button">...</div>
                                    </div>
                                    <div class="mptrs_foodMenuContaine">
                                        <?php
                                        $fallbackImgUrl = get_site_url() . '/wp-content/uploads/2025/02/fallbackimage.webp';
                                        foreach ($existing_menus as $key => $existing_menu) {
                                            $img = empty($existing_menu['menuImgUrl']) ? $fallbackImgUrl : $existing_menu['menuImgUrl'];
                                            $price = $existing_menu['menuPrice'];
                                            if (!empty($existing_edited_price) && isset($existing_edited_price[$key])) {
                                                $price = $existing_edited_price[$key];
                                            }
                                            ?>
                                            <div class="mptrs_foodMenuContent" id="mptrs_foodMenuContent-<?php echo esc_attr($key); ?>" data-category="<?php echo esc_attr($existing_menu['menuCategory']); ?>">
                                                <div class="mptrs_menuImageHolder">
                                                    <img class="mptrs_menuImage" src="<?php echo esc_attr($img); ?>" >
                                                </div>
                                                <div class="mptrs_menuInfoHolder">
                                                    <div class="mptrs_topMenuInFo">
                                                        <div class="mptrs_menuName"><?php echo esc_html($existing_menu['menuName']); ?></div>
                                                    </div>
                                                    <div class="mptrs_BottomMenuInFo">
                                                        <div class="mptrs_menuPrice"><?php echo wp_kses_post( wc_price( $price ) ); ?></div>
                                                        <div class="mptrs_menuPersion"><i class='fas fa-user-alt' style='font-size:10px'></i><span class="mptrs_numberOfPerson"><?php echo esc_html($existing_menu['numPersons']); ?></span></div>
                                                    </div>
                                                </div>
                                                <div class="mptrs_addedMenuordered" data-menuCategory="<?php echo esc_attr($existing_menu['menuCategory']); ?>" data-menuName="<?php echo esc_attr($existing_menu['menuName']); ?>"
                                                     data-menuImgUrl="<?php echo esc_attr($img); ?>" data-menuPrice="<?php echo esc_attr(wc_price($price)); ?>" data-numOfPerson="<?php echo esc_attr($existing_menu['numPersons']); ?>">
                                                    <button class="mptrs_addBtn" id="mptrs_addBtn-<?php echo esc_attr($key); ?>">+</button>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="mptrs_restaurantRightSide">

                            <div class="mptrs_orderedFoodMenuInfoHolder" id="mptrs_orderedFoodMenuInfoHolder" style="display: none">
                                <div class="mptrs_orderedMenuHolder">
                                    <span class=""><?php esc_html_e( 'Your Orders', 'tablely' ); ?></span>
                                    <span class="mptrs_clearOrder"><?php esc_html_e( 'Clear Order', 'tablely' ); ?></span>
                                </div>
                                <div class="mptrs_orderedFoodMenuHolder" id="mptrs_orderedFoodMenuHolder"></div>
                                <div class="mptrs_totalPriceHolder" id="mptrs_totalPriceHolder">
                                    <span class="mptrs_totalPricetext"><?php esc_html_e( 'Total', 'tablely' ); ?></span>
                                    <input class="mptrs_totalPrice" id="mptrs_totalPrice" name="mptrs_totalPrice" value="" readonly placeholder="total price" disabled>
                                </div>
                                <div class="mptrs_orderTypeDatesDisplay">
                                    <span class="mptrs_orderTypeDates" id="mptrs_orderTypeDates"></span>
                                    <span class="mptrs_orderTypeDatesChange" id="mptrs_orderTypeDatesChange"><?php esc_html_e( 'Change', 'tablely' )?></span>
                                </div>
                                <div class="mptrs_dineInOrderPlaceBtn" id="mptrs_dineInOrderPlaceBtn"><?php esc_html_e( 'Process Checkout', 'tablely' )?></div>
                            </div>

                            <div class="mptrs_orderedFoodMenuInfoHolder" id="mptrs_foodMenuAddedCart">
                                <i class="fas fa-shopping-bag"></i> Beg
                            </div>


                        </div>
                    </div>
                    <input type="hidden" id="mptrs_getPost" value="<?php echo esc_attr( $post_id )?>">
                    <?php
                    return ob_get_clean();
                }else{
                    return 'No Restaurant Found';
                }
            }

            public function display_restaurant_menu_content_shortcode( $atts ) {
                $post_id = isset( $atts['post_id'] ) ? $atts['post_id'] : '';

                ob_start();
                ?>
                <main class="mptrs-default-template">

                    <div class="mptrs-content">
                        <div class="mptrs-content-left">
                            <?php do_action('mptrs_template_menus', $post_id ); ?>
                        </div>
                        <div class="mptrs-content-right">
                            <?php do_action('mptrs_template_basket'); ?>
                        </div>
                    </div>
                </main>

                <?php
                return ob_get_clean();
            }

		}

		new MPTRS_Shortcodes();
	}