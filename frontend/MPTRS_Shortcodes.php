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
                add_shortcode( 'mptrs_restaurant_lists', array( $this, 'restaurant_lists_display' ) );
			}

            public static function mptrs_food_menu_data( $attrs ){
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

            public static function get_restaurant_lists( $attrs ) {
                $args = [
                    'post_type'      => 'mptrs_item',
                    'posts_per_page' => -1, // Load all, JS can control display
                    'post_status'    => 'publish',
                ];

                $query = new WP_Query($args);
                $restaurants = [];
                $cities = [];

                if ($query->have_posts()) {
                    while ($query->have_posts()) {
                        $query->the_post();
                        $post_id = get_the_ID();

                        $restaurants[] = [
                            'id'                => $post_id,
                            'title'             => get_the_title($post_id),
                            'permalink'         => get_permalink($post_id),
                            'thumbnail'         => get_the_post_thumbnail_url($post_id, 'medium'),
                            'address'           => get_post_meta($post_id, 'mptrs_restaurant_address', true),
                            'restaurant_type'   => get_post_meta($post_id, 'mptrs_restaurant_type', true),
                            'email'             => get_post_meta($post_id, 'mptrs_restaurant_email', true),
                            'city'              => get_post_meta($post_id, 'mptrs_restaurant_city', true),
                            'rating'            => get_post_meta($post_id, 'mptrs_restaurant_rating', true),
                            'description'       => wp_trim_words(get_the_excerpt(), 15, '...'),
                        ];
                        $cities [] = get_post_meta( $post_id, 'mptrs_restaurant_city', true );
                    }
                    wp_reset_postdata();
                }

                $cities = array_filter($cities);
                $cities = array_values($cities);

                return array(
                        'restaurants' => $restaurants,
                        'cities' => $cities,
                );
            }

            public function restaurant_lists_display( $attrs ){
                $attrs = shortcode_atts( [
                    'per_page'          => 2,
                    'columns'           => 4,
                    'style'             => 'grid',
                    'city_filter'       => 'yes',
                    'load_more_button'  => 'yes',
                ], $attrs, 'mptrs_restaurant_lists' );

                $restaurant_data = self::get_restaurant_lists( $attrs );
                $restaurant_lists = $restaurant_data['restaurants'];

                $total_restaurant = count( $restaurant_lists );
                $cities = $restaurant_data['cities'];


                $default_columns = isset( $attrs['columns'] ) ? $attrs['columns'] : 3;
                $style = isset( $attrs['style'] ) ? $attrs['style'] : 'grid';
                $city_filter = isset( $attrs['city_filter'] ) ? $attrs['city_filter'] : 'no';
                $per_page = isset( $attrs['per_page'] ) ? $attrs['per_page'] : 10;
                $load_more = isset( $attrs['load_more_button'] ) ? $attrs['load_more_button'] : 'no';
                if( $style === 'list' ){
                    $list_grid_class = 'list-view';
                    $description_show = 'block';
                }else{
                    $list_grid_class = 'grid-view';
                    $description_show = 'none';
                }
                ob_start();
                ?>

                <div class="mptrs_restaurant_wrapper" data-columns="<?php echo esc_attr($default_columns); ?>">
                    <input type="hidden" name="mptrs_display_restaurant_count" value="<?php echo esc_attr( $per_page );?>">

                    <div class="mptrs_restaurant_title">
                        <h3><?php esc_attr_e( 'Restaurants near you', 'tablely' )?></h3>
                        <div class="mptrs_restaurant_toolbar" style="display: block">
                            <button class="mptrs_restaurant_btn_grid active"><?php esc_attr_e( 'Grid View', 'tablely' );?></button>
                            <button class="mptrs_restaurant_btn_list"><?php esc_attr_e( 'List View', 'tablely' );?></button>
                        </div>
                    </div>



                    <?php if( $city_filter === 'yes' ){?>
                        <div class="mptrs_restaurant_city_holder">
                            <div class="mptrs_restaurant_city active" data-city="all"><?php esc_attr_e( 'All', 'tablely' );?></div>
                            <?php  if( !empty( $cities ) ){
                                foreach ( $cities as $key => $city) : ?>
                                    <div class="mptrs_restaurant_city" data-city="<?php echo esc_html( $city )?>"><?php echo  esc_html( ucfirst( $city ) );?></div>
                                <?php
                                endforeach;
                            }
                            ?>
                        </div>
                    <?php }?>

                    <div class="mptrs_restaurant_container <?php echo esc_html( $list_grid_class );?>">
                        <?php
                        if( !empty( $restaurant_lists ) ){
                            foreach ($restaurant_lists as $key => $restaurant) : ?>
                                <div class="mptrs_restaurant_card" data-city-filter="<?php echo esc_html($restaurant['city']); ?>">
                                    <div class="mptrs_restaurant_img_wrap">
                                        <img src="<?php echo esc_url($restaurant['thumbnail']); ?>" alt="<?php echo esc_attr($restaurant['title']); ?>">
                                    </div>
                                    <div class="mptrs_restaurant_content">
                                        <h3 class="mptrs_restaurant_name"><?php echo esc_html($restaurant['title']); ?></h3>
                                        <p class="mptrs_restaurant_desc" style="display: <?php echo esc_attr( $description_show );?>"><?php echo esc_html($restaurant['description']); ?></p>
                                        <p class="mptrs_restaurant_type"><?php echo esc_html($restaurant['restaurant_type']); ?></p>
                                    </div>
                                </div>
                            <?php endforeach;
                        }
                        ?>
                    </div>

                    <?php if( $load_more === 'yes' && $total_restaurant > $per_page ){ ?>
                        <div class="mptrs_restaurant_load_more_btn_holder">
                            <div class="mptrs_restaurant_load_more_btn"><?php esc_attr_e( 'Load More', 'tablely' )?></div>
                        </div>
                    <?php }?>


                </div>

            <?php

            return ob_get_clean();
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
                        <button class="mptrs_food_menu_btn_grid active"><?php esc_attr_e( 'Grid View', 'tablely' );?></button>
                        <button class="mptrs_food_menu_btn_list"><?php esc_attr_e( 'List View', 'tablely' );?></button>
                    </div>

                    <?php if( $category_filter === 'yes' ){?>
                        <div class="mptrs_food_category_holder">
                            <div class="mptrs_food_category active" data-category="all"><?php esc_attr_e( 'All', 'tablely' );?></div>
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