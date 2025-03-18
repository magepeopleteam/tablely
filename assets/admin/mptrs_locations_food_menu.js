jQuery(document).ready(function ($) {

    /*$(document).on( 'click', '.mptrs_foodMenuTab', function () {
       let tabClickedId = $(this).attr('id');
       $('.mptrs_foodMenuTab').removeClass('mptrs_selectedMenuTab');
       $(this).addClass('mptrs_selectedMenuTab');

       let menuTabContainer = tabClickedId+'Container';
       $("#"+menuTabContainer).siblings().hide();
       $("#"+menuTabContainer).fadeIn(1000);
    });*/

    $(document).on("click", ".mptrs_categoryFilter", function () {
        let filterValue = $(this).data("filter");
        $(".mptrs_categoryFilter").removeClass("active");
        $(this).addClass("active");

        if (filterValue === "all") {
            $(".mptrsTableRow").fadeIn();
        } else {
            $(".mptrsTableRow").hide().filter(`[data-category='${filterValue}']`).fadeIn();
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
                        <i class="fas fa-bars mptrs-drag"></i>
                        <input class="mptrs-category-name" type="text" value="${value}">
                        <i class="fas fa-trash-alt mptrs-delete"></i>
                    </div>
            `;
            });
        } else {
            displayCategories = 'No Category Found!';
        }

        let mptrs_category_data = `
            <div class="mptrs_categoryDataHolder">
                <div class="mptrs-popup-header">
                    Categories
                </div>
                <div class="mptrs-category-list">
                    ${displayCategories}
                </div>
                <div class="mptrs-add-category">
                    <i class="fas fa-plus"></i> Add category
                </div>
                <div class="mptrs-popup-footer">
                    <button class="mptrs-btn mptrs-cancel">Cancel</button>
                    <button class="mptrs-btn mptrs-save">Save</button>
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
            /* $('.custom-foodMenu-image-preview').html('<img src="' + attachment.url + '" style="max-width: 100%; margin-bottom: 10px;">');
             $('.custom-foodMenu-image-preview').show();*/
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

    function mptrs_menuPopup_old(data, categories) {
        /*const categories = {
            "starter": "Starter",
            "main_course": "Main Course",
            "dessert": "Dessert",
            "beverage": "Beverage"
        };*/

        let displayCategory = '';
        $.each(categories, (key, value) => {
            if (key === data.category) {
                displayCategory += `<option value="${key}" selected>${value}</option>`;
            } else {
                displayCategory += `<option value="${key}" >${value}</option>`;
            }
        });

        return `
        <h2 class="mptrs_addEditMenuTitleText">${data.popUpTitleText}</h2>
        <form id="mptrs_foodMenuForm" class="mptrs_food_menu_form">
            <div class="mptrs_form_group">
                <label for="mptrs_menuName">Menu Name</label>
                <input type="text" id="mptrs_menuName" name="mptrs_menuName" class="mptrs_input" required value="${data.name}">
            </div>

            <div class="mptrs_form_group">
                <label for="mptrs_menuCategory">Category</label>
                <select id="mptrs_menuCategory" name="mptrs_menuCategory" class="mptrs_select" required>
                    ${displayCategory}
                </select>
            </div>

            <div class="mptrs_form_group">
                <label for="mptrs_menuPrice">Price ($)</label>
                <input type="number" id="mptrs_menuPrice" name="mptrs_menuPrice" class="mptrs_input" required value="${data.price}">
            </div>

            <div class="mptrs_form_group">
                <label for="mptrs_numPersons">Number of Persons</label>
                <input type="number" id="mptrs_numPersons" name="mptrs_numPersons" class="mptrs_input" min="1" required value="${data.person}">
            </div>

            <div class="mptrs_form_group">
                <label for="mptrs_menuImage">Menu Image</label>
                <input type="file" id="mptrs_menuImage" name="mptrs_menuImage" class="mptrs_input">
                <input type="hidden" id="mptrs_menuImage_url" name="mptrs_menuImage_url" value="${data.imgUrl}">
                <div class="custom-foodMenu-image-preview"></div>
            </div>

            <button type="submit" class="${data.btnIdClass}" id="mptrs_AddEdit-${data.key}">${data.btnText}</button>
        </form>
    `;
    }

    function mptrs_menuPopup( data, categories ) {
        console.log( categories );
        let displayCategory = '';
        $.each(categories, (key, value) => {
            displayCategory += `<option value="${key}" ${key === data.category ? 'selected' : ''}>${value}</option>`;
        });

        return `
        <h2 class="mptrs_addEditMenuTitleText">${data.popUpTitleText}</h2>
        <form id="mptrs_foodMenuForm" class="mptrs_food_menu_form">
            <div class="mptrs_form_group">
                <label for="mptrs_menuName">Menu Name</label>
                <input type="text" id="mptrs_menuName" name="mptrs_menuName" class="mptrs_input" required value="${data.name}">
            </div>

            <div class="mptrs_form_group">
                <label for="mptrs_menuCategory">Category</label>
                <select id="mptrs_menuCategory" name="mptrs_menuCategory" class="mptrs_select" required>
                    ${displayCategory}
                </select>
            </div>

            <div class="mptrs_form_group">
                <label for="mptrs_menuPrice">Price ($)</label>
                <input type="number" id="mptrs_menuPrice" name="mptrs_menuPrice" class="mptrs_input" required value="${data.price}">
            </div>

            <div class="mptrs_form_group">
                <label for="mptrs_numPersons">Number of Persons</label>
                <input type="number" id="mptrs_numPersons" name="mptrs_numPersons" class="mptrs_input" min="1" required value="${data.person}">
            </div>

            <div class="mptrs_form_group">
                <label for="mptrs_menuImage">Menu Image</label>
                <input type="file" id="mptrs_menuImage" name="mptrs_menuImage" class="mptrs_input">
                <input type="hidden" id="mptrs_menuImage_url" name="mptrs_menuImage_url" value="${data.imgUrl}">
                <div class="custom-foodMenu-image-preview"></div>
            </div>

            <div id="mptrs_variationsContainer">
                <button type="button" id="mptrs_addVariationCategory">+ Add Variation Category</button>
            </div>

            <button type="submit" class="${data.btnIdClass}" id="mptrs_AddEdit-${data.key}">${data.btnText}</button>
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
        let imgUrl = '';
        let category = '';
        let btnText = 'Add Menu';
        let btnIdClass = 'mptrs_submit_btn';
        let popUpTitleText = 'Add New Food Menu!';

        let data = {
            name, price, person, imgUrl, category, btnText, btnIdClass, popUpTitleText, key
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
                    variations = mptrs_edited_menu['variations'];

                    let data = {
                        name, price, person, imgUrl, category, btnText, btnIdClass, popUpTitleText, key, variations
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

        $(this).text('Updating...');
        let get_keys = $(this).attr('id');
        let menuKeys = get_keys.split('-');
        let menuKey = menuKeys[1];
        let menuEditData = {};
        menuEditData.menuName = $("#mptrs_menuName").val().trim();
        menuEditData.menuCategory = $("#mptrs_menuCategory").val().trim();
        menuEditData.menuPrice = $("#mptrs_menuPrice").val().trim();
        menuEditData.menunumPersons = $("#mptrs_numPersons").val().trim();
        menuEditData.menuImgUrl = $("#mptrs_menuImage_url").val().trim();
        menuEditData.menuKey = menuKey;

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

    function addVariationCategory() {
        let categoryId = 'category_' + new Date().getTime(); // Unique ID
        let categoryHTML = `
            <div class="mptrs_variationCategory" id="${categoryId}">
                <input type="text" class="mptrs_variationCategoryName" placeholder="Variation Name">
                <button type="button" class="mptrs_addVariationItem">+ Add Item</button>
                <button type="button" class="mptrs_removeVariationCategory">Remove Category</button>
                <div class="mptrs_variationItems"></div>
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
                <button type="button" class="mptrs_removeVariationItem">Remove</button>
            </div>
        `;
        category.find(".mptrs_variationItems").append(itemHTML);
    }

    $(document).on("click", "#mptrs_addVariationCategory", function () {
        addVariationCategory();
    });
    $(document).on("click", ".mptrs_addVariationItem", function () {
        addVariationItem($(this).closest(".mptrs_variationCategory"));
    });
    $(document).on("click", ".mptrs_removeVariationCategory", function () {
        $(this).closest(".mptrs_variationCategory").remove();
    });
    $(document).on("click", ".mptrs_removeVariationItem", function () {
        $(this).closest(".mptrs_variationItem").remove();
    });
    function getVariationData() {
        let variations = [];

        $(".mptrs_variationCategory").each(function () {
            let categoryName = $(this).find(".mptrs_variationCategoryName").val();
            let items = [];

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
                items: items
            });
        });

        console.log(variations);
        return variations;
    }

    $(document).on('click', '.mptrs_submit_btn', function (e) {
        e.preventDefault();
        let variations = [];
        let formData = {};
        formData.menuName = $("#mptrs_menuName").val().trim();
        formData.menuCategory = $("#mptrs_menuCategory").val().trim();
        formData.menuPrice = $("#mptrs_menuPrice").val().trim();
        formData.menunumPersons = $("#mptrs_numPersons").val().trim();
        formData.menuImgUrl = $("#mptrs_menuImage_url").val().trim();


       /* $('.mptrs_variation_group').each(function() {
            let size = $(this).find('input[name="variation_size[]"]').val();
            let price = $(this).find('input[name="variation_price[]"]').val();
            variations.push({ size, price });
        });*/

        variations = getVariationData();

        formData.variations = variations;
        // console.log("formData:", formData);

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
        // console.log( menuAddText );
        let imgContentId = 'mptrs_MenuImg-' + menuId;
        let nameContentId = 'mptrs-menuName-' + menuId;
        let priceContentId = 'mptrs-menuPrice-' + menuId;

        let action = '';
        let removedKey = 'mptrs_addedFoodMenu' + menuId;
        if (menuAddText === 'Add') {
            $("#" + getClickedId).text('Adding..');
            action = 'mptrs_save_food_menu_for_restaurant';
        } else if (menuAddText === 'Added') {
            action = 'mptrs_remove_saved_food_menu_for_restaurant';
            $("#" + getClickedId).text('Removing..');
        } else {
            $("#" + getClickedId).text('Deleting..');
            action = 'mptrs_remove_saved_food_menu_for_restaurant';
        }
        const postId = $('#mptrs_mapping_plan_id').val();
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
                                <button class="mptrs_addMenuToPost" id="mptrs_addedMenuToPost-${menuId}"><i class="fa-solid fa-trash"></i></button>
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

});