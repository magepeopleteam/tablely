<?php

	/*
   * @Author 		engr.sumonazma@gmail.com
   * Copyright: 	mage-people.com
   */
	if ( ! defined( 'ABSPATH' ) ) {
		die;
	} // Cannot access pages directly.

	$post_id = get_the_id();
    $thumbnail_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
    $existing_menus = get_option( '_mptrs_food_menu' );
    $categories = get_option( 'mptrs_categories' );
    ?>
    <div class="mptrs_postHolder">
        <div id="seatPopup" class="popup">
            <div class="popup-content">
                <span class="close-btn">&times;</span>
                <div class="mptrs_seatMappedHolder">
                    <div id="mptrs_foodDeliveryOptions" class="mptrs_foodDeliveryOptions">
                        <div class="mptrs_orderOptionsTab">
                            <div class="mptrs_orderOptionTab mptrs_orderTabActive" id="mptrs_dineInTab">Dine-In</div>
                            <div class="mptrs_orderOptionTab" id="mptrs_deliveryTab">Delivery</div>
                            <div class="mptrs_orderOptionTab" id="mptrs_takeawayTab">Takeaway</div>
                        </div>
                    </div>
                </div>

                <div class="mptrs_foodOrderContentholder">
                    <div class="mptrs_seatMappedHolder" id="mptrs_dineInTabHolder" style="display: block">
                        <div class="mptrs_seatMapDisplay" id="mptrs_seatMapDisplay"></div>
                    </div>
                    <div class="mptrs_seatMappedHolder" id="mptrs_deliveryTabHolder" style="display: none">
                        <div class="">
                            <input type="text" class="mptrs_input-field" id="location" placeholder="Location">
                            <input type="text" class="mptrs_input-field" id="street-address" placeholder="Enter your street address">
                            <input type="text" class="mptrs_input-field" id="postal-code" placeholder="Postal Code">
                        </div>
                    </div>
                    <div class="mptrs_seatMappedHolder" id="mptrs_takeawayTabHolder" style="display: none">
                        <div class="">Takeaway</div>
                    </div>
                </div>

                <div class="mptrs_OrderPlaceBtn" id="mptrs_OrderPlaceBtn-<?php echo esc_attr( $post_id )?>">Order</div>
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
                    <span class="mptrs_restaurantOpeningText">Opening Time</span>
                    <span class="mptrs_restaurantOpeningTime">11:00 AM - 11:00 PM</span>
                </div>
                <div class="mptrs_restaurantOpeninglocation">5th floor, Concord MK Heritage, Dhaka, Dhanmondi Dhaka</div>
            </div>
        </div>


        <div class="mptrs_restaurantLeftSide">
            <div class="mptrs_restaurantDesHolder">
                <div class="mptrs_restaurantDes">
                    <?php the_content(); ?>
                </div>
                <button class="mptrs_toggleBtn">See More</button>
            </div>

            <?php

            if( is_array( $existing_menus ) && count( $existing_menus ) > 0 ){?>

            <div class="mptrs_FoodMenuHolder">
                <h3 class="mptrs_FoodMenuHolderTitle">Menu(<?php echo esc_attr( count( $existing_menus ) ) ?>)</h3>

                <div class="mptrs_category_container">
                    <?php
                    if( is_array( $categories ) && count( $categories ) > 0 ){ ?>
                        <div class="mptrs_category_item mptrs_active" data-filter="<?php echo __( 'all', 'tablely' )?>"><?php echo __( 'All', 'tablely' )?></div>
                       <?php foreach( $categories as $key => $category ){ ?>
                            <div class="mptrs_category_item" data-filter="<?php echo esc_attr( $key )?>"><?php echo esc_attr( $category )?></div>
                     <?php }
                    }
                    ?>
                    <div class="mptrs_more_button">...</div>
                </div>
                <div class="mptrs_hidden_items"></div>
                <?php
                    $fallbackImgUrl = get_site_url().'/wp-content/uploads/2025/02/fallbackimage.webp';
                ?>
                <div class="mptrs_foodMenuContaine">

                    <?php
                        foreach ( $existing_menus as $key => $existing_menu ){
                            if( $existing_menu['menuImgUrl'] === '' ){
                                $img = $fallbackImgUrl;
                            }else{
                                $img = $existing_menu['menuImgUrl'];
                            }
                            ?>
                            <div class="mptrs_foodMenuContent" data-category ="<?php echo esc_attr( $existing_menu['menuCategory'] )?>">
                                <div class="mptrs_menuImageHolder">
                                    <img class="mptrs_menuImage" src="<?php echo esc_attr( $img ) ?>" >
                                </div>
                                <div class="mptrs_menuInfoHolder">
                                    <div class="mptrs_topMenuInFo">
                                        <div class="mptrs_menuName">
                                            <?php echo esc_attr( $existing_menu['menuName'] );?>
                                        </div>
                                    </div>
                                    <div class="mptrs_BottomMenuInFo">
                                        <div class="mptrs_menuPrice">$<?php echo esc_attr( $existing_menu['menuPrice'] );?></div>
                                        <div class="mptrs_menuPersion"><i class='fas fa-user-alt' style='font-size:14px'></i><span class="mptrs_numberOfPerson"><?php echo esc_attr( $existing_menu['numPersons'] );?></span></div>
                                    </div>
                                </div>
                            </div>
                        <?php
                        }
                    ?>
                </div>
            </div>
            <?php }?>

            <div class="mptrs_openingHours">
                <h3 class="mptrs_openingTitle">Opening Hours</h3>
                <ul class="mptrs_openingList">
                    <li><span class="mptrs_day">Saturday</span> <span class="mptrs_time">11:00 AM - 11:00 PM</span></li>
                    <li><span class="mptrs_day">Sunday</span> <span class="mptrs_time">11:00 AM - 11:00 PM</span></li>
                    <li><span class="mptrs_day">Monday</span> <span class="mptrs_time">11:00 AM - 11:00 PM</span></li>
                    <li><span class="mptrs_day">Tuesday</span> <span class="mptrs_time">11:00 AM - 11:00 PM</span></li>
                    <li><span class="mptrs_day">Wednesday</span> <span class="mptrs_time">11:00 AM - 11:00 PM</span></li>
                    <li><span class="mptrs_day">Thursday</span> <span class="mptrs_time">11:00 AM - 11:00 PM</span></li>
                    <li><span class="mptrs_day">Friday</span> <span class="mptrs_time">11:00 AM - 11:00 PM</span></li>
                </ul>
            </div>
        </div>
        <div class="mptrs_restaurantRightSide">

                <div class="mptrs_orderCardHolder" id="mptrs_orderCardHolder">
                    <h2 class="mptrs_title">Make a reservation</h2>

                    <!-- Date Selection -->
                    <div class="mptrs_formGroup">
                        <label for="mptrs_date">Check in</label>
                        <div class="mptrs_input_wrapper">
                            <div class="mptrs_DatePickerContainer" style="display: block">
                                <input type="text" id="mptrs_date" placeholder="Select a Date">
                                <span class="mptrs_calendarIcon">&#128197;</span>
                            </div>
                        </div>
                    </div>

                    <!-- Party Size -->
                    <div class="mptrs_formGroup">
                        <label for="mptrs_party_size">Party size</label>
                        <div class="mptrs_input_wrapper">
                            <select id="mptrs_party_size" class="mptrs_input">
                                <option value="1">1</option>
                                <option value="2" selected>2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6+</option>
                            </select>
                        </div>
                    </div>
                    <button id="mptrs_search" class="mptrs_button">Find a time</button>
                    <div class="mptrs_time_container">
                        <!-- Time slots will be added dynamically -->
                    </div>
            </div>

            <div class="mptrs_rightSidebar">
                <div class="mptrs_rightSidebarItem">
                    <h4>Dress Code</h4>
                    <p>Casual, Business Casual, Semi-Formal, Western, Formal</p>
                </div>

                <div class="mptrs_rightSidebarItem">
                    <h4>Noise</h4>
                    <p>Silence, Party, Normal</p>
                </div>

                <div class="mptrs_rightSidebarItem">
                    <h4>Dining Style</h4>
                    <p>Casual Dining, Family</p>
                </div>

                <div class="mptrs_rightSidebarItem">
                    <h4>Cuisine</h4>
                    <p>International, Local</p>
                </div>

                <div class="mptrs_rightSidebarItem">
                    <h4>Address</h4>
                    <p class="mptrs_address">
                        5th floor, Concord MK Heritage, Dhaka, Dhanmondi Dhaka
                    </p>
                    <a href="#" class="mptrs_openMap">Open in Map</a>
                </div>

                <div class="mptrs_rightSidebarItem">
                    <a href="#" class="mptrs_socialMedia">Find on Social Media</a>
                </div>
            </div>

        </div>

        <div class="mptrs_timePickerContainer" style="display: none">
            <select id="mptrs-timepicker">
                <option value="">Select Time</option>
            </select>
        </div>

        <div class="mptrs_selectedSeatInfoHolder" id="mptrs_selectedSeatInfoHolder" style="display: none">
            <table class="mptrs_table">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Seat</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="mptrs_selectedSeatInfo"></tbody>
                <tfoot>
                <tr>
                    <td colspan="2"><strong>Total Price</strong></td>
                    <td id="mptrs_totalPrice"><strong>0</strong></td>
                    <td></td>
                </tr>
                </tfoot>
            </table>
        </div>
        <button class="mptrs_orderBtn" id="mptrs_orderBtn-<?php echo esc_attr( $post_id )?>"><?php esc_attr_e( 'Order', 'tablely')?></button>
        <input type="hidden" id="mptrs_getPost" value="<?php echo esc_attr( $post_id )?>">
    </div>
