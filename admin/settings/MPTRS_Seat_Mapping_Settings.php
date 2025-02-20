<?php
/**
 * @author rubel mia <rubelcuet10@gmail.com>
 * @license mage-people.com
 * @var 1.0.0
 */
if (!defined('ABSPATH'))
    die;
if (!class_exists('MPTRS_Seat_Mapping_Settings')) {
    class MPTRS_Seat_Mapping_Settings{
        public function __construct() {
            // mptrs_set seat maps
            add_action('wp_ajax_mptrs_save_seat_maps_meta_data', [$this, 'mptrs_save_seat_maps_meta_data']);
            add_action('wp_ajax_nopriv_mptrs_save_seat_maps_meta_data', [$this, 'mptrs_save_seat_maps_meta_data']);

            add_action('wp_ajax_import_from_template_checkbox_state',[ $this,  'import_from_template_checkbox_state']);
            add_action('wp_ajax_nopriv_import_from_template_checkbox_state', [ $this, 'import_from_template_checkbox_state']);

            add_action('wp_ajax_mptrs_render_manage_seat_templates_for_import', [ $this,  'mptrs_render_manage_seat_templates_for_import'] );
            add_action('wp_ajax_nopriv_mptrs_render_manage_seat_templates_for_import', [ $this,  'mptrs_render_manage_seat_templates_for_import'] ); // Allow non-logged-in users if needed

            add_action('wp_ajax_remove_from_templates',[ $this,  'remove_from_templates']);

            add_action('wp_ajax_image_upload', [ $this,'handle_image_upload' ] );
            add_action('wp_ajax_nopriv_image_upload', [ $this,'handle_image_upload' ] );

            add_action('wp_ajax_mptrs_save_food_menu',[ $this, 'mptrs_save_food_menu' ] );
            add_action('wp_ajax_nopriv_mptrs_save_food_menu', [ $this, 'mptrs_save_food_menu' ] );

            add_action('wp_ajax_mptrs_delete_food_menu',[ $this, 'mptrs_delete_food_menu' ] );
            add_action('wp_ajax_nopriv_mptrs_delete_food_menu', [ $this, 'mptrs_delete_food_menu' ] );

        }

        public function mptrs_delete_food_menu() {
            if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'mptrs_admin_nonce')) {
                wp_send_json_error(['message' => 'Security check failed.'], 403);
            }
            if (!isset($_POST['post_id']) || empty($_POST['post_id'])) {
                wp_send_json_error(['message' => 'Invalid post ID.'], 400);
            }
            $post_id = isset($_POST['post_id']) ? sanitize_text_field($_POST['post_id']) : 0;
            $delete_menu_key = isset($_POST['deleteKey']) ? sanitize_text_field( $_POST['deleteKey'] ) : '';

            $success = false;
            if( $delete_menu_key !== '' ){
                $existing_menus = get_post_meta($post_id, '_mptrs_food_menu', true);
                unset($existing_menus[ $delete_menu_key ]);
                $updated = update_post_meta($post_id, '_mptrs_food_menu', $existing_menus);
                if( $updated ===  1 ){
                    $success = true;
                }
            }

            wp_send_json_success([
                'message' => 'Menu data deleted successfully!',
                'success' => $success,
            ]);
        }
        public function mptrs_save_food_menu() {
            if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'mptrs_admin_nonce')) {
                wp_send_json_error(['message' => 'Security check failed.'], 403);
            }
            if (!isset($_POST['post_id']) || empty($_POST['post_id'])) {
                wp_send_json_error(['message' => 'Invalid post ID.'], 400);
            }
            $post_id = intval($_POST['post_id']);
            if (!isset($_POST['menuData']) || !is_array($_POST['menuData'])) {
                wp_send_json_error(['message' => 'Invalid menu data.'], 400);
            }
            $new_menu_data = [
                'menuName'     => sanitize_text_field($_POST['menuData']['menuName']),
                'menuCategory' => sanitize_text_field($_POST['menuData']['menuCategory']),
                'menuPrice'    => floatval($_POST['menuData']['menuPrice']),
                'numPersons'   => intval($_POST['menuData']['menunumPersons']),
                'menuImgUrl'   => esc_url_raw($_POST['menuData']['menuImgUrl']),
            ];

            if (empty($new_menu_data['menuName']) || empty($new_menu_data['menuCategory']) || $new_menu_data['menuPrice'] <= 0 || $new_menu_data['numPersons'] <= 0) {
                wp_send_json_error(['message' => 'All fields are required and must be valid.'], 400);
            }
            $existing_menus = get_post_meta($post_id, '_mptrs_food_menu', true);
            if (!is_array($existing_menus)) {
                $existing_menus = [];
            }
            $existing_menus[] = $new_menu_data;
            $saved = update_post_meta($post_id, '_mptrs_food_menu', $existing_menus);
            if (!$saved) {
                wp_send_json_error(['message' => 'Failed to save menu data.'], 500);
            }
            wp_send_json_success([
                'message' => 'Menu data saved successfully!',
                'menuData' => $existing_menus
            ]);
        }



        public function mptrs_save_seat_maps_meta_data(){

            if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'mptrs_admin_nonce')) {
                wp_send_json_error(['message' => 'Security check failed.'], 403);
            }

            $post_id = intval(isset( $_POST['post_id']) ? sanitize_text_field( $_POST['post_id'] ) : 0 );
            if (!$post_id || get_post_type($post_id) !== 'mptrs_item') {
                wp_send_json_error(['message' => 'Invalid post ID or post type.']);
            }

            if (!current_user_can('edit_post', $post_id)) {
                wp_send_json_error(['message' => 'Permission denied.']);
            }

            $seat_maps_meta_data = isset( $_POST['seat_maps_meta_data'] ) ? MPTRS_Function::data_sanitize( $_POST['seat_maps_meta_data'] ) : [];

            $seat_plan_texts= isset( $_POST['seatPlanTexts'] ) ? MPTRS_Function::data_sanitize( $_POST['seatPlanTexts'] ) : '' ;
            $dynamicShapes = isset( $_POST['dynamicShapes'] ) ? MPTRS_Function::data_sanitize( $_POST['dynamicShapes'] ) : '';
            $template = isset( $_POST['template'] ) ? sanitize_text_field( $_POST['template'] ) : '';
            $seat_plan_data = array(
                'seat_data' => $seat_maps_meta_data,
                'seat_text_data' => $seat_plan_texts,
                'dynamic_shapes' => $dynamicShapes,
            );
            update_post_meta( $post_id, '_mptrs_seat_maps_data', $seat_plan_data );
            if( $template !== '' ){
                update_post_meta( $post_id, 'mptrs_is_seat_map_template', $template );
            }

            wp_send_json_success(['message' => 'Meta data saved successfully.']);
        }

        function handle_image_upload() {
            // check_ajax_referer('image_upload_nonce', 'nonce');

            if (!empty($_FILES['image'])) {
                $file = $_FILES['image'];

                $allowed_types = array('image/jpeg', 'image/png', 'image/gif', 'image/webp');
                if (!in_array($file['type'], $allowed_types)) {
                    wp_send_json_error(['message' => 'Invalid file type.']);
                }

                $assets_dir = MPTRS_Plan_PATH . '/assets/images/icons/seatIcons';

                if (!file_exists( $assets_dir ) ) {
                    wp_mkdir_p( $assets_dir );
                }

                $timestamp = time();
                $unique_name = substr( hash('sha256', $timestamp ), 0, 12 );
                $file_extension = pathinfo( $file['name'], PATHINFO_EXTENSION );
                $new_file_name = $unique_name . '.' . $file_extension;

                $target_file = $assets_dir . '/' . $new_file_name;

                if (move_uploaded_file( $file['tmp_name'], $target_file ) ) {
                    $file_url = MPTRS_Plan_ASSETS . 'images/icons/seatIcons/' . $new_file_name;

                    wp_send_json_success( [ 'message' => 'Image uploaded successfully!', 'file_url' => $file_url, 'image_name' => $unique_name ] );
                } else {
                    wp_send_json_error( [ 'message' => 'Failed to move the uploaded file.' ] );
                }
            } else {
                wp_send_json_error( [ 'message' => 'No file uploaded.' ] );
            }
        }

        public function remove_from_templates(){
            $template_id = isset($_POST['templateId']) ? absint($_POST['templateId']) : '';
            $result = delete_post_meta( $template_id, 'is_template');
            wp_send_json_success( $result );
        }
        function mptrs_render_manage_seat_templates_for_import() {
            $paged = isset($_POST['paged']) ? absint($_POST['paged']) : 1;
            $original_post_id = isset($_POST['postId']) ? absint($_POST['postId']) : 1;
            $args = [
                'post_type'      => 'mptrs_item',
                'posts_per_page' => 100,
                'post_status'    => 'publish',
                'paged'          => $paged,
                'meta_query'     => [
                    [
                        'key'   => 'mptrs_is_seat_map_template',
                        'value' => 'template',
                        'compare' => '=' // Exact match
                    ]
                ],
            ];

            $query = new WP_Query($args);
            ob_start();
            ?>
            <div class="templateWrap">
                <span class="mptrs_importSeatPlanTitleText"><?php _e('Seat Plan Templates', 'tablely'); ?></span>
                <span class="mptrs_importSeatPlanText"><?php _e('Select any template', 'tablely'); ?></span>
                <div class="mptrs_popupTemplateContainer">
                    <?php if ($query->have_posts()) :

                        ?>
                        <div class="mptrs_templatesHolder">
                            <?php while ($query->have_posts()) : $query->the_post(); ?>
                                <?php
                                $post_id = get_the_ID();
                                $title = get_the_title() ?: __('No Title Available', 'tablely');
                                $edit_url = admin_url("post.php?post={$original_post_id}&action=edit&templateId={$post_id}");
//                                $thumbnail_url = get_post_meta($post_id, '_custom_feature_image', true);
                                $thumbnail_id = get_post_meta( $post_id, '_thumbnail_id', true );
                                $thumbnail_url = wp_get_attachment_url( $thumbnail_id );
                                ?>
                                <div class="mptrs_templates" id="mptrs_template-<?php echo esc_attr($post_id); ?>">
                                    <div class="mptrs_featureImagesHolder">
                                        <img class="mptrs_featureImages" src="<?php echo $thumbnail_url?>">
                                    </div>
                                    <a class="mptrs_templateLinks" href="<?php echo esc_url($edit_url); ?>">
                                        <?php echo esc_html($title); ?>
                                    </a>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else : ?>
                        <p><?php _e('No templates found.', 'tablely'); ?></p>
                    <?php endif; ?>
                    <?php wp_reset_postdata(); ?>
                </div>
                <div class="mptrs_openTemplateBtnHolder"><button class="mptrs_openAsTemplate" id="mptrs_open_<?php echo $original_post_id ?>">Open template</button></div>
            </div>
            <?php
            $output = ob_get_clean();
            wp_send_json_success($output);
        }

        public function import_from_template_checkbox_state(){
            check_ajax_referer('ajax_nonce', 'nonce');
            $is_checked = isset($_POST['is_checked']) ? intval($_POST['is_checked']) : 0;
            update_option('import_design_from_template', $is_checked);
            wp_send_json_success(['message' => 'Checkbox state saved successfully']);
        }

    }

    new MPTRS_Seat_Mapping_Settings();
}