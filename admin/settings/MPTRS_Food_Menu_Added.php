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
            $menu_categories = get_option( 'mptrs_categories' );
            $existing_menu_by_id = get_post_meta($post_id, '_mptrs_food_menu_items', true);
            $existing_edited_price = get_post_meta($post_id, '_mptrs_food_menu_edited_prices', true);
            $existing_menus = [];

            $all_food_menus = get_option( '_mptrs_food_menu', true);

            if ( is_array( $existing_menu_by_id ) && !empty( $existing_menu_by_id ) ) {
                foreach ( $existing_menu_by_id as $item ) {
                    if ( isset($all_food_menus[ $item ] ) ) {
                        $existing_menus[$item] = $all_food_menus[ $item ];
                    }
                }
            }

            ?>
            <div class="tabsItem" data-tabs="#mptrs_food_menu_add">
                <header>
                    <h2><?php esc_html_e('Food Menu', 'tablely'); ?></h2>
                    <span><?php esc_html_e('In this section you will food menu.', 'tablely'); ?></span>
                </header>
                <section class="section">
                    <h2><?php esc_html_e('Food Menu Details', 'tablely'); ?></h2>
                    <span><?php esc_html_e('Add Food menu from left side to right side. You can create new menu from here ', 'tablely'); ?>
                    <a href="<?php echo esc_attr( admin_url('edit.php?post_type=mptrs_item&page=mptrs_new_food_menu') ); ?>">
                        <?php esc_attr_e( 'Add New Food Menu', 'tablely' );?>
                    </a>
                </span>
                </section>
                <section>
                    <?php  if( is_array( $all_food_menus ) && count( $all_food_menus ) > 0 ): ?>
                        <div class="mptrs_foodMenuTabHoilder">
                            <div class="mptrs_filterByCategory active" data-filter="all"><?php esc_attr_e( 'All', 'tablely' ) ?></div>
                                <?php
                                if( is_array( $menu_categories ) && !empty( $menu_categories ) ) {
                                    foreach ( $menu_categories as $key => $category ) { ?>
                                        <div class="mptrs_filterByCategory" data-filter="<?php echo esc_attr( $key ) ?>"><?php echo esc_attr( $category ) ?></div>
                                    <?php }
                                }
                                ?>
                        </div>
                        <div class="mptrs_foodMenuContentHolder" id="mptrs_foodMenuContentHolder">
                            <div id="mptrs_foodMenuAddedContainer" class="mptrs_foodMenuContainer">
                                <div class="mptrs-menu-container">
                                    <span id="mptrs_foodMenuAdded" class="mptrs_foodMenuTab"><?php esc_attr_e( 'All Food Menu', 'tablely' )?></span>
                                        <table class="mptrs-menu-table">
                                            <thead>
                                            <tr>
                                                <th><?php esc_attr_e( 'Image', 'tablely' )?></th>
                                                <th><?php esc_attr_e( 'Name', 'tablely' )?></th>
                                                <th><?php esc_attr_e( 'Price', 'tablely' )?></th>
                                                <th><?php esc_attr_e( 'Action', 'tablely' )?></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php

                                            foreach( $all_food_menus as $key => $food_menu ){
                                                $add = 'Add';
                                                $className = 'mptrs_addMenuClass';
                                                if ( isset( $existing_menus[ $key ] ) ) {
                                                    $add = 'Added';
                                                    $className = 'mptrs_addedMenuClass';
                                                }

                                                ?>
                                                <tr class="mptrs_menuInfoHolderFilter" id="mptrs_menuInfoHolderFilter-<?php echo esc_attr( $key )?>" data-category ="<?php echo esc_attr( $food_menu['menuCategory'] )?>">
                                                    <td>
                                                        <div class="mptrsMenuImg">
                                                            <img id="mptrs_MenuImg-<?php echo esc_attr( $key )?>" src="<?php echo esc_attr( $food_menu['menuImgUrl'])?>" alt="<?php echo esc_attr( $food_menu['menuName'])?>">
                                                        </div>
                                                    </td>
                                                    <td class="mptrs-menuName" id="mptrs-menuName-<?php echo esc_attr( $key )?>"><?php echo esc_attr( $food_menu['menuName'])?></td>
                                                    <td class="mptrs-menuPrice" id="mptrs-menuPrice-<?php echo esc_attr( $key )?>">$<?php echo esc_attr( $food_menu['menuPrice'])?></td>
                                                    <td class="mptrs-menuAction">
                                                        <button class="mptrs_addMenuToPost <?php echo esc_attr( $className )?>" id="mptrs_addMenuToPost-<?php echo esc_attr( $key )?>"><?php echo esc_attr( $add )?></button>
                                                    </td>
                                                </tr>
                                            <?php }?>
                                            </tbody>
                                        </table>
                                </div>
                                <div class="mptrs-menu-container">
                                    <span id="mptrs_foodMenuAdded" class="mptrs_foodMenuTab"><?php esc_attr_e( 'Added Food Menu', 'tablely' )?></span>

                                    <?php
                                    if( is_array( $all_food_menus ) && count( $all_food_menus ) > 0 ){


                                        ?>
                                        <table class="mptrs-menu-table" id="mptrs_AddedMenuData">
                                            <thead>
                                            <tr>
                                                <th><?php esc_attr_e( 'Image', 'tablely' )?></th>
                                                <th><?php esc_attr_e( 'Name', 'tablely' )?></th>
                                                <th><?php esc_attr_e( 'Price', 'tablely' )?></th>
                                                <th><?php esc_attr_e( 'Action', 'tablely' )?></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            $fallbackImgUrl = get_site_url().'/wp-content/uploads/2025/02/fallbackimage.webp';
                                            foreach ( $existing_menus as $key => $existing_menu ){
                                                if( $existing_menu['menuImgUrl'] === '' ){
                                                    $menu_img = $fallbackImgUrl;
                                                }else{
                                                    $menu_img = $existing_menu['menuImgUrl'];
                                                }
                                                $price =$existing_menu['menuPrice'];

                                                if( is_array( $existing_edited_price ) && !empty( $existing_edited_price ) ){
                                                    if ( isset($existing_edited_price[ $key ] ) ) {
                                                        $price = $existing_edited_price[ $key ] ;
                                                    }
                                                }
                                                ?>
                                                <tr class="mptrs_menuInfoHolderFilter" id="mptrs_addedFoodMenu<?php echo esc_attr( $key ) ?>" data-category ="<?php echo esc_attr( $existing_menu['menuCategory'] )?>">
                                                    <td>
                                                        <div class="mptrsMenuImg">
                                                            <img src="<?php echo esc_attr( $menu_img )?>" alt="<?php echo esc_attr( $existing_menu['menuName'] )?>">
                                                        </div>
                                                    </td>
                                                    <td class="mptrs-menuName">
                                                        <?php echo esc_attr( $existing_menu['menuName'])?>
                                                    </td>
                                                    <td class=" mptrm_editFromFoodMenu mptrs-menuPrice">
                                                        $<span class="mptrs_addedMenuPrice" id="mptrs_addedMenuPrice-<?php echo esc_attr( $key ) ?>"><?php echo esc_attr( $price )?></span>
                                                        <span class="mptrm_editAddedMenuPrice" id="mptrm_editAddedMenuPrice-<?php echo esc_attr( $key )?>" style="display: block">Edit Price</span>
                                                    </td>
                                                    <td class="mptrs-menuAction">
                                                        <button class="mptrs_addMenuToPost" id="mptrs_addedMenuToPost-<?php echo esc_attr( $key )?>"><i class="fa-solid fa-trash"></i></button>
                                                    </td>
                                                </tr>
                                            <?php }?>
                                            </tbody>
                                        </table>

                                    <?php } ?>

                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <a class="mptrs_addNewFoodMenuBtn" href="<?php echo esc_attr( admin_url('edit.php?post_type=mptrs_item&page=mptrs_new_food_menu' ) ); ?>" ><?php esc_attr_e( 'Add New Food Menu', 'tablely' );?></a>
                    <?php endif; ?>
                </section>
            </div>
            <?php
        }

    }

    new MPTRS_Food_Menu_Added();
}