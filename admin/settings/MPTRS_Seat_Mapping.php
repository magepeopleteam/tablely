<?php
	/**
	 * @author Sahahdat Hossain <raselsha@gmail.com>
	 * @license mage-people.com
	 * @var 1.0.0
	 */
	if (!defined('ABSPATH'))
		die;
	if (!class_exists('MPTRS_Seat_Mapping')) {
		class MPTRS_Seat_Mapping {
			public function __construct() {
				add_action('add_mptrs_settings_tab_content', [$this, 'seat_mapping_tab_content']);
			}

             public function render_meta_box( $post_id ) {

                $post = get_post( $post_id );
                if ( ! $post ) {
                    wp_die( 'Invalid post ID' );
                }
                $template_id = isset( $_GET['templateId'] ) ? sanitize_text_field( $_GET['templateId'] ) : '';

                // Output the meta box content
                ?>
                <h1><?php esc_html_e('Make Seat Plan', 'tablely'); ?></h1>

                <div class="mptrs_mapping_controls" id="<?php echo esc_attr( $post->ID ); ?>">
                    <input type="hidden" id="mptrs_mapping_plan_id" name="mptrs_mapping_plan_id" value="<?php echo esc_attr( $post->ID ); ?>">
                    <div class="mptrs_mapping_planControlHolder">
                        <button class="mptrs_mapping_multiselect" id="mptrs_mapping_multiselect"><?php esc_html_e('Multiselect', 'tablely'); ?></button>
                        <button class="mptrs_mapping_singleSelect" id="mptrs_mapping_singleSelect"><?php esc_html_e('Single Select', 'tablely'); ?></button>
                        <button class="mptrs_mapping_set_seat enable_set_seat" id="mptrs_mapping_set_seat"><?php esc_html_e('Add Seat +', 'tablely'); ?></button>
                        <button class="mptrs_mapping_set_shape" id="mptrs_mapping_set_shape"><?php esc_html_e('Add Shape +', 'tablely'); ?></button>
                        <button class="mptrs_mapping_setText" id="mptrs_mapping_setText" ><?php esc_html_e('Set Text', 'tablely'); ?></button>
                        <button class="mptrs_importFromTemplate" id="mptrs_importFromTemplate"><?php esc_html_e('Import From Template', 'tablely'); ?></button>
                        <button class="mptrs_removeSelected" id="mptrs_removeSelected"><?php esc_html_e('Erase', 'tablely'); ?></button>
                        <button class="mptrs_undo" id="mptrs_undo" ><?php esc_html_e('Undo', 'tablely'); ?></button>
                        <button class="mptrs_copyPaste" id="mptrs_copyPaste" ><?php esc_html_e('Paste', 'tablely'); ?></button>
                    </div>
                </div>

                <div class="mptrs_seatContentHolder" id="mptrs_seatContentHolder">
                    <div id="mptrs_popupContainer" class="mptrs_popup">
                        <div class="mptrs_popupContent">
                            <span id="mptrs_closePopup" class="mptrs_close">&times;</span>
                            <div id="mptrs_popupInnerContent"></div>
                        </div>
                    </div>
                    <div class="mptrs_seatPlanHolder">
                        <?php
                        $dynamic_shape_texts = array(
                            'rectangle'    => array( 'Rectangle', 'rectangle' ),
                            'circle'        => array( 'Circle', 'circle' ),
                            'square'        => array( 'Square', 'square' ),
                            'pentagon'      => array( 'Pentagon', 'pentagon' ),
                            'hexagon'      => array( 'Hexagon', 'hexagon' ),
                            'rhombus'      => array( 'Rhombus', 'rhombus' ),
                            'parallelogram' => array( 'Parallelogram', 'parallelogram' ),
                            'trapezoid'     => array( 'Trapezoid', 'trapezoid' ),
                            'oval'          => array( 'Oval', 'oval' ),
                        );

                        $shapeText = '<span class="mptrs_setShapeTitle">' . esc_html__('Select Shape', 'tablely') . '</span>';
                        foreach ( $dynamic_shape_texts as $key => $val ) {
                            $select_class = ( $key === 'rectangle' ) ? 'shapeTextSelected' : '';
                            $src = esc_url( MPTRS_Plan_ASSETS . 'images/icons/shape_icons/' . $val[1] . '.jpg' );
                            $shapeText .= '<div class="mptrs_shapeText ' . esc_attr( $select_class ) . '" id="' . esc_attr( $key ) . '"><img class="shapeIcon" src="' . $src . '" /></div>';
                        }

                        $get_create_box_data = get_option( 'create_box_data' );
                        $box_size = isset( $get_create_box_data['box_size'] ) ? absint( $get_create_box_data['box_size'] ) : 35;
                        $rows = isset( $get_create_box_data['numberOfRows'] ) ? absint( $get_create_box_data['numberOfRows'] ) : 30;
                        $columns = isset( $get_create_box_data['numberOfColumns'] ) ? absint( $get_create_box_data['numberOfColumns'] ) : 24;
                        $gap = isset( $get_create_box_data['boxGap'] ) ? absint( $get_create_box_data['boxGap'] ) : 10;

                        $childWidth = $box_size;
                        $childHeight = $box_size + 5;

                        $seats = [];
                        for ( $row = 0; $row < $rows; $row++ ) {
                            for ( $col = 0; $col < $columns; $col++ ) {
                                $seats[] = array( 'col' => $row, 'row' => $col );
                            }
                        }

                        $parent_width = $columns * ( $childWidth + $gap ) - $gap;
                        $parent_height = $rows * ( $childHeight + $gap ) - $gap;

                        echo '<div class="mptrs_parentDiv" id="mptrs_parentDiv" style="position: absolute; width: ' . esc_attr( $parent_width ) . 'px; height: ' . esc_attr( $parent_height ) . 'px;">';

                        $templates = $template_id ? array_map( 'absint', explode( '_', $template_id ) ) : array( $post->ID );

                        foreach ( $templates as $template ) {
                            $plan_data = get_post_meta( $template, '_mptrs_seat_maps_data', true );
                            $plan_seats = isset( $plan_data['seat_data'] ) ? $plan_data['seat_data'] : array();
                            $plan_seat_texts = isset( $plan_data['seat_text_data'] ) ? $plan_data['seat_text_data'] : array();
                            $dynamic_shapes = isset( $plan_data['dynamic_shapes'] ) ? $plan_data['dynamic_shapes'] : '';

                            if ( is_array( $dynamic_shapes ) && count( $dynamic_shapes ) > 0 ) {
                                foreach ( $dynamic_shapes as $dynamic_shape ) {
                                    $shape_rotate_deg = isset( $dynamic_shape['shapeRotateDeg'] ) ? absint( $dynamic_shape['shapeRotateDeg'] ) : 0;
                                    echo '<div class="mptrs_dynamicShape ui-resizable ui-draggable ui-draggable-handle" data-shape-rotate="' . esc_attr( $shape_rotate_deg ) . '" style=" 
                                        left: ' . esc_attr( $dynamic_shape['textLeft'] ) . 'px; 
                                        top: ' . esc_attr( $dynamic_shape['textTop'] ) . 'px; 
                                        width: ' . esc_attr( $dynamic_shape['width'] ) . 'px;
                                        height: ' . esc_attr( $dynamic_shape['height'] ) . 'px;
                                        background-color: ' . esc_attr( $dynamic_shape['backgroundColor'] ) . '; 
                                        border-radius: ' . esc_attr( $dynamic_shape['borderRadius'] ) . ';
                                        clip-path: ' . esc_attr( $dynamic_shape['clipPath'] ) . ';
                                        transform: rotate(' . esc_attr( $shape_rotate_deg ) . 'deg);">
                                    </div>';
                                }
                            }

                            if ( is_array( $plan_seat_texts ) && count( $plan_seat_texts ) > 0 ) {
                                foreach ( $plan_seat_texts as $plan_seat_text ) {
                                    $text_rotate_deg = isset( $plan_seat_text['textRotateDeg'] ) ? absint( $plan_seat_text['textRotateDeg'] ) : 0;
                                    echo '<div class="text-wrapper" data-text-degree="' . esc_attr( $text_rotate_deg ) . '"
                                        style="
                                        position: absolute; 
                                        left: ' . esc_attr( $plan_seat_text['textLeft'] ) . 'px; 
                                        top: ' . esc_attr( $plan_seat_text['textTop'] ) . 'px; 
                                        transform: rotate(' . esc_attr( $text_rotate_deg ) . 'deg);">
                                        <span class="dynamic-text" 
                                            style="
                                                display: block; 
                                                color: ' . esc_attr( $plan_seat_text['color'] ) . '; 
                                                font-size: ' . esc_attr( $plan_seat_text['fontSize'] ) . ';
                                                cursor: pointer;">
                                            ' . esc_html( $plan_seat_text['text'] ) . '
                                        </span>
                                    </div>';
                                }
                            }
                            foreach ( $seats as $seat ) {
                                $isSelected = false;
                                $row = $seat['row'];
                                $col = $seat['col'];
                                $left = $row * ( $childWidth + $gap ) + 10;
                                $top = $col * ( $childHeight + $gap ) + 10;
                                $seat_number = $col * $columns + $row;
                                $seat_num = '';
                                $seat_price = 0;
                                $background_color = '';
                                $zindex = 'auto';
                                $to = $top;
                                $le = $left;
                                $width = $childWidth;
                                $height = $childHeight;
                                $degree = 0;
                                $background_img_url = '';
                                $seat_icon_name = '';

                                if ( is_array( $plan_seats ) && count( $plan_seats ) > 0 ) {
                                    foreach ( $plan_seats as $plan_seat ) {
                                        if ( $plan_seat['col'] == $row && $plan_seat['row'] == $col ) {

                                            $isSelected = true;
                                            $background_color = sanitize_text_field( $plan_seat['color'] ) ;
                                            $seat_num = isset( $plan_seat['seat_number'] ) ? sanitize_text_field( $plan_seat['seat_number'] ) : '';
                                            $seat_price = floatval( $plan_seat['price'] );
                                            $width = absint( $plan_seat['width'] );
                                            $height = absint( $plan_seat['height'] );
                                            $zindex = absint( $plan_seat['z_index'] );
                                            $to = absint( $plan_seat['top'] );
                                            $le = absint( $plan_seat['left'] );
                                            $degree = absint( $plan_seat['data_degree'] );
                                            if ( isset( $plan_seat['backgroundImage'] ) && $plan_seat['backgroundImage'] !== '' ) {
                                                $seat_icon_name = sanitize_file_name( $plan_seat['backgroundImage'] );
                                                $background_img_url = esc_url( MPTRS_Plan_ASSETS . 'images/icons/seatIcons/' . $plan_seat['backgroundImage'] . '.png' );
                                            }
                                            break;
                                        }
                                    }
                                }

                                $class = $isSelected ? ' save ' : '';
                                $color = $isSelected ? $background_color : '';
                                $seat_number = $isSelected ? $seat_num : '';
                                $wi = $isSelected ? $width : $childWidth;
                                $hi = $isSelected ? $height : $childHeight;
                                $zindex = $isSelected ? $zindex : 'auto';
                                $top = $isSelected ? $to : $top;
                                $left = $isSelected ? $le : $left;

                                $hover_price = $seat_price === 0 ? '' : 'Price: ' . esc_attr( $seat_price );
                                $block = $seat_num ? 'block' : 'none';

                                echo '<div class=" childDiv ' . esc_attr( $class ) . '"
                                    id = "div' . esc_attr( $col ) . '_' . esc_attr( $row ) . '"
                                    data-id="' . esc_attr( $col ) . '-' . esc_attr( $row ) . '" 
                                    data-row="' . esc_attr( $col ) . '" 
                                    data-col="' . esc_attr( $row ) . '" 
                                    data-seat-num=" ' . esc_attr( $seat_num ) . ' " 
                                    data-price=" ' . esc_attr( $seat_price ) . ' " 
                                    data-degree=' . esc_attr( $degree ) . '
                                    data-background-image="' . esc_attr( $seat_icon_name ) . '"
                                    style="
                                    left: ' . esc_attr( $left ) . 'px; 
                                    top: ' . esc_attr( $top ) . 'px;
                                    width: ' . esc_attr( $wi ) . 'px;
                                    height: ' . esc_attr( $hi ) . 'px;
                                    background-color: ' . esc_attr( $color ) . ';
                                    background-image:url(' . esc_url( $background_img_url ) . ');
                                    z-index: ' . esc_attr( $zindex ) . ';
                                    transform: rotate(' . esc_attr( $degree ) . 'deg);
                                    ">
                                    <div class="tooltip" style="display: none;z-index: 999">' . esc_attr( $hover_price ) . '</div>
                                    <div class="seatNumber" id="seatNumber' . esc_attr( $col ) . '_' . esc_attr( $row ) . '" style="display: ' . esc_attr( $block ) . ';">' . esc_html( $seat_num ) . '</div>
                                </div>';
                            }
                        }

                        $seat_icons_dir = MPTRS_Plan_PATH . '/assets/images/icons/seatIcons';
                        $images = array_diff( scandir( $seat_icons_dir ), array( '.', '..' ) );
                        $image_files = array_filter( $images, function( $file ) use ( $seat_icons_dir ) {
                            $file_path = $seat_icons_dir . '/' . $file;
                            $allowed_extensions = array( 'jpg', 'jpeg', 'png', 'gif', 'webp' );
                            $extension = strtolower( pathinfo( $file_path, PATHINFO_EXTENSION ) );
                            return in_array( $extension, $allowed_extensions );
                        } );
                        $image_files = array_values( $image_files );

                        $icon_images = '<div class="seatIconHolder" id="seatIconHolder">';
                        foreach ( $image_files as $seat_icon ) {
                            if ( $seat_icon === 'uploadIcon.png' || $seat_icon === 'remove.png' ) {
                                continue;
                            }
                            if ( $seat_icon ) {
                                $split_image = explode( '.', $seat_icon );
                                $icon_images .= '<img class="seatIcon" id="' . esc_attr( $split_image[0] ) . '" src="' . esc_url( MPTRS_Plan_ASSETS . 'images/icons/seatIcons/' . $seat_icon ) . '"/>';
                            }
                        }
                        $icon_images .= '<img alt="No" class="seatIcon" id="seatnull" src="' . esc_url( MPTRS_Plan_ASSETS . 'images/icons/seatIcons/remove.png' ) . '"/>
                                         <div class="seat-icon-upload-container" style="display: block">
                                             <label for="seatIconUpload" class="seat-icon-upload-label">
                                                <img src="' . esc_url( MPTRS_Plan_ASSETS . 'images/icons/seatIcons/uploadIcon.png' ) . '" alt="Upload Icon" class="seat-icon-image">
                                             </label>
                                            <input class="seatIconUpload" type="file" id="seatIconUpload" name="filename">
                                         </div>  
                                     </div>';
                        echo '</div> 
                            </div>
                            <div class="seatActionControl">
                                <div class="dynamicShapeHolder" id="dynamicShapeHolder">
                                    ' . $shapeText . '
                                </div>
                                <div class="dynamicShapeColorHolder" style="display: none">
                                    <div class="dynamicShapeControl">
                                        <div class="dynamicShapeControlText">Shape Setting</div>
                                        <div class="colorRemoveHolder">
                                            <div class="shapeRotationHolder">
                                                <img class="shapeRotate" id="shapeRotateRight" src="' . esc_url( MPTRS_Plan_ASSETS . 'images/icons/rotate/rotate_right.webp' ) . '"/>
                                                <img class="shapeRotate" id="shapeRotateLeft" src="' . esc_url( MPTRS_Plan_ASSETS . 'images/icons/rotate/rotate_left.webp' ) . '"/>
                                            </div>
                                            <input type="color" id="setShapeColor" value="#3498db">
                                            <button class="removeDynamicShape" id="removeDynamicShape">X</button>
                                        </div>
                                        <div class="shapeDisplayIconHolder">
                                            <div class="shapeIconTitleTextHolder"><span class="shapeIconTitleText">Add Shape</span></div>
                                                <div class="shapeDisplayIcons">
                                                    <img class="shapeDisplayIcon" id="table1" src="' . esc_url( MPTRS_Plan_ASSETS . 'images/icons/tableIcon/table1.png' ) . '"/>
                                                    <img class="shapeDisplayIcon" id="table2" src="' . esc_url( MPTRS_Plan_ASSETS . 'images/icons/tableIcon/table2.png' ) . '"/>
                                                    <img class="shapeDisplayIcon" id="table3" src="'. esc_url( MPTRS_Plan_ASSETS.'images/icons/tableIcon/table3.png' ) .'"/>
                                                    <img class="shapeDisplayIcon" id="table4" src="'. esc_url( MPTRS_Plan_ASSETS.'images/icons/tableIcon/table4.png' ) .'"/>
                                                    <img class="shapeDisplayIcon" id="dining2" src="'. esc_url( MPTRS_Plan_ASSETS.'images/icons/tableIcon/dining2.png' ) .'"/>
                                                    <img class="shapeDisplayIcon" id="dining1" src="'. esc_url( MPTRS_Plan_ASSETS.'images/icons/tableIcon/dining1.png' ) .'"/>
                                                </div>
                                            </div>
                                            <div class="copyHolder">
                                                <button class="shapeCopyStore">Copy</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="dynamicTextControlHolder" style="display: none">
                                        <div class="dynamicTextControlText">Text Setting</div>
                                        <div class="dynamicTextControlContainer">
                                            <div class="textControl">
                                                <button class="zoom-in">+</button>
                                                <button class="zoom-out">-</button>
                                                <button class="remove-text">X</button>
                                                <input type="color" id="setTextColor" value="#3498db">
                                            </div>
                                            <div class="textRotationHolder">
                                                <img class="textRotate" id="textRotateRight" src="'.MPTRS_Plan_ASSETS.'images/icons/rotate/rotate_right.webp'.'"/>
                                                <img class="textRotate" id="textRotateLeft" src="'.MPTRS_Plan_ASSETS.'images/icons/rotate/rotate_left.webp'.'"/>
                                            </div>
                                        </div>
                                        <div class="copyHolder">
                                            <button class="textCopy">Copy</button>
                                        </div>
                                    </div>
                                    
                                    <button id="clearAll"> All Clear</button>
                                    <button class="savePlan" id="savePlan">Save Plan</button>
                                    <button class="savePlan" id="savePlanAsTemplate">Save Plan with Template</button>
                                    <button id="setTextPlan" class="setTextPlan" style="display: none">Set text</button>
                                    <div class="setPriceColorHolder" id="setPriceColorHolder" style="display: none">
                                        <div class="copyHolder">
                                            <button class="seatCopyStore">Copy</button>
                                        </div>
                                        <div class="rotateControls">
                                            <select class="rotationHandle" name="rotationHandle" id="rotationHandle" style="display: none">
                                                <option class="options" selected value="top-to-bottom">Rotate top to bottom</option>
                                                <option class="options"  value="bottom-to-top">Rotate bottom to Top</option>
                                                <option class="options"  value="right-to-left">Rotate right to Left</option>
                                                <option class="options"  value="left-to-right">Rotate left to Right</option>
                                            </select>
                                            <div class="seatRotateIconTextHolder">
                                                <span class="seatRotateIconText">Seat Rotate In Degree</span>
                                                <div class="seatRotateIconImgHolder"> 
                                                    <div class="seatRotateIconHolder">
                                                        <img class="shapeRotate" id="rotateRight" src="'.MPTRS_Plan_ASSETS.'images/icons/rotate/rotate_right.webp'.'"/>
                                                        <img class="shapeRotate" id="rotateLeft" src="'.MPTRS_Plan_ASSETS.'images/icons/rotate/rotate_left.webp'.'"/>
                                                    </div>
                                                    <input class="seatRotateDegree" type="number" name="rotationAngle" id="rotationAngle" value="10" placeholder="10 degree">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="seatIconContainer">
                                            <span class="seatIconTitle">Select seat icon</span>
                                            '.$icon_images.'
                                        </div>
                                        <div class="movementHolder" id="movementHolder">
                                             <div class="movementControl">
                                                <span class="movementText">Movement In Px</span>
                                                <input class="movementInPx" id="movementInPx" name="movementInPx" type="number" value="15" placeholder="movement in px" style="display: none">
                                            </div>
                                            <div class="movementControl">
                                                <div id="left" class="movement"><i class="arrowIcon far fa-arrow-alt-circle-left"></i></div>
                                                <div id="top" class="movement"><i class="arrowIcon far fa-arrow-alt-circle-up"></i></div>
                                                <div id="bottom" class="movement"><i class="arrowIcon far fa-arrow-alt-circle-down"></i></div>
                                                <div id="right" class="movement"><i class="arrowIcon far fa-arrow-alt-circle-right"></i></div>
                                            </div>
                                        </div>
                                        <div class="colorPriceHolder">
                                            <div>
                                                <span>Select Color</span>:
                                                <input type="color" id="setColor" value="#3498db">
                                            </div>
                                            <button id="applyColorChanges">Set Color</button>
                                        </div>
                                        <div class="colorPriceHolder">
                                            <div class="textPriceHolder">
                                                <span class="priceText"> Set Price:</span>
                                                <input type="number" id="setPrice" placeholder="Enter price">
                                            </div>
                                            <button id="applyChanges">Set Price</button>
                                        </div>
                                        <div class="setSeatNumber"  style="display: block">
                                             <div class="seatNumberContainer">
                                                <input type="text" id="seat_number_prefix" placeholder="Set Prefix">
                                                <input type="number" id="seat_number_count" placeholder="1" value="0">
                                             </div>
                                            <button class="set_seat_number" id="set_seat_number">Set Seat Number</button>
                                        </div>
                                    </div>
                                </div>
                            </div>';
            }

			public function seat_mapping_tab_content($post_id) {

                $post = get_post($post_id);
//                error_log( print_r( $post, true ) );

				?>
                
                <div class="tabsItem" data-tabs="#mptrs_seat_mapping">
                    <header>
                        <h2><?php esc_html_e('Seat Mapping Settings', 'tablely'); ?></h2>
                        <span><?php esc_html_e('Seat Mapping Settings will be here.', 'tablely'); ?></span>
                    </header>
                    <section class="section">
                        <h2><?php esc_html_e('Seat Mapping Settings', 'tablely'); ?></h2>
                        <span><?php esc_html_e('Seat Mapping', 'tablely'); ?></span>
                    </section>

                    <section class="mptrs-seat-mapping-section">
                    <?php echo  $this->render_meta_box( $post_id );?>
                    </section>
                </div>
				<?php
			}
		}
		new MPTRS_Seat_Mapping();
	}