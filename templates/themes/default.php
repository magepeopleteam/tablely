<?php

	/*
   * @Author 		engr.sumonazma@gmail.com
   * Copyright: 	mage-people.com
   */
	if ( ! defined( 'ABSPATH' ) ) {
		die;
	}

	$post_id = get_the_id();
    $thumbnail_url = get_the_post_thumbnail_url(get_the_ID(), 'full');

    function display_restaurant_menu_content_shortcode( $post_id ) {

        $categories = get_option('mptrs_categories');
        $existing_menu_by_id = get_post_meta( $post_id, '_mptrs_food_menu_items', true );
        $existing_edited_price = get_post_meta( $post_id, '_mptrs_food_menu_edited_prices', true );

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
                <div class="mptrs_restaurantDesHolder">
                    <div class="mptrs_restaurantDes">
                        <?php the_content(); ?>
                    </div>
                    <button class="mptrs_toggleBtn"><?php esc_html_e('See More', 'tablely'); ?></button>
                </div>

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
                                            <div class="mptrs_menuPrice"><?php echo wc_price($price); ?></div>
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
                        <span class="mptrs_clearOrder">Clear Order</span>
                    </div>
                    <div class="mptrs_orderedFoodMenuHolder" id="mptrs_orderedFoodMenuHolder"></div>
                    <div class="mptrs_totalPriceHolder" id="mptrs_totalPriceHolder">

                        <span class="mptrs_totalPricetext"><?php esc_html_e( 'Total', 'tablely' ); ?></span>
                        <!--                        <span class="mptrs_sitePriceSymble" id="mptrs_sitePriceSymble"></span>-->
                        <input class="mptrs_totalPrice" id="mptrs_totalPrice" name="mptrs_totalPrice" value="" readonly placeholder="total price" disabled>

                    </div>
                    <div class="mptrs_orderTypeDatesDisplay">
                        <span class="mptrs_orderTypeDates" id="mptrs_orderTypeDates"></span>
                        <span class="mptrs_orderTypeDatesChange" id="mptrs_orderTypeDatesChange">change</span>
                    </div>
                    <div class="mptrs_dineInOrderPlaceBtn" id="mptrs_dineInOrderPlaceBtn"><?php esc_html_e( 'Process Checkout', 'tablely' )?></div>
                </div>

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

            </div>
        </div>
        <input type="hidden" id="mptrs_getPost" value="<?php echo esc_attr( $post_id )?>">
        <?php

        return ob_get_clean();
    }

    ?>
    <div class="mptrs_postHolder">
    <div id="seatPopup" class="popup">
    <div class="popup-content">
        <span class="close-btn">&times;</span>
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

    <?php if ( has_post_thumbnail() ) : ?>
    <div class="mptrs_featureImageHolder">
        <img class="mptrs_featureImage mptrs_shadow" alt="<?php esc_attr( get_the_title() );?>" src=" <?php  echo esc_attr( $thumbnail_url );?>">
    </div>
<?php endif; ?>

    <div class="mptrs_restaurantInfoHolder">
    <div class="mptrs_restaurantInfoImgHolder">
        <img class="mptrs_restaurantInfoImg" alt="<?php esc_attr( get_the_title() );?>" src="<?php  echo esc_attr( $thumbnail_url );?>">
    </div>
    <div class="mptrs_restaurantInfoRightHolder">
        <div class="mptrs_restaurantName"><?php echo esc_attr( get_the_title() );?></div>
        <div class="mptrs_restaurantOpening">
            <span class="mptrs_restaurantOpeningText"><?php esc_html_e( 'Opening Time', 'tablely' ); ?></span>
            <span class="mptrs_restaurantOpeningTime"><?php esc_html_e( '11:00 AM - 11:00 PM', 'tablely' ); ?></span>
        </div>
        <div class="mptrs_restaurantOpeninglocation"><?php esc_html_e( '5th floor, Concord MK Heritage, Dhaka, Dhanmondi Dhaka', 'tablely' ); ?></div>
    </div>
</div>
    <?php
    echo display_restaurant_menu_content_shortcode($post_id );
    ?>

</div>
