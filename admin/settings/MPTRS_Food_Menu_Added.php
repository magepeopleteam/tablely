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
            $existing_menus = get_post_meta($post_id, '_mptrs_food_menu', true);
            ?>
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

                                <?php foreach ( $existing_menus as $key => $existing_menu ){?>
                                <div class="mptrs_foodMenuContent" id="mptrs_foodMenuContent<?php echo esc_attr( $key )?>">
                                    <div class="mptrs_menuImageHolder">
                                        <img class="mptrs_menuImage" id="mptrs_memuImgUrl<?php echo esc_attr( $key )?>" src="<?php echo esc_attr( $existing_menu['menuImgUrl'] ) ?>" >
                                    </div>
                                    <div class="mptrs_menuInfoHolder">
                                        <div class="mptrs_topMenuInFo">
                                            <div class="mptrs_menuName" id="mptrs_memuName<?php echo esc_attr( $key )?>">
                                                <?php echo esc_attr( $existing_menu['menuName'] );?>
                                            </div>
                                        </div>
                                        <div class="mptrs_BottomMenuInFo">
                                            <div class="mptrs_menuPrice" id="mptrs_memuPrice<?php echo esc_attr( $key )?>">$<?php echo esc_attr( $existing_menu['menuPrice'] );?></div>
                                            <div class="mptrs_menuPersion" id="mptrs_memuPersons<?php echo esc_attr( $key )?>"><?php echo esc_attr( $existing_menu['numPersons'] );?></div>
                                        </div>
                                        <div class="mptrs_BottomMenuInFo">
                                            <span class="mptrm_editFoodMenu" id="mptrsEditMenu_<?php echo esc_attr( $key )?>" style="display: none">Edit </span>
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
            <?php
        }

    }

    new MPTRS_Food_Menu_Added();
}