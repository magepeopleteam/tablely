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
				add_action('save_post', array($this, 'save_settings'));
			}
			public function general_settings( $post_id ) {
				$image_id = get_post_meta($post_id, 'mptrs_restaurant_logo', true);
				$restaurant_address = get_post_meta( $post_id, 'mptrs_restaurant_address', true );
				$image_url = $image_id ? wp_get_attachment_image_src($image_id, 'full')[0] : '';

                $selected_seat_map_id = get_post_meta( $post_id, 'mptrs_selected_seat_map', true );
				?>
                <div class="tabsItem" data-tabs="#mptrs_general_info">
					<header>
                        <h2><?php esc_html_e('General Settings', 'tablely'); ?></h2>
                        <span><?php esc_html_e('In this section you will get basic settings.', 'tablely'); ?></span>
                    </header>
                    <section class="section">
                        <h2><?php esc_html_e('Restaurant Address', 'tablely'); ?></h2>
                        <span><?php esc_html_e('Restaurant Address.', 'tablely'); ?></span>
                    </section>
                    <section>
                        <label class="label">
                            <p><?php esc_html_e('Add Edit restaurant address', 'tablely'); ?></p>

                        </label>
                        <input name="mptrs_restaurant_address" class="mptrs_restaurant_address" value="<?php echo esc_attr( $restaurant_address );?>">
                    </section>

                    <section class="section">
                        <h2><?php esc_html_e('Restaurant Branding', 'tablely'); ?></h2>
                        <span><?php esc_html_e('Restaurant Branding.', 'tablely'); ?></span>
                    </section>
                    <section>
                        <label class="label">
                            <div>
                                <p><?php esc_html_e('Add restaurant logo', 'tablely'); ?></p>
								<span><?php esc_html_e('Upload logo for your rastaurant. Best size 156px x 156px.', 'tablely'); ?></span>
                            </div>
							<div class="mptrs-logo">
								<div class="mptrs-logo-wrapper">
									<?php if ($image_url): ?>
										<img  src="<?php echo esc_url($image_url); ?>"/>
									<?php endif; ?>
								</div>
								<div class="mptrs-logo-upload-wrapper">
									<input type="hidden" name="mptrs_restaurant_logo" id="mptrs-restaurant-logo" value="<?php echo esc_attr($image_id); ?>" />
									<span class="mptrs-logo-upload"><i class="fas fa-upload"></i>Upload</span>
									<span class="mptrs-logo-remove"><i class="fa fa-times"></i>Remove</span>
								</div>
							</div>
                        </label>
                    </section>
					
					<!-- shortcode -->
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
                    <?php if( $selected_seat_map_id ){?>
					<section>
                        <label class="label">
                            <div>
                                <p><?php esc_html_e('Table Reservation ShortCode', 'tablely'); ?></p>
								<span><?php esc_html_e('Put this shortcode in any page or post to show Table Reservation.', 'tablely'); ?></span>
                            </div>
                            <div>
								<code>
									<?php echo esc_html('[mptrs_reserve_table seat_mapping="yes/no" post_id="'.$selected_seat_map_id.'"]') ?>
								</code>
							</div>
                        </label>
                    </section>
                    <?php }?>
                </div>
				<?php
			}

			public function save_settings($post_id) {
				if (!isset($_POST['mptrs_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['mptrs_nonce'])), 'mptrs_nonce') && defined('DOING_AUTOSAVE') && DOING_AUTOSAVE && !current_user_can('edit_post', $post_id)) {
					return;
				}
				if (get_post_type($post_id) == MPTRS_Function::get_cpt()) {
					$image_logo = isset($_POST['mptrs_restaurant_logo']) ? sanitize_text_field(wp_unslash($_POST['mptrs_restaurant_logo'])) : '';
					update_post_meta($post_id, 'mptrs_restaurant_logo', $image_logo);

					$restaurant_address = isset($_POST['mptrs_restaurant_address']) ? sanitize_text_field(wp_unslash($_POST['mptrs_restaurant_address'])) : '';
					update_post_meta($post_id, 'mptrs_restaurant_address', $restaurant_address );
				}
			}
		}
		new MPTRS_General_Settings();
	}