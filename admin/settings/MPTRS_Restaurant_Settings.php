<?php

/**
 * @author Md Rubel Mia <rubelcuet10@gmail.com>
 * @license mage-people.com
 * @var 1.0.0
 */

if (!defined('ABSPATH'))
    die;
if ( !class_exists('MPTRS_Restaurant_Settings' ) ) {
    class MPTRS_Restaurant_Settings{
        public function __construct() {
            add_action('add_mptrs_settings_tab_content', [$this, 'restaurant_settings_tab']);
        }

        public function restaurant_settings_tab(){
            $menu_display_limit = get_option( 'mptrs_menu_display_limit' );

            $seat_mapping_info = get_option( 'mptrs_seat_mapping_info' );
            $mptrs_box_size = isset($seat_mapping_info[ 'mptrs_box_size' ]) ? $seat_mapping_info[ 'mptrs_box_size' ] : 35;
            $mptrs_num_of_rows = isset($seat_mapping_info[ 'mptrs_num_of_rows' ]) ? $seat_mapping_info[ 'mptrs_num_of_rows' ] : 30;
            $mptrs_num_of_columns = isset($seat_mapping_info[ 'mptrs_num_of_columns' ]) ? $seat_mapping_info[ 'mptrs_num_of_columns' ] : 20;

            $menu_display_limit = !empty($menu_display_limit) ? $menu_display_limit : 20;
            ?>
            <div class="tabsItem mptrs_restaurant_settings" data-tabs="#mptrs_restaurant_Settings">

                <section class="mptrs_section mptrs_restaurant_menu_set_limit_section">
                    <div class="mptrs_section_header">
                        <p><?php esc_html_e('Set Display Food Menu Limit', 'tablely'); ?> <span class="mptrs_text_required">*</span></p>
                    </div>
                    <div class="mptrs_input_limit_group">
                        <label for="mptrs_menu_display_limit" class="mptrs_label"><?php esc_html_e('Menu Display Limit', 'tablely'); ?></label>
                        <input type="number" id="mptrs_menu_display_limit" name="mptrs_menu_display_limit" class="mptrs_input mptrs_menu_display_limit" value="<?php echo esc_attr($menu_display_limit); ?>" min="1" />
                    </div>
                </section>

                <section class="mptrs_restaurant_seat_mapping_section" >
                    <div class="mptrs_label_wrapper">
                        <div class="mptrs_label_header">
                            <p><?php esc_html_e('Set Seat Mapping Settings', 'tablely'); ?> <span class="mptrs_text_required">*</span></p>
                        </div>

                        <div class="mptrs_setting_row">
                            <label for="mptrs_seat_map_box_size" class="mptrs_label"><?php esc_html_e('Seat Box Size', 'tablely'); ?></label>
                            <input type="number" id="mptrs_seat_map_box_size" name="mptrs_seat_map_box_size" class="mptrs_input mptrs_seat_map_box_size" value="<?php echo esc_attr( $mptrs_box_size ); ?>" />
                        </div>

                        <div class="mptrs_setting_row">
                            <label for="mptrs_seat_num_of_rows" class="mptrs_label"><?php esc_html_e('Number of Rows', 'tablely'); ?></label>
                            <input type="number" id="mptrs_seat_num_of_rows" name="mptrs_seat_num_of_rows" class="mptrs_input mptrs_seat_num_of_rows" value="<?php echo esc_attr( $mptrs_num_of_rows ); ?>" />
                        </div>

                        <div class="mptrs_setting_row">
                            <label for="mptrs_seat_num_of_columns" class="mptrs_label"><?php esc_html_e('Number of Columns', 'tablely'); ?></label>
                            <input type="number" id="mptrs_seat_num_of_columns" name="mptrs_seat_num_of_columns" class="mptrs_input mptrs_seat_num_of_columns" value="<?php echo esc_attr( $mptrs_num_of_columns ); ?>" />
                        </div>

                        <div class="mptrs_submit_wrapper">
                            <button type="submit" class="mptrs_set_seat_mapping_info"><?php esc_html_e('Save Seat Mapping Data', 'tablely'); ?></button>
                        </div>
                    </div>

                </section>
            </div>



        <?php
        }
    }


}
new MPTRS_Restaurant_Settings();
