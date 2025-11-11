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
                add_action('wp_ajax_mptrs_add_taxonomy_term', array($this, 'mptrs_add_taxonomy_term_callback' ) );
			}

            public static function display_restaurant_cities( $post_id ){

                $get_taxonomy = 'mptrs_restaurant_city';
                $restaurant_cities = get_terms([
                    'taxonomy'   => $get_taxonomy,
                    'hide_empty' => false,
                ]);
                ob_start();
                ?>
                <option value=""><?php esc_attr_e( '-- Select City --', 'tablely' )?></option>
                <?php
                $selected_city = get_post_meta( $post_id, 'mptrs_restaurant_city', true );
                if ( ! empty( $restaurant_cities ) && ! is_wp_error( $restaurant_cities ) ) : ?>
                    <?php foreach ( $restaurant_cities as $restaurant_city ) :
                        $select_city = '';
                        if( $selected_city === $restaurant_city->name ){
                            $select_city = 'selected';
                        }
                        ?>
                        <option value="<?php echo esc_attr( $restaurant_city->name ); ?>" <?php echo esc_attr( $select_city );?>>
                            <?php echo esc_html( $restaurant_city->name ); ?>
                        </option>
                    <?php endforeach; ?>
                <?php else : ?>
                    <option value=""><?php esc_attr_e( 'No cities found', 'tablely' )?></option>
                <?php endif;

                return ob_get_clean();
            }
            function mptrs_add_taxonomy_term_callback() {
                $cities_html = '';
                if ( isset( $_POST['nonce']) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'mptrs_admin_nonce')) {

                    $post_id = isset($_POST['restaurant_id']) ? sanitize_text_field(wp_unslash($_POST['restaurant_id'])) : '';
                    $taxo_image_id = isset($_POST['mptrs_city_image_id'])
                        ? sanitize_text_field(wp_unslash($_POST['mptrs_city_image_id']))
                        : '';
                    $taxonomy = 'mptrs_restaurant_city';
                    $name     = isset($_POST['mptrs_taxonomy_name']) ? sanitize_text_field(wp_unslash($_POST['mptrs_taxonomy_name'])) : '';
                    $slug     = isset($_POST['mptrs_taxonomy_slug']) ? sanitize_title(wp_unslash($_POST['mptrs_taxonomy_slug'])) : '';
                    $desc     = isset($_POST['mptrs_taxonomy_desc']) ? sanitize_textarea_field(wp_unslash($_POST['mptrs_taxonomy_desc'])) : '';


                    if (empty($name)) {
                        wp_send_json_error('Taxonomy name is required!');
                    }
                    $term = wp_insert_term($name, $taxonomy, [
                        'slug' => $slug,
                        'description' => $desc,
                    ]);

                    if (is_wp_error( $term ) ) {
                        wp_send_json_error($term->get_error_message());
                    }
                    if (!empty($taxo_image_id) && !is_wp_error($term)) {
                        update_term_meta( $term['term_id'], 'mptrs_restaurant_city_image_id', $taxo_image_id );
                    }


                    $cities_html = self::display_restaurant_cities( $post_id );
                    ?>


                    <?php
                    if (is_wp_error($term)) {
                        wp_send_json_error($term->get_error_message());
                    }

                    wp_send_json_success( [
                        'message' => 'Taxonomy term added & assigned successfully!',
                        'cities_html'    => $cities_html,
                    ] );
                }else{
                    wp_send_json_success(
                        [
                            'message' => ' Security issue Taxonomy term added failed!',
                            'html'    => $cities_html,
                        ]
                    );
                }


            }

            public static function add_taxonomy_html_data( $type, $term_name, $post_id, $term_id = '' ){
                $image_id = '';
                ob_start();
                ?>
                <div class="mptrs_create_taxo_popup">
                    <div class="mptrs_create_taxo_popup_content">
                        <input type="hidden" name="mptrs_get_taxonomy_name" value="<?php echo esc_attr( $term_name );?>">
                        <input type="hidden" name="mptrs_get_taxonomy_image_id" value="<?php echo esc_attr( $image_id ); ?>"/>
                        <span class="mptrs_create_taxo_close">&times;</span>
                        <h3><?php esc_html_e('Add New '.$type.'', 'tablely'); ?></h3>
                        <div id="mptrs_create_taxo_form">
                            <label><?php esc_html_e('Name:', 'tablely'); ?></label>
                            <input type="text" name="mptrs_taxo_name" class="mptrs_create_taxo_input"><br>

                            <label><?php esc_html_e('Slug:', 'tablely'); ?></label>
                            <input type="text" name="mptrs_taxo_slug" class="mptrs_create_taxo_input"><br>

                            <label><?php esc_html_e('Description:', 'tablely'); ?></label>
                            <textarea name="mptrs_taxo_desc" class="mptrs_create_taxo_input"></textarea><br>

                            <label><?php esc_html_e('Image:', 'tablely'); ?></label>
                            <div class="mptrs_taxo_image_holder">
                                <img src="" alt="" style="width: 100px; height: auto">
                                <button class="mptrs_taxo_image_add" type="button">
                                    <?php esc_html_e('Add Image', 'tablely'); ?> <span class="fas fa-images"></span>
                                </button>
                            </div>


                            <button type="submit" class="mptrs_create_taxo_submitBtn" data-restaurant-id="<?php echo esc_attr( $post_id );?>"><?php esc_html_e('Save '.$type.'', 'tablely'); ?></button>
                        </div>
                        <div class="mptrs_create_taxo_message"></div>
                    </div>
                </div>
                <?php

                return ob_get_clean();
            }
			public function general_settings( $post_id ) {
				$image_id = get_post_meta($post_id, 'mptrs_restaurant_logo', true);
				$restaurant_address = get_post_meta( $post_id, 'mptrs_restaurant_address', true );
				$selected_city = get_post_meta( $post_id, 'mptrs_restaurant_city', true );

                $taxonomy = 'mptrs_restaurant_city';
                $restaurant_cities = get_terms([
                    'taxonomy'   => $taxonomy,
                    'hide_empty' => false,
                ]);

				$image_url = $image_id ? wp_get_attachment_image_src($image_id, 'full')[0] : '';
                $selected_seat_map_id = get_post_meta( $post_id, 'mptrs_selected_seat_map', true );

                $term_name = 'mptrs_restaurant_city';
                $type = 'City';
                $term_id = '';
                echo self::add_taxonomy_html_data( $type, $term_name, $post_id, $term_id );
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
                    <section>
                        <label class="label">
                            <p><?php esc_html_e('Add City', 'tablely'); ?></p>
                            <button class="mptrs_restaurant_city_add_btn"><?php esc_html_e('+Add City', 'tablely'); ?></button>
                        </label>
                        <label for="mptrs_restaurant_city"><?php esc_html_e('Select City:', 'tablely'); ?></label>
                        <select name="mptrs_restaurant_city" id="mptrs_restaurant_city" class="mptrs_input">
                            <option value=""><?php esc_html_e('-- Select City --', 'tablely'); ?></option>
                            <?php
                            if ( ! empty( $restaurant_cities ) && ! is_wp_error( $restaurant_cities ) ) : ?>
                                <?php foreach ( $restaurant_cities as $restaurant_city ) :
                                    $select_city = '';
                                    if( $selected_city === $restaurant_city->name ){
                                        $select_city = 'selected';
                                    }
                                    ?>
                                    <option value="<?php echo esc_attr( $restaurant_city->name ); ?>" <?php echo esc_attr( $select_city );?>>
                                        <?php echo esc_html( $restaurant_city->name ); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <option value=""><?php esc_html_e('No cities found', 'tablely'); ?></option>
                            <?php endif; ?>
                        </select>
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
									<?php echo esc_html('[mptrs_display_food_menu restaurant_id_id="'.get_the_ID().'"]') ?>
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
									<?php echo esc_html('[mptrs_reserve_table seat_mapping="yes/no" restaurant_id="'.$selected_seat_map_id.'"]') ?>
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

					$restaurant_city = isset($_POST['mptrs_restaurant_city']) ? sanitize_text_field(wp_unslash($_POST['mptrs_restaurant_city'])) : '';
					update_post_meta($post_id, 'mptrs_restaurant_city', $restaurant_city );
				}
			}
		}
		new MPTRS_General_Settings();
	}