/**
 * Tablely - Restaurant Management Plugin
 * Color Theme Selection and Management
 * Version: 1.0.0
 */

(function($) {
    'use strict';

    // Theme options with their color values
    const themeOptions = {
        default: {
            primary: '#0793C9',
            secondary: '#FF5722'
        },
        blue: {
            primary: '#3B82F6',
            secondary: '#F97316'
        },
        green: {
            primary: '#10B981',
            secondary: '#8B5CF6'
        },
        purple: {
            primary: '#8B5CF6',
            secondary: '#EC4899'
        },
        dark: {
            primary: '#1F2937',
            secondary: '#F97316'
        }
    };

    // Initialize color theme functionality
    function initColorThemes() {
        // Add theme selector to settings page if it's available
        $(document).ready(function() {
            if ($('.mptrs_style_settings').length > 0) {
                addThemeSelector();
            }
            
            // Apply saved theme if it exists
            applySavedTheme();
            
            // Add color filter controls to the restaurant list page
            if ($('.mptrs_restaurant_page_wrap').length > 0) {
                addColorFilterControls();
            }
        });
    }

    // Apply the previously saved theme from localStorage
    function applySavedTheme() {
        const savedTheme = localStorage.getItem('mptrs_color_theme') || 'default';
        
        // Remove any existing theme classes
        $('body').removeClass(function(index, className) {
            return (className.match(/(^|\s)mptrs-theme-\S+/g) || []).join(' ');
        });
        
        // Add the saved theme class if it's not the default
        if (savedTheme !== 'default') {
            $('body').addClass('mptrs-theme-' + savedTheme);
        }
        
        // Update color picker fields if they exist
        updateColorPickerFields(savedTheme);
    }

    // Add theme selector to the style settings page
    function addThemeSelector() {
        // Create selector HTML
        const selectorHtml = `
            <div class="mptrs_color_scheme_section">
                <h3>${mptrsTrans.colorTheme || 'Color Theme'}</h3>
                <p>${mptrsTrans.selectColorTheme || 'Select a predefined color theme or customize your own colors below.'}</p>
                
                <div class="mptrs_color_theme_selector">
                    ${createThemeOptions()}
                </div>
                <p class="description">${mptrsTrans.themeNote || 'Note: Selecting a theme will update your color settings below.'}</p>
            </div>
        `;
        
        // Insert before the first color setting
        $('.mptrs_style_settings table tr:first').before(selectorHtml);
        
        // Add click handler for theme options
        $('.mptrs_color_theme_option').on('click', function() {
            const themeId = $(this).data('theme');
            selectTheme(themeId);
        });
    }

    // Create HTML for theme options
    function createThemeOptions() {
        let optionsHtml = '';
        
        const savedTheme = localStorage.getItem('mptrs_color_theme') || 'default';
        
        for (const [themeId, colors] of Object.entries(themeOptions)) {
            const activeClass = themeId === savedTheme ? 'active' : '';
            
            optionsHtml += `
                <div class="mptrs_color_theme_option ${activeClass}" data-theme="${themeId}">
                    <div class="mptrs_color_preview">
                        <div class="mptrs_color_preview_main" style="background-color: ${colors.primary}"></div>
                        <div class="mptrs_color_preview_accent" style="background-color: ${colors.secondary}"></div>
                    </div>
                    <div class="mptrs_color_theme_name">${capitalizeFirstLetter(themeId)}</div>
                </div>
            `;
        }
        
        return optionsHtml;
    }

    // Select a theme and update color settings
    function selectTheme(themeId) {
        // Update active class
        $('.mptrs_color_theme_option').removeClass('active');
        $(`.mptrs_color_theme_option[data-theme="${themeId}"]`).addClass('active');
        
        // Save selection to localStorage
        localStorage.setItem('mptrs_color_theme', themeId);
        
        // Remove any existing theme classes and add the selected one
        $('body').removeClass(function(index, className) {
            return (className.match(/(^|\s)mptrs-theme-\S+/g) || []).join(' ');
        });
        
        if (themeId !== 'default') {
            $('body').addClass('mptrs-theme-' + themeId);
        }
        
        // Update color picker fields
        updateColorPickerFields(themeId);
    }

    // Update color picker fields with the selected theme colors
    function updateColorPickerFields(themeId) {
        const themeColors = themeOptions[themeId];
        
        if (!themeColors) return;
        
        // Update theme color field if it exists
        const $themeColorField = $('input[name="mptrs_style_settings[theme_color]"]');
        if ($themeColorField.length) {
            $themeColorField.val(themeColors.primary).change();
            $themeColorField.closest('.wp-picker-container').find('.wp-color-result').css('background-color', themeColors.primary);
        }
        
        // Update secondary color fields if they exist (button bg, etc.)
        const $buttonBgField = $('input[name="mptrs_style_settings[button_bg]"]');
        if ($buttonBgField.length) {
            $buttonBgField.val(themeColors.secondary).change();
            $buttonBgField.closest('.wp-picker-container').find('.wp-color-result').css('background-color', themeColors.secondary);
        }
    }

    // Add color filter controls to the restaurant list page
    function addColorFilterControls() {
        // Check if filter already exists (to avoid duplicates)
        if ($('#mptrsStatusFilter').length > 0) {
            // If it already exists, remove the duplicate
            $('.mptrs_color_filters').slice(1).remove();
            return;
        }
        
        const filterControlsHtml = `
            <div class="mptrs_color_filters mptrs_modern_filters">
                <div class="mptrs_filter_group">
                    <label for="mptrsStatusFilter">${mptrsTrans.filterByStatus || 'Filter by Status'}</label>
                    <select id="mptrsStatusFilter" class="mptrs_status_filter">
                        <option value="all">${mptrsTrans.allStatuses || 'All Statuses'}</option>
                        <option value="active">${mptrsTrans.active || 'Active'}</option>
                        <option value="closed">${mptrsTrans.closed || 'Closed'}</option>
                        <option value="temp-closed">${mptrsTrans.tempClosed || 'Temporarily Closed'}</option>
                        <option value="draft">${mptrsTrans.draft || 'Draft'}</option>
                    </select>
                </div>
            </div>
        `;
        
        // Add to filters section if it exists
        if ($('.mptrs_filters_section').length) {
            // First, clear any existing filter HTML
            $('.mptrs_filters_section').html('');
            
            // Add search box
            $('.mptrs_filters_section').append(`
                <div class="mptrs_search_container">
                    <input type="text" id="mptrsRestaurantSearch" placeholder="${mptrsTrans.searchRestaurants || 'Search restaurants...'}">
                    <button id="mptrsRestaurantSearchBtn"><i class="fas fa-search"></i></button>
                </div>
            `);
            
            // Add our new filters
            $('.mptrs_filters_section').append(filterControlsHtml);
            
            // Add filter functionality
            $('#mptrsStatusFilter').on('change', function() {
                const statusFilter = $(this).val();
                
                if (statusFilter === 'all') {
                    $('.mptrs_restaurant_row').show();
                } else {
                    $('.mptrs_restaurant_row').hide();
                    $(`.mptrs_restaurant_row .mptrs_restaurant_status.status-${statusFilter}`).closest('.mptrs_restaurant_row').show();
                }
            });
            
            // Re-add search functionality
            $('#mptrsRestaurantSearch').on('keyup', function() {
                const searchTerm = $(this).val().toLowerCase();
                
                $('.mptrs_restaurant_row').each(function() {
                    const rowData = $(this).data('search').toLowerCase();
                    if (rowData.indexOf(searchTerm) > -1) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
                
                if ($('.mptrs_restaurant_row:visible').length === 0) {
                    if ($('#mptrs-no-results').length === 0) {
                        $('#mptrs-restaurant-list').append('<tr id="mptrs-no-results"><td colspan="6" class="mptrs_empty_state"><p>No restaurants match your search.</p></td></tr>');
                    }
                } else {
                    $('#mptrs-no-results').remove();
                }
            });
            
            $('#mptrsRestaurantSearchBtn').on('click', function() {
                $('#mptrsRestaurantSearch').trigger('keyup');
            });
        }
    }

    // Helper function to capitalize the first letter of a string
    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    // Initialize when document is ready
    $(document).ready(function() {
        // Define translations object if it doesn't exist
        window.mptrsTrans = window.mptrsTrans || {};
        
        // Initialize color themes
        initColorThemes();
    });

})(jQuery); 