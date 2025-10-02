<?php

if ( ! class_exists('MPTRS_Restaurant_Seat_Mapping') ) {
    class MPTRS_Restaurant_Seat_Mapping {

        public function __construct() {
            add_action( 'admin_menu', [ $this, 'register_seat_mapping_submenu' ] );
        }

        public function register_seat_mapping_submenu() {
            add_submenu_page(
                'edit.php?post_type=mptrs_item',
                esc_html__( 'Seat Mapping', 'tablely' ),
                esc_html__( 'Seat Mapping', 'tablely' ),
                'manage_options',
                'mptrs_seat-mapping',
                array( $this, 'seat_mapping_tab_content' )
            );
        }

        public function seat_mapping_tab_content() {
            ?>
            <div class="wrap mptrs_create_seat_wrap">

                <h1 class="mptrs_create_seat_title"><?php esc_html_e( 'Seat Mapping', 'tablely' ); ?></h1>

                <!-- Add New Button -->
                <a href="<?php echo admin_url( 'post-new.php?post_type=mptrs_seat_mapping' ); ?>" class="button button-primary mptrs_create_seat_add_new">
                    <?php esc_html_e( 'Add New Seat Reservation', 'tablely' ); ?>
                </a>

                <!-- Search Field -->
                <form method="get" class="mptrs_create_seat_search_form" style="margin-top: 20px;">
                    <input type="hidden" name="post_type" value="mptrs_item">
                    <input type="hidden" name="page" value="mptrs_seat-mapping">
                    <input type="search" name="s" placeholder="Search seat mappings..." class="mptrs_create_seat_search_input" value="<?php echo isset($_GET['s']) ? esc_attr($_GET['s']) : ''; ?>" />
                    <button type="submit" class="button mptrs_create_seat_search_btn"><?php esc_html_e( 'Search', 'tablely' ); ?></button>
                </form>

                <!-- List Seat Mappings -->
                <div class="mptrs_create_seat_list_wrap" style="margin-top: 30px;">
                    <h2 class="mptrs_create_seat_list_title"><?php esc_html_e( 'Existing Seat Mappings', 'tablely' ); ?></h2>

                    <?php
                    $search_query = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '';

                    $args = array(
                        'post_type'      => 'mptrs_seat_mapping',
                        'posts_per_page' => -1,
                        's'              => $search_query,
                    );

                    $seat_mappings = new WP_Query( $args );

                    if ( $seat_mappings->have_posts() ) :
                        echo '<ul class="mptrs_create_seat_list">';

                        while ( $seat_mappings->have_posts() ) : $seat_mappings->the_post();
                            $edit_url = get_edit_post_link( get_the_ID() );
                            $delete_url = get_delete_post_link( get_the_ID(), '', true );
                            ?>
                            <li class="mptrs_create_seat_item" style="margin-bottom: 15px;">
                                <strong><?php the_title(); ?></strong><br>
                                <a href="<?php echo esc_url( $edit_url ); ?>" class="button mptrs_create_seat_edit_btn"><?php esc_html_e( 'Edit', 'tablely' ); ?></a>
                                <a href="<?php echo esc_url( $delete_url ); ?>" class="button mptrs_create_seat_delete_btn" onclick="return confirm('Are you sure you want to delete this seat mapping?');"><?php esc_html_e( 'Delete', 'tablely' ); ?></a>
                            </li>
                        <?php
                        endwhile;

                        echo '</ul>';
                        wp_reset_postdata();
                    else :
                        echo '<p class="mptrs_create_seat_no_results">' . esc_html__( 'No seat mappings found.', 'tablely' ) . '</p>';
                    endif;
                    ?>
                </div>
            </div>
            <?php
        }

    }

    new MPTRS_Restaurant_Seat_Mapping();
}