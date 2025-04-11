<?php

if (!defined('ABSPATH')) {
    die;
} // Cannot access pages directly.
if (!class_exists('MPTRS_Menu')) {
    class MPTRS_Menu{
        public function __construct(){
            add_action( 'admin_menu', array( $this, 'added_menu_pages' ) );

            add_action('admin_head', [ $this, 'mptrs_admin_menu_icons'] );
        }

        public function added_menu_pages() {
            add_submenu_page( 'edit.php?post_type=mptrs_item', esc_html__( 'Restaurant Lists', 'tablely' ), esc_html__( 'Restaurant Lists', 'tablely' ), 'manage_options', 'mptrs_restaurant_lists', array( $this, 'mptrs_restaurant_lists_callback' ) );
            add_submenu_page( 'edit.php?post_type=mptrs_item', esc_html__( 'New Food Menu', 'tablely' ), esc_html__( 'New Food Menu', 'tablely' ), 'manage_options', 'mptrs_new_food_menu', array( $this, 'mptrs_new_food_menu_callback' ) );
            add_submenu_page( 'edit.php?post_type=mptrs_item', esc_html__( 'Order Lists', 'tablely' ), esc_html__( 'Order Lists', 'tablely' ), 'manage_options', 'mptrs_order', array( $this, 'mptrs_all_order_callback' ) );
            add_submenu_page( 'edit.php?post_type=mptrs_item', esc_html__( 'Table Reserve Lists', 'tablely' ), esc_html__( 'Table Reserve Lists', 'tablely' ), 'manage_options', 'mptrs_reserve', array( $this, 'mptrs_table_reserved_callback' ) );
        }

        public function mptrs_restaurant_lists_callback() {
            // Get current page
            $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;

            $args = array(
                'post_type'      => 'mptrs_item',
                'order'          => 'DESC',
                'posts_per_page' => 1,
                'paged'          => $paged,
            );

            $query = new WP_Query($args);
            ?>
            <div class="mptrs_order_page_wrap wrap">

                <h1 class="mptrs_awesome-heading"><?php esc_html_e( 'Restaurant Lists', 'tablely' ); ?></h1>

                <?php if ($query->have_posts()) : ?>
                    <div class="mptrs_restaurant_list">
                        <div class="mptrs_add_new_restaurant">
                            <a href="<?php echo esc_url( site_url( '/wp-admin/post-new.php?post_type=mptrs_item' ) ); ?>" class="mptrs_add_button">
                                <button class="mptrs_add_button"><?php esc_html_e( 'Add New Restaurant', 'tablely' ); ?></button>
                            </a>
                        </div>


                        <?php while ($query->have_posts()) : $query->the_post();
                            $post_id = get_the_ID();
                            $thumbnail_url = get_the_post_thumbnail_url( $post_id, 'full');
                            if ( empty( $thumbnail_url ) ) {
                                $thumbnail_url = esc_url( MPTRS_Plan_ASSETS . 'images/fast-food.png' );
                            }
                            ?>
                            <div class="mptrs_restaurant_item">
                                <div class="mptrs-restaurant-content">
                                    <div class="thumbnail">
                                        <img src=" <?php  echo esc_attr( $thumbnail_url );?>" alt="<?php the_title(); ?>" class="mptrs_restaurant_image" />
                                    </div>
                                    <div class="content">
                                        <h2><?php the_title(); ?></h2>
                                        <?php the_content(); ?>
                                    </div>
                                    <div class="actions">
                                        <a class="view" href="<?php echo esc_url( get_permalink( get_the_ID() ) ); ?>" class="mptrs_edit_button">
                                        <i class="fas fa-eye"></i> <?php esc_html_e( 'View', 'tablely' ); ?>
                                        </a>
                                        <a class="edit" href="<?php echo esc_url( get_edit_post_link( get_the_ID() ) ); ?>" class="mptrs_edit_button">
                                        <i class="fas fa-edit"></i> <?php esc_html_e( 'Edit', 'tablely' ); ?>
                                        </a>
                                        <a class="delete" href="<?php echo get_delete_post_link( get_the_ID() ); ?>" onclick="return confirm('Are you sure you want to move this to trash?')">
                                            <i class="fas fa-trash"></i> <?php esc_html_e( 'Delete', 'tablely' ); ?>

                                        </a>
                                    </div>
                                        <div class="mptrs_restaurant_content"><?php echo wp_kses_data(get_the_content()); ?></div>
                                    </div>
                                </div>
                                <div class="actions">
                                    <a class="view" href="<?php echo esc_url( get_permalink( get_the_ID() ) ); ?>" class="mptrs_edit_button">
                                    <i class="fas fa-eye"></i> <?php esc_html_e( 'View', 'tablely' ); ?>
                                    </a>
                                    <a class="edit" href="<?php echo esc_url( get_edit_post_link( get_the_ID() ) ); ?>" class="mptrs_edit_button">
                                    <i class="fas fa-edit"></i> <?php esc_html_e( 'Edit', 'tablely' ); ?>
                                    </a>
                                    <a class="delete" href="<?php echo get_delete_post_link( get_the_ID() ); ?>" onclick="return confirm('Are you sure you want to move this to trash?')">
                                        <i class="fas fa-trash"></i> <?php esc_html_e( 'Delete', 'tablely' ); ?>
                                    </a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>

                    <!-- Pagination -->
                    <div class="mptrs_pagination">
                        <?php
                        echo paginate_links(array(
                            'total'   => $query->max_num_pages,
                            'current' => $paged,
                            'format'  => '?paged=%#%',
                        ));
                        ?>
                    </div>



                <?php else : ?>
                    <p><?php esc_html_e( 'No restaurants found.', 'tablely' ); ?></p>
                <?php endif; ?>

                <?php wp_reset_postdata(); ?>
            </div>
            <?php
        }

        public function mptrs_table_reserved_callback() {
            // Get current page
            $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;

            $args = array(
                'post_type'      => 'mptrs_table_reserve',
                'order'          => 'DESC',
                'posts_per_page' => 2,
                'paged'          => $paged,
            );

            $query = new WP_Query($args);
            $total_pages = $query->max_num_pages;
            ?>

            <div class="mptrs_order_page_wrap wrap">
                <h1 class="mptrs_awesome-heading"><?php esc_html_e( 'Table Reserve List', 'tablely' ); ?></h1>

                <table class="mptrs_order_page_table">
                    <thead>
                    <tr>
                        <th><?php esc_html_e( 'Occasion', 'tablely' ); ?></th>
                        <th><?php esc_html_e( 'Total guests', 'tablely' ); ?></th>
                        <th><?php esc_html_e( 'Reserve date', 'tablely' ); ?></th>
                        <th><?php esc_html_e( 'Reserve time', 'tablely' ); ?></th>
                        <th><?php esc_html_e( 'Seats Number', 'tablely' ); ?></th>
                        <th><?php esc_html_e( 'User Name', 'tablely' ); ?></th>
                        <th><?php esc_html_e( 'User Phone Num', 'tablely' ); ?></th>
                        <th><?php esc_html_e( 'User Email', 'tablely' ); ?></th>
                        <th><?php esc_html_e( 'User Advice', 'tablely' ); ?></th>
                        <th><?php esc_html_e( 'Reserve Status', 'tablely' ); ?></th>
                        <th style="display: none"><?php esc_html_e( 'Action', 'tablely' ); ?></th>
                    </tr>
                    </thead>
                    <tbody id="order-list">
                    <?php
                    if ($query->have_posts()) :
                        while ($query->have_posts()) :
                            $query->the_post();
                            global $post;

                            $seatNames = '';
                            $post_id = $post->ID;
                            $reservation_status = get_post_meta($post_id, '_mptrs_table_reservation_status', true);
                            $table_reservation_info = maybe_unserialize(get_post_meta($post_id, '_mptrs_table_reservation_info', true));
                            if (is_array($table_reservation_info['seatNames']) && !empty($table_reservation_info['seatNames'])) {
                                foreach ($table_reservation_info['seatNames'] as $value) {
                                    $seatNames .= $value . ', ';
                                }
                            }

                            $formattedDate = date('jS F Y', strtotime($table_reservation_info['reserve_date']));
                            ?>
                            <tr class="mptrs_order_row">
                                <td><?php echo esc_html($table_reservation_info['occasion']); ?></td>
                                <td><?php echo esc_html($table_reservation_info['guests']); ?></td>
                                <td><?php echo esc_html($formattedDate); ?></td>
                                <td><?php echo esc_html($table_reservation_info['reserve_time']); ?></td>
                                <td><?php echo esc_html($seatNames); ?></td>
                                <td><?php echo esc_html($table_reservation_info['userName']); ?></td>
                                <td><?php echo esc_html($table_reservation_info['userPhoneNum']); ?></td>
                                <td><?php echo esc_html($table_reservation_info['userEmailId']); ?></td>
                                <td><?php echo esc_html($table_reservation_info['userAdvice']); ?></td>
                                <td>
                                    <select name="mptrs_reserved_status" id="mptrsReservedStatus-<?php echo esc_attr($post_id); ?>" class="mptrs_reserved_status">
                                        <option value="0" <?php selected($reservation_status, 0); ?>><?php esc_attr_e('In Progress', 'tablely'); ?></option>
                                        <option value="1" <?php selected($reservation_status, 1); ?>><?php esc_attr_e('Reserved', 'tablely'); ?></option>
                                    </select>
                                </td>
                            </tr>
                        <?php endwhile;
                        wp_reset_postdata();
                    else : ?>
                        <tr>
                            <td colspan="10"><?php esc_html_e('No reservations found.', 'tablely'); ?></td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>

                <?php
                // Pagination
                if ($total_pages > 1) {
                    echo '<div class="mptrs_pagination" style="margin-top: 20px;">';
                    echo paginate_links(array(
                        'base'      => add_query_arg('paged', '%#%'),
                        'format'    => '',
                        'current'   => $paged,
                        'total'     => $total_pages,
                        'prev_text' => __('« Prev', 'tablely'),
                        'next_text' => __('Next »', 'tablely'),
                    ));
                    echo '</div>';
                }
                ?>
            </div>

            <?php
        }


        public function mptrs_all_order_callback( $key ) {
            $order_types = array(
                'dine_in'   => 'Dine In',
                'delivery'  => 'Delivery',
                'take_away' => 'Takeaway',
            );

            $paged = isset($_GET['paged']) ? absint($_GET['paged']) : 1;

            $args = array(
                'post_type'      => 'mptrs_order',
                'order'          => 'DESC',
                'posts_per_page' => 10,
                'paged'          => $paged,
            );

            $query = new WP_Query( $args );
            ?>
            <div class="mptrs_order_page_wrap wrap">
                <div class="mptrs_orderDetailsDisplayHolder" id="mptrs_orderDetailsDisplayHolder"></div>
                <h1 class="mptrs_awesome-heading"><?php esc_html_e( 'Order List', 'tablely' ); ?></h1>

                <div class="mptrs_orderTypeContainer">
                    <?php if ( is_array($order_types) && count($order_types) > 0 ) : ?>
                        <div class="mptrs_order_type_item mptrs_active" data-filter="<?php echo __( 'all', 'tablely' ); ?>"><?php echo __( 'All', 'tablely' ); ?></div>
                        <?php foreach( $order_types as $key => $order_type ) : ?>
                            <div class="mptrs_order_type_item" data-filter="<?php echo esc_attr($order_type); ?>"><?php echo esc_attr($order_type); ?></div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <table class="mptrs_order_page_table">
                    <thead>
                    <tr>
                        <th><?php esc_html_e( 'Order', 'tablely' ); ?></th>
                        <th><?php esc_html_e( 'Order Type', 'tablely' ); ?></th>
                        <th><?php esc_html_e( 'Billing Name', 'tablely' ); ?></th>
                        <th><?php esc_html_e( 'Order Created Date', 'tablely' ); ?></th>
                        <th><?php esc_html_e( 'Booking Date', 'tablely' ); ?></th>
                        <th><?php esc_html_e( 'Booking Time', 'tablely' ); ?></th>
                        <th><?php esc_html_e( 'Service Status', 'tablely' ); ?></th>
                        <th><?php esc_html_e( 'Order Status', 'tablely' ); ?></th>
                        <th><?php esc_html_e( 'Total', 'tablely' ); ?></th>
                        <th style="display: none"><?php esc_html_e( 'Action', 'tablely' ); ?></th>
                        <th><?php esc_html_e( 'Order Info', 'tablely' ); ?></th>
                    </tr>
                    </thead>
                    <tbody id="order-list">
                    <?php if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post();
                        global $post;

                        $post_id = $post->ID;
                        $rbfw_order_id = get_post_meta( $post_id, '_mptrs_order_id', true );
                        $order = wc_get_order( $rbfw_order_id );
                        $billing_name = get_post_meta( $post_id, '_mptrs_customer_name', true );
                        $ordered_type = get_post_meta( $post_id, '_mptrs_order_type', true );
                        $ordered_type = isset( $order_types[$ordered_type] ) ? $order_types[$ordered_type] : '';
                        $status = ( $order ) ? $order->get_status() : get_post_meta( $post_id, '_mptrs_order_status', true );
                        $total_price = get_post_meta( $post_id, '_mptrs_order_total', true );
                        $ticket_infos = get_post_meta( $post_id, '_mptrs_ordered_food_menu', true );
                        $ticket_info_array = maybe_unserialize( $ticket_infos );
                        $rbfw_start_datetime = get_post_meta( $post_id, '_mptrs_order_date', true );
                        $rbfw_end_datetime = get_post_meta( $post_id, '_mptrs_order_time', true );
                        $rbfw_service_status = get_post_meta( $post_id, '_mptrs_service_status', true );
                        $rbfw_service_status = empty( $rbfw_service_status ) ? 'In progress' : $rbfw_service_status;
                        $rbfw_service_status_val = strtolower( str_replace( ' ', '_', $rbfw_service_status ) );
                        ?>
                        <tr class="mptrs_order_row" data-orderId="<?php echo esc_attr( $rbfw_order_id ); ?>" data-order_type_filter="<?php echo esc_html( $ordered_type ); ?>">
                            <td><?php echo esc_html( $rbfw_order_id ); ?></td>
                            <td><?php echo esc_html( $ordered_type ); ?></td>
                            <td><?php echo esc_html( $billing_name ); ?></td>
                            <td><?php echo esc_html( get_the_date( 'F j, Y' ) . ' ' . get_the_time() ); ?></td>
                            <td><?php echo esc_html( ! empty( $rbfw_start_datetime ) ? date_i18n( 'F j, Y', strtotime( $rbfw_start_datetime ) ) : '' ); ?></td>
                            <td>
                                <?php
                                if ( ! empty( $rbfw_end_datetime ) ) {
                                    $fullDateTime = $rbfw_end_datetime . ":00:00";
                                    $formattedTime = date_i18n( 'g A', strtotime( $fullDateTime ) );
                                    echo esc_html( $formattedTime );
                                }
                                ?>
                            </td>
                            <td>
                                <select name="mptrs_service_status" id="mptrsServiceStatus-<?php echo esc_attr( $post_id ); ?>" class="mptrs_service_status">
                                    <option value="in_progress" <?php selected( $rbfw_service_status_val, 'in_progress' ); ?>><?php esc_attr_e( 'In Progress', 'tablely' ); ?></option>
                                    <option value="done" <?php selected( $rbfw_service_status_val, 'done' ); ?>><?php esc_attr_e( 'Done', 'tablely' ); ?></option>
                                    <option value="service_out" <?php selected( $rbfw_service_status_val, 'service_out' ); ?>><?php esc_attr_e( 'Service Out', 'tablely' ); ?></option>
                                </select>
                            </td>
                            <td><span class="mptrs_order_status <?php echo esc_attr( $status ); ?>"><?php echo esc_html( $status ); ?></span></td>
                            <td><?php echo wp_kses_post( wc_price( $total_price ) ); ?></td>
                            <td><span class="mptrs_orderDetailsBtn"><?php esc_attr_e( 'Details', 'tablely' ); ?></span></td>
                        </tr>
                        <tr id="order-details-<?php echo esc_attr( $post_id ); ?>" class="order-details" style="display: none;">
                            <td colspan="12"><div class="order-details-content"></div></td>
                        </tr>
                    <?php endwhile; else : ?>
                        <tr><td colspan="12"><?php esc_html_e( 'Sorry, No data found!', 'tablely' ); ?></td></tr>
                    <?php endif; wp_reset_postdata(); ?>
                    </tbody>
                </table>

                <div id="loader" style="display: none;"><div class="loader"></div></div>

                <label for="posts-per-page"><?php esc_html_e( 'Posts per Page:', 'tablely' ); ?></label>

                <div id="mptrs_pagination" class="mptrs_pagination">
                    <?php
                    echo paginate_links( array(
                        'total'   => $query->max_num_pages,
                        'current' => $paged,
                        'format'  => '?paged=%#%',
                        'prev_text' => __('« Prev'),
                        'next_text' => __('Next »'),
                    ) );
                    ?>
                </div>
            </div>
            <?php
        }

        public function mptrs_new_food_menu_callback(){
            $existing_menus = get_option( '_mptrs_food_menu' );
            $menu_categories = get_option( 'mptrs_categories' );

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

        function mptrs_admin_menu_icons() {
            ?>
            <style>
                .menu-icon-mptrs_item .wp-submenu a[href$="mptrs_restaurant_lists"]::before {
                    content: "\f333";
                    margin-right: 6px;
                }

                .menu-icon-mptrs_item .wp-submenu a[href$="mptrs_new_food_menu"]::before {
                    content: "\1F374";
                    margin-right: 6px;
                }

                .menu-icon-mptrs_item .wp-submenu a[href$="mptrs_order"]::before {
                    content: "\1F4E6";
                    margin-right: 6px;
                }

                .menu-icon-mptrs_item .wp-submenu a[href$="mptrs_reserve"]::before {
                    content: "\1F4C5";
                    margin-right: 6px;
                }
            </style>
            <?php
        }


    }

    new MPTRS_Menu();
}