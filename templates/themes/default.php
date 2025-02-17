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
    </div>
