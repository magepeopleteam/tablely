jQuery(document).ready(function ($) {


    /*function initAutocomplete() {
        const input = document.getElementById('mptrs_deliveryLocation');
        const autocomplete = new google.maps.places.Autocomplete(input, {
            types: ['geocode'], // You can also use 'establishment' or others
            componentRestrictions: { country: 'us' }, // Optional: restrict to a country
        });

        autocomplete.addListener('place_changed', function () {
            const place = autocomplete.getPlace();
            console.log(place); // You can extract place.formatted_address, place.geometry, etc.
        });
    }
    // initAutocomplete();

    function mptrsInitAutocomplete() {
        /!*const mptrsInput = document.getElementById('mptrs_deliveryLocation');
        if (!mptrsInput) return;
        const mptrsAutocomplete = new google.maps.places.Autocomplete(mptrsInput);
        *!/
        var autocomplete;
        var id = 'mptrs_deliveryLocation';
        autocomplete = new google.maps.places.Autocomplete((document.getElementById(id)), {
            types: ['geocode'],
        });

    }

    $(window).on('load', function () {
        mptrsInitAutocomplete();
    });*/

    /*$(document).on( 'click', '.mptrs_foodMenuTab', function () {
       let tabClickedId = $(this).attr('id');
       $('.mptrs_foodMenuTab').removeClass('mptrs_selectedMenuTab');
       $(this).addClass('mptrs_selectedMenuTab');

       let menuTabContainer = tabClickedId+'Container';
       $("#"+menuTabContainer).siblings().hide();
       $("#"+menuTabContainer).fadeIn(1000);
    });*/

    /*$(document).on("click", ".mptrs_categoryFilter", function () {

        $("#mptrs_LoadMoreMenuHolder").hide();
        let filterValue = $(this).data("filter");
        $(".mptrs_categoryFilter").removeClass("active");
        $(this).addClass("active");

        if (filterValue === "all") {
            $(".mptrsTableRow").fadeIn();
        } else {
            $(".mptrsTableRow").hide().filter(`[data-category='${filterValue}']`).fadeIn();
        }
    });*/

    function getRowsPerPage() {
        return parseInt($("#mptrs_displayMenuCount").val(), 10) || 10;
    }
    let currentIndex = 0;
    let $visibleRows = $('.mptrsTableRow');
    const $loadMoreBtn = $('.mptrs_LoadMoreMenuText');
    const $loadMoreHolder = $('#mptrs_LoadMoreMenuHolder');
    function mptrs_showRows() {
        const rowsPerPage = getRowsPerPage();
        $visibleRows.hide();
        $visibleRows.slice(0, currentIndex + rowsPerPage).fadeIn();
        currentIndex += rowsPerPage;
        if (currentIndex >= $visibleRows.length) {
            $loadMoreHolder.hide();
        } else {
            $loadMoreHolder.show();
        }
    }
    mptrs_showRows();

    $loadMoreBtn.on('click', function() {
        mptrs_showRows();
    });

    $(document).on("click", ".mptrs_categoryFilter", function () {
        currentIndex = 0;
        let filterValue = $(this).data("filter");

        $(".mptrs_categoryFilter").removeClass("active");
        $(this).addClass("active");

        // Hide all rows first
        $(".mptrsTableRow").hide();

        if (filterValue === "all") {
            $visibleRows = $(".mptrsTableRow");
        } else {
            $visibleRows = $(".mptrsTableRow").filter(`[data-category='${filterValue}']`);
        }

        mptrs_showRows();
    });

    $(document).on('click',".mptrs_set_seat_mapping_info", function ( e ) {
        e.preventDefault();
        let mptrs_box_size = $('#mptrs_seat_map_box_size').val().trim();
        let mptrs_num_of_columns = $('#mptrs_seat_num_of_columns').val().trim();
        let mptrs_num_of_rows = $('#mptrs_seat_num_of_rows').val().trim();

        let limitKey = 'mptrs_seat_mapping_info'
        // alert( newLimit );
        $.ajax({
            url: mptrs_admin_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'mptrs_set_seat_mapping_info',
                nonce: mptrs_admin_ajax.nonce,
                mptrs_box_size: mptrs_box_size,
                mptrs_num_of_columns: mptrs_num_of_columns,
                mptrs_num_of_rows: mptrs_num_of_rows,
                limitKey: limitKey,
            },
            success: function (response) {
                location.reload();
                alert(response.data.message);
            },
            error: function () {
                alert('An unexpected error occurred.');
            }
        });
        // console.log( set_mapping_info );
    });

    $(document).on("blur", ".mptrs_setDisplayLimit", function () {
        // Get the new value
        const newLimit = parseInt($(this).val(), 10) || 20;
        let limitKey = 'mptrs_food_menu_display_limit'
        // alert( newLimit );
        $.ajax({
            url: mptrs_admin_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'mptrs_set_food_menu_display_limit',
                nonce: mptrs_admin_ajax.nonce,
                newLimit: newLimit,
                limitKey: limitKey,
            },
            success: function (response) {
                $(".mptrs_display_limit").val( newLimit );
                $(".mptrsTableRow").hide();
                currentIndex = 0;
                mptrs_showRows();
                // Only show alert if the value actually changed
                if (response.data.success) {
                    alert(response.data.message);
                }
            },
            error: function () {
                alert('An unexpected error occurred.');
            }
        });
    });

    $(document).on("blur", "#mptrs_menu_display_limit", function () {
        const newLimit = parseInt($(this).val(), 10) || 20;
        let limitKey = 'mptrs_menu_display_limit'
        // Store the original value to compare
        const originalValue = $(this).data('original-value') || $(this).val();
        
        // Only make the AJAX call if the value has changed
        if (newLimit.toString() !== originalValue.toString()) {
            $(this).data('original-value', newLimit);
            
            $.ajax({
                url: mptrs_admin_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'mptrs_set_food_menu_display_limit',
                    nonce: mptrs_admin_ajax.nonce,
                    newLimit: newLimit,
                    limitKey: limitKey,
                },
                success: function (response) {
                    if (response.data.success) {
                        alert(response.data.message);
                    }
                },
                error: function () {
                    alert('An unexpected error occurred.');
                }
            });
        }
    });

    let mptrsInput = $('#mptrs_set_google_map_api_key');
    const mptrsButtonHolder = $('#mptrs_set_apikey_holder');
    const mptrsInputHolder = $('#mptrs_set_google_map_api_key_holder');
    $(document).on("change", "#mptrs_toggle_autocomplete", function () {
        let mptrsIsAutoComplete = $(this).is(':checked') ? 'yes' : 'no';
        let mptrsOptionKey = 'mptrs_enable_location_autocomplete';

        $.ajax({
            url: mptrs_admin_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'mptrs_set_food_menu_display_limit',
                nonce: mptrs_admin_ajax.nonce,
                newLimit: mptrsIsAutoComplete,
                limitKey: mptrsOptionKey,
            },
            success: function (response) {
                alert(response.data.message);
                if ( mptrsIsAutoComplete == 'yes' ) {
                    mptrsInputHolder.fadeIn();
                } else {
                    mptrsInputHolder.fadeOut();
                    mptrsButtonHolder.fadeOut();
                    mptrsInput.val('');
                }
            },
            error: function () {
                alert('An unexpected error occurred.');
            }
        });
    });

    $(document).on("click", ".mptrs_set_apikey", function ( e ) {
        e.preventDefault();
        let mptrsApiKey = '';
        mptrsApiKey = $("#mptrs_set_google_map_api_key").val().trim();
        let mptrs_API_Key_Option = 'mptrs_google_map_key';

        if( mptrsApiKey ) {
            $.ajax({
                 url: mptrs_admin_ajax.ajax_url,
                 type: 'POST',
                 data: {
                     action: 'mptrs_set_food_menu_display_limit',
                     nonce: mptrs_admin_ajax.nonce,
                     newLimit: mptrsApiKey,
                     limitKey: mptrs_API_Key_Option,
                 },
                 success: function (response) {
                     alert(response.data.message);
                 },
                 error: function () {
                     alert('An unexpected error occurred.');
                 }
             });
        }else{
            alert('Fill API Input Fields');
            $("#mptrs_set_google_map_api_key").focus();
        }
    });

    mptrsInput.on('input', function () {
        if ($(this).val().trim() !== '') {
            mptrsButtonHolder.fadeIn();
        } else {
            mptrsButtonHolder.fadeOut();
        }
    });

    $(document).on("blur", ".mptrs_ordersPerPage", function () {
        const newLimit = parseInt($(this).val(), 10) || 20;
        let limitKey = 'mptrs_order_lists_display_limit';
        
        // Store the original value to compare
        const originalValue = $(this).data('original-value') || $(this).val();
        
        // Only make the AJAX call if the value has changed
        if (newLimit.toString() !== originalValue.toString()) {
            $(this).data('original-value', newLimit);
            
            $.ajax({
                url: mptrs_admin_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'mptrs_set_food_menu_display_limit',
                    nonce: mptrs_admin_ajax.nonce,
                    newLimit: newLimit,
                    limitKey: limitKey,
                },
                success: function (response) {
                    $(".mptrs_display_limit").val(newLimit);
                    if (response.data.success) {
                        alert(response.data.message);
                        location.reload();
                    }
                },
                error: function () {
                    alert('An unexpected error occurred.');
                }
            });
        }
    });


    $(document).on("click", ".mptrs_filterByCategory", function () {
        let filterValue = $(this).data("filter");
        $(".mptrs_filterByCategory").removeClass("active");
        $(this).addClass("active");

        if (filterValue === "all") {
            $(".mptrs_menuInfoHolderFilter").fadeIn();
        } else {
            $(".mptrs_menuInfoHolderFilter").hide().filter(`[data-category='${filterValue}']`).fadeIn();
        }
    });


    function appendFoodMenu(foodMenuData, key) {
        let row = `
            <tr class="mptrsTableRow" data-category ="${foodMenuData.menuCategory}" id="mptrs_foodMenuContent${key}">
                <td class="mptrsTableTd mptrsTdImage">
                    <div class="mptrsImageWrapper">
                        <img class="mptrsImage" id="mptrs_memuImgUrl${key}" src="${foodMenuData.menuImgUrl}" alt="${foodMenuData.menuName}">
                    </div>
                </td>
                <td class="mptrsTableTd mptrsTdName">
                    <div class="mptrs_menuName" id="mptrs_memuName${key}">
                        ${foodMenuData.menuName}
                    </div>
                    <input type="hidden" name="mptrs_menuCategory" id="mptrs_menuCategory${key}" value="${foodMenuData.menuCategory}">
                </td>
                <td class="mptrsTableTd mptrsTdCategory" >
                    <div class="mptrs_memuPrice" id="mptrs_Category${key}">${foodMenuData.menuCategory}</div>
                </td>
                <td class="mptrsTableTd mptrsTdPrice">
                    <div class="mptrs_memuPrice" id="mptrs_memuPrice${key}">$${foodMenuData.menuPrice}</div>
                </td>
                <td class="mptrsTableTd mptrsTdServes">
                    <div class="mptrs_menuPersion" id="mptrs_memuPersons${key}">
                        <i class='fas fa-user-alt' style='font-size:14px'></i> ${foodMenuData.menunumPersons}
                    </div>
                </td>
                <td class="mptrsTableTd mptrsTdActions">
                    <div class="mptrs_BottomMenuInFo">
                        <span class="mptrm_editFoodMenu" id="mptrsEditMenu_${key}"><i class='far fa-edit' style='font-size:20px'></i></span>
                        <span class="mptrm_deleteFoodMenu" id="mptrsDeleteMenu_${key}"><i class='far fa-trash-alt' style='font-size:20px'></i></span>
                    </div>
                </td>
            </tr>
        `;
        $("#mptrs_showAllMenu").append(row);
    }

    function mptrs_category_display(categories) {

        let displayCategories = '';
        if (categories) {
            $.each(categories, (key, value) => {
                displayCategories += `
                    <div class="mptrs-category-item">
                        <i class="fas fa-arrows-alt mptrs-drag"></i>
                        <input class="mptrs-category-name" type="text" value="${value}" placeholder="Category name">
                        <i class="fas fa-trash-alt mptrs-delete"></i>
                    </div>
            `;
            });
        } else {
            displayCategories = '<div class="no-categories">No categories found. Add your first category below!</div>';
        }

        let mptrs_category_data = `
            <div class="mptrs_categoryDataHolder">
                <div class="mptrs-popup-header">
                    <h3><i class="fas fa-tags mR_xs"></i> Manage Categories</h3>
                    <span class="mptrs-close"><i class="fas fa-times"></i></span>
                </div>
                <div class="mptrs-category-list">
                    ${displayCategories}
                </div>
                <button class="mptrs-add-category">
                    <i class="fas fa-plus"></i> Add Category
                </button>
                <div class="mptrs-popup-footer">
                    <button class="mptrs-btn mptrs-cancel"><i class="fas fa-times"></i> Cancel</button>
                    <button class="mptrs-btn mptrs-save"><i class="fas fa-check"></i> Save Changes</button>
                </div>
            </div>
        `
        if ($('#mptrs_foodMenuContentContainer').is(':empty')) {
            $('#mptrs_foodMenuContentContainer').append(mptrs_category_data);
        }
        $('#mptrs_foodMenuPopup').fadeIn();
        $('body').addClass('mptrs_no_scroll');
    }


    var frame;
    var food_menu_image_url = ''
    $(document).on('click', '#mptrs_menuImage', function (e) {
        e.preventDefault();
        if (frame) {
            frame.open();
            return;
        }
        frame = wp.media({
            title: 'Select or Upload Food Menu Image',
            button: {text: 'Use this image'},
            multiple: false
        });
        frame.on('select', function () {
            var attachment = frame.state().get('selection').first().toJSON();
            food_menu_image_url = attachment.url;
            $('#mptrs_menuImage_url').val(attachment.url);
             $('.custom-foodMenu-image-preview').html('<img src="' + attachment.url + '" style="max-width: 100%; margin-bottom: 10px;">');
             $('.custom-foodMenu-image-preview').show();
        });
        frame.open();
    });

    $(document).on('click', '.mptrm_deleteFoodMenu', function (e) {
        let deleteMenuId = $(this).attr('id').trim();
        let deleteKeys = deleteMenuId.split('_');
        let deleteKey = deleteKeys[1];
        $.ajax({
            url: mptrs_admin_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'mptrs_delete_food_menu',
                nonce: mptrs_admin_ajax.nonce,
                deleteKey: deleteKey,
            },
            success: function (response) {
                let removeMenu = 'mptrs_foodMenuContent' + deleteKey;
                alert(response.data.message);
                $("#" + removeMenu).fadeOut();
            },
            error: function () {
                alert('An unexpected error occurred.');
            }
        });
    });



    function mptrs_menuPopup( data, categories ) {

        let displayCategory = '<option value="">' + (Object.keys(categories).length > 0 ? 'Select a category' : 'No categories available') + '</option>';
        $.each(categories, (key, value) => {
            displayCategory += `<option value="${key}" ${key === data.category ? 'selected' : ''}>${value}</option>`;
        });

        let variations = data.variations;
        let variationsHTML = '';
        let is_variations_exists = true;
        $.each(variations, function (index, variationData) {
            let categoryId = `category_${new Date().getTime()}_${index}`;
            let itemsHTML = '';
            let addOneVariation = '';

            $.each(variationData.items, function (key, item) {
                itemsHTML += `
                <div class="mptrs_variationItem">
                    <input type="text" name="variation_item_name[]" class="mptrs_input" placeholder="Item Name" value="${item.name}">
                    <input type="number" name="variation_item_price[]" class="mptrs_input" placeholder="Price" value="${item.price}">
                    <input type="number" name="variation_item_qty[]" class="mptrs_qty" min="1" value="${item.qty}">
                    <button type="button" class="mptrs_removeVariationItem"><i class="fas fa-times"></i></button>
                </div>
            `;
            });

            if (variationData && variationData.hasOwnProperty('variationOrAddOne')) {
                addOneVariation = variationData.variationOrAddOne;
            }
            let options = '';
            let select_type = '';
            let removeCategory = '';

            if( addOneVariation === "variations" ){
                removeCategory = addOneVariation;
                is_variations_exists = false;
                options = `<option class="mptrs_variationAddons" value="variations" ${addOneVariation === 'variations' ? 'selected' : '' }>Variations</option>`;
                select_type = `<option class="mptrs_select" value="single" ${variationData.radioOrCheckbox === 'single' ? 'selected' : ''}>Single Select (Radio)</option>`;
            } else {
                options = `<option class="mptrs_variationAddons" value="addons" ${addOneVariation === 'addons' ? 'selected' : '' }>Addons</option>`;
                select_type = `<option class="mptrs_select" value="multiple" ${variationData.radioOrCheckbox === 'multiple' ? 'selected' : ''}>Multiple (Check Box)</option>
                            <option class="mptrs_select" value="single" ${variationData.radioOrCheckbox === 'single' ? 'selected' : ''}>Single Select (Radio)</option>
                            `;
            }

            variationsHTML += `
            <div class="mptrs_variationCategory" id="${categoryId}">
                <input type="text" class="mptrs_variationCategoryName" placeholder="${addOneVariation}" value="${variationData.category}">
                <select class="mptrs_variationOrAddone" name="mptrs_variationOrAddone">
                    ${options}
                </select>
                <select class="mptrs_singleMultiSelect" name="mptrs_singleMultiSelect">
                    ${select_type}
                </select>
                
                <div class="mptrs_variationItems">${itemsHTML}</div>
                <div class="mptrs_addRemoveHolder">
                    <button type="button" class="mptrs_addVariationItem"><i class="fas fa-plus"></i> Add More ${addOneVariation}</button>
                    <button type="button" class="mptrs_removeVariationCategory" id="${removeCategory}"><i class="fas fa-trash-alt"></i> Remove</button>
                </div>
                
            </div>
        `;
        });

        let variationsBtnDisplay = 'none';
        if( is_variations_exists ){
            variationsBtnDisplay = 'block';
        }

        // Get any existing image for preview
        let imagePreview = '';
        if (data.imgUrl) {
            imagePreview = `<img src="${data.imgUrl}" alt="Menu Preview">`;
        } else {
            imagePreview = `<div class="empty-preview-text"><i class="fas fa-image"></i><p>Upload an image</p></div>`;
        }

        return `
        <h2 class="mptrs_addEditMenuTitleText">
            <i class="fas fa-utensils"></i> 
            ${data.popUpTitleText}
        </h2>
        <form id="mptrs_foodMenuForm" class="mptrs_food_menu_form">
            <div class="mptrs_form_group">
                <label for="mptrs_menuName">Menu Name</label>
                <div class="input-icon-wrapper">
                    <i class="fas fa-hamburger input-icon"></i>
                    <input type="text" id="mptrs_menuName" name="mptrs_menuName" class="mptrs_input" required value="${data.name}" placeholder="Enter menu item name">
                </div>
            </div>

            <div class="mptrs_form_group">
                <label for="mptrs_menuCategory">Category</label>
                <div class="input-icon-wrapper">
                    <i class="fas fa-layer-group input-icon"></i>
                    <select id="mptrs_menuCategory" name="mptrs_menuCategory" class="mptrs_select" required>
                        ${displayCategory}
                    </select>
                </div>
                <span class="mptrs_form_helper">Select a category or create one from Categories button</span>
            </div>

            <div class="mptrs_form_group mptrs_menuPriceHolder">
                <div class="mptrs_regularSalePrice">
                    <label for="mptrs_menuPrice">Regular Price</label>
                    <div class="input-icon-wrapper">
                        <i class="fas fa-dollar-sign input-icon"></i>
                        <input type="number" id="mptrs_menuPrice" name="mptrs_menuPrice" class="mptrs_input" min="0" step="0.01" required value="${data.price}" placeholder="0.00">
                    </div>
                </div>
                <div class="mptrs_regularSalePrice">
                    <label for="mptrs_menuSalePrice">Sale Price <span class="optional">(optional)</span></label>
                    <div class="input-icon-wrapper">
                        <i class="fas fa-tags input-icon"></i>
                        <input type="number" id="mptrs_menuSalePrice" name="mptrs_menuSalePrice" class="mptrs_input" min="0" step="0.01" value="${data.menuSalePrice}" placeholder="0.00">
                    </div>
                </div>
            </div>

            <div class="mptrs_form_group">
                <label for="mptrs_numPersons">Number of Persons</label>
                <div class="input-icon-wrapper">
                    <i class="fas fa-users input-icon"></i>
                    <input type="number" id="mptrs_numPersons" name="mptrs_numPersons" class="mptrs_input" min="1" required value="${data.person}" placeholder="1">
                </div>
                <span class="mptrs_form_helper">How many people does this item serve?</span>
            </div>

            <div class="mptrs_form_group">
                <label for="mptrs_menuDescription">Short Description</label>
                <div class="input-icon-wrapper">
                    <i class="fas fa-pencil-alt input-icon description-icon"></i>
                    <textarea class="mptrs_menuDescription mptrs_input" 
                      id="mptrs_menuDescription" 
                      name="mptrs_menuDescription"
                      placeholder="Describe this menu item"
                      required>${data.menuDescription}</textarea>
                </div>
            </div>

            <div class="mptrs_form_group">
                <label for="mptrs_menuImage">Menu Image</label>
                <div class="input-icon-wrapper file-input-wrapper">
                    <i class="fas fa-image input-icon"></i>
                    <input type="file" id="mptrs_menuImage" name="mptrs_menuImage" class="mptrs_input" accept="image/*">
                </div>
                <input type="hidden" id="mptrs_menuImage_url" name="mptrs_menuImage_url" value="${data.imgUrl}">
                <div class="custom-foodMenu-image-preview">
                    ${imagePreview}
                </div>
            </div>

            <div id="mptrs_variationsContainer">
                <div class="mptrs_variationAddonsHolder">
                    <button type="button" id="mptrs_addVariationCategory" style="display: ${variationsBtnDisplay}"><i class="fas fa-plus"></i> Add Variation Category</button>
                    <button type="button" id="mptrs_addAddonsCategory"><i class="fas fa-plus"></i> Add Addons Category</button>
                </div>
                <div class="mptrs_addonsDataHolder" id="mptrs_addonsDataHolder">
                    ${variationsHTML}
                </div>
                
            </div>
            <button type="submit" class="${data.btnIdClass}" id="mptrs_AddEdit-${data.key}">
                <i class="fas fa-${data.btnText.includes('Update') ? 'save' : 'plus-circle'}"></i> ${data.btnText}
            </button>
        </form>
    `;
    }

    $(document).on('click', '#mptrs_openCategoryPopup', function (e) {
        e.preventDefault();
        $(this).text('Loading...');
        $.ajax({
            url: mptrs_admin_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'mptrs_get_categories',
                nonce: mptrs_admin_ajax.nonce,
            },
            success: function (response) {
                if (response.success) {
                    $('#mptrs_openCategoryPopup').text('Categories..');
                    let categories = response.data.mptrs_categories;
                    mptrs_category_display(categories);
                } else {
                    alert("Failed to save categories.");
                    $('#mptrs_openCategoryPopup').text('Categories..');
                }
            },
            error: function () {
                alert("Something went wrong. Try again!");
                $('#mptrs_openCategoryPopup').text('Categories..');
            }
        });

        // mptrs_category_display();
    });
    // Add new category
    $(document).on('click', '.mptrs-add-category', function (e) {
        e.preventDefault();
        let newCategory = `
                    <div class="mptrs-category-item">
                        <i class="fas fa-bars mptrs-drag"></i>
                        <input class="mptrs-category-name" type="text" value="" placeholder="New Category">
                        <i class="fas fa-trash-alt mptrs-delete"></i>
                    </div>
                `;
        $(".mptrs-category-list").append(newCategory);
    });

    $(document).on("input", ".mptrs-category-name", function () {
        let value = $(this).val();
        let validValue = value.replace(/[^a-zA-Z0-9\s]/g, '');
        $(this).val(validValue);
    });

    $(document).on("click", ".mptrs-delete", function () {
        $(this).closest(".mptrs-category-item").remove();
    });
    $(document).on('click', ".mptrs-close, .mptrs-cancel", function (e) {
        mptrs_close_popup(e);
    });

    // Save categories via AJAX
    $(document).on("click", ".mptrs-save", function () {
        let categories = [];
        $(".mptrs-category-item").each(function () {
            let categoryName = $(this).find(".mptrs-category-name").val().trim();
            if (categoryName !== "") {
                categories.push(categoryName);
            }
        });
        if (categories.length === 0) {
            alert("Please add at least one category.");
            return;
        }
        let categoryJsonData = JSON.stringify(categories);
        $.ajax({
            url: mptrs_admin_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'mptrs_set_categories',
                nonce: mptrs_admin_ajax.nonce,
                categoryJsonData: categoryJsonData,
            },
            success: function (response) {
                if (response.success) {
                    alert("Categories saved successfully!");
                    $(".mptrs-overlay").fadeOut();
                } else {
                    alert("Failed to save categories.");
                }
            },
            error: function () {
                alert("Something went wrong. Try again!");
            }
        });
    });

    $(document).on('click', '#mptrs_openPopup', function (e) {
        e.preventDefault();
        $(this).text('Loading...');
        let name = '';
        let key = 'addMenu';
        let price = 0;
        let person = 0;
        let menuDescription = '';
        let variations = [];
        let menuSalePrice = 0;
        let imgUrl = '';
        let category = '';
        let btnText = 'Add Menu';
        let btnIdClass = 'mptrs_submit_btn';
        let popUpTitleText = 'Add New Food Menu!';

        let data = {
            name, price, person, imgUrl, category, btnText, btnIdClass, popUpTitleText, key, variations, menuDescription, menuSalePrice
        }

        $.ajax({
            url: mptrs_admin_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'mptrs_get_categories',
                nonce: mptrs_admin_ajax.nonce,
            },
            success: function (response) {
                if (response.success) {
                    $('#mptrs_openPopup').text('+Add New Food Menu ');
                    let categories = response.data.mptrs_categories;
                    let formContent = mptrs_menuPopup(data, categories);
                    if ($('#mptrs_foodMenuContentContainer').is(':empty')) {
                        $('#mptrs_foodMenuContentContainer').append(formContent);
                    }
                    $('#mptrs_foodMenuPopup').fadeIn();
                    $('body').addClass('mptrs_no_scroll');
                } else {
                    alert("Failed to save categories.");
                }
            },
            error: function () {
                $('#mptrs_openPopup').text('+Add New Food Menu ');
                alert("Something went wrong. Try again!");
            }
        });

    });

    $(document).on('click', '.mptrm_editFoodMenu', function (e) {
        let editMenuId = $(this).attr('id').trim();
        let keys = editMenuId.split('_');
        let key = keys[1];

        let name = '';
        let imgUrl = '';
        let category = '';
        let person = '';
        let price = '';
        let menuDescription = '';
        let menuSalePrice = '';
        let variations = '';
        let btnText = 'Update Food Menu Data';
        let btnIdClass = 'mptrs_edit_food_menu_data'
        let popUpTitleText = 'Edit Food Menu';


        $.ajax({
            url: mptrs_admin_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'mptrs_get_categories',
                nonce: mptrs_admin_ajax.nonce,
                menuKey: key,
            },
            success: function (response) {
                if (response.success) {
                    let categories = response.data.mptrs_categories;
                    let mptrs_edited_menu = response.data.mptrs_edited_menu;
                    name = mptrs_edited_menu['menuName'];
                    price = mptrs_edited_menu['menuPrice'];
                    person = mptrs_edited_menu['numPersons'];
                    imgUrl = mptrs_edited_menu['menuImgUrl'];
                    category = mptrs_edited_menu['menuCategory'];
                    menuDescription = mptrs_edited_menu['menuDescription'];
                    menuSalePrice = mptrs_edited_menu['menuSalePrice'];
                    variations = mptrs_edited_menu['variations'];

                    let data = {
                        name, price, person, imgUrl, category, btnText, btnIdClass, popUpTitleText, key, variations, menuDescription, menuSalePrice
                    }

                    let formContent = mptrs_menuPopup( data, categories );
                    if ($('#mptrs_foodMenuContentContainer').is(':empty')) {
                        $('#mptrs_foodMenuContentContainer').append(formContent);
                    }
                    $('#mptrs_foodMenuPopup').fadeIn();
                    $('body').addClass('mptrs_no_scroll');
                } else {
                    alert("Failed to save categories.");
                }
            },
            error: function () {
                alert("Something went wrong. Try again!");
            }
        });
    });

    $(document).on('click', '.mptrs_edit_food_menu_data', function (e) {
        e.preventDefault();

        let editVariations = getVariationData();

        $(this).text('Updating...');
        let get_keys = $(this).attr('id');
        let menuKeys = get_keys.split('-');
        let menuKey = menuKeys[1];
        let menuEditData = {};
        menuEditData.menuName = $("#mptrs_menuName").val().trim();
        menuEditData.menuCategory = $("#mptrs_menuCategory").val().trim();
        menuEditData.menuPrice = $("#mptrs_menuPrice").val().trim();
        menuEditData.menuSalePrice = $("#mptrs_menuSalePrice").val().trim();
        menuEditData.menunumPersons = $("#mptrs_numPersons").val().trim();
        menuEditData.menuDescription = $("#mptrs_menuDescription").val().trim();
        menuEditData.menuImgUrl = $("#mptrs_menuImage_url").val().trim();
        menuEditData.menuKey = menuKey;

        menuEditData.variations = JSON.stringify( editVariations );

        $.ajax({
            url: mptrs_admin_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'mptrs_edit_food_menu',
                nonce: mptrs_admin_ajax.nonce,
                menuEditData: menuEditData,
            },
            success: function (response) {
                alert(response.data.message);
                let buttonId = 'mptrs_AddEdit-' + menuKey;
                $("#" + buttonId).text('Update Food Menu Data');
                let editedMenu = 'mptrs_foodMenuContent' + menuKey;
                $("#" + editedMenu).fadeOut();
                $("#" + editedMenu).empty();
                appendFoodMenu(menuEditData, menuKey);
                mptrs_close_popup(e);
            },
            error: function () {
                alert('An unexpected error occurred.');
                $(this).text('Update Food Menu Data');
            }
        });
    });

    function mptrs_close_popup(e) {
        e.preventDefault();
        $('#mptrs_foodMenuPopup').fadeOut();
        $('body').removeClass('mptrs_no_scroll');
        $('#mptrs_foodMenuContentContainer').empty();
    }

    $(document).on('click', '.mptrs_closePopup', function (e) {
        mptrs_close_popup(e);
    });

    function addVariationCategory( type, placeholder ) {
        let categoryId = 'category_' + new Date().getTime(); // Unique ID

        let is_type = '';
        let is_type_options = '';
        let removeCategory = '';
        if( type === 'variations' ){
            is_type = `<option class="mptrs_variationAddons" value="variations">Variations</option>`;
            is_type_options = `<option class="mptrs_select" value="single">Single Select (Radio)</option>`;
            removeCategory = `<button type="button" class="mptrs_removeVariationCategory" id="${type}">Remove</button>`;
        }else{
            is_type = `<option class="mptrs_variationAddons" value="addons">Addons</option>`;
            is_type_options = `
                    <option class="mptrs_select" value="multiple">Multiple (Check Box)</option>
                    <option class="mptrs_select" value="single">Single Select (Radio)</option>`;
            removeCategory = `<button type="button" class="mptrs_removeVariationCategory">Remove</button>`;
        }

        let categoryHTML = `
            <div class="mptrs_variationCategory" id="${categoryId}">
                <input type="text" class="mptrs_variationCategoryName" placeholder="${placeholder}">
                <select class="mptrs_variationOrAddone" name="mptrs_variationOrAddone">
                    ${is_type}
                </select>
                <select class="mptrs_singleMultiSelect" name="mptrs_singleMultiSelect">
                    ${is_type_options}
                </select>
                <div class="mptrs_variationItems"></div>
                <div class="mptrs_addRemoveHolder">
                    <button type="button" class="mptrs_addVariationItem">+Add More ${type} </button>
                    ${removeCategory}
                </div>
                
            </div>
        `;
        $("#mptrs_variationsContainer").append(categoryHTML);
    }
    function addVariationItem(category) {
        let itemHTML = `
            <div class="mptrs_variationItem">
                <input type="text" name="variation_item_name[]" class="mptrs_input" placeholder="Item Name">
                <input type="number" name="variation_item_price[]" class="mptrs_input" placeholder="Price">
                <input type="number" name="variation_item_qty[]" class="mptrs_qty" value="1" min="1">
                <button type="button" class="mptrs_removeVariationItem">X</button>
            </div>
        `;
        category.find(".mptrs_variationItems").append(itemHTML);
    }

    $(document).on("click", "#mptrs_addAddonsCategory", function () {
        addVariationCategory( 'addons', 'Addons name' );
    });

    let variationAddClick = 0;

    $(document).on("click", "#mptrs_addVariationCategory", function () {
        variationAddClick++
        if(variationAddClick > 0 ){
            $(this).fadeOut();
        }
        addVariationCategory( 'variations', 'Variation Name' );
    });
    $(document).on("click", ".mptrs_addVariationItem", function () {
        addVariationItem($(this).closest(".mptrs_variationCategory"));
    });
    $(document).on("click", ".mptrs_removeVariationCategory", function () {
        let clickedId = $(this).attr('id');
        if( clickedId === 'variations' ){
            variationAddClick = 0;
            $("#mptrs_addVariationCategory").fadeIn();
        }
        $(this).closest(".mptrs_variationCategory").remove();
    });
    $(document).on("click", ".mptrs_removeVariationItem", function () {
        $(this).closest(".mptrs_variationItem").remove();
    });
    function getVariationData() {
        let variations = [];
        $(".mptrs_variationCategory").each(function () {
            let categoryName = $(this).find(".mptrs_variationCategoryName").val().trim();

            let items = [];
            let radioOrCheckbox = $(this).find("select[name='mptrs_singleMultiSelect']").val().trim();
            let variationOrAddOne = $(this).find("select[name='mptrs_variationOrAddone']").val().trim();

            $(this).find(".mptrs_variationItem").each(function () {
                let itemName = $(this).find("input[name='variation_item_name[]']").val();
                let itemPrice = $(this).find("input[name='variation_item_price[]']").val();
                let itemQty = $(this).find("input[name='variation_item_qty[]']").val();

                items.push({
                    name: itemName,
                    price: parseFloat(itemPrice) || 0,
                    qty: parseInt(itemQty) || 1,
                });
            });

            variations.push({
                category: categoryName,
                radioOrCheckbox: radioOrCheckbox,
                variationOrAddOne: variationOrAddOne,
                items: items
            });
        });

        console.log( variations );

        return variations;
    }

    $(document).on('click', '.mptrs_submit_btn', function (e) {
        e.preventDefault();
        let variations = [];
        let formData = {};
        formData.menuName = $("#mptrs_menuName").val().trim();
        formData.menuCategory = $("#mptrs_menuCategory").val().trim();
        formData.menuPrice = $("#mptrs_menuPrice").val().trim();
        formData.menuDescription = $("#mptrs_menuDescription").val().trim();
        formData.menuSalePrice = $("#mptrs_menuSalePrice").val().trim();
        formData.menunumPersons = $("#mptrs_numPersons").val().trim();
        formData.menuImgUrl = $("#mptrs_menuImage_url").val().trim();

       /* $('.mptrs_variation_group').each(function() {
            let size = $(this).find('input[name="variation_size[]"]').val();
            let price = $(this).find('input[name="variation_price[]"]').val();
            variations.push({ size, price });
        });*/

        variations = getVariationData();

        formData.variations = JSON.stringify( variations );

        $.ajax({
            url: mptrs_admin_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'mptrs_save_food_menu',
                nonce: mptrs_admin_ajax.nonce,
                menuData: formData,
            },
            success: function (response) {
                alert(response.data.message);
                let key = response.data.uniqueKey;
                appendFoodMenu(formData, key);

                $("#mptrs_menuName").val('');
                $("#mptrs_menuCategory").val('');
                $("#mptrs_menuPrice").val('');
                $("#mptrs_menuDescription").val('');
                $("#mptrs_menuSalePrice").val('');
                $("#mptrs_numPersons").val('');
                $("#mptrs_menuImage_url").val('');
            },
            error: function () {
                alert('An unexpected error occurred.');
            }
        });
    });


    $(document).on('click', '.mptrs_addMenuToPost', function (e) {
        e.preventDefault();
        let getClickedId = $(this).attr('id');
        let menuId = getClickedId.split('-')[1];
        let menuCategory = $(this).parent().parent().attr('data-category').trim();

        let menuAddText = $("#" + getClickedId).text().trim();
        let imgContentId = 'mptrs_MenuImg-' + menuId;
        let nameContentId = 'mptrs-menuName-' + menuId;
        let priceContentId = 'mptrs-menuPrice-' + menuId;

        let action = '';
        let removedKey = 'mptrs_addedFoodMenu' + menuId;
        if (menuAddText === 'Add') {
            $("#" + getClickedId).text('Adding..');
            action = 'mptrs_save_food_menu_for_restaurant';
        } else if (menuAddText === '' || menuAddText === 'Remove') {
            action = 'mptrs_remove_saved_food_menu_for_restaurant';
            $("#" + getClickedId).text('Removing..');
        } else {
            // $("#" + getClickedId).text('Removing..');
            action = '';
        }
        const postId = $('#mptrs_mapping_plan_id').val();
        if (menuAddText !== 'Added') {
            $.ajax({
                url: mptrs_admin_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: action,
                    nonce: mptrs_admin_ajax.nonce,
                    menu_key: menuId,
                    postId: postId,
                },
                success: function (response) {
                    if (menuAddText === 'Add') {
                        $("#" + getClickedId).text('Added');
                        let imgUrl = $("#" + imgContentId).attr("src").trim();
                        let nameContent = $("#" + nameContentId).text().trim();
                        let priceContent = $("#" + priceContentId).text().trim();


                        priceContent = priceContent.replace("$", "");
                        $("#" + getClickedId).removeClass('mptrs_addMenuClass').addClass('mptrs_addedMenuClass');
                        let newAddedMenu = `
                        <tr class="mptrs_menuInfoHolderFilter" id="mptrs_addedFoodMenu${menuId}" data-category="${menuCategory}">
                            <td>
                                <div class="mptrsMenuImg">
                                    <img src="${imgUrl}" alt="${nameContent}">
                                </div>
                            </td>
                            <td class="mptrs-menuName">${nameContent}</td>
                            <td class=" mptrm_editFromFoodMenu mptrs-menuPrice">
                                $<span class="mptrs_addedMenuPrice" id="mptrs_addedMenuPrice-${menuId}">${priceContent}</span>
                                <span class="mptrm_editAddedMenuPrice" id="mptrm_editAddedMenuPrice-${menuId}">Edit Price</span>
                            </td>
                            <td class="mptrs-menuAction">
                                <button class="mptrs_addMenuToPost mptrs-remove-menu" id="mptrs_addedMenuToPost-${menuId}">Remove</button>
                            </td>
                        </tr>`
                        $("#mptrs_AddedMenuData").append(newAddedMenu);

                    } else if (menuAddText === 'Added') {
                        $("#" + getClickedId).text('Add');
                        $("#" + getClickedId).removeClass('mptrs_addedMenuClass').addClass('mptrs_addMenuClass');
                        $("#" + removedKey).fadeOut();
                        $("#" + removedKey).empty();
                    } else {
                        $("#" + removedKey).fadeOut();
                        $("#" + removedKey).empty();
                        let addedId = 'mptrs_addMenuToPost-' + menuId;
                        // alert( removedKey );
                        $("#" + addedId).removeClass('mptrs_addedMenuClass').addClass('mptrs_addMenuClass');
                        $("#" + addedId).text('Add');
                        // $("#"+removedKey).data('category', '');
                    }
                },
                error: function () {
                    alert('An unexpected error occurred.');
                }
            });
        }

    });

    $(document).on('click', '.mptrs_editMenuPriceSave', function (e) {
        e.preventDefault();
        $(this).text('Updating..');
        let editBtnClickId = $(this).attr('id').trim();
        let editKeys = editBtnClickId.split('-');
        let editKey = editKeys[1];
        let price = $("#mptrs_editMenuPrice").val();
        const postId = $('#mptrs_mapping_plan_id').val();
        $.ajax({
            url: mptrs_admin_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'mptrs_price_change_food_menu_restaurant',
                nonce: mptrs_admin_ajax.nonce,
                editKey: editKey,
                price: price,
                postId: postId,
            },
            success: function (response) {
                if (response.data.success) {
                    $("#" + editBtnClickId).text('Set');
                    let id = 'mptrs_addedMenuPrice-' + editKey;
                    $("#" + id).text(price);
                    $(".mptrs-overlay").fadeOut();
                    $("#mptrs-overlay").empty();
                } else {
                    $("#" + editBtnClickId).text('Set');
                    $(".mptrs-overlay").fadeOut();
                    $("#mptrs-overlay").empty();
                    alert('Update failed.');
                }

            },
            error: function () {
                alert('An unexpected error occurred.');
                $("#" + editBtnClickId).text('Set');
                $(".mptrs-overlay").fadeOut();
                $("#mptrs-overlay").empty();
            }
        });
    });

    $(document).on('click', '.mptrm_editAddedMenuPrice', function (e) {
        e.preventDefault();
        let editPriceClickedId = $(this).attr('id').trim();
        let editPriceKeys = editPriceClickedId.split('-');
        let editPriceKey = editPriceKeys[1];
        let priceId = "mptrs_addedMenuPrice-" + editPriceKey;

        let priceVal = $("#" + priceId).text().trim();
        let priceWithoutDollar = priceVal.replace("$", "");
        let mptrsEditMenuPrice = `
            <div class="mptrs-overlay" id="mptrs-overlay">
                <div class="mptrs-popup">
                    <div class="mptrs-popup-header">
                        <div class="mptrs_editMenuPrice">
                            <input type="number" id="mptrs_editMenuPrice" class="mptrs_editMenuPrice" value="${priceWithoutDollar}">
                            <button class="mptrs_editMenuPriceSave" id="mptrs_editMenuPriceSave-${editPriceKey}">Set</button>
                            <button class="mptrs_editMenuPriceClose" >Cancel</button>
                        </div>
                         <span class="mptrs-close">&times;</span>
                    </div>
                    
                </div>
            </div>
        `
        $("#mptrs_foodMenuContentHolder").append(mptrsEditMenuPrice);
    });

    $(document).on('click', ".mptrs-close, .mptrs_editMenuPriceClose", function (e) {
        e.preventDefault();
        $("#mptrs-overlay").empty();
        $(".mptrs-overlay").fadeOut();
    });


    $(document).on('click', ".mptrs_orderDetailsBtn", function (e) {
        e.preventDefault();
        let orderId = $(this).parent().parent().attr('data-orderId');
        let postId = $(this).attr('id').trim();
        $.ajax({
            url: mptrs_admin_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'mptrs_order_details_display',
                nonce: mptrs_admin_ajax.nonce,
                orderId: orderId,
                postId: postId,
            },
            success: function (response) {
                if (response.data.success) {

                    let orderDetailsHtml = `<div class="mptrs_orderDetailPopupOverlay" style="display:block;">
                                                        <div class="orderDetailPopupContent">
                                                            <span class="mptrs_closePopup">&times;</span>
                            <h2 style="margin-top:0; font-size:22px; color:#333; margin-bottom:20px; border-bottom:1px solid #eee; padding-bottom:10px;">Order #${orderId} Details</h2>
                            
                            <div class="order-details-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:25px;">
                                <div class="order-details-section">
                                    <h3>Order Information</h3>
                                    <p><strong>Order Type:</strong> <span class="mptrs_orderType badge-order-type"></span></p>
                                    <p><strong>Order Status:</strong> <span class="order-status"></span></p>
                                    <p><strong>Order Date:</strong> <span class="order-date"></span></p>
                                    <p style="margin-top:15px;">
                                        <strong>Change Status:</strong><br>
                                        <select name="mptrs_service_status" id="mptrsServiceStatus-${postId}" class="mptrs_service_status" style="margin-top:5px;">
                                            <option value="in_progress">In Progress</option>
                                            <option value="done">Completed</option>
                                            <option value="service_out">Cancelled</option>
                                        </select>
                                    </p>
                                </div>
                                
                                <div class="order-details-section">
                                    <h3>Customer Information</h3>
                                    <p><strong>Name:</strong> <span class="customer-name"></span></p>
                                    <p><strong>Email:</strong> <span class="mptrs_billingEmail"></span></p>
                                    <p><strong>Phone:</strong> <span class="customer-phone"></span></p>
                                </div>
                            </div>
                            
                            <h3>Order Details</h3>
                                                            <div class="mptrs_orderDetailsDisplay"></div>
                            
                            <div style="margin-top:20px; text-align:right;">
                                <button id="update-status-btn" class="status-update-btn" data-post-id="${postId}" style="padding:8px 16px; background-color:#ff5722; color:white; border:none; border-radius:4px; cursor:pointer;">Update Status</button>
                                                        </div>
                        </div>
                    </div>`;
                    
                    $("#mptrs_orderDetailsDisplayHolder").html(orderDetailsHtml);

                    const orderData = response.data.order_data;
                    $('.mptrs_billingEmail').text(orderData.billing_email || 'N/A');
                    $('.mptrs_orderType').text(orderData.order_type || 'N/A');
                    
                    // Set additional info if available
                    if (orderData.order_date) {
                        $('.order-date').text(orderData.order_date);
                    } else {
                        $('.order-date').text('N/A');
                    }
                    
                    if (orderData.customer_name) {
                        $('.customer-name').text(orderData.customer_name);
                    } else {
                        $('.customer-name').text('N/A');
                    }
                    
                    if (orderData.customer_phone) {
                        $('.customer-phone').text(orderData.customer_phone);
                    } else {
                        $('.customer-phone').text('N/A');
                    }
                    
                    if (orderData.order_status) {
                        $('.order-status').text(orderData.order_status);
                        
                        // Set the correct status in the dropdown
                        let statusLower = orderData.order_status.toLowerCase();
                        if (statusLower.includes("progress")) {
                            $('#mptrsServiceStatus-' + postId).val('in_progress');
                        } else if (statusLower.includes("complete") || statusLower.includes("done")) {
                            $('#mptrsServiceStatus-' + postId).val('done');
                        } else if (statusLower.includes("cancel") || statusLower.includes("out")) {
                            $('#mptrsServiceStatus-' + postId).val('service_out');
                        }
                    } else {
                        $('.order-status').text('N/A');
                    }
                    
                    // Add class to order type badge
                    let typeClass = '';
                    if (orderData.order_type === 'Takeaway') {
                        typeClass = 'takeaway';
                    } else if (orderData.order_type === 'Delivery') {
                        typeClass = 'online';
                    } else if (orderData.order_type === 'Dine In') {
                        typeClass = 'dine-in';
                    }
                    
                    $('.badge-order-type').addClass(typeClass);

                    let detailsHtml = '<div class="order-items">';
                    $.each(orderData.order_info, function(key, value) {
                        if (key === "Food Menu") {
                            detailsHtml += `<div class="order-section">
                                <h4>${key}</h4>
                                <div class="food-menu-items">${value}</div>
                            </div>`;
                        } else {
                            detailsHtml += `<div class="order-info-item"><strong>${key}:</strong> ${value}</div>`;
                        }
                    });
                    detailsHtml += '</div>';

                    $('.mptrs_orderDetailsDisplay').html(detailsHtml);
                    
                    // Handle status update button click
                    $('#update-status-btn').on('click', function() {
                        let postId = $(this).data('post-id');
                        let selectedVal = $('#mptrsServiceStatus-' + postId).val();
                        
                        $.ajax({
                            url: mptrs_admin_ajax.ajax_url,
                            type: 'POST',
                            data: {
                                action: 'mptrs_save_service_status_update',
                                nonce: mptrs_admin_ajax.nonce,
                                post_id: postId,
                                selectedVal: selectedVal,
                            },
                            success: function(response) {
                                if (response.data.success) {
                                    // Update status text in popup
                                    if (selectedVal === 'in_progress') {
                                        $('.order-status').text('In Progress');
                                    } else if (selectedVal === 'done') {
                                        $('.order-status').text('Completed');
                                    } else if (selectedVal === 'service_out') {
                                        $('.order-status').text('Cancelled');
                                    }
                                    
                                    // Update status badge in the table row
                                    let statusHtml = '';
                                    if (selectedVal === 'in_progress') {
                                        statusHtml = '<span class="mptrs_order_status on-process">On Process</span>';
                                    } else if (selectedVal === 'done') {
                                        statusHtml = '<span class="mptrs_order_status completed">Completed</span>';
                                    } else if (selectedVal === 'service_out') {
                                        statusHtml = '<span class="mptrs_order_status cancelled">Cancelled</span>';
                                    }
                                    
                                    // Find the table row and update the status
                                    $('.mptrs_order_row').each(function() {
                                        if ($(this).find('.mptrs_orderDetailsBtn').attr('id') === postId) {
                                            $(this).find('td:last-child').html(statusHtml + 
                                                '<span class="mptrs_orderDetailsBtn" id="' + postId + '">Details</span>');
                                        }
                                    });
                                    
                                    alert('Order status updated successfully!');
                                } else {
                                    alert('Error: ' + response.data.message);
                                }
                            },
                            error: function() {
                                alert('An unexpected error occurred.');
                            }
                        });
                    });
                } else {
                    alert('Something Wrong.');
                }

            },
            error: function () {
                alert('An unexpected error occurred.');
            }
        });

    });

    $(document).on('click', '.mptrs_closePopup', function() {
        $('.mptrs_orderDetailPopupOverlay').fadeOut();
        $('.mptrs_orderDetailsDisplayHolder').empty();

    });


    //load more menu
    /*const rowsPerPage = parseInt($("#mptrs_displayMenuCount").val(), 10) || 0;
    let currentIndex = 0;
    const $rows = $('.mptrsTableRow');
    const $loadMoreBtn = $('.mptrs_LoadMoreMenuText');

    if( rowsPerPage > 0 ){
        $loadMoreBtn.parent().show();
    }
    $rows.hide();
    $rows.slice(0, rowsPerPage).show();
    currentIndex += rowsPerPage;

    if ($rows.length <= rowsPerPage) {
        $loadMoreBtn.hide();
    }
    $loadMoreBtn.on('click', function() {
        $rows.slice(currentIndex, currentIndex + rowsPerPage).fadeIn();
        currentIndex += rowsPerPage;
        if (currentIndex >= $rows.length) {
            $loadMoreBtn.hide();
        }
    });*/
    //End

});