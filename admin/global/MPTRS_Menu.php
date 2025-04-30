<?php

if (!defined('ABSPATH')) {
    die;
} // Cannot access pages directly.
if (!class_exists('MPTRS_Menu')) {
    class MPTRS_Menu{
        public function __construct(){
            add_action( 'admin_menu', array( $this, 'added_menu_pages' ) );
        }

        public function added_menu_pages() {
            add_submenu_page( 'edit.php?post_type=mptrs_item', esc_html__( 'Restaurant Lists', 'tablely' ), esc_html__( 'Restaurant Lists', 'tablely' ), 'manage_options', 'mptrs_restaurant_lists', array( $this, 'mptrs_restaurant_lists_callback' ) );
            add_submenu_page( 'edit.php?post_type=mptrs_item', esc_html__( 'New Food Menu', 'tablely' ), esc_html__( 'New Food Menu', 'tablely' ), 'manage_options', 'mptrs_new_food_menu', array( $this, 'mptrs_new_food_menu_callback' ) );
            add_submenu_page( 'edit.php?post_type=mptrs_item', esc_html__( 'Order Lists', 'tablely' ), esc_html__( 'Order Lists', 'tablely' ), 'manage_options', 'mptrs_order', array( $this, 'mptrs_all_order_callback' ) );
            add_submenu_page( 'edit.php?post_type=mptrs_item', esc_html__( 'Table Reserve Lists', 'tablely' ), esc_html__( 'Table Reserve Lists', 'tablely' ), 'manage_options', 'mptrs_reserve', array( $this, 'mptrs_table_reserved_callback' ) );
        }

        public function mptrs_restaurant_lists_callback() {
            // Get current page
            $order_display_limit = (int) get_option('mptrs_order_lists_display_limit', 20);

            if (isset($_GET['_wpnonce']) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'mptrs_pagination')) {
                $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
            } else {
                $paged = 1;
            }

            $args = array(
                'post_type'      => 'mptrs_item',
                'order'          => 'DESC',
                'posts_per_page' => $order_display_limit,
                'paged'          => $paged,
            );

            $query = new WP_Query($args);

            ?>
            <div class="mptrs_restaurant_page_wrap wrap">
                <div class="mptrs_header_section">
                    <h1 class="mptrs_page_title"><?php esc_html_e('Restaurant Lists', 'tablely'); ?></h1>

                    <a href="<?php echo esc_url(site_url('/wp-admin/post-new.php?post_type=mptrs_item')); ?>" class="mptrs_add_button">
                        <i class="fas fa-plus"></i>
                        <?php esc_html_e('Add New Restaurant', 'tablely'); ?>
                    </a>
                </div>

                <?php
                if ($query->have_posts()) :
                    ?>
                    <div class="mptrs_filters_section">
                        <!-- Filter controls will be added by JS -->
                    </div>

                    <div class="mptrs_restaurant_list_table_container">
                        <table class="mptrs_restaurant_list_table">
                            <thead>
                                <tr>
                                    <th class="mptrs_col_image"><?php esc_html_e('Image', 'tablely'); ?></th>
                                    <th class="mptrs_col_name"><?php esc_html_e('Restaurant Name', 'tablely'); ?></th>
                                    <th class="mptrs_col_address"><?php esc_html_e('Address', 'tablely'); ?></th>
                                    <th class="mptrs_col_description"><?php esc_html_e('Description', 'tablely'); ?></th>
                                    <th class="mptrs_col_status"><?php esc_html_e('Status', 'tablely'); ?></th>
                                    <th class="mptrs_col_actions"><?php esc_html_e('Actions', 'tablely'); ?></th>
                                </tr>
                            </thead>
                            <tbody id="mptrs-restaurant-list">
                        <?php
                        while ($query->have_posts()) :
                            $query->the_post();
                            global $post;

                            $post_id = $post->ID;
                                    $thumbnail_url = get_the_post_thumbnail_url($post_id, 'thumbnail');
                            if (empty($thumbnail_url)) {
                                $thumbnail_url = esc_url(MPTRS_Plan_ASSETS . 'images/fast-food.png');
                            }
                            
                            // Get a short excerpt from the content
                            $excerpt = wp_trim_words(get_the_content(), 20, '...');
                                    
                                    // Get restaurant address
                                    $address = get_post_meta($post_id, '_mptrs_restaurant_address', true);
                                    if (empty($address)) {
                                        $address = get_post_meta($post_id, '_restaurant_address', true);
                                    }
                                    
                                    // Get restaurant status
                                    $status = get_post_meta($post_id, '_mptrs_restaurant_status', true);
                                    $status_class = 'status-active';
                                    $status_text = __('Active', 'tablely');
                                    
                                    if ($status === 'closed') {
                                        $status_class = 'status-closed';
                                        $status_text = __('Closed', 'tablely');
                                    } elseif ($status === 'temporary-closed') {
                                        $status_class = 'status-temp-closed';
                                        $status_text = __('Temporarily Closed', 'tablely');
                                    }
                                    
                                    $post_status = get_post_status($post_id);
                                    if ($post_status !== 'publish') {
                                        $status_class = 'status-draft';
                                        $status_text = __('Draft', 'tablely');
                                    }
                                    ?>
                                    <tr class="mptrs_restaurant_row" data-search="<?php echo esc_attr(strtolower(get_the_title() . ' ' . $address)); ?>">
                                        <td class="mptrs_col_image">
                                            <div class="mptrs_restaurant_img_container">
                                    <img src="<?php echo esc_attr($thumbnail_url); ?>" alt="<?php the_title(); ?>" class="mptrs_restaurant_image" />
                                </div>
                                        </td>
                                        <td class="mptrs_col_name">
                                            <div class="mptrs_restaurant_name">
                                                <?php the_title(); ?>
                                            </div>
                                        </td>
                                        <td class="mptrs_col_address">
                                            <?php if (!empty($address)) : ?>
                                                <div class="mptrs_restaurant_address">
                                                    <i class="fas fa-map-marker-alt"></i> <?php echo esc_html($address); ?>
                                </div>
                                            <?php else : ?>
                                                <span class="mptrs_no_address"><?php esc_html_e('No address available', 'tablely'); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="mptrs_col_description">
                                            <div class="mptrs_restaurant_excerpt"><?php echo wp_kses_post($excerpt); ?></div>
                                        </td>
                                        <td class="mptrs_col_status">
                                            <span class="mptrs_restaurant_status <?php echo esc_attr($status_class); ?>"><?php echo esc_html($status_text); ?></span>
                                        </td>
                                        <td class="mptrs_col_actions">
                                            <div class="mptrs_restaurant_actions">
                                                <a class="mptrs_action_btn mptrs_view_btn" href="<?php echo esc_url(get_permalink(get_the_ID())); ?>" title="<?php esc_attr_e('View Restaurant', 'tablely'); ?>">
                                                    <i class="fas fa-eye"></i>
                                    </a>
                                                <a class="mptrs_action_btn mptrs_edit_btn" href="<?php echo esc_url(get_edit_post_link(get_the_ID())); ?>" title="<?php esc_attr_e('Edit Restaurant', 'tablely'); ?>">
                                                    <i class="fas fa-edit"></i>
                                    </a>
                                                <a class="mptrs_action_btn mptrs_delete_btn" href="<?php echo get_delete_post_link(get_the_ID()); ?>" onclick="return confirm('<?php esc_attr_e('Are you sure you want to move this to trash?', 'tablely'); ?>')" title="<?php esc_attr_e('Delete Restaurant', 'tablely'); ?>">
                                                    <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                                        </td>
                                    </tr>
                        <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="mptrs_display_limit_wrap">
                        <label for="mptrs_ordersPerPage"><?php esc_html_e('Restaurants per Page:', 'tablely'); ?></label>
                        <input type="number" id="mptrs_ordersPerPage" class="mptrs_ordersPerPage" value="<?php echo esc_attr($order_display_limit); ?>" min="1" max="100">
                    </div>

                    <!-- Pagination -->
                    <div class="mptrs_pagination">
                        <?php
                        echo wp_kses_post(paginate_links(array(
                            'total'     => $query->max_num_pages,
                            'current'   => $paged,
                            'format'    => '?paged=%#%',
                            'prev_text' => __('« Prev', 'tablely'),
                            'next_text' => __('Next »', 'tablely'),
                            'add_args'  => array(
                                '_wpnonce' => wp_create_nonce('mptrs_pagination'),
                            ),
                        )));
                        ?>
                    </div>

                    <script>
                    jQuery(document).ready(function($) {
                        // Search functionality for restaurants
                        $('#mptrsRestaurantSearch').on('keyup', function() {
                            const searchTerm = $(this).val().toLowerCase();
                            
                            $('.mptrs_restaurant_row').each(function() {
                                const rowData = $(this).data('search').toLowerCase();
                                if (rowData.indexOf(searchTerm) > -1) {
                                    $(this).show();
                                } else {
                                    $(this).hide();
                                }
                            });
                            
                            if ($('.mptrs_restaurant_row:visible').length === 0) {
                                if ($('#mptrs-no-results').length === 0) {
                                    $('#mptrs-restaurant-list').append('<tr id="mptrs-no-results"><td colspan="6" class="mptrs_empty_state"><p><?php esc_html_e('No restaurants match your search.', 'tablely'); ?></p></td></tr>');
                                }
                            } else {
                                $('#mptrs-no-results').remove();
                            }
                        });
                        
                        // Button click trigger for search
                        $('#mptrsRestaurantSearchBtn').on('click', function() {
                            $('#mptrsRestaurantSearch').trigger('keyup');
                        });
                        
                        // Update display limit
                        $('#mptrs_ordersPerPage').on('change', function() {
                            const limit = $(this).val();
                            
                            $.ajax({
                                url: ajaxurl,
                                type: 'POST',
                                data: {
                                    action: 'mptrs_update_display_limit',
                                    limit: limit,
                                    nonce: '<?php echo wp_create_nonce('mptrs_admin_nonce'); ?>',
                                    type: 'restaurant'
                                },
                                success: function(response) {
                                    if (response.success) {
                                        window.location.reload();
                                    }
                                }
                            });
                        });
                    });
                    </script>

                <?php else : ?>
                    <div class="mptrs_empty_state">
                        <p><?php esc_html_e('No restaurants found.', 'tablely'); ?></p>
                        <a href="<?php echo esc_url(site_url('/wp-admin/post-new.php?post_type=mptrs_item')); ?>" class="mptrs_add_button">
                            <i class="fas fa-plus"></i>
                            <?php esc_html_e('Add Your First Restaurant', 'tablely'); ?>
                        </a>
                    </div>
                <?php endif;

                wp_reset_postdata();
                ?>
            </div>
            <?php
        }


        public function mptrs_table_reserved_callback() {

            $order_display_limit = (int) get_option('mptrs_order_lists_display_limit', 20);

            if (isset($_GET['_wpnonce']) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'mptrs_pagination')) {
                $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
            } else {
                $paged = 1;
            }

            $args = array(
                'post_type'      => 'mptrs_table_reserve',
                'order'          => 'DESC',
                'posts_per_page' => $order_display_limit,
                'paged'          => $paged,
            );

            $query = new WP_Query($args);
            $total_pages = $query->max_num_pages;
            ?>

            <div class="mptrs_reserve_page_wrap wrap">
                <div class="mptrs_header_section">
                    <h1 class="mptrs_page_title"><?php esc_html_e('Table Reserve List', 'tablely'); ?></h1>
                </div>
                
                <div class="mptrs_filters_section">
                    <div class="mptrs_search_container">
                        <input type="text" id="mptrsReserveSearch" placeholder="<?php esc_attr_e('Search by name, occasion or seats', 'tablely'); ?>">
                        <button id="mptrsReserveSearchBtn"><i class="dashicons dashicons-search"></i></button>
                    </div>
                    
                    <div class="mptrs_filters_dropdown">
                        <select id="mptrsStatusFilter">
                            <option value="all"><?php esc_html_e('All Status', 'tablely'); ?></option>
                            <option value="0"><?php esc_html_e('In Progress', 'tablely'); ?></option>
                            <option value="1"><?php esc_html_e('Reserved', 'tablely'); ?></option>
                        </select>
                    </div>
                    
                    <div class="mptrs_filters_dropdown">
                        <select id="mptrsDateFilter">
                            <option value="all"><?php esc_html_e('All Dates', 'tablely'); ?></option>
                            <option value="today"><?php esc_html_e('Today', 'tablely'); ?></option>
                            <option value="tomorrow"><?php esc_html_e('Tomorrow', 'tablely'); ?></option>
                            <option value="week"><?php esc_html_e('This Week', 'tablely'); ?></option>
                            <option value="month"><?php esc_html_e('This Month', 'tablely'); ?></option>
                        </select>
                    </div>
                </div>

                <table class="mptrs_reserve_table">
                    <thead>
                    <tr>
                        <th><?php esc_html_e('Occasion', 'tablely'); ?></th>
                        <th><?php esc_html_e('Total guests', 'tablely'); ?></th>
                        <th><?php esc_html_e('Reserve date', 'tablely'); ?></th>
                        <th><?php esc_html_e('Reserve time', 'tablely'); ?></th>
                        <th><?php esc_html_e('Seats', 'tablely'); ?></th>
                        <th><?php esc_html_e('Guest Name', 'tablely'); ?></th>
                        <th><?php esc_html_e('Phone', 'tablely'); ?></th>
                        <th><?php esc_html_e('Email', 'tablely'); ?></th>
                        <th><?php esc_html_e('Notes', 'tablely'); ?></th>
                        <th><?php esc_html_e('Status', 'tablely'); ?></th>
                    </tr>
                    </thead>
                    <tbody id="mptrs-reserve-list">
                    <?php
                    if ($query->have_posts()) :
                        $today = date('Y-m-d');
                        
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
                                $seatNames = rtrim($seatNames, ', ');
                            }

                            $reserve_date = $table_reservation_info['reserve_date'];
                            $formattedDate = gmdate('jS F Y', strtotime($reserve_date));
                            $reserve_date_ymd = gmdate('Y-m-d', strtotime($reserve_date));
                            
                            // Determine if this is today's reservation
                            $row_class = '';
                            if ($reserve_date_ymd === $today) {
                                $row_class = 'mptrs_today_row';
                            } elseif ($reserve_date_ymd < $today) {
                                $row_class = 'mptrs_past_row';
                            }
                            
                            $status_class = $reservation_status == 1 ? 'mptrs_status_reserved' : 'mptrs_status_in_progress';
                            $status_text = $reservation_status == 1 ? __('Reserved', 'tablely') : __('In Progress', 'tablely');
                            ?>
                            <tr class="mptrs_reserve_row <?php echo esc_attr($row_class); ?>" 
                                data-date="<?php echo esc_attr($reserve_date_ymd); ?>" 
                                data-status="<?php echo esc_attr($reservation_status); ?>"
                                data-search="<?php echo esc_attr($table_reservation_info['userName'] . ' ' . $table_reservation_info['occasion'] . ' ' . $seatNames); ?>">
                                
                                <td><?php echo esc_html($table_reservation_info['occasion']); ?></td>
                                <td><?php echo esc_html($table_reservation_info['guests']); ?></td>
                                <td data-sort="<?php echo esc_attr(strtotime($reserve_date)); ?>"><?php echo esc_html($formattedDate); ?></td>
                                <td><?php echo esc_html($table_reservation_info['reserve_time']); ?></td>
                                <td class="mptrs_truncate mptrs_tooltip" data-tooltip="<?php echo esc_attr($seatNames); ?>"><?php echo esc_html($seatNames); ?></td>
                                <td><?php echo esc_html($table_reservation_info['userName']); ?></td>
                                <td><?php echo esc_html($table_reservation_info['userPhoneNum']); ?></td>
                                <td><?php echo esc_html($table_reservation_info['userEmailId']); ?></td>
                                <td class="mptrs_truncate mptrs_tooltip" data-tooltip="<?php echo esc_attr($table_reservation_info['userAdvice']); ?>">
                                    <?php echo esc_html($table_reservation_info['userAdvice']); ?>
                                </td>
                                <td>
                                    <div class="mptrs_status_container">
                                        <span class="mptrs_status_badge <?php echo esc_attr($status_class); ?>"><?php echo esc_html($status_text); ?></span>
                                        <select name="mptrs_reserved_status" id="mptrsReservedStatus-<?php echo esc_attr($post_id); ?>" class="mptrs_reserved_status" data-post-id="<?php echo esc_attr($post_id); ?>">
                                            <option value="0" <?php selected($reservation_status, 0); ?>><?php esc_html_e('In Progress', 'tablely'); ?></option>
                                            <option value="1" <?php selected($reservation_status, 1); ?>><?php esc_html_e('Reserved', 'tablely'); ?></option>
                                        </select>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile;
                        wp_reset_postdata();
                    else : ?>
                        <tr>
                            <td colspan="10" class="mptrs_empty_state">
                                <p><?php esc_html_e('No reservations found.', 'tablely'); ?></p>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>

                <div class="mptrs_display_limit_wrap">
                    <label for="mptrs_ordersPerPage"><?php esc_html_e('Reservations per Page:', 'tablely'); ?></label>
                    <input type="number" id="mptrs_ordersPerPage" class="mptrs_ordersPerPage" value="<?php echo esc_attr($order_display_limit); ?>" min="1" max="100">
                </div>

                <?php
                // Pagination
                if ($total_pages > 1) {
                    echo '<div class="mptrs_pagination">';
                    echo wp_kses_post(paginate_links(array(
                        'base'      => add_query_arg('paged', '%#%'),
                        'format'    => '',
                        'current'   => $paged,
                        'total'     => $total_pages,
                        'prev_text' => __('« Prev', 'tablely'),
                        'next_text' => __('Next »', 'tablely'),
                        'add_args'  => array(
                            '_wpnonce' => wp_create_nonce('mptrs_pagination'),
                        ),
                    )));
                    echo '</div>';
                }
                ?>
            </div>

            <script>
            jQuery(document).ready(function($) {
                // Search functionality
                $('#mptrsReserveSearch').on('keyup', function() {
                    const searchTerm = $(this).val().toLowerCase();
                    filterReservations();
                });
                
                // Status filter functionality
                $('#mptrsStatusFilter').on('change', function() {
                    filterReservations();
                });
                
                // Date filter functionality
                $('#mptrsDateFilter').on('change', function() {
                    filterReservations();
                });
                
                // Function to filter reservations based on all filters
                function filterReservations() {
                    const searchTerm = $('#mptrsReserveSearch').val().toLowerCase();
                    const statusFilter = $('#mptrsStatusFilter').val();
                    const dateFilter = $('#mptrsDateFilter').val();
                    
                    let today = new Date();
                    today = new Date(today.getFullYear(), today.getMonth(), today.getDate());
                    
                    let tomorrow = new Date(today);
                    tomorrow.setDate(tomorrow.getDate() + 1);
                    
                    let weekEnd = new Date(today);
                    weekEnd.setDate(weekEnd.getDate() + 7);
                    
                    let monthEnd = new Date(today);
                    monthEnd.setMonth(monthEnd.getMonth() + 1);
                    
                    $('.mptrs_reserve_row').each(function() {
                        let show = true;
                        
                        // Search filter
                        if (searchTerm) {
                            if ($(this).data('search').toLowerCase().indexOf(searchTerm) === -1) {
                                show = false;
                            }
                        }
                        
                        // Status filter
                        if (statusFilter !== 'all' && $(this).data('status') != statusFilter) {
                            show = false;
                        }
                        
                        // Date filter
                        if (dateFilter !== 'all') {
                            const rowDate = new Date($(this).data('date'));
                            
                            if (dateFilter === 'today' && rowDate.toDateString() !== today.toDateString()) {
                                show = false;
                            } else if (dateFilter === 'tomorrow' && rowDate.toDateString() !== tomorrow.toDateString()) {
                                show = false;
                            } else if (dateFilter === 'week' && (rowDate < today || rowDate > weekEnd)) {
                                show = false;
                            } else if (dateFilter === 'month' && (rowDate < today || rowDate > monthEnd)) {
                                show = false;
                            }
                        }
                        
                        $(this).toggle(show);
                    });
                    
                    if ($('.mptrs_reserve_row:visible').length === 0) {
                        if ($('#mptrs-no-results').length === 0) {
                            $('#mptrs-reserve-list').append('<tr id="mptrs-no-results"><td colspan="10" class="mptrs_empty_state"><p><?php esc_html_e('No reservations match your filters.', 'tablely'); ?></p></td></tr>');
                        }
                    } else {
                        $('#mptrs-no-results').remove();
                    }
                }
                
                // Status change handling
                $('.mptrs_reserved_status').on('change', function() {
                    const postId = $(this).data('post-id');
                    const status = $(this).val();
                    const $row = $(this).closest('tr');
                    const $statusBadge = $row.find('.mptrs_status_badge');
                    
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'mptrs_save_table_reserved_status_update',
                            nonce: '<?php echo esc_attr( wp_create_nonce('mptrs_admin_nonce') ); ?>',
                            post_id: postId,
                            selectedVal: status
                        },
                        success: function(response) {
                            if (response.data && response.data.success) {
                                // Update the status badge
                                $statusBadge.removeClass('mptrs_status_in_progress mptrs_status_reserved')
                                           .addClass(status == 1 ? 'mptrs_status_reserved' : 'mptrs_status_in_progress')
                                           .text(status == 1 ? '<?php esc_html_e('Reserved', 'tablely'); ?>' : '<?php esc_html_e('In Progress', 'tablely'); ?>');
                                
                                // Update the data attribute for filtering
                                $row.attr('data-status', status);
                            }
                        }
                    });
                });
            });
            </script>
            <?php
        }


        public function mptrs_all_order_callback( $key ) {
//            $order_display_limit = get_option( 'mptrs_order_lists_display_limit' );
            $order_display_limit = (int) get_option('mptrs_order_lists_display_limit', 20);

            $order_types = array(
                'dine_in'   => 'Dine In',
                'delivery'  => 'Delivery',
                'take_away' => 'Takeaway',
            );

            if (isset($_GET['_wpnonce']) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'mptrs_pagination')) {
                $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
            } else {
                $paged = 1;
            }

            $args = array(
                'post_type'      => 'mptrs_order',
                'order'          => 'DESC',
                'posts_per_page' => $order_display_limit,
                'paged'          => $paged,
            );

            $query = new WP_Query( $args );


            ?>
            <div class="mptrs_order_page_wrap wrap">
                <div class="mptrs_orderDetailsDisplayHolder" id="mptrs_orderDetailsDisplayHolder"></div>
                <h1 class="mptrs_awesome-heading"><?php esc_html_e( 'Order List', 'tablely' ); ?></h1>

                <div class="mptrs_orderTypeContainer">
                    <?php if ( is_array($order_types) && count($order_types) > 0 ) : ?>
                        <div class="mptrs_order_type_item mptrs_active" data-filter="all"><?php esc_html_e( 'All', 'tablely' ); ?></div>
                        <?php foreach( $order_types as $key => $order_type ) : ?>
                            <div class="mptrs_order_type_item" data-filter="<?php echo esc_attr($order_type); ?>"><?php echo esc_attr($order_type); ?></div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <div class="mptrs_search_container">
                        <input type="text" id="mptrsOrderSearch" placeholder="<?php esc_attr_e('Search order ID, customer, etc', 'tablely'); ?>">
                        <button id="mptrsOrderSearchBtn"><i class="dashicons dashicons-search"></i></button>
                    </div>
                    
                    <select class="mptrs_time_filter" id="mptrsTimeFilter">
                        <option value="this_week"><?php esc_html_e('This Week', 'tablely'); ?></option>
                        <option value="this_month"><?php esc_html_e('This Month', 'tablely'); ?></option>
                        <option value="last_month"><?php esc_html_e('Last Month', 'tablely'); ?></option>
                        <option value="all_time"><?php esc_html_e('All Time', 'tablely'); ?></option>
                    </select>
                </div>

                <table class="mptrs_order_page_table">
                    <thead>
                    <tr>
                        <th width="20"><input type="checkbox" class="mptrs_checkbox" id="selectAllOrders"></th>
                        <th><?php esc_html_e( 'Order ID', 'tablely' ); ?></th>
                        <th><?php esc_html_e( 'Date', 'tablely' ); ?></th>
                        <th><?php esc_html_e( 'Customer', 'tablely' ); ?></th>
                        <th><?php esc_html_e( 'Order Type', 'tablely' ); ?></th>
                        <th><?php esc_html_e( 'Address', 'tablely' ); ?></th>
                        <th><?php esc_html_e( 'Qty', 'tablely' ); ?></th>
                        <th><?php esc_html_e( 'Amount', 'tablely' ); ?></th>
                        <th><?php esc_html_e( 'Status', 'tablely' ); ?></th>
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
                        
                        // Get order quantity
                        $quantity = 0;
                        if (is_array($ticket_info_array)) {
                            foreach ($ticket_info_array as $item) {
                                if (isset($item['quantity'])) {
                                    $quantity += (int)$item['quantity'];
                                }
                            }
                        }
                        
                        // Get customer address if available
                        $customer_address = '-';
                        if ($order) {
                            $address = $order->get_billing_address_1();
                            if (!empty($address)) {
                                $customer_address = $address;
                            }
                        }
                        
                        // Format date in a more readable way
                        $formatted_date = '';
                        if (!empty($rbfw_start_datetime)) {
                            $date_obj = new DateTime($rbfw_start_datetime);
                            $formatted_date = $date_obj->format('Y-m-d');
                            $time = !empty($rbfw_end_datetime) ? $rbfw_end_datetime : '';
                            $formatted_date .= !empty($time) ? ' ' . $time : '';
                        }
                        
                        // Get order type CSS class
                        $order_type_class = '';
                        if ($ordered_type === 'Takeaway') {
                            $order_type_class = 'takeaway';
                        } elseif ($ordered_type === 'Delivery') {
                            $order_type_class = 'online';
                        } elseif ($ordered_type === 'Dine In') {
                            $order_type_class = 'dine-in';
                        }
                        
                        ?>
                        <tr class="mptrs_order_row" data-orderId="<?php echo esc_attr( $rbfw_order_id ); ?>" data-order_type_filter="<?php echo esc_html( $ordered_type ); ?>">
                            <td><input type="checkbox" class="mptrs_checkbox order-checkbox"></td>
                            <td><?php echo esc_html( $rbfw_order_id ); ?></td>
                            <td>
                                <?php echo esc_html( $formatted_date ); ?>
                            </td>
                            <td><?php echo esc_html( $billing_name ); ?></td>
                            <td>
                                <span class="order-type-indicator <?php echo esc_attr($order_type_class); ?>">
                                    <?php echo esc_html( $ordered_type ); ?>
                                </span>
                            </td>
                            <td><?php echo esc_html( $customer_address ); ?></td>
                            <td><?php echo esc_html( $quantity ); ?></td>
                            <td><?php echo wp_kses_post( wc_price( $total_price ) ); ?></td>
                            <td>
                                <?php if ($rbfw_service_status_val === 'done'): ?>
                                    <span class="mptrs_order_status completed status-toggle"><?php esc_html_e('Completed', 'tablely'); ?></span>
                                <?php elseif ($rbfw_service_status_val === 'service_out'): ?>
                                    <span class="mptrs_order_status cancelled status-toggle"><?php esc_html_e('Cancelled', 'tablely'); ?></span>
                                <?php else: ?>
                                    <span class="mptrs_order_status on-process status-toggle"><?php esc_html_e('On Process', 'tablely'); ?></span>
                                <?php endif; ?>
                                <select name="mptrs_service_status" id="mptrsServiceStatus-<?php echo esc_attr( $post_id ); ?>" class="mptrs_service_status" style="display: none;">
                                    <option value="in_progress" <?php selected( $rbfw_service_status_val, 'in_progress' ); ?>><?php esc_html_e('On Process', 'tablely'); ?></option>
                                    <option value="done" <?php selected( $rbfw_service_status_val, 'done' ); ?>><?php esc_html_e('Completed', 'tablely'); ?></option>
                                    <option value="service_out" <?php selected( $rbfw_service_status_val, 'service_out' ); ?>><?php esc_html_e('Cancelled', 'tablely'); ?></option>
                                </select>
                                <span class="mptrs_orderDetailsBtn" id="<?php echo esc_attr( $post_id )?>"><?php esc_attr_e( 'Details', 'tablely' ); ?></span>
                            </td>
                        </tr>
                    <?php endwhile; else : ?>
                        <tr><td colspan="9"><?php esc_html_e( 'Sorry, No data found!', 'tablely' ); ?></td></tr>
                    <?php endif; wp_reset_postdata(); ?>
                    </tbody>
                </table>

                <div id="loader" style="display: none;"><div class="loader"></div></div>

                <div class="mptrs_ordersPerPage_container">
                    <label for="mptrs_ordersPerPage"><?php esc_html_e( 'Posts per Page:', 'tablely' ); ?></label>
                    <input type="number" id="mptrs_ordersPerPage" class="mptrs_ordersPerPage" value="<?php echo esc_attr( $order_display_limit );?>" placeholder="Limit 20">
                </div>

                <div id="mptrs_pagination" class="mptrs_pagination">
                    <?php
                    echo wp_kses_post( paginate_links( array(
                        'total'     => $query->max_num_pages,
                        'current'   => $paged,
                        'format'    => '?paged=%#%',
                        'prev_text' => __( '« Prev', 'tablely' ),
                        'next_text' => __( 'Next »', 'tablely' ),
                        'add_args'  => array(
                            '_wpnonce' => wp_create_nonce('mptrs_pagination'),
                        ),
                    ) ) );
                    ?>
                </div>
            </div>
            <?php
        }

        public function mptrs_new_food_menu_callback(){
            $existing_menus = get_option( '_mptrs_food_menu' );
            $menu_categories = get_option( 'mptrs_categories' );
            $total_menus = 0;
            $display_limit = (int) get_option('mptrs_food_menu_display_limit', 20);

            if( is_array( $existing_menus ) ){
                $total_menus = count( $menu_categories );
            }

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
                        <input type="number" id="mptrs_displayMenuCount" class="mptrs_setDisplayLimit" value="<?php echo esc_attr( $display_limit )?>" placeholder="Display Limit 20">
                        <button id="mptrs_openCategoryPopup" class="mptrs_open_popup_btn"><i class="fas fa-tags"></i> <?php esc_attr_e( 'Categories', 'tablely' ) ?></button>
                        <button id="mptrs_openPopup" class="mptrs_open_popup_btn"><i class="fas fa-plus"></i> <?php esc_attr_e( 'Add New Food Menu', 'tablely' ) ?> </button>
                    </div>
                    <div class="mptrs_foodMenuContentHolder">

                        <div id="mptrs_foodMenuShowContainer" class="mptrs_foodMenuContainer" style="display: block">
                            <div id="mptrs_allFoodMenu" class="mptrs_allFoodMenu">
                                <div class="mptrs_categoryFilterHolder">
                                    <div class="mptrs_categoryFilter active" data-filter="all"><?php esc_attr_e( 'All', 'tablely' ) ?></div>
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
                                        <th class="mptrsTableTh mptrsThImage"><?php esc_attr_e( 'Image', 'tablely' ) ?></th>
                                        <th class="mptrsTableTh mptrsThName"><?php esc_attr_e( 'Name', 'tablely' ) ?></th>
                                        <th class="mptrsTableTh mptrsThcategory"><?php esc_attr_e( 'Category', 'tablely' ) ?></th>
                                        <th class="mptrsTableTh mptrsThPrice"><?php esc_attr_e( 'Price', 'tablely' ) ?></th>
                                        <th class="mptrsTableTh mptrsThServes"><?php esc_attr_e( 'Serves', 'tablely' ) ?></th>
                                        <th class="mptrsTableTh mptrsThActions"><?php esc_attr_e( 'Actions', 'tablely' ) ?></th>
                                    </tr>
                                    </thead>
                                    <tbody id="mptrs_foodMenuContainer">
                                    <?php
                                    if( is_array( $existing_menus ) && !empty( $existing_menus ) ) {
                                        foreach ( $existing_menus as $key => $existing_menu ){
                                            $category = isset( $menu_categories[$existing_menu['menuCategory']]) ? $menu_categories[$existing_menu['menuCategory']] : '';
                                            ?>
                                            <tr class="mptrsTableRow" data-category ="<?php echo esc_attr( $existing_menu['menuCategory'] )?>" id="mptrs_foodMenuContent<?php echo esc_attr( $key )?>" style="display: none">
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
                                                    <div class="mptrs_memuPrice" id="mptrs_memuPrice<?php echo esc_attr( $key )?>"><i class="fas fa-dollar-sign"></i> <?php echo esc_html($existing_menu['menuPrice']); ?></div>
                                                </td>
                                                <td class="mptrsTableTd mptrsTdServes" >
                                                    <div class="mptrs_menuPersion" id="mptrs_memuPersons<?php echo esc_attr( $key )?>"><i class="fas fa-user-alt"></i> <?php echo esc_attr( $existing_menu['numPersons'] );?></div>
                                                </td>
                                                <td class="mptrsTableTd mptrsTdActions">
                                                    <div class="mptrs_BottomAllMenuInFo">
                                                        <span class="mptrm_editFoodMenu" id="mptrsEditMenu_<?php echo esc_attr( $key )?>"><i class="far fa-edit"></i></span>
                                                        <span class="mptrm_deleteFoodMenu" id="mptrsDeleteMenu_<?php echo esc_attr( $key )?>"><i class="far fa-trash-alt"></i></span>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                                <?php if( $total_menus >= $display_limit ){?>
                                    <div class="mptrs_LoadMoreMenuHolder" id="mptrs_LoadMoreMenuHolder" style="display: none">
                                        <span class="mptrs_LoadMoreMenuText"><i class="fas fa-sync-alt"></i> <?php esc_attr_e( 'Load More Menu', 'tablely' ); ?></span>
                                    </div>
                                <?php }?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
    }

    new MPTRS_Menu();
}