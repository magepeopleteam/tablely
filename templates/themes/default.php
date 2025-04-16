<?php

	/*
   * @Author 		engr.sumonazma@gmail.com
   * Copyright: 	mage-people.com
   */
	if ( ! defined( 'ABSPATH' ) ) {
		die;
	}
    ?>
<main class="mptrs-default-template">
    <?php do_action('mptrs_template_baner'); ?>
    <div class="mptrs-header">
        <?php do_action('mptrs_template_logo'); ?>
        <div class="mptrs-restaurant-info">
            <?php do_action('mptrs_restaurant_info'); ?>
        </div>
    </div>
    <div class="mptrs-content">
        <?php do_action('mptrs_template_content'); ?>
    </div>
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
</main>
