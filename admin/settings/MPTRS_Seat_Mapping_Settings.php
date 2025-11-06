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

//            add_action('wp_ajax_remove_from_templates',[ $this,  'remove_from_templates']);

            add_action('wp_ajax_mptrs_icon_image_upload', [ $this,'handle_image_upload' ] );
            add_action('wp_ajax_nopriv_mptrs_icon_image_upload', [ $this,'handle_image_upload' ] );

            add_action('wp_ajax_mptrs_save_food_menu',[ $this, 'mptrs_save_food_menu' ] );
            add_action('wp_ajax_nopriv_mptrs_save_food_menu', [ $this, 'mptrs_save_food_menu' ] );

            add_action('wp_ajax_mptrs_edit_food_menu',[ $this, 'mptrs_edit_food_menu' ] );
            add_action('wp_ajax_noprivmptrs_edit_food_menu', [ $this, 'mptrs_edit_food_menu' ] );

            add_action('wp_ajax_mptrs_delete_food_menu',[ $this, 'mptrs_delete_food_menu' ] );
            add_action('wp_ajax_nopriv_mptrs_delete_food_menu', [ $this, 'mptrs_delete_food_menu' ] );

            add_action('wp_ajax_mptrs_save_food_menu_for_restaurant',[ $this, 'mptrs_save_food_menu_for_restaurant' ] );
            add_action('wp_ajax_nopriv_mptrs_save_food_menu_for_restaurant', [ $this, 'mptrs_save_food_menu_for_restaurant' ] );

            add_action('wp_ajax_mptrs_remove_saved_food_menu_for_restaurant',[ $this, 'mptrs_remove_saved_food_menu_for_restaurant' ] );
            add_action('wp_ajax_nopriv_mptrs_remove_saved_food_menu_for_restaurant', [ $this, 'mptrs_remove_saved_food_menu_for_restaurant' ] );

            add_action('wp_ajax_mptrs_set_categories',[ $this, 'mptrs_set_categories' ] );
            add_action('wp_ajax_nopriv_mptrs_set_categories', [ $this, 'mptrs_set_categories' ] );

        }

        public  static  function generateUniqueKey() {
            return substr(hash('sha256', uniqid(time() . wp_rand(), true)), 0, 12);
        }

        public function mptrs_set_categories(){

            if (!isset( $_POST['nonce']) || !wp_verify_nonce( sanitize_text_field( wp_unslash($_POST['nonce'] ) ), 'mptrs_admin_nonce')) {
                wp_send_json_error(['message' => 'Security check failed.'], 403);
            }

            if (!current_user_can('manage_options')) {
                wp_send_json_error(['message' => 'Permission denied']);
            }

            $categories_json = isset( $_POST['categoryJsonData']) ? sanitize_text_field( wp_unslash( $_POST['categoryJsonData'] ) ) : '';
            $categories = json_decode($categories_json, true);

            $result = 0;
            $new_categories = [];
            if ( empty( $categories ) ) {
                wp_send_json_error(['message' => 'Invalid data received.']);
            }else{
                foreach ($categories as $category) {
                    $index = strtolower(str_replace(' ', '_', $category));
                    $new_categories[$index] = $category;
                }
                $result = update_option('mptrs_categories', $new_categories );
            }

            wp_send_json_success([
                'message' => 'Food Menu successfully Added In Your List!',
                'success' => $result,
            ]);
        }

        public function mptrs_save_food_menu_for_restaurant(){
            if ( ! isset( $_POST['nonce'] ) ||
                ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'mptrs_admin_nonce' ) ) {
                wp_send_json_error( [ 'message' => 'Security check failed.' ], 403 );
            }
            $post_id = intval(isset( $_POST['postId']) ? sanitize_text_field( wp_unslash( $_POST['postId'] ) ) : 0 );
            if (!$post_id || get_post_type($post_id) !== 'mptrs_item') {
                wp_send_json_error(['message' => 'Invalid post ID or post type.']);
            }
            $menuItemkey = isset( $_POST['menu_key'] ) ? sanitize_text_field( wp_unslash( $_POST['menu_key'] ) ) : [];
            $menuItems[0] = $menuItemkey;
            $existing_menuItems = get_post_meta( $post_id, '_mptrs_food_menu_items', true );
            if ( is_array( $existing_menuItems ) ) {
                $menuItems = array_merge( $menuItems , $existing_menuItems );
            }
            $menuItems = array_unique( $menuItems );
            $menuItems = array_values( $menuItems );
            $update = update_post_meta( $post_id, '_mptrs_food_menu_items', $menuItems );
            wp_send_json_success([
                'message' => 'Food Menu successfully Added In Your List!',
                'success' => $update,
            ]);
        }

        public function mptrs_remove_saved_food_menu_for_restaurant(){
            $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '' ;
            if ( ! wp_verify_nonce( $nonce, 'mptrs_admin_nonce' ) ) {
                wp_send_json_error( [ 'message' => 'Security check failed.' ], 403 );
            }

            $post_id = intval(isset( $_POST['postId']) ? sanitize_text_field( wp_unslash( $_POST['postId'] ) ) : 0 );
            if (!$post_id || get_post_type( $post_id ) !== 'mptrs_item') {
                wp_send_json_error(['message' => 'Invalid post ID or post type.']);
            }
            $menu_key = isset( $_POST['menu_key'] ) ? sanitize_file_name( wp_unslash( $_POST['menu_key'] ) ) : '';

            $existing_menuItems = get_post_meta( $post_id, '_mptrs_food_menu_items', true );
            if ( is_array( $existing_menuItems ) ) {
                $unset_key = array_search( $menu_key, $existing_menuItems, true);
                if ( $unset_key !== false ) {
                    unset($existing_menuItems[ $unset_key ] );
                }
                $existing_menuItems = array_values($existing_menuItems);
            }

            $update = update_post_meta( $post_id, '_mptrs_food_menu_items', $existing_menuItems );
            wp_send_json_success([
                'message' => 'Food Menu successfully Added In Your List!',
                'success' => $update,
            ]);
        }
        public function mptrs_delete_food_menu() {

            if (!isset($_POST['nonce']) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'mptrs_admin_nonce')) {
                wp_send_json_error(['message' => 'Security check failed.'], 403);
            }
            $delete_menu_key = isset($_POST['deleteKey']) ? sanitize_text_field( wp_unslash( $_POST['deleteKey'] ) ) : '';
            $success = false;
            if( $delete_menu_key !== '' ){
                $existing_menus = get_option( '_mptrs_food_menu' );
                unset( $existing_menus[ $delete_menu_key ] );
                $updated = update_option( '_mptrs_food_menu', $existing_menus );
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

            if (!isset($_POST['nonce']) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'mptrs_admin_nonce')) {
                wp_send_json_error(['message' => 'Security check failed.'], 403);
            }

            $variations_ary = [];
            $variations = isset(  $_POST['menuData']['variations'] ) ?  sanitize_text_field( wp_unslash( $_POST['menuData']['variations'] ) ) : '';
            if( !empty($variations) ){
                $variations_ary = json_decode( stripslashes( sanitize_text_field( $variations ) ), true);
            }
            $uniqueKey = self::generateUniqueKey();
            $new_menu_data = [
                'menuName'          => isset( $_POST['menuData']['menuName'] ) ? sanitize_text_field( wp_unslash( $_POST['menuData']['menuName'] ) ) : '',
                'menuCategory'      =>  isset( $_POST['menuData']['menuCategory'] ) ? sanitize_text_field( wp_unslash( $_POST['menuData']['menuCategory'] ) ) : '',
                'menuDescription'   => isset( $_POST['menuData']['menuDescription'] ) ? sanitize_text_field( wp_unslash( $_POST['menuData']['menuDescription'] ) ) : '',
                'menuPrice'         => isset( $_POST['menuData']['menuPrice'] ) ? floatval( wp_unslash( $_POST['menuData']['menuPrice'] ) ) : 0,
                'menuSalePrice'     => isset( $_POST['menuData']['menuSalePrice'] ) ? floatval( wp_unslash( $_POST['menuData']['menuSalePrice'] ) ) : 0,
                'numPersons'        => isset( $_POST['menuData']['menunumPersons'] ) ? intval( wp_unslash( $_POST['menuData']['menunumPersons'] ) ) : 0,
                'menuImgUrl'        => isset( $_POST['menuData']['menuImgUrl'] ) ? esc_url_raw( wp_unslash( $_POST['menuData']['menuImgUrl'] ) ) : '',
                'variations'        => $variations_ary,
            ];

            if (empty($new_menu_data['menuName']) || empty($new_menu_data['menuCategory']) || $new_menu_data['menuPrice'] <= 0 || $new_menu_data['numPersons'] <= 0) {
                wp_send_json_error(['message' => 'All fields are required and must be valid.'], 400);
            }
            $existing_menus = get_option( '_mptrs_food_menu' );
            if ( !is_array( $existing_menus ) ) {
                $existing_menus = [];
            }
            $existing_menus[ $uniqueKey ] = $new_menu_data;
            $saved = update_option( '_mptrs_food_menu', $existing_menus );
            if ( !$saved ) {
                wp_send_json_error(['message' => 'Failed to save menu data.'], 500);
            }

            wp_send_json_success([
                'message' => 'Menu data saved successfully!',
                'uniqueKey' => $uniqueKey
            ]);
        }

        public function mptrs_edit_food_menu() {

            if ( !isset($_POST['nonce']) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'mptrs_admin_nonce')) {
                wp_send_json_error(['message' => 'Security check failed.'], 403);
            }

            $variations_ary = [];
            $variations = isset(  $_POST['menuEditData']['variations'] ) ?  sanitize_text_field( wp_unslash( $_POST['menuEditData']['variations'] ) ) : '';
            if( !empty($variations) ){
                $variations_ary = json_decode( stripslashes( sanitize_text_field( $variations ) ), true);
            }

            error_log( print_r( [ '$variations_ary' => $variations_ary ], true ) );

            $uniqueKey = isset(  $_POST['menuEditData']['menuKey'] ) ? sanitize_text_field( wp_unslash( $_POST['menuEditData']['menuKey'] ) ) : '';
            $new_menu_data = [
                'menuName'     => isset( $_POST['menuEditData']['menuName'] ) ? sanitize_text_field( wp_unslash( $_POST['menuEditData']['menuName'] ) ) : '',
                'menuCategory' => isset( $_POST['menuEditData']['menuCategory'] ) ? sanitize_text_field( wp_unslash( $_POST['menuEditData']['menuCategory'] ) ) : '',
                'menuDescription' => isset( $_POST['menuEditData']['menuDescription'] ) ? sanitize_text_field( wp_unslash( $_POST['menuEditData']['menuDescription'] ) ) : '',
                'menuPrice'    => isset( $_POST['menuEditData']['menuPrice'] ) ? floatval( wp_unslash( $_POST['menuEditData']['menuPrice'] ) ) : '',
                'menuSalePrice'    => isset( $_POST['menuEditData']['menuSalePrice'] ) ? floatval( wp_unslash( $_POST['menuEditData']['menuSalePrice'] ) ) : '',
                'numPersons'   => isset( $_POST['menuEditData']['menunumPersons'] ) ? intval( wp_unslash( $_POST['menuEditData']['menunumPersons'] ) ) : 0,
                'menuImgUrl'   => isset( $_POST['menuEditData']['menuImgUrl'] ) ? esc_url_raw( wp_unslash( $_POST['menuEditData']['menuImgUrl'] ) ) : '',
                'variations'   => $variations_ary,
            ];


            if (empty($new_menu_data['menuName']) || empty($new_menu_data['menuCategory']) || $new_menu_data['menuPrice'] <= 0 || $new_menu_data['numPersons'] <= 0) {
                wp_send_json_error(['message' => 'All fields are required and must be valid.'], 400);
            }
            $existing_menus = get_option( '_mptrs_food_menu' );
            if ( !is_array( $existing_menus ) ) {
                $existing_menus = [];
            }else{
                unset( $existing_menus[ $uniqueKey ] );
            }

            $existing_menus[ $uniqueKey ] = $new_menu_data;
            $saved = update_option( '_mptrs_food_menu', $existing_menus );
            if (!$saved) {
                wp_send_json_error(['message' => 'Failed to save menu data.'], 500);
            }

            wp_send_json_success([
                'message' => 'Menu data edited successfully!',
                'uniqueKey' => $uniqueKey
            ]);
        }

        public function mptrs_save_seat_maps_meta_data(){

            if (!isset($_POST['nonce']) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'mptrs_admin_nonce')) {
                wp_send_json_error(['message' => 'Security check failed.'], 403);
            }

            $post_id = intval(isset( $_POST['post_id']) ? sanitize_text_field( wp_unslash( $_POST['post_id'] ) ) : 0 );
            if (!$post_id || get_post_type($post_id) !== 'mptrs_seat_mapping') {
//            if (!$post_id || get_post_type($post_id) !== 'mptrs_item') {
                wp_send_json_error(['message' => 'Invalid post ID or post type.']);
            }

            if (!current_user_can('edit_post', $post_id)) {
                wp_send_json_error(['message' => 'Permission denied.']);
            }


            $seat_maps_meta_data = isset( $_POST['seat_maps_meta_data'] ) ? json_decode( sanitize_text_field( wp_unslash( $_POST['seat_maps_meta_data'] ) ), true ) : [];
            $seat_plan_texts= isset( $_POST['seatPlanTexts'] ) ? json_decode( sanitize_text_field( wp_unslash( $_POST['seatPlanTexts'] ) ), true ) : '' ;
            $dynamicShapes = isset( $_POST['dynamicShapes'] ) ? json_decode( sanitize_text_field( wp_unslash( $_POST['dynamicShapes'] ) ), true ) : '';

            $template = isset( $_POST['template'] ) ? sanitize_text_field( wp_unslash( $_POST['template'] ) ) : '';
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
            if ( !isset($_POST['nonce']) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'mptrs_admin_nonce')) {
                wp_send_json_error(['message' => 'Security check failed.'], 403);
            }

            if ( isset( $_FILES['image'] ) && $_FILES['image']['error'] === UPLOAD_ERR_OK ) {
                $file = $_FILES['image'];

                // Allowed types
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $tmp_path = $file['tmp_name'];
                $mime_type = mime_content_type($tmp_path);

                if ( ! in_array($mime_type, $allowed_types, true) ) {
                    wp_send_json_error(['message' => 'Invalid file type.']);
                }

                // Sanitize file name
                $original_name = sanitize_file_name( $file['name'] );
                $file_ext = pathinfo( $original_name, PATHINFO_EXTENSION );
                $unique_name = substr( hash( 'sha256', time() . wp_rand() ), 0, 12 ) . '.' . $file_ext;

                $upload_dir = MPTRS_Plan_PATH . '/assets/images/icons/seatIcons/';

                if ( ! file_exists($upload_dir) ) {
                    wp_mkdir_p($upload_dir);
                }

                // Final file path
                $destination = $upload_dir . $unique_name;

                if ( move_uploaded_file( $tmp_path, $destination ) ) {
                    $plugin_url = MPTRS_Plan_ASSETS.'images/icons/seatIcons/' . $unique_name;

                    wp_send_json_success([
                        'message'    => 'Image uploaded successfully!',
                        'file_url'   => esc_url_raw($plugin_url),
                        'image_name' => sanitize_file_name($unique_name),
                    ]);
                } else {
                    wp_send_json_error(['message' => 'Failed to move uploaded file.']);
                }
            } else {
                wp_send_json_error(['message' => 'No file uploaded or upload error.']);
            }
        }


       /* public function remove_from_templates(){
            $template_id = isset($_POST['templateId']) ? absint( $_POST['templateId']) : '';
            $result = delete_post_meta( $template_id, 'is_template');
            wp_send_json_success( $result );
        }*/
        function mptrs_render_manage_seat_templates_for_import() {

            if ( !isset($_POST['nonce']) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'mptrs_admin_nonce')) {
                wp_send_json_error(['message' => 'Security check failed.'], 403);
            }
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
                <span class="mptrs_importSeatPlanTitleText"><?php esc_html_e('Seat Plan Templates', 'tablely'); ?></span>
                <span class="mptrs_importSeatPlanText"><?php esc_html_e('Select any template', 'tablely'); ?></span>
                <div class="mptrs_popupTemplateContainer">
                    <?php if ($query->have_posts()) :

                        ?>
                        <div class="mptrs_templatesHolder">
                            <?php while ($query->have_posts()) : $query->the_post(); ?>
                                <?php
                                $post_id = get_the_ID();
                                $title = get_the_title() ?: __('No Title Available', 'tablely');
                                $nonce = wp_create_nonce( 'mptrs_template' );
                                $edit_url = admin_url("post.php?post={$original_post_id}&action=edit&templateId={$post_id}&nonce={$nonce}");
//                                $thumbnail_url = get_post_meta($post_id, '_custom_feature_image', true);
                                $thumbnail_id = get_post_meta( $post_id, '_thumbnail_id', true );
                                $thumbnail_url = wp_get_attachment_url( $thumbnail_id );
                                ?>
                                <div class="mptrs_templates" id="mptrs_template-<?php echo esc_attr( $post_id ); ?>">
                                    <div class="mptrs_featureImagesHolder">
                                        <img class="mptrs_featureImages" src="<?php echo esc_attr( $thumbnail_url )?>">
                                    </div>
                                    <a class="mptrs_templateLinks" href="<?php echo esc_url($edit_url); ?>">
                                        <?php echo esc_html($title); ?>
                                    </a>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else : ?>
                        <p><?php esc_html_e('No templates found.', 'tablely'); ?></p>
                    <?php endif; ?>
                    <?php wp_reset_postdata(); ?>
                </div>
                <div class="mptrs_openTemplateBtnHolder"><button class="mptrs_openAsTemplate" id="mptrs_open_<?php echo esc_attr( $original_post_id ) ?>">Open template</button></div>
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