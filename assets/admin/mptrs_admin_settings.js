function mptrs_load_sortable_datepicker(parent, item) {
    if (parent.find('.item_insert_before').length > 0) {
        jQuery(item).insertBefore(parent.find('.item_insert_before').first()).promise().done(function () {
            parent.find('.sortable_area').sortable({
                handle: jQuery(this).find('.sortable_button')
            });
            mptrs_load_date_picker(parent);
        });
    } else {
        parent.find('.item_insert').first().append(item).promise().done(function () {
            parent.find('.sortable_area').sortable({
                handle: jQuery(this).find('.sortable_button')
            });
            mptrs_load_date_picker(parent);
        });
    }
    return true;
}
(function ($) {
    "use strict";
    $(document).ready(function () {
        //=========Short able==============//
        $(document).find('.mptrs_area .sortable_area').sortable({
            handle: $(this).find('.sortable_button')
        });
    });
    //=========upload image==============//
    $(document).on('click', '.mptrs_area .add_single_image', function () {
        let parent = $(this);
        parent.find('.single_image_item').remove();
        wp.media.editor.send.attachment = function (props, attachment) {
            let attachment_id = attachment.id;
            let attachment_url = attachment.url;
            let html = '<div class="single_image_item" data-image-id="' + attachment_id + '"><span class="fas fa-times circleIcon_xs remove_single_image"></span>';
            html += '<img src="' + attachment_url + '" alt="' + attachment_id + '"/>';
            html += '</div>';
            parent.append(html);
            parent.find('input').val(attachment_id);
            parent.find('button').slideUp('fast');
        }
        wp.media.editor.open($(this));
        return false;
    });
    $(document).on('click', '.mptrs_area .remove_single_image', function (e) {
        e.stopPropagation();
        let parent = $(this).closest('.add_single_image');
        $(this).closest('.single_image_item').remove();
        parent.find('input').val('');
        parent.find('button').slideDown('fast');
    });
    $(document).on('click', '.mptrs_area .remove_multi_image', function () {
        let parent = $(this).closest('.multi_image_area');
        let current_parent = $(this).closest('.multi_image_item');
        let img_id = current_parent.data('image-id');
        current_parent.remove();
        let all_img_ids = parent.find('.multi_image_value').val();
        all_img_ids = all_img_ids.replace(',' + img_id, '')
        all_img_ids = all_img_ids.replace(img_id + ',', '')
        all_img_ids = all_img_ids.replace(img_id, '')
        parent.find('.multi_image_value').val(all_img_ids);
    });
    $(document).on('click', '.mptrs_area .add_multi_image', function () {
        let parent = $(this).closest('.multi_image_area');
        wp.media.editor.send.attachment = function (props, attachment) {
            let attachment_id = attachment.id;
            let attachment_url = attachment.url;
            let html = '<div class="multi_image_item" data-image-id="' + attachment_id + '"><span class="fas fa-times circleIcon_xs remove_multi_image"></span>';
            html += '<img src="' + attachment_url + '" alt="' + attachment_id + '"/>';
            html += '</div>';
            parent.find('.mptrs_multi_image').append(html);
            let value = parent.find('.multi_image_value').val();
            value = value ? value + ',' + attachment_id : attachment_id;
            parent.find('.multi_image_value').val(value);
        }
        wp.media.editor.open($(this));
        return false;
    });
    //=========Remove Setting Item ==============//
    $(document).on('click', '.mptrs_area .item_remove', function (e) {
        e.preventDefault();
        if (confirm('Are You Sure , Remove this row ? \n\n 1. Ok : To Remove . \n 2. Cancel : To Cancel .')) {
            $(this).closest('.mp_remove_area').slideUp(250).remove();
            return true;
        } else {
            return false;
        }
    });
    //=========Add Setting Item==============//
    $(document).on('click', '.mptrs_area .add_item', function () {
        let parent = $(this).closest('.settings_area');
        let item = $(this).next($('.hidden_content')).find(' .hidden_item').html();
        if (!item || item === "undefined" || item === " ") {
            item = parent.find('.hidden_content').first().find('.hidden_item').html();
        }
        mptrs_load_sortable_datepicker(parent, item);
        parent.find('.item_insert').find('.add_select2').select2({});
        return true;
    });
}(jQuery));
(function ($) {
    "use strict";
    //=================select icon=========================//
    $(document).on('click', '.mptrs_area .add_icon_image_area button.icon_add', function () {
        let target_popup = $('.add_icon_popup');
        target_popup.find('.iconItem').click(function () {
            let parent = $('[data-active-popup]').closest('.add_icon_image_area');
            let icon_class = $(this).data('icon-class');
            if (icon_class) {
                parent.find('input[type="hidden"]').val(icon_class);
                parent.find('.add_icon_image_button_area').slideUp('fast');
                parent.find('.image_item').slideUp('fast');
                parent.find('.icon_item').slideDown('fast');
                parent.find('[data-add-icon]').removeAttr('class').addClass(icon_class);
                target_popup.find('.iconItem').removeClass('active');
                target_popup.find('.popupClose').trigger('click');
            }
        });
        target_popup.find('[data-icon-menu]').click(function () {
            if (!$(this).hasClass('active')) {
                let target = $(this);
                let tabsTarget = target.data('icon-menu');
                target_popup.find('[data-icon-menu]').removeClass('active');
                target.addClass('active');
                target_popup.find('[data-icon-list]').each(function () {
                    let targetItem = $(this).data('icon-list');
                    if (tabsTarget === 'all_item' || targetItem === tabsTarget) {
                        $(this).slideDown(250);
                    } else {
                        $(this).slideUp(250);
                    }
                });
            }
            return false;
        });
        target_popup.find('.popupClose').click(function () {
            target_popup.find('[data-icon-menu="all_item"]').trigger('click');
            target_popup.find('.iconItem').removeClass('active');
        });
    });
    $(document).on('click', '.mptrs_area .add_icon_image_area .icon_remove', function () {
        let parent = $(this).closest('.add_icon_image_area');
        parent.find('input[type="hidden"]').val('');
        parent.find('[data-add-icon]').removeAttr('class');
        parent.find('.icon_item').slideUp('fast');
        parent.find('.add_icon_image_button_area').slideDown('fast');
    });
    //=================select Single image=========================//
    $(document).on('click', '.mptrs_area button.mp_image_add', function () {
        let $this = $(this);
        let parent = $this.closest('.add_icon_image_area');
        wp.media.editor.send.attachment = function (props, attachment) {
            let attachment_id = attachment.id;
            let attachment_url = attachment.url;
            parent.find('input[type="hidden"]').val(attachment_id);
            parent.find('.icon_item').slideUp('fast');
            parent.find('img').attr('src', attachment_url);
            parent.find('.image_item').slideDown('fast');
            parent.find('.add_icon_image_button_area').slideUp('fast');
        }
        wp.media.editor.open($this);
        return false;
    });
    $(document).on('click', '.mptrs_area .add_icon_image_area .image_remove', function () {
        let parent = $(this).closest('.add_icon_image_area');
        parent.find('input[type="hidden"]').val('');
        parent.find('img').attr('src', '');
        parent.find('.image_item').slideUp('fast');
        parent.find('.add_icon_image_button_area').slideDown('fast');
    });

    // Click on status text to toggle dropdown visibility
    $(document).on('click', '.status-toggle', function(e) {
        e.stopPropagation();
        // Hide all other dropdowns first
        $('.mptrs_service_status').hide();
        // Show only this dropdown
        $(this).next('.mptrs_service_status').show();
    });
    
    // Hide dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).hasClass('status-toggle') && !$(e.target).hasClass('mptrs_service_status')) {
            $('.mptrs_service_status').hide();
        }
    });
    
    // Hide dropdown after selection is made (after ajax success)
    $(document).on('change', '.mptrs_service_status', function () {
        let mptrs_service_status_id = $(this).attr('id');
        mptrs_service_status_id = mptrs_service_status_id.split('-');
        let orderPostId = mptrs_service_status_id[1];
        let selectedVal = $(this).val().trim();
        
        // Update the status display immediately in the UI
        let parent = $(this).parent();
        let statusSpan = parent.find('.mptrs_order_status');
        
        if (selectedVal === 'in_progress') {
            statusSpan.removeClass('completed cancelled').addClass('on-process').text('On Process');
        } else if (selectedVal === 'done') {
            statusSpan.removeClass('on-process cancelled').addClass('completed').text('Completed');
        } else if (selectedVal === 'service_out') {
            statusSpan.removeClass('on-process completed').addClass('cancelled').text('Cancelled');
        }
        
        // Hide the dropdown after selection
        $(this).hide();
        
        $.ajax({
            url: mptrs_admin_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'mptrs_save_service_status_update',
                nonce: mptrs_admin_ajax.nonce,
                post_id: orderPostId,
                selectedVal: selectedVal,
            },
            success: function (response) {
                if ( response.data.success ) {
                    // Success feedback
                    parent.append('<span class="status-updated-msg">Status updated!</span>');
                    setTimeout(function() {
                        parent.find('.status-updated-msg').fadeOut(function() {
                            $(this).remove();
                        });
                    }, 2000);
                } else {
                    alert('Error: ' + response.data.message);
                }
            },
            error: function () {
                alert('An unexpected error occurred.');
            }
        });
    });

    $(document).on('change', '.mptrs_reserved_status', function () {
        let mptrs_service_status_id = $(this).attr('id');
        mptrs_service_status_id = mptrs_service_status_id.split('-');
        let orderPostId = mptrs_service_status_id[1];
        let selectedVal =$(this).val().trim();
        alert( selectedVal );
        $.ajax({
            url: mptrs_admin_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'mptrs_save_table_reserved_status_update',
                nonce: mptrs_admin_ajax.nonce,
                post_id: orderPostId,
                selectedVal: selectedVal,
            },
            success: function (response) {
                if ( response.data.success ) {
                    alert(  response.data.message );
                } else {
                    alert('Error: ' + response.data.message);
                }
            },
            error: function () {
                alert('An unexpected error occurred.');
            }
        });
    });

    $(document).on("click", ".mptrs_order_type_item",function () {
        $(".mptrs_order_type_item").removeClass('mptrs_active');
        $(this).addClass('mptrs_active');
        let filterValue = $(this).data("filter");


        if (filterValue === "all") {
            $(".mptrs_order_row").fadeIn();
        } else {
            $(".mptrs_order_row").hide().filter(`[ data-order_type_filter='${filterValue}']`).fadeIn();
        }
    });
    
    // Search functionality
    $(document).on("keyup", "#mptrsOrderSearch", function() {
        let searchValue = $(this).val().toLowerCase();
        searchOrders(searchValue);
    });
    
    $(document).on("click", "#mptrsOrderSearchBtn", function(e) {
        e.preventDefault();
        let searchValue = $("#mptrsOrderSearch").val().toLowerCase();
        searchOrders(searchValue);
    });
    
    function searchOrders(searchValue) {
        if (searchValue.length > 0) {
            $("#order-list tr").each(function() {
                let found = false;
                $(this).find('td').each(function() {
                    if ($(this).text().toLowerCase().indexOf(searchValue) > -1) {
                        found = true;
                        return false; // Break the loop if found
                    }
                });
                
                if (found) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        } else {
            // If search is empty, restore original filter view
            const activeFilter = $(".mptrs_order_type_item.mptrs_active").data("filter");
            if (activeFilter === "all") {
                $(".mptrs_order_row").show();
            } else {
                $(".mptrs_order_row").hide().filter(`[ data-order_type_filter='${activeFilter}']`).show();
            }
        }
    }
    
    // Time filter functionality
    $(document).on("change", "#mptrsTimeFilter", function() {
        const value = $(this).val();
        let fromDate = null;
        const today = new Date();
        
        switch(value) {
            case 'this_week':
                // Get first day of current week (Sunday)
                const firstDayOfWeek = new Date(today.setDate(today.getDate() - today.getDay()));
                fromDate = firstDayOfWeek;
                break;
            case 'this_month':
                // First day of current month
                fromDate = new Date(today.getFullYear(), today.getMonth(), 1);
                break;
            case 'last_month':
                // First day of last month
                fromDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                const lastDayOfLastMonth = new Date(today.getFullYear(), today.getMonth(), 0);
                break;
            case 'all_time':
                // Show all orders
                fromDate = null;
                break;
        }
        
        filterOrdersByDate(fromDate);
    });
    
    function filterOrdersByDate(fromDate) {
        if (!fromDate) {
            // If no date filter, show according to current type filter
            const activeFilter = $(".mptrs_order_type_item.mptrs_active").data("filter");
            if (activeFilter === "all") {
                $(".mptrs_order_row").fadeIn();
            } else {
                $(".mptrs_order_row").hide().filter(`[ data-order_type_filter='${activeFilter}']`).fadeIn();
            }
            return;
        }
        
        $("#order-list tr").each(function() {
            const dateCell = $(this).find('td:nth-child(3)').text().trim();
            
            if (dateCell) {
                const parts = dateCell.split(' ')[0].split('-');
                if (parts.length >= 3) {
                    const orderDate = new Date(parts[0], parts[1] - 1, parts[2]);
                    
                    if (orderDate >= fromDate) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                } else {
                    $(this).hide();
                }
            }
        });
    }
    
    // Select all checkboxes functionality
    $(document).on("change", "#selectAllOrders", function() {
        const isChecked = $(this).prop("checked");
        $(".order-checkbox").prop("checked", isChecked);
    });
    
    $(document).on("change", ".order-checkbox", function() {
        const allChecked = $(".order-checkbox:checked").length === $(".order-checkbox").length;
        $("#selectAllOrders").prop("checked", allChecked);
    });
    
    // upload image
    var mediaUploader;
    $(document).on("click", ".mptrs-logo-upload",function (e) {
        e.preventDefault();
        if(mediaUploader){
            mediaUploader.open();
            return;
        }
        mediaUploader = wp.media.frames.file_frame = wp.media({
            title: 'Upload Logo',
            button: { text: 'Choose Image' },
            multiple: false
        });
        mediaUploader.on('select', function(){
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            var imageUrl = (attachment.sizes && attachment.sizes.medium) ? attachment.sizes.medium.url : attachment.url;
            $('#mptrs-restaurant-logo').val(attachment.id);
            $('.mptrs-logo-wrapper').html('<img src="' + imageUrl + '" style="max-width:100%; margin-bottom:10px;" />');
        });
        mediaUploader.open();
    });
    $(document).on("click", ".mptrs-logo-remove",function (e) {
        e.preventDefault();
        $('#mptrs-restaurant-logo').val('');
        $('.mptrs-logo-wrapper').html('');
    });


    $(document).on( 'click', '.mptrs_restaurant_city_add_btn', function( e ) {
        e.preventDefault();
        $('.mptrs_create_taxo_popup').fadeIn();
    });

    $(document).on( 'click', '.mptrs_create_taxo_close', function(e) {
        e.preventDefault();
        $('.mptrs_create_taxo_popup').fadeOut();
    });

    $(document).on( 'click', '.mptrs_create_taxo_submitBtn', function( e ) {
        e.preventDefault();

        let taxoName = $('[name="mptrs_taxo_name"]').val();
        let taxoSlug = $('[name="mptrs_taxo_slug"]').val();
        let taxoDesc = $('[name="mptrs_taxo_desc"]').val();
        let restaurant_id = $(this).attr( 'data-restaurant-id' );

        $.ajax({
            url: mptrs_admin_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'mptrs_add_taxonomy_term',
                taxo_name: taxoName,
                taxo_slug: taxoSlug,
                taxo_descname: taxoDesc,
                restaurant_id: restaurant_id,
                nonce: mptrs_admin_ajax.nonce,
            },
            success: function(response) {
                $('.mptrs_create_taxo_message').html('<p style="color:green;">' + response.data.message + '</p>');
                $('[name="mptrs_taxo_name"]').val('');
                $('[name="mptrs_taxo_slug"]').val('');
                $('[name="mptrs_taxo_desc"]').val('');
                $('.mptrs_create_taxo_popup').fadeOut();
                if( response.data.cities_html ){
                    $("#mptrs_restaurant_city").html( response.data.cities_html );
                }
            },
            error: function() {
                $('.mptrs_create_taxo_message').html('<p style="color:red;">Something went wrong!</p>');
                $('[name="mptrs_taxo_name"]').val('');
                $('[name="mptrs_taxo_slug"]').val('');
                $('[name="mptrs_taxo_desc"]').val('');
                $('.mptrs_create_taxo_popup').fadeOut();
            }
        });
    });

}(jQuery));
