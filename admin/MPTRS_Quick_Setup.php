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
                    add_submenu_page( 'edit.php?post_type=mptrs_item', esc_html__( 'Order Lists', 'tablely' ), esc_html__( 'Order Lists', 'tablely' ), 'manage_options', 'mptrs_order', array( $this, 'mptrs_all_order_callback' ) );

                } else {
					add_menu_page( esc_html__( 'Tablely', 'tablely' ), esc_html__( 'Tablely', 'tablely' ), 'manage_options', 'mptrs_item', array( $this, 'quick_setup' ), 'dashicons-admin-site-alt2', 6 );
					add_submenu_page( 'mptrs_item', esc_html__( 'Quick Setup', 'tablely' ), '<span style="color:#10dd17">' . esc_html__( 'Quick Setup', 'tablely' ) . '</span>', 'manage_options', 'mptrs_quick_setup', array( $this, 'quick_setup' ) );
                    add_submenu_page( 'edit.php?post_type=mptrs_item', esc_html__( 'New Food Menu', 'tablely' ), esc_html__( 'New Food Menu', 'tablely' ), 'manage_options', 'mptrs_new_food_menu', array( $this, 'mptrs_new_food_menu_callback' ) );

                }
			}


            public function mptrs_all_order_callback( $key ) {
                $order_types = array(
                        'dine_in' => 'Dine In',
                        'delivery' => 'Delivery',
                        'take_away' => 'Takeaway',
                );

                $args                 = array(
                    'post_type'      => 'mptrs_order',
                    'order'          => 'DESC',
                    'posts_per_page' => - 1
                );
                $query                = new WP_Query( $args );
                ?>
                <div class="mptrs_order_page_wrap wrap">
                    <h1 class="mptrs_awesome-heading"><?php esc_html_e( 'Order List', 'booking-and-rental-manager-for-woocommerce' ); ?></h1>
<!--                    <input type="text" id="search" class="search-input awesome-search" placeholder="--><?php //esc_attr_e( 'Search by order id or customer name..', 'booking-and-rental-manager-for-woocommerce' ); ?><!--"/>-->
                    <div class="mptrs_orderTypeContainer">
                        <?php
                        if( is_array( $order_types ) && count( $order_types ) > 0 ){ ?>
                            <div class="mptrs_order_type_item mptrs_active" data-filter="<?php echo __( 'all', 'tablely' )?>"><?php echo __( 'All', 'tablely' )?></div>
                            <?php foreach( $order_types as $key => $order_type ){ ?>
                                <div class="mptrs_order_type_item" data-filter="<?php echo esc_attr( $order_type )?>"><?php echo esc_attr( $order_type )?></div>
                            <?php }
                        }
                        ?>
                    </div>
                    <table class="mptrs_order_page_table">
                        <thead>
                        <tr>
                            <th><?php esc_html_e( 'Order', 'booking-and-rental-manager-for-woocommerce' ); ?></th>
                            <th><?php esc_html_e( 'Order Type', 'booking-and-rental-manager-for-woocommerce' ); ?></th>
                            <th><?php esc_html_e( 'Billing Name', 'booking-and-rental-manager-for-woocommerce' ); ?></th>
                            <th><?php esc_html_e( 'Order Created Date', 'booking-and-rental-manager-for-woocommerce' ); ?></th>
                            <th><?php esc_html_e( 'Booking Date', 'booking-and-rental-manager-for-woocommerce' ); ?></th>
                            <th><?php esc_html_e( 'Booking Time', 'booking-and-rental-manager-for-woocommerce' ); ?></th>
                            <th><?php esc_html_e( 'Service Status', 'booking-and-rental-manager-for-woocommerce' ); ?></th>
                            <th><?php esc_html_e( 'Order Status', 'booking-and-rental-manager-for-woocommerce' ); ?></th>
                            <th><?php esc_html_e( 'Total', 'booking-and-rental-manager-for-woocommerce' ); ?></th>
                            <th style="display: none"><?php esc_html_e( 'Action', 'booking-and-rental-manager-for-woocommerce' ); ?></th>
                        </tr>
                        </thead>
                        <tbody id="order-list">
                        <?php if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post();
                            global $post;

                            $post_id             = $post->ID;
                            $rbfw_order_id       = get_post_meta( $post_id, '_mptrs_order_id', true );
                            $order = wc_get_order($rbfw_order_id);
                            $billing_name        = get_post_meta( $post_id, '_mptrs_customer_name', true );
                            $ordered_type        = get_post_meta( $post_id, '_mptrs_order_type', true );
                            $ordered_type        = isset( $order_types[$ordered_type] ) ? $order_types[$ordered_type]: '' ;
//                            $status              = ( $order && $order->get_status() === 'trash')? $order->get_status() : get_post_meta( $post_id, '_mptrs_order_status', true );
                            $status              = ( $order )? $order->get_status() : get_post_meta( $post_id, '_mptrs_order_status', true );
                            $total_price         = get_post_meta( $post_id, '_mptrs_order_total', true );
                            $ticket_infos        = get_post_meta( $post_id, '_mptrs_ordered_food_menu', true );
                            $ticket_info_array   = maybe_unserialize( $ticket_infos );
                            $rbfw_start_datetime = get_post_meta( $post_id, '_mptrs_order_date', true );
                            $rbfw_end_datetime = get_post_meta( $post_id, '_mptrs_order_time', true );
                            $rbfw_service_status = get_post_meta( $post_id, '_mptrs_service_status', true );
                            $rbfw_service_status = empty( $rbfw_service_status ) ? 'In progress' : $rbfw_service_status;
                            $rbfw_service_status_val = strtolower(str_replace(' ', '_', $rbfw_service_status ) );

                            ?>
                            <tr class="mptrs_order_row" data-order_type_filter="<?php echo esc_html( $ordered_type ); ?>">
                                <td><?php echo esc_html( $rbfw_order_id ); ?></td>
                                <td><?php echo esc_html( $ordered_type ); ?></td>
                                <td><?php echo esc_html( $billing_name ); ?></td>
                                <td><?php echo esc_html( get_the_date( 'F j, Y' ) . ' ' . get_the_time() ); ?></td>
                                <td><?php echo esc_html( ! empty( $rbfw_start_datetime ) ? date_i18n( 'F j, Y', strtotime( $rbfw_start_datetime ) ) : '' ); ?></td>
                                <td>
                                    <?php
                                    if ( ! empty( $rbfw_end_datetime ) ) {
                                        // Assuming $rbfw_end_datetime is just the hour (1, 2, or 24), append ":00:00" to simulate full time.
                                        $fullDateTime = $rbfw_end_datetime . ":00:00";
                                        $formattedTime = date_i18n( 'g A', strtotime( $fullDateTime ) );
                                        echo esc_html( $formattedTime );
                                    }
                                    ?>
                                </td>
                                <td>
                                    <select name="mptrs_service_status" id="mptrsServiceStatus-<?php echo esc_attr( $post_id ); ?>" class="mptrs_service_status">
                                        <option value="in_progress" <?php echo ($rbfw_service_status_val == "in_progress") ? 'selected' : ''; ?>><?php esc_attr_e( 'In Progress', 'tablely');?></option>
                                        <option value="done" <?php echo ($rbfw_service_status_val == "done") ? 'selected' : ''; ?>><?php esc_attr_e( 'Done', 'tablely');?></option>
                                        <option value="service_out" <?php echo ($rbfw_service_status_val == "service_out") ? 'selected' : ''; ?>><?php esc_attr_e( 'Service Out', 'tablely');?></option>
                                    </select>
                                </td>
                                <td><span class="mptrs_order_status <?php echo esc_attr( $status ); ?>"><?php echo esc_html( $status ) ;  ?></span></td>
                                <td><?php echo wp_kses_post( wc_price( $total_price ) ); ?></td>
                                <?php if ( function_exists( 'rbfw_pro_tab_menu_list' ) ) { ?>
                                    <td style="display: none">
                                        <a href="javascript:void(0);" class="mptrs_order_view_btn" data-post-id="<?php echo esc_attr( $post_id ); ?>">
                                            <i class="fas fa-pen-to-square"></i>
                                            <?php esc_html_e( 'View Details', 'booking-and-rental-manager-for-woocommerce' ); ?>
                                        </a>
                                        <a href="<?php echo esc_url( admin_url( 'post.php?post=' . $post_id . '&action=edit' ) ); ?>" class="mptrs_order_edit_btn">
                                            <i class="fas fa-pen-to-square"></i>
                                            <?php esc_html_e( 'Order status changes', 'booking-and-rental-manager-for-woocommerce' ); ?>
                                        </a>
                                    </td>
                                <?php
                                } else {
                                ?>
                                    <td style="display: none">
                                        <a href="javascript:void(0);" class="mptrs_order_view_btn pro-overlay">
                                            <i class="fas fa-pen-to-square"></i>
                                            <?php esc_html_e( 'View Details', 'booking-and-rental-manager-for-woocommerce' ); ?>
                                        </a>
                                        <a href="javascript:void(0);" class="mptrs_order_edit_btn pro-overlay">
                                            <i class="fas fa-pen-to-square"></i>
                                            <?php esc_html_e( 'Order status changes', 'booking-and-rental-manager-for-woocommerce' ); ?>
                                        </a>
                                    </td>
                                    <script>
                                        document.querySelectorAll('.pro-overlay').forEach(function (button) {
                                            button.replaceWith(button.cloneNode(true));
                                        });

                                        document.querySelectorAll('.pro-overlay').forEach(function (button) {
                                            button.addEventListener('click', function (event) {
                                                event.preventDefault(); // Prevent default link behavior
                                                window.open('<?php echo esc_js( esc_url( 'https://mage-people.com/product/booking-and-rental-manager-for-woocommerce/' ) ); ?>', '_blank');
                                            });
                                        });
                                    </script>
                                    <?php
                                }
                                ?>
                            </tr>
                            <tr id="order-details-<?php echo esc_attr( $post_id ); ?>" class="order-details" style="display: none;">
                                <td colspan="12">
                                    <div class="order-details-content"></div>
                                </td>
                            </tr>
                        <?php endwhile; else : ?>
                            <tr>
                                <td colspan="12"><?php esc_html_e( 'Sorry, No data found!', 'booking-and-rental-manager-for-woocommerce' ); ?></td>
                            </tr>
                        <?php endif;
                        wp_reset_postdata(); ?>
                        </tbody>
                    </table>
                    <div id="loader" style="display: none;">
                        <div class="loader"></div> <!-- Loader element -->
                    </div>
                    <label for="posts-per-page"><?php esc_html_e( 'Posts per Page:', 'booking-and-rental-manager-for-woocommerce' ); ?></label>
                    <!--<select id="posts-per-page">
                            <option value="2" <?php /*selected( $this->posts_per_page, 2 ); */?>>2</option>
                            <option value="5" <?php /*selected( $this->posts_per_page, 5 ); */?>>5</option>
                            <option value="10" <?php /*selected( $this->posts_per_page, 10 ); */?>>10</option>
                            <option value="20" <?php /*selected( $this->posts_per_page, 20 ); */?>>20</option>
                            <option value="25" <?php /*selected( $this->posts_per_page, 25 ); */?>>25</option>
                            <option value="30" <?php /*selected( $this->posts_per_page, 30 ); */?>>30</option>
                        </select>-->
                    <div id="pagination" class="pagination"></div>
                </div>
                <?php
            }

            public function mptrs_new_food_menu_callback(){
                $existing_menus = get_option( '_mptrs_food_menu' );
                $menu_categories = get_option( 'mptrs_categories' );

//                error_log( print_r( [ '$existing_menus' => $existing_menus['variations'] ], true ) );
                ?>
                <div id="mptrs_foodMenuPopup" class="mptrs_foodMenuPopupContainer" style="display: none;">
                    <div class="mptrs_foodMenuContentPopup">
                        <span class="mptrs_closePopup">&times;</span>
                        <div id="mptrs_foodMenuContentContainer"></div>
                    </div>
                </div>

                <div class="mptrs_area mptrs_global_settings">
                    <div class="tabsItem" data-tabs="#mptrs_food_menu_add">
                        <div class="mptrs_categoryMenubtnHolder">
                            <button id="mptrs_openCategoryPopup" class="mptrs_open_popup_btn">Categories..</button>
                            <button id="mptrs_openPopup" class="mptrs_open_popup_btn">+Add New Food Menu </button>
                        </div>
                        <div class="mptrs_foodMenuContentHolder">

                            <div id="mptrs_foodMenuShowContainer" class="mptrs_foodMenuContainer" style="display: block">
                                <div id="mptrs_allFoodMenu" class="mptrs_allFoodMenu">
                                    <div class="mptrs_categoryFilterHolder">
                                        <div class="mptrs_categoryFilter active" data-filter="all"><?php echo __( 'All', 'tablely' ) ?></div>
                                        <?php
                                        if( is_array( $menu_categories ) && !empty( $menu_categories ) ) {
                                            foreach ( $menu_categories as $key => $category ) { ?>
                                                <div class="mptrs_categoryFilter" data-filter="<?php echo esc_attr( $key ) ?>"><?php echo esc_attr( $category ) ?></div>
                                            <?php }
                                        }
                                        ?>
                                    </div>
                                    <table class="mptrsTable" id="mptrs_showAllMenu">
                                        <thead>
                                        <tr>
                                            <th class="mptrsTableTh mptrsThImage">Image</th>
                                            <th class="mptrsTableTh mptrsThName">Name</th>
                                            <th class="mptrsTableTh mptrsThcategory">Category</th>
                                            <th class="mptrsTableTh mptrsThPrice">Price</th>
                                            <th class="mptrsTableTh mptrsThServes">Serves</th>
                                            <th class="mptrsTableTh mptrsThActions">Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody id="mptrs_foodMenuContainer">
                                        <?php
                                        if( is_array( $existing_menus ) && !empty( $existing_menus ) ) {
                                            foreach ( $existing_menus as $key => $existing_menu ){
//                                                error_log( print_r( [ '$existing_menus' => $existing_menu ], true ) );
                                                $category = isset( $menu_categories[$existing_menu['menuCategory']]) ? $menu_categories[$existing_menu['menuCategory']] : '';
                                                ?>
                                                <tr class="mptrsTableRow" data-category ="<?php echo esc_attr( $existing_menu['menuCategory'] )?>" id="mptrs_foodMenuContent<?php echo esc_attr( $key )?>">
                                                    <td class="mptrsTableTd mptrsTdImage">
                                                        <div class="mptrsImageWrapper" >
                                                            <img class="mptrsImage" id="mptrs_memuImgUrl<?php echo esc_attr($key)?>" src="<?php echo esc_attr($existing_menu['menuImgUrl']); ?>" alt="<?php echo esc_attr($existing_menu['menuName']); ?>">
                                                        </div>
                                                    </td>
                                                    <td class="mptrsTableTd mptrsTdName">
                                                        <div class="mptrs_menuName" id="mptrs_memuName<?php echo esc_attr( $key )?>">
                                                            <?php echo esc_attr( $existing_menu['menuName'] );?>
                                                        </div>
                                                        <input type="hidden" name="mptrs_menuCategory" id="mptrs_menuCategory<?php echo esc_attr( $key )?>" value="<?php echo esc_attr( $existing_menu['menuCategory'] )?>">
                                                    </td>
                                                    <td class="mptrsTableTd mptrsTdCategory" >
                                                        <div class="mptrs_memuPrice" id="mptrs_Category<?php echo esc_attr( $key )?>"><?php echo esc_attr( $category )?></div>
                                                    </td>
                                                    <td class="mptrsTableTd mptrsTdPrice" >
                                                        <div class="mptrs_memuPrice" id="mptrs_memuPrice<?php echo esc_attr( $key )?>">$<?php echo esc_html($existing_menu['menuPrice']); ?></div>
                                                    </td>
                                                    <td class="mptrsTableTd mptrsTdServes" >
                                                        <div class="mptrs_menuPersion" id="mptrs_memuPersons<?php echo esc_attr( $key )?>"><i class='fas fa-user-alt' style='font-size:14px'></i><?php echo esc_attr( $existing_menu['numPersons'] );?></div>
                                                    </td>
                                                    <td class="mptrsTableTd mptrsTdActions">
                                                        <div class="mptrs_BottomAllMenuInFo">
                                                            <span class="mptrm_editFoodMenu" id="mptrsEditMenu_<?php echo esc_attr( $key )?>"><i class='far fa-edit' style='font-size:20px'></i></span>
                                                            <span class="mptrm_deleteFoodMenu" id="mptrsDeleteMenu_<?php echo esc_attr( $key )?>"><i class='far fa-trash-alt' style='font-size:20px'></i> </span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php }
                                        }
                                        ?>
                                        </tbody>
                                    </table>
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