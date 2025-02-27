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
    $existing_menus = get_post_meta($post_id, '_mptrs_food_menu', true);
    ?>
    <div class="mptrs_postHolder">
        <!--<div class="mptrs_postTitleHolder">
            <h1 class="mptrs_postTitleText"><?php /*echo esc_attr( get_the_title() );*/?></h1>
        </div>-->
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
    </div>
    <div class="mptrs_postHolder">

        <div class="mptrs_restaurantLeftSide">
            <div class="mptrs_restaurantDesHolder">
                <div class="mptrs_restaurantDes">
                    <?php the_content(); ?>
                </div>
                <button class="mptrs_toggleBtn">See More</button>
            </div>

            <?php  if( is_array( $existing_menus ) && count( $existing_menus ) > 0 ){ ?>
            <div class="mptrs_FoodMenuHolder">
                <h3 class="mptrs_FoodMenuHolderTitle">Menu(<?php echo esc_attr( count( $existing_menus ) ) ?>)</h3>
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
                            <div class="mptrs_foodMenuContent">
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



        <?php
            echo MPTRS_Details_Layout::display_seat_mapping();
        ?>

        <div class="mptrs_DatePickerContainer" style="display: none">
            <input type="text" id="mptrs-datepicker" placeholder="Select a Date">
            <span class="mptrs-calendar-icon">&#128197;</span>
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
           <!-- <div class="mptrs_totalPriceHolder">
                <span class="mptrs_totalPrice"></span>
            </div>-->
        </div>
        <button class="mptrs_orderBtn" id="mptrs_orderBtn-<?php echo esc_attr( $post_id )?>"><?php echo esc_attr_e( 'Order', 'tablely')?></button>
    </div>
