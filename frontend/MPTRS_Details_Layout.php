<?php
	/*
   * @Author 		engr.sumonazma@gmail.com
   * Copyright: 	mage-people.com
   */
	if ( ! defined( 'ABSPATH' ) ) {
		die;
	} // Cannot access pages directly.
	if ( ! class_exists( 'MPTRS_Details_Layout' ) ) {
		class MPTRS_Details_Layout {
			public function __construct() {
				/**************/
			}

            public static function display_seat_mapping( $post_id, $not_available_seats = [] ): string{

                $content = '';
                global $post;
//                $post_id = $post->ID;
                $plan_data = get_post_meta($post_id, '_mptrs_seat_maps_data', true);
                $plan_seats = isset( $plan_data['seat_data'] ) ? $plan_data['seat_data'] : array();
                $plan_seat_texts = isset( $plan_data['seat_text_data'] ) ? $plan_data['seat_text_data'] : array();
                $dynamic_shapes = isset( $plan_data['dynamic_shapes'] ) ? $plan_data['dynamic_shapes'] : '';


                if (!empty($plan_seats) && is_array( $plan_seats )) {
                    $leastLeft = PHP_INT_MAX;
                    $leastTop = PHP_INT_MAX;

                    foreach ($plan_seats as $item) {
                        if (isset($item["left"])) {
                            $currentLeft = (int)rtrim($item["left"], "px");
                            $currentTop = (int)rtrim($item["top"], "px");

                            if ($currentLeft < $leastLeft) {
                                $leastLeft = $currentLeft;
                            }
                            if ($currentTop < $leastTop) {
                                $leastTop = $currentTop;
                            }
                        }
                    }

                    $data_tableBindIds = [];
                    $height = $leastTop + 200;
                    $seat_grid_width = $leastLeft + 1;
                    $seat_grid_height = $leastTop + 1;
                    // Start building custom content
                    $custom_content = '
                    <div id="mptrs_seatInfo" style="margin-top: 20px; font-size: 16px;">
                        <strong>Seat Info:</strong> <span id="info"></span>
                        </div>
                        <div id="mptrs_seatGrid" /*style="height: '.$height.'px"*/>
                        <div id="mptrs_seatMapHolder-'.$post_id.'" class="mptrs_seatMapHolder">';
                            if( is_array( $plan_seat_texts ) && count( $plan_seat_texts ) > 0 ) {
                                foreach ($plan_seat_texts as $plan_seat_text) {
                                    $text_rotate_deg = isset($plan_seat_text['textRotateDeg']) ? $plan_seat_text['textRotateDeg'] : 0;
                                    $custom_content .= '
                                    <div class="mptrs_dynamicTextWrapper" data-text-degree=' . $text_rotate_deg . '
                                        style="
                                        position: absolute; 
                                        left: ' . ((int)$plan_seat_text['textLeft'] - $leastLeft) . 'px; 
                                        top: ' . ((int)$plan_seat_text['textTop'] - $leastTop) . 'px; 
                                        transform: rotate(' . $text_rotate_deg . 'deg);
                                        ">
                                        <span class="mptrs_dynamicText" 
                                            style="
                                                display: inline-block; 
                                                color: ' . $plan_seat_text['color'] . '; 
                                                font-size: ' . $plan_seat_text['fontSize'] . ';
                                                cursor: pointer;">
                                           ' . $plan_seat_text['text'] . '
                                        </span>
                                    </div>';
                                }
                            }
                            foreach ($plan_seats as $seat) {
                                if ( isset($seat["left"] ) ) {

//                                    error_log( print_r( [ 'data_tableBind' => $seat['data_tableBind'] ], true ) );
                                   $data_tableBind = isset( $seat['data_tableBind'] ) ? $seat['data_tableBind'] : '';
                                    if( $data_tableBind !== '' ){
                                        $data_tableBindIds[] = $seat['data_tableBind'];
                                    }

                                    $seat_id = isset( $seat['id'] ) ? $seat['id'] : 0;
                                    if( $seat_id !== 0 ){
                                        $seat_id = 'seat_'.$seat['id'];
                                    }

                                    $parent_class_name = 'mptrs_mappedSeat';
                                    $class_name = 'mptrs_mappedSeatInfo';
                                    $seat_bg_color = esc_attr( $seat['color']);
                                    if( in_array(  $seat_id, $not_available_seats ) ) {
                                        $seat_bg_color = '#333333';
                                        $class_name = 'mptrs_reservedMappedSeatInfo';
                                        $parent_class_name = 'mptrs_reservedMappedSeat';
                                    }

                                    $icon_url = '';
                                    $width = isset($seat['width']) ? (int)$seat['width'] : 0;
                                    $height = isset($seat['height']) ? (int)$seat['height'] : 0;
                                    $uniqueId = "seat_{$seat['id']}"; // Unique ID for each seat
                                    $border_radius = isset( $seat['border_radius'] ) ? $seat['border_radius'] : '';

                                    if( isset( $seat['backgroundImage'] ) && $seat['backgroundImage'] !== '' ){
                                        $icon_url = MPTRS_Plan_ASSETS."images/icons/seatIcons/".$seat['backgroundImage'].".png";
                                    }

                                    $tableBind = isset( $seat['data_tableBind'] ) ? $seat['data_tableBind'] : '';

                                    $custom_content .= '<div class="'.esc_attr( $parent_class_name ).'" 
                                                        id="' . esc_attr($uniqueId) . '" 
                                                        data-price="' . esc_attr($seat['price']) . '" 
                                                        data-seat-num="' . esc_attr($seat['seat_number']) . '" 
                                                        data-tableBind="' . esc_attr( $tableBind ) . '"
                                                        style="
                                                            width: ' . $width . 'px;
                                                            height: ' . $height . 'px;
                                                            left: ' . ((int)$seat['left'] - $leastLeft) . 'px;
                                                            top: ' . ((int)$seat['top'] - $leastTop) . 'px;
                                                            border-radius: ' .$border_radius. ';
                                                            transform: rotate('.(int)$seat['data_degree'].'deg);"
                                                        title="Price: $' . esc_attr($seat['price']) . '">
                                                        <div class="'.esc_attr( $class_name ).'" 
                                                            style="
                                                                background-color: ' . $seat_bg_color . ';
                                                                background-image: url('.$icon_url.');
                                                                width: ' . $width . 'px;
                                                                height: ' . $height . 'px;">
                                                            <span class="mptrs_seatNumber">' . esc_html($seat['seat_number'] ?? '') . '</span>
                                                        </div>
                                                    </div>';
                                }
                            }

                    if ( is_array( $dynamic_shapes ) && count( $dynamic_shapes ) > 0 ) {
//                        error_log( print_r( [ '$data_tableBindIds' =>$data_tableBindIds ], true ) );


                        foreach ( $dynamic_shapes as $dynamic_shape ) {
                           /* if( in_array( $dynamic_shape['tableBindID'], $data_tableBindIds)){
                                $shape_class = 'mptrs_selectedDynamicShape';
                            }else{
                                $shape_class = 'mptrs_dynamicShape';
                            }*/

                            $shape_class = 'mptrs_dynamicShape';

                            $shape_rotate_deg = isset( $dynamic_shape['shapeRotateDeg'] ) ? $dynamic_shape['shapeRotateDeg'] : 0;
                            $custom_content .= '<div id="'.esc_attr( $dynamic_shape['tableBindID'] ).'" class="'.$shape_class.'" style=" 
                                                            left: ' . esc_attr( $dynamic_shape['textLeft']  - $leastLeft ) . 'px; 
                                                            top: ' . esc_attr( $dynamic_shape['textTop']  - $leastTop ) . 'px; 
                                                            width: ' . esc_attr( $dynamic_shape['width'] ) . 'px;
                                                            height: ' . esc_attr( $dynamic_shape['height'] ) . 'px;
                                                            background-color: ' . esc_attr( $dynamic_shape['backgroundColor'] ).'; 
                                                            border-radius: ' . esc_attr( $dynamic_shape['borderRadius'] ).';
                                                            clip-path: ' . esc_attr( $dynamic_shape['clipPath'] ).';
                                                            transform: rotate(' . $shape_rotate_deg . 'deg);
                                                        ">
                                                        </div>';
                        }
                    }

                            $custom_content .= '
                        </div>
                    </div>';
                    $content .= $custom_content;
                }
//    }
                return $content;
            }
		}

		new MPTRS_Details_Layout();
	}