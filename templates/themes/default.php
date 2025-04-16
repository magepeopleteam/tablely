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
    <?php do_action('mptrs_template_header'); ?>
    <div class="mptrs-header">
        <?php do_action('mptrs_template_logo'); ?>
        <div class="mptrs-restaurant-info">
            <?php do_action('mptrs_restaurant_info'); ?>
        </div>
    </div>
    <div class="mptrs-content">
        <div class="mptrs_restaurantLeftSide">
            <?php do_action('mptrs_template_menus'); ?>
        </div>
        <div class="mptrs_restaurantRightSide">
            <?php do_action('mptrs_template_basket'); ?>
        </div>
    </div>
</main>
