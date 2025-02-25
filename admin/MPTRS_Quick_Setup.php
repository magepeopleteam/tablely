<?php
	/*
   * @Author 		engr.sumonazma@gmail.com
   * Copyright: 	mage-people.com
   */
	if ( ! defined( 'ABSPATH' ) ) {
		die;
	} // Cannot access pages directly.
	if ( ! class_exists( 'MPTRS_Quick_Setup' ) ) {
		class MPTRS_Quick_Setup {
			public function __construct() {
				add_action( 'admin_menu', array( $this, 'quick_setup_menu' ) );
			}

			public function quick_setup_menu() {
				if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
					add_submenu_page( 'edit.php?post_type=mptrs_item', esc_html__( 'Quick Setup', 'tablely' ), '<span style="color:#10dd10">' . esc_html__( 'Quick Setup', 'tablely' ) . '</span>', 'manage_options', 'mptrs_quick_setup', array( $this, 'quick_setup' ) );
					add_submenu_page( 'mptrs_item', esc_html__( 'Quick Setup', 'tablely' ), '<span style="color:#10dd10">' . esc_html__( 'Quick Setup', 'tablely' ) . '</span>', 'manage_options', 'mptrs_quick_setup', array( $this, 'quick_setup' ) );
                    add_submenu_page( 'edit.php?post_type=mptrs_item', esc_html__( 'New Food Menu', 'tablely' ), esc_html__( 'New Food Menu', 'tablely' ), 'manage_options', 'mptrs_new_food_menu', array( $this, 'mptrs_new_food_menu_callback' ) );

                } else {
					add_menu_page( esc_html__( 'Tablely', 'tablely' ), esc_html__( 'Tablely', 'tablely' ), 'manage_options', 'mptrs_item', array( $this, 'quick_setup' ), 'dashicons-admin-site-alt2', 6 );
					add_submenu_page( 'mptrs_item', esc_html__( 'Quick Setup', 'tablely' ), '<span style="color:#10dd17">' . esc_html__( 'Quick Setup', 'tablely' ) . '</span>', 'manage_options', 'mptrs_quick_setup', array( $this, 'quick_setup' ) );
                    add_submenu_page( 'edit.php?post_type=mptrs_item', esc_html__( 'New Food Menu', 'tablely' ), esc_html__( 'New Food Menu', 'tablely' ), 'manage_options', 'mptrs_new_food_menu', array( $this, 'mptrs_new_food_menu_callback' ) );

                }
			}


            public function mptrs_new_food_menu_callback(){
                $existing_menus = get_option( '_mptrs_food_menu' );
//                $existing_menus = get_post_meta($post_id, '_mptrs_food_menu', true);
                ?>
                <div id="mptrs_foodMenuPopup" class="mptrs_foodMenuPopupContainer" style="display: none;">
                    <div class="mptrs_foodMenuContentPopup">
                        <span class="mptrs_closePopup">&times;</span>
                        <div id="mptrs_foodMenuContentContainer"></div> <!-- Form will be appended here -->
                    </div>
                </div>

                <div class="mptrs_area mptrs_global_settings">
                    <div class="tabsItem" data-tabs="#mptrs_food_menu_add">

                <div class="mptrs_foodMenuTabHoilder">
                    <ul class="mptrs_foodMenuTabs">
                        <li id="mptrs_foodMenuAdded" class="mptrs_foodMenuTab"><?php esc_attr_e( 'Added Food Menu', 'tablely' )?></li>
                        <li id="mptrs_foodMenuShow" class="mptrs_foodMenuTab"><?php esc_attr_e( 'All Food Menu', 'tablely' )?></li>
                    </ul>

                </div>
                <div class="mptrs_foodMenuContentHolder">
                    <div id="mptrs_foodMenuAddedContainer" class="mptrs_foodMenuContainer">
                        <h2><?php esc_html_e('Added Food Menu Here', 'tablely'); ?></h2>
                        <form id="mptrs_foodMenuForm" class="mptrs_food_menu_form">
                            <div class="mptrs_form_group">
                                <label for="mptrs_menuName"><?php esc_attr_e( 'Menu Name', 'tablely' )?></label>
                                <input type="text" id="mptrs_menuName" name="mptrs_menuName" class="mptrs_input" required>
                            </div>

                            <div class="mptrs_form_group">
                                <label for="mptrs_menuCategory"><?php esc_attr_e( 'Category', 'tablely' )?></label>
                                <select id="mptrs_menuCategory" name="mptrs_menuCategory" class="mptrs_select" required>
                                    <option value="starter"><?php esc_attr_e( 'Menu Name', 'tablely' )?></option>
                                    <option value="main_course"><?php esc_attr_e( 'Main Course', 'tablely' )?></option>
                                    <option value="dessert"><?php esc_attr_e( 'Dessert', 'tablely' )?></option>
                                    <option value="beverage"><?php esc_attr_e( 'Beverage', 'tablely' )?></option>
                                </select>
                            </div>

                            <div class="mptrs_form_group">
                                <label for="mptrs_menuPrice"><?php esc_attr_e( 'Price ($)', 'tablely' )?></label>
                                <input type="number" id="mptrs_menuPrice" name="mptrs_menuPrice" class="mptrs_input" required>
                            </div>

                            <div class="mptrs_form_group">
                                <label for="mptrs_numPersons"><?php esc_attr_e( 'Number of Persons', 'tablely' )?></label>
                                <input type="number" id="mptrs_numPersons" name="mptrs_numPersons" class="mptrs_input" min="1" required>
                            </div>

                            <div class="mptrs_form_group">
                                <label for="mptrs_menuImage"><?php esc_attr_e( 'Menu Image', 'tablely' )?></label>
                                <input type="file" id="mptrs_menuImage" name="mptrs_menuImage" class="mptrs_input">
                                <input  type="hidden" id="mptrs_menuImage_url" name="mptrs_menuImage_url" value="">
                                <div class="custom-foodMenu-image-preview" ></div>
                            </div>

                            <button type="submit" class="mptrs_submit_btn"><?php esc_attr_e( 'Add Menu', 'tablely' )?></button>
                        </form>
                    </div>
                    <div id="mptrs_foodMenuShowContainer" class="mptrs_foodMenuContainer" style="display: none">
                        <div id="mptrs_allFoodMenu" class="mptrs_allFoodMenu">

                            <div class="mptrs_foodMenuContaine">

                                <?php
                                $fallbackImgUrl = get_site_url().'/wp-content/uploads/2025/02/fallbackimage.webp';
                                foreach ( $existing_menus as $key => $existing_menu ){
                                    if( $existing_menu['menuImgUrl'] === '' ){
                                        $menu_img = $fallbackImgUrl;
                                    }else{
                                        $menu_img = $existing_menu['menuImgUrl'];
                                    }
                                    ?>
                                    <div class="mptrs_foodMenuContent" id="mptrs_foodMenuContent<?php echo esc_attr( $key )?>">
                                        <div class="mptrs_menuImageHolder">
                                            <img class="mptrs_menuImage" id="mptrs_memuImgUrl<?php echo esc_attr( $key )?>" src="<?php echo esc_attr( $menu_img ) ?>" >
                                        </div>
                                        <div class="mptrs_menuInfoHolder">
                                            <div class="mptrs_topMenuInFo">
                                                <div class="mptrs_menuName" id="mptrs_memuName<?php echo esc_attr( $key )?>">
                                                    <?php echo esc_attr( $existing_menu['menuName'] );?>
                                                </div>
                                            </div>
                                            <div class="mptrs_BottomMenuInFo">
                                                <div class="mptrs_menuPrice" id="mptrs_memuPrice<?php echo esc_attr( $key )?>">$<?php echo esc_attr( $existing_menu['menuPrice'] );?></div>
                                                <div class="mptrs_menuPersion" id="mptrs_memuPersons<?php echo esc_attr( $key )?>"><i class='fas fa-user-alt' style='font-size:14px'></i><?php echo esc_attr( $existing_menu['numPersons'] );?></div>
                                            </div>
                                            <div class="mptrs_BottomMenuInFo">
                                                <span class="mptrm_editFoodMenu" id="mptrsEditMenu_<?php echo esc_attr( $key )?>" style="display: block">Edit </span>
                                                <span class="mptrm_deleteFoodMenu" id="mptrsDeleteMenu_<?php echo esc_attr( $key )?>">Delete </span>
                                            </div>
                                        </div>
                                    </div>
                                <?php }?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
                </div>
                <?php
            }

			public function quick_setup() {
				if ( isset( $_POST['mptrs_quick_setup_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mptrs_quick_setup_nonce'] ) ), 'mptrs_quick_setup_nonce' ) ) {
					if ( isset( $_POST['active_woo_btn'] ) ) {
						?>
                        <script>
                            mptrs_loader_body();
                        </script>
						<?php
						activate_plugin( 'woocommerce/woocommerce.php' );
						?>
                        <script>
                            (function ($) {
                                "use strict";
                                $(document).ready(function () {
                                    let mptrs_admin_location = window.location.href;
                                    mptrs_admin_location = mptrs_admin_location.replace('admin.php?post_type=mptrs_item&page=mptrs_quick_setup', 'edit.php?post_type=mptrs_item&page=mptrs_quick_setup');
                                    mptrs_admin_location = mptrs_admin_location.replace('admin.php?page=mptrs_item', 'edit.php?post_type=mptrs_item&page=mptrs_quick_setup');
                                    mptrs_admin_location = mptrs_admin_location.replace('admin.php?page=mptrs_quick_setup', 'edit.php?post_type=mptrs_item&page=mptrs_quick_setup');
                                    window.location.href = mptrs_admin_location;
                                });
                            }(jQuery));
                        </script>
						<?php
					}
					if ( isset( $_POST['install_and_active_woo_btn'] ) ) {
						echo '<div style="display:none">';
						include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
						include_once( ABSPATH . 'wp-admin/includes/file.php' );
						include_once( ABSPATH . 'wp-admin/includes/misc.php' );
						include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
						$plugin             = 'woocommerce';
						$api                = plugins_api( 'plugin_information', array(
							'slug'   => $plugin,
							'fields' => array(
								'short_description' => false,
								'sections'          => false,
								'requires'          => false,
								'rating'            => false,
								'ratings'           => false,
								'downloaded'        => false,
								'last_updated'      => false,
								'added'             => false,
								'tags'              => false,
								'compatibility'     => false,
								'homepage'          => false,
								'donate_link'       => false,
							),
						) );
						$title              = 'title';
						$url                = 'url';
						$nonce              = 'nonce';
						$woocommerce_plugin = new Plugin_Upgrader( new Plugin_Installer_Skin( compact( 'title', 'url', 'nonce', 'plugin', 'api' ) ) );
						$woocommerce_plugin->install( $api->download_link );
						activate_plugin( 'woocommerce/woocommerce.php' );
						echo '</div>';
						?>
                        <script>
                            (function ($) {
                                "use strict";
                                $(document).ready(function () {
                                    let mptrs_admin_location = window.location.href;
                                    mptrs_admin_location = mptrs_admin_location.replace('admin.php?post_type=mptrs_item&page=mptrs_quick_setup', 'edit.php?post_type=mptrs_item&page=mptrs_quick_setup');
                                    mptrs_admin_location = mptrs_admin_location.replace('admin.php?page=mptrs_item', 'edit.php?post_type=mptrs_item&page=mptrs_quick_setup');
                                    mptrs_admin_location = mptrs_admin_location.replace('admin.php?page=mptrs_quick_setup', 'edit.php?post_type=mptrs_item&page=mptrs_quick_setup');
                                    window.location.href = mptrs_admin_location;
                                });
                            }(jQuery));
                        </script>
						<?php
					}
					if ( isset( $_POST['finish_quick_setup'] ) ) {
						$label                       = isset( $_POST['mptrs_label'] ) ? sanitize_text_field( wp_unslash( $_POST['mptrs_label'] ) ) : 'tablely';
						$slug                        = isset( $_POST['mptrs_slug'] ) ? sanitize_text_field( wp_unslash( $_POST['mptrs_slug'] ) ) : 'tablely';
						$general_settings_data       = get_option( 'mptrs_general_settings' );
						$update_general_settings_arr = [
							'label' => $label,
							'slug'  => $slug
						];
						$new_general_settings_data   = is_array( $general_settings_data ) ? array_replace( $general_settings_data, $update_general_settings_arr ) : $update_general_settings_arr;
						update_option( 'mptrs_general_settings', $new_general_settings_data );
						flush_rewrite_rules();
						wp_redirect( admin_url( 'edit.php?post_type=mptrs_item' ) );
					}
				}
				?>
                <div class="mptrs_area">
                    <div class=_dShadow_6_adminLayout">
                        <form method="post" action="">
							<?php wp_nonce_field( 'mptrs_quick_setup_nonce', 'mptrs_quick_setup_nonce' ); ?>
                            <div class="mptrs_tab_next">
                                <div class="tabListsNext _max_700_mAuto">
                                    <div data-tabs-target-next="#mptrs_qs_welcome" class="tabItemNext" data-open-text="1" data-close-text=" " data-open-icon="" data-close-icon="fas fa-check" data-add-class="success">
                                        <h4 class="circleIcon" data-class>
                                            <span class="mp_zero" data-icon></span>
                                            <span class="mp_zero" data-text>1</span>
                                        </h4>
                                        <h6 class="circleTitle" data-class><?php esc_html_e( 'Welcome', 'tablely' ); ?></h6>
                                    </div>
                                    <div data-tabs-target-next="#mptrs_qs_general" class="tabItemNext" data-open-text="2" data-close-text="" data-open-icon="" data-close-icon="fas fa-check" data-add-class="success">
                                        <h4 class="circleIcon" data-class>
                                            <span class="mp_zero" data-icon></span>
                                            <span class="mp_zero" data-text>2</span>
                                        </h4>
                                        <h6 class="circleTitle" data-class><?php esc_html_e( 'General', 'tablely' ); ?></h6>
                                    </div>
                                    <div data-tabs-target-next="#mptrs_qs_done" class="tabItemNext" data-open-text="3" data-close-text="" data-open-icon="" data-close-icon="fas fa-check" data-add-class="success">
                                        <h4 class="circleIcon" data-class>
                                            <span class="mp_zero" data-icon></span>
                                            <span class="mp_zero" data-text>3</span>
                                        </h4>
                                        <h6 class="circleTitle" data-class><?php esc_html_e( 'Done', 'tablely' ); ?></h6>
                                    </div>
                                </div>
                                <div class="tabsContentNext _infoLayout_mT">
									<?php
										$this->setup_welcome_content();
										$this->setup_general_content();
										$this->setup_content_done();
									?>
                                </div>
								<?php if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) { ?>
                                    <div class="justifyBetween">
                                        <button type="button" class="mpBtn mptrs_prev_tab">
                                            <span>&longleftarrow;<?php esc_html_e( 'Previous', 'tablely' ); ?></span>
                                        </button>
                                        <div></div>
                                        <button type="button" class="themeButton mptrs_next_tab">
                                            <span><?php esc_html_e( 'Next', 'tablely' ); ?>&longrightarrow;</span>
                                        </button>
                                    </div>
								<?php } ?>
                            </div>
                        </form>
                    </div>
                </div>
				<?php
			}

			public function setup_welcome_content() {
				$plugin_dir = ABSPATH . 'wp-content/plugins/woocommerce';
				?>
                <div data-tabs-next="#mptrs_qs_welcome">
                    <h2><?php esc_html_e( 'Tablely Manager For Woocommerce Plugin', 'tablely' ); ?></h2>
                    <p class="mTB_xs"><?php esc_html_e( 'Tablely Manager Plugin for WooCommerce for your site, Please go step by step and choose some options to get started.', 'tablely' ); ?></p>
                    <div class="_dLayout_mT_alignCenter justifyBetween">
                        <h5>
							<?php if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
								esc_html_e( 'Woocommerce already installed and activated', 'tablely' );
							} elseif ( is_dir( $plugin_dir ) ) {
								esc_html_e( 'Woocommerce already install , please activate it', 'tablely' );
							} else {
								esc_html_e( 'Woocommerce need to install and active', 'tablely' );
							} ?>
                        </h5>
						<?php if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) { ?>
                            <h5><span class="fas fa-check-circle textSuccess"></span></h5>
						<?php } elseif ( is_dir( $plugin_dir ) ) { ?>
                            <button class="themeButton" type="submit" name="active_woo_btn"><?php esc_html_e( 'Active Now', 'tablely' ); ?></button>
						<?php } else { ?>
                            <button class="warningButton" type="submit" name="install_and_active_woo_btn"><?php esc_html_e( 'Install & Active Now', 'tablely' ); ?></button>
						<?php } ?>
                    </div>
                </div>
				<?php
			}

			public function setup_general_content() {
				$label = MPTRS_Function::get_settings( 'mptrs_general_settings', 'label', 'Tablely' );
				$slug  = MPTRS_Function::get_settings( 'mptrs_general_settings', 'slug', 'service-booking' );
				?>
                <div data-tabs-next="#mptrs_qs_general">
                    <div class="section">
                        <h2><?php esc_html_e( 'General settings', 'tablely' ); ?></h2>
                        <p class="mTB_xs"><?php esc_html_e( 'Choose some general option.', 'tablely' ); ?></p>
                        <div class="_dLayout_mT">
                            <label class="fullWidth">
                                <span class="min_300"><?php esc_html_e( 'Tablely Manager Label:', 'tablely' ); ?></span>
                                <input type="text" class="formControl" name="mptrs_label" value='<?php echo esc_attr( $label ); ?>'/>
                            </label>
                            <i class="info_text">
                                <span class="fas fa-info-circle"></span>
								<?php esc_html_e( 'It will change the Tablely Manager post type label on the entire plugin.', 'tablely' ); ?>
                            </i>
                            <div class="divider"></div>
                            <label class="fullWidth">
                            <span
                                class="min_300"><?php esc_html_e( 'Tablely Manager Slug:', 'tablely' ); ?></span>
                                <input type="text" class="formControl" name="mptrs_slug" value='<?php echo esc_attr( $slug ); ?>'/>
                            </label>
                            <i class="info_text">
                                <span class="fas fa-info-circle"></span>
								<?php esc_html_e( 'It will change the Tablely Manager slug on the entire plugin. Remember after changing this slug you need to flush permalinks. Just go to Settings->Permalinks hit the Save Settings button', 'tablely' ); ?>
                            </i>
                        </div>
                    </div>
                </div>
				<?php
			}

			public function setup_content_done() {
				?>
                <div data-tabs-next="#mptrs_qs_done">
                    <h2><?php esc_html_e( 'Finalize Setup', 'tablely' ); ?></h2>
                    <p class="mTB_xs"><?php esc_html_e( 'You are about to Finish & Save tablely For Woocommerce Plugin setup process', 'tablely' ); ?></p>
                    <div class="mT allCenter">
                        <button type="submit" name="finish_quick_setup" class="themeButton"><?php esc_html_e( 'Finish & Save', 'tablely' ); ?></button>
                    </div>
                </div>
				<?php
			}
		}
		new MPTRS_Quick_Setup();
	}