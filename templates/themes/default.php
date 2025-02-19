<?php

	/*
   * @Author 		engr.sumonazma@gmail.com
   * Copyright: 	mage-people.com
   */
	if ( ! defined( 'ABSPATH' ) ) {
		die;
	} // Cannot access pages directly.

	$post_id = get_the_id();
    ?>
    <div class="mptrs_postHolder">
        <div class="mptrs_postTitleHolder">
            <h1 class="mptrs_postTitleText"><?php echo get_the_title()?></h1>
        </div>
        <?php
            echo MPTRS_Details_Layout::display_seat_mapping();
        ?>
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
                <tbody id="mptrs_selectedSeatInfo">

                </tbody>
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
