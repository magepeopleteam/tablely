<?php
	/**
	 * @author Sahahdat Hossain <raselsha@gmail.com>
	 * @license mage-people.com
	 * @var 1.0.0
	 */
	if (!defined('ABSPATH'))
		die;
	if (!class_exists('MPTRS_Seat_Mapping')) {
		class MPTRS_Seat_Mapping {
			public function __construct() {
				add_action('add_mptrs_settings_tab_content', [$this, 'seat_mapping_tab_content']);
			}

			public function seat_mapping_tab_content($post_id) {
				?>
                
                <div class="tabsItem" data-tabs="#mptrs_seat_mapping">
                    <header>
                        <h2><?php esc_html_e('Seat Mapping Settings', 'tablely'); ?></h2>
                        <span><?php esc_html_e('Seat Mapping Settings will be here.', 'tablely'); ?></span>
                    </header>
                    <section class="section">
                        <h2><?php esc_html_e('Seat Mapping Settings', 'tablely'); ?></h2>
                        <span><?php esc_html_e('Seat Mapping', 'tablely'); ?></span>
                    </section>

                    <section class="mptrs-seat-mapping-section">

                    </section>
                </div>
				<?php
			}
		}
		new MPTRS_Seat_Mapping();
	}