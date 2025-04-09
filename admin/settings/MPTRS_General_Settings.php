<?php
	/*
   * @Author 		engr.sumonazma@gmail.com
   * Copyright: 	mage-people.com
   */
	if (!defined('ABSPATH')) {
		die;
	} // Cannot access pages directly.
	if (!class_exists('MPTRS_General_Settings')) {
		class MPTRS_General_Settings {
			public function __construct() {
				add_action('add_mptrs_settings_tab_content', [$this, 'general_settings'], 10, 1);
			}
			public function general_settings($post_id) {
				?>
                <div class="tabsItem" data-tabs="#mptrs_general_info">
					<header>
                        <h2><?php esc_html_e('General Settings', 'tablely'); ?></h2>
                        <span><?php esc_html_e('In this section you will get basic settings.', 'tablely'); ?></span>
                    </header>
                    <section class="section">
                        <h2><?php esc_html_e('Shortcode Details', 'tablely'); ?></h2>
                        <span><?php esc_html_e('Get the restaurant and tarble reservation shortcode.', 'tablely'); ?></span>
                    </section>
                    <section>
                        <label class="label">
                            <div>
                                <p><?php esc_html_e('Food Menu Shortcode', 'tablely'); ?></p>
								<span><?php esc_html_e('Put this shortcode in any page or post to show Food Menu.', 'tablely'); ?></span>
                            </div>
                            <div>
								<code>
									<?php echo esc_html('[mptrs_display_food_menu post_id="'.get_the_ID().'"]') ?>
								</code>
							</div>
                        </label>
                    </section>
					<section>
                        <label class="label">
                            <div>
                                <p><?php esc_html_e('Table Reservation ShortCode', 'tablely'); ?></p>
								<span><?php esc_html_e('Put this shortcode in any page or post to show Table Reservation.', 'tablely'); ?></span>
                            </div>
                            <div>
								<code>
									<?php echo esc_html('[mptrs_reserve_table seat_mapping="yes/no" post_id="'.get_the_ID().'"]') ?>
								</code>
							</div>
                        </label>
                    </section>
                </div>
				<?php
			}
		}
		new MPTRS_General_Settings();
	}