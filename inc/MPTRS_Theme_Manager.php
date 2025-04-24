<?php
/**
 * Tablely Restaurant Theme Manager
 * Handles color theme settings and customizations
 */

if (!defined('ABSPATH')) {
    die;
} // Cannot access pages directly.

if (!class_exists('MPTRS_Theme_Manager')) {
    class MPTRS_Theme_Manager {
        
        public function __construct() {
            // Add color theme settings to the style settings section
            add_filter('filter_mptrs_style_settings', array($this, 'add_color_theme_settings'));
            
            // Enqueue color theme assets
            add_action('admin_enqueue_scripts', array($this, 'enqueue_color_theme_assets'));
            add_action('wp_enqueue_scripts', array($this, 'enqueue_color_theme_assets'));
            
            // Add translation strings for JS
            add_action('admin_footer', array($this, 'add_translation_strings'));
            
            // Add theme color output to head
            add_action('admin_head', array($this, 'add_theme_css_variables'), 99);
            add_action('wp_head', array($this, 'add_theme_css_variables'), 99);
        }
        
        /**
         * Add color theme settings to the style settings section
         */
        public function add_color_theme_settings($style_settings) {
            // Add new color settings
            $new_settings = array(
                array(
                    'name' => 'secondary_color',
                    'label' => esc_html__('Secondary Color', 'tablely'),
                    'desc' => esc_html__('Select Secondary/Accent Color', 'tablely'),
                    'type' => 'color',
                    'default' => '#FF5722'
                ),
                array(
                    'name' => 'success_color',
                    'label' => esc_html__('Success Color', 'tablely'),
                    'desc' => esc_html__('Select Success Color for status indicators', 'tablely'),
                    'type' => 'color',
                    'default' => '#10B981'
                ),
                array(
                    'name' => 'info_color',
                    'label' => esc_html__('Info Color', 'tablely'),
                    'desc' => esc_html__('Select Info Color for notifications and indicators', 'tablely'),
                    'type' => 'color',
                    'default' => '#3B82F6'
                ),
            );
            
            // Insert after default text color (position 3)
            array_splice($style_settings, 3, 0, $new_settings);
            
            return $style_settings;
        }
        
        /**
         * Enqueue color theme assets
         */
        public function enqueue_color_theme_assets() {
            // Only enqueue on admin pages or frontend pages with tablely content
            if (is_admin() || has_shortcode(get_the_content(), 'mptrs')) {
                // Enqueue color theme CSS
                wp_enqueue_style(
                    'mptrs-color-themes',
                    MPTRS_Plan_ASSETS . 'admin/css/mptrs_color_themes.css',
                    array()
                );
                
                // Enqueue color theme JS
                wp_enqueue_script(
                    'mptrs-color-themes',
                    MPTRS_Plan_ASSETS . 'admin/js/mptrs_color_themes.js',
                    array('jquery'),
                    null,
                    true
                );
            }
        }
        
        /**
         * Add translation strings for the color theme JavaScript
         */
        public function add_translation_strings() {
            if (!is_admin()) return;
            
            // Prepare translation strings for JS
            $translations = array(
                'colorTheme' => esc_html__('Color Theme', 'tablely'),
                'selectColorTheme' => esc_html__('Select a predefined color theme or customize your own colors below.', 'tablely'),
                'themeNote' => esc_html__('Note: Selecting a theme will update your color settings below.', 'tablely'),
                'filterByStatus' => esc_html__('Filter by Status', 'tablely'),
                'allStatuses' => esc_html__('All Statuses', 'tablely'),
                'active' => esc_html__('Active', 'tablely'),
                'closed' => esc_html__('Closed', 'tablely'),
                'tempClosed' => esc_html__('Temporarily Closed', 'tablely'),
                'draft' => esc_html__('Draft', 'tablely'),
                'searchRestaurants' => esc_html__('Search restaurants...', 'tablely'),
            );
            
            // Output JS object with translations
            echo '<script type="text/javascript">
                window.mptrsTrans = ' . json_encode($translations) . ';
            </script>';
        }
        
        /**
         * Add theme CSS variables to head
         */
        public function add_theme_css_variables() {
            // Get theme color settings
            $theme_color = MPTRS_Function::get_style_settings('theme_color', '#0793C9');
            $secondary_color = MPTRS_Function::get_style_settings('secondary_color', '#FF5722');
            $success_color = MPTRS_Function::get_style_settings('success_color', '#10B981');
            $warning_color = MPTRS_Function::get_style_settings('warning_color', '#E67C30');
            $info_color = MPTRS_Function::get_style_settings('info_color', '#3B82F6');
            $danger_color = MPTRS_Function::get_style_settings('warning_color', '#EF4444');
            
            // Calculate darker and lighter versions
            $primary_dark = $this->adjust_brightness($theme_color, -20);
            $primary_light = $this->adjust_brightness($theme_color, 20);
            $secondary_dark = $this->adjust_brightness($secondary_color, -20);
            $secondary_light = $this->adjust_brightness($secondary_color, 20);
            
            // Convert hex to rgb for rgba usage
            $primary_rgb = $this->hex_to_rgb($theme_color);
            $secondary_rgb = $this->hex_to_rgb($secondary_color);
            $success_rgb = $this->hex_to_rgb($success_color);
            $warning_rgb = $this->hex_to_rgb($warning_color);
            $danger_rgb = $this->hex_to_rgb($danger_color);
            $info_rgb = $this->hex_to_rgb($info_color);
            
            // Output custom CSS variables
            echo '<style id="mptrs-dynamic-colors">
                :root {
                    --mptrs-primary: ' . esc_attr($theme_color) . ';
                    --mptrs-primary-dark: ' . esc_attr($primary_dark) . ';
                    --mptrs-primary-light: ' . esc_attr($primary_light) . ';
                    --mptrs-secondary: ' . esc_attr($secondary_color) . ';
                    --mptrs-secondary-dark: ' . esc_attr($secondary_dark) . ';
                    --mptrs-secondary-light: ' . esc_attr($secondary_light) . ';
                    
                    --mptrs-success: ' . esc_attr($success_color) . ';
                    --mptrs-warning: ' . esc_attr($warning_color) . ';
                    --mptrs-danger: ' . esc_attr($danger_color) . ';
                    --mptrs-info: ' . esc_attr($info_color) . ';
                    
                    --mptrs-primary-rgb: ' . esc_attr($primary_rgb) . ';
                    --mptrs-secondary-rgb: ' . esc_attr($secondary_rgb) . ';
                    --mptrs-success-rgb: ' . esc_attr($success_rgb) . ';
                    --mptrs-warning-rgb: ' . esc_attr($warning_rgb) . ';
                    --mptrs-danger-rgb: ' . esc_attr($danger_rgb) . ';
                    --mptrs-info-rgb: ' . esc_attr($info_rgb) . ';
                }
            </style>';
        }
        
        /**
         * Helper function to adjust color brightness
         * 
         * @param string $hex Hex color code
         * @param int $steps Steps to adjust brightness (negative = darker, positive = lighter)
         * @return string Adjusted hex color
         */
        private function adjust_brightness($hex, $steps) {
            // Remove # if present
            $hex = ltrim($hex, '#');
            
            // Convert to RGB
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
            
            // Adjust brightness
            $r = max(0, min(255, $r + $steps));
            $g = max(0, min(255, $g + $steps));
            $b = max(0, min(255, $b + $steps));
            
            // Convert back to hex
            return '#' . sprintf('%02x%02x%02x', $r, $g, $b);
        }
        
        /**
         * Helper function to convert hex to rgb
         * 
         * @param string $hex Hex color code
         * @return string RGB values separated by commas
         */
        private function hex_to_rgb($hex) {
            // Remove # if present
            $hex = ltrim($hex, '#');
            
            // Convert to RGB
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
            
            return $r . ', ' . $g . ', ' . $b;
        }
    }
    
    // Initialize the theme manager
    new MPTRS_Theme_Manager();
} 