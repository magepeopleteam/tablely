jQuery(document).ready(function ($) {

    $(document).on( 'click', '.mptrs_foodMenuTab', function () {
       let tabClickedId = $(this).attr('id');
       $('.mptrs_foodMenuTab').removeClass('mptrs_selectedMenuTab');
       $(this).addClass('mptrs_selectedMenuTab');

       let menuTabContainer = tabClickedId+'Container';
       $("#"+menuTabContainer).siblings().hide();
       $("#"+menuTabContainer).fadeIn(1000);
    });


    var frame;
    var food_menu_image_url = ''
    $('#mptrs_menuImage').click(function(e) {
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
        const postId = $('#mptrs_mapping_plan_id').val();
        console.log( deleteKey );
        $.ajax({
            url: mptrs_admin_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'mptrs_delete_food_menu',
                nonce: mptrs_admin_ajax.nonce,
                post_id: postId,
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

    $(document).on('click', '.mptrm_editFoodMenu', function (e) {
        let editMenuId = $(this).attr('id').trim();
        let keys = editMenuId.split('_');
        let key = keys[1];
        let menuNameId  = 'mptrs_memuName'+key;
        let menuPriceId  = 'mptrs_memuPrice'+key;
        let menuPersonId  = 'mptrs_memuPersons'+key;
        let menuImgUrlId  = 'mptrs_memuImgUrl'+key;

        let name = $("#"+menuNameId).text();
        let price = $("#"+menuPriceId).text();
        let person = $("#"+menuPersonId).text();
        let imgUrl = $("#"+menuImgUrlId).attr("src");

        console.log( name, price, person, imgUrl);

    });

    $(document).on('click', '.mptrs_submit_btn', function (e) {
        e.preventDefault();
        let formData = {};
        formData.menuName = $("#mptrs_menuName").val().trim();
        formData.menuCategory = $("#mptrs_menuCategory").val().trim();
        formData.menuPrice = $("#mptrs_menuPrice").val().trim();
        formData.menunumPersons = $("#mptrs_numPersons").val().trim();
        formData.menuImgUrl = $("#mptrs_menuImage_url").val().trim();

        const postId = $('#mptrs_mapping_plan_id').val();
        $.ajax({
            url: mptrs_admin_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'mptrs_save_food_menu',
                nonce: mptrs_admin_ajax.nonce,
                post_id: postId,
                menuData: formData,
            },
            success: function (response) {
                alert(response.data.message);
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