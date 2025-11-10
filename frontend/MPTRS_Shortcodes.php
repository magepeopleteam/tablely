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
                $post_id = isset( $attrs['restaurant_id'] ) ? $attrs['restaurant_id'] : '';
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
                    'restaurant_id'     => '',
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
                $post_id = isset( $atts['restaurant_id'] ) ? $atts['restaurant_id'] : '';
                $table_reserve = '';
                if( $post_id ){
                    $not_available = [];
//                    $seat_map = MPTRS_Details_Layout::display_seat_mapping( $post_id, $not_available );
                    $table_reserve = MPTRS_Details_Layout::reserve_table( $post_id, $atts );
                }

                return $table_reserve;
            }

            public function display_restaurant_menu_content_shortcode( $atts ) {
                $post_id = isset( $atts['restaurant_id'] ) ? $atts['restaurant_id'] : '';

                ob_start();
                ?>
                <main class="mptrs-default-template">

                    <div class="mptrs-content">
                        <div class="mptrs-content-left">
                            <?php do_action('mptrs_template_menus', $post_id ); ?>
                        </div>
                        <div class="mptrs-content-right">
                            <?php do_action('mptrs_template_basket', $post_id ); ?>
                        </div>
                    </div>
                </main>

                <?php
                return ob_get_clean();
            }

		}

		new MPTRS_Shortcodes();
	}