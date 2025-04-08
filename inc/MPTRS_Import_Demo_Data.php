<?php
/*
	* Author 	:	MagePeople Team
	* Version	:	1.0.0
	*/

if ( ! defined( 'ABSPATH' ) ) {
    die;
}
if ( ! class_exists( 'MPTRS_Import_Demo_Data' ) ) {
    class MPTRS_Import_Demo_Data{

        public function __construct() {
            add_action( 'plugins_loaded', [ $this, 'mptrs_import_after_plugins_loaded' ], 10, 0 );
        }

        public function mptrs_import_after_plugins_loaded(){
            $sample_rent_items = get_option( 'mptrs_sample_restaurant' );
            if ( $sample_rent_items != 'imported' ) {
                $this->mptrs_import_demo_function();
            }
        }
        public function mptrs_import_demo_function() {
            $xml_url     = MPTRS_PLUGIN_URL . '/assets/sample-restaurant_data.xml';
            $xml         = simplexml_load_file( $xml_url );
            $json_string = wp_json_encode( $xml );
            $xml_array   = json_decode( $json_string, true );
            $item = ! empty( $xml_array['item'] ) ? $xml_array['item'] : [];

            if ( $xml !== false && ! empty( $xml_array ) ) {
                $title   = ! empty( $item['title'] ) ? $item['title'] : '';
                $content = ! empty( $item['content'] ) ? $item['content'] : '';
                unset($rent_args);
                $rent_args = array(
                    'post_title'   => $title,
                    'post_content' => $content,
                    'post_status'  => 'publish',
                    'post_type'    => 'mptrs_item',
                );
                $rent_post_id = wp_insert_post( $rent_args );
                update_option( 'mptrs_restaurant_id', $rent_post_id );
                update_option( 'mptrs_sample_restaurant', 'imported' );

            }
        }

    }

    new MPTRS_Import_Demo_Data();
}