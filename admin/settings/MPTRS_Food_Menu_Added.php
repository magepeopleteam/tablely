<?php

/**
 * @author Md Rubel Mia <rubelcuet10@gmail.com>
 * @license mage-people.com
 * @var 1.0.0
 */

if (!defined('ABSPATH'))
    die;
if (!class_exists('MPTRS_Food_Menu_Added')) {

    class MPTRS_Food_Menu_Added{
        public function __construct() {
            add_action('add_mptrs_settings_tab_content', [$this, 'add_food_menu_tab_content']);
        }

        public function add_food_menu_tab_content(){
            $post_id = get_the_ID();
            $existing_menu_by_id = get_post_meta($post_id, '_mptrs_food_menu_items', true);
            $existing_menus = [];

            $all_food_menus = get_option( '_mptrs_food_menu', true);
            if (is_array($existing_menu_by_id) && !empty($existing_menu_by_id)) {
                foreach ($existing_menu_by_id as $item) {
                    if (isset($all_food_menus[$item])) {
                        $existing_menus[] = $all_food_menus[$item];
                    }
                }
            }

            error_log( print_r($existing_menus, true));

            ?>
            <div class="tabsItem" data-tabs="#mptrs_food_menu_add">

                <div class="mptrs_foodMenuTabHoilder">
                    <ul class="mptrs_foodMenuTabs">
                        <li id="mptrs_foodMenuAdded" class="mptrs_foodMenuTab"><?php esc_attr_e( 'All Food Menu', 'tablely' )?></li>
                        <li id="mptrs_foodMenuShow" class="mptrs_foodMenuTab"><?php esc_attr_e( 'Added Food Menu', 'tablely' )?></li>
                    </ul>

                </div>
                <div class="mptrs_foodMenuContentHolder">
                    <div id="mptrs_foodMenuAddedContainer" class="mptrs_foodMenuContainer">
                        <h2><?php esc_html_e('Added Food Menu From Here', 'tablely'); ?></h2>

                        <div class="mptrs-menu-container">
                            <?php  if( is_array( $all_food_menus ) && count( $all_food_menus ) > 0 ){ ?>
                                    <table class="mptrs-menu-table">
                                        <thead>
                                            <tr>
                                                <th>Image</th>
                                                <th>Name</th>
                                                <th>Price</th>
                                                <th>Serves</th>
                                                <th>Category</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        foreach( $all_food_menus as $key => $food_menu ){
                                        ?>
                                            <tr>
                                                <td>
                                                    <div class="mptrs-menu-img">
                                                        <img src="<?php echo esc_attr( $food_menu['menuImgUrl'])?>" alt="<?php echo esc_attr( $food_menu['menuName'])?>">
                                                    </div>
                                                </td>
                                                <td><?php echo esc_attr( $food_menu['menuName'])?></td>
                                                <td>$<?php echo esc_attr( $food_menu['menuPrice'])?></td>
                                                <td><?php echo esc_attr( $food_menu['numPersons'])?> persons</td>
                                                <td><?php echo esc_attr( $food_menu['menuCategory'])?></td>
                                                <td>
                                                    <input type="checkbox" class="mptrs-menu-checkbox" id="mptrs_menuItem-<?php echo esc_attr( $key )?>">
                                                </td>
                                            </tr>
                                        <?php }?>
                                        </tbody>
                                    </table>

                                <?php } ?>
                        </div>

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
                                            <span class="mptrm_editFromFoodMenu" id="mptrm_editFromFoodMenu-<?php echo esc_attr( $key )?>" style="display: block">Edit </span>
                                            <span class="mptrm_removeFromFoodMenu" id="mptrm_removeFromFoodMenu-<?php echo esc_attr( $key )?>">Delete </span>
                                        </div>
                                    </div>
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

    new MPTRS_Food_Menu_Added();
}