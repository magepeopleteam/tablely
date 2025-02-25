jQuery(document).ready(function ($) {

    /*$(document).on( 'click', '.mptrs_foodMenuTab', function () {
       let tabClickedId = $(this).attr('id');
       $('.mptrs_foodMenuTab').removeClass('mptrs_selectedMenuTab');
       $(this).addClass('mptrs_selectedMenuTab');

       let menuTabContainer = tabClickedId+'Container';
       $("#"+menuTabContainer).siblings().hide();
       $("#"+menuTabContainer).fadeIn(1000);
    });*/

    function appendFoodMenu( foodMenuData, key ) {
        let menuHtml = `
        <div class="mptrs_foodMenuContent" id="mptrs_foodMenuContent${key}">
            <div class="mptrs_menuImageHolder">
                <img class="mptrs_menuImage" id="mptrs_memuImgUrl${key}" src="${foodMenuData.menuImgUrl}" >
            </div>
            <div class="mptrs_menuInfoHolder">
                <div class="mptrs_topMenuInFo">
                    <div class="mptrs_menuName" id="mptrs_memuName${key}">
                        ${foodMenuData.menuName}
                    </div>
                    <input type="hidden" name="mptrs_menuCategory" id="mptrs_menuCategory${key}" value="${foodMenuData.menuCategory}">
                </div>
                <div class="mptrs_BottomMenuInFo">
                    <div class="mptrs_menuPrice" id="mptrs_memuPrice${key}">$${foodMenuData.menuPrice}</div>
                    <div class="mptrs_menuPersion" >
                        <i class='fas fa-user-alt' style='font-size:14px'></i> <span id="mptrs_memuPersons${key}">${foodMenuData.menunumPersons}</span>
                    </div>
                </div>
                <div class="mptrs_BottomMenuInFo">
                    <span class="mptrm_editFoodMenu" id="mptrsEditMenu_${key}" style="display: block">Edit</span>
                    <span class="mptrm_deleteFoodMenu" id="mptrsDeleteMenu_${key}">Delete</span>
                </div>
            </div>
        </div>
    `;

        // Append the generated HTML to a container (e.g., `#mptrs_foodMenuContainer`)
        $("#mptrs_foodMenuContainer").append(menuHtml);
    }


    var frame;
    var food_menu_image_url = ''
    $(document).on('click', '#mptrs_menuImage', function(e) {
        e.preventDefault();
        if (frame) {
            frame.open();
            return;
        }
        frame = wp.media({
            title: 'Select or Upload Food Menu Image',
            button: { text: 'Use this image' },
            multiple: false
        });
        frame.on('select', function() {
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
        console.log( deleteKey );
        $.ajax({
            url: mptrs_admin_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'mptrs_delete_food_menu',
                nonce: mptrs_admin_ajax.nonce,
                deleteKey: deleteKey,
            },
            success: function (response) {
                let removeMenu = 'mptrs_foodMenuContent'+deleteKey;
                alert(response.data.message);
                $("#"+removeMenu).fadeOut();
            },
            error: function () {
                alert('An unexpected error occurred.');
            }
        });
    });

    function menuPopup( data ){
        const categories = {
            "starter": "Starter",
            "main_course": "Main Course",
            "dessert": "Dessert",
            "beverage": "Beverage"
        };
        let displayCategory = '';
        $.each(categories, (key, value) => {
            if( key === data.category ){
                displayCategory += `<option value="${key}" selected>${value}</option>`;
            }else{
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

    $(document).on('click', '#mptrs_openPopup', function (e) {
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
        let formContent = menuPopup( data );
        if ($('#mptrs_foodMenuContentContainer').is(':empty')) {
            $('#mptrs_foodMenuContentContainer').append(formContent);
        }
        $('#mptrs_foodMenuPopup').fadeIn();

    });

    $(document).on('click', '.mptrm_editFoodMenu', function (e) {
        let editMenuId = $(this).attr('id').trim();
        let keys = editMenuId.split('_');
        let key = keys[1];
        let menuNameId  = 'mptrs_memuName'+key;
        let menuPriceId  = 'mptrs_memuPrice'+key;
        let menuPersonId  = 'mptrs_memuPersons'+key;
        let menuImgUrlId  = 'mptrs_memuImgUrl'+key;
        let mptrs_menuCategoryID  = 'mptrs_menuCategory'+key;

        let name = '';
        let imgUrl = '';
        let category = '';
        let person = '';
        let price = '';

        name = $("#"+menuNameId).text().trim();
        price = $("#"+menuPriceId).text().trim();
        price = price.replace("$", "");
        person = $("#"+menuPersonId).text().trim();
        imgUrl = $("#"+menuImgUrlId).attr("src").trim();
        category = $("#"+mptrs_menuCategoryID).val().trim();
        let btnText = 'Update Food Menu Data';
        let btnIdClass = 'mptrs_edit_food_menu_data'
        let popUpTitleText = 'Edit Food Menu';

        console.log( category );

        let data = {
            name, price, person, imgUrl, category, btnText, btnIdClass, popUpTitleText, key,
        }
        let formContent = menuPopup( data );

        if ($('#mptrs_foodMenuContentContainer').is(':empty')) {
            $('#mptrs_foodMenuContentContainer').append(formContent);
        }
        $('#mptrs_foodMenuPopup').fadeIn();
    });

    $(document).on('click', '.mptrs_edit_food_menu_data', function ( e ) {
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
                let buttonId = 'mptrs_AddEdit-'+menuKey;
                $("#"+buttonId).text('Update Food Menu Data');
                let editedMenu = 'mptrs_foodMenuContent'+menuKey;
                $("#"+editedMenu).fadeOut();
                $("#"+editedMenu).empty();
                appendFoodMenu( menuEditData, menuKey );
                mptrs_close_popup( e );
            },
            error: function () {
                alert('An unexpected error occurred.');
                $(this).text('Update Food Menu Data');
            }
        });
    });

    function mptrs_close_popup( e ){
        e.preventDefault();
        $('#mptrs_foodMenuPopup').fadeOut();
        $('#mptrs_foodMenuContentContainer').empty();
    }
    $(document).on('click', '.mptrs_closePopup', function (e) {
        mptrs_close_popup( e );
    });

    $(document).on('click', '.mptrs_submit_btn', function (e) {
        e.preventDefault();
        let formData = {};
        formData.menuName = $("#mptrs_menuName").val().trim();
        formData.menuCategory = $("#mptrs_menuCategory").val().trim();
        formData.menuPrice = $("#mptrs_menuPrice").val().trim();
        formData.menunumPersons = $("#mptrs_numPersons").val().trim();
        formData.menuImgUrl = $("#mptrs_menuImage_url").val().trim();

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
                appendFoodMenu( formData, key );

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
});