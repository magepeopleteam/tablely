jQuery(document).ready(function($){
    const wrapper = $('.mptrs_food_menu_wrapper');
    const container = wrapper.find('.mptrs_food_menu_container');
    const colCount = wrapper.data('columns') || 3;

    // set CSS variable
    container.css('--mptrs-columns', colCount);

    // Animation on load
    $('.mptrs_food_menu_card').each(function(i){
        let card = $(this);
        setTimeout(()=> card.addClass('show'), i * 150);
    });

    // View switch
    $(document).on('click','.mptrs_food_menu_btn_grid', function(){
        $(this).addClass('active');
        $('.mptrs_food_menu_btn_list').removeClass('active');
        container.removeClass('list-view').addClass('grid-view');
    });

    $(document).on( 'click', '.mptrs_food_menu_btn_list', function(){
        $(this).addClass('active');
        $('.mptrs_food_menu_btn_grid').removeClass('active');
        container.removeClass('grid-view').addClass('list-view');
    });

    $(document).on('click', '.mptrs_food_category', function() {
        let selectedCategory = $(this).data('category');

        // Active state
        $('.mptrs_food_category').removeClass('active');
        $(this).addClass('active');

        // Filter logic
        if (selectedCategory === 'all') {
            $('.mptrs_food_menu_card').fadeIn(300).removeClass('hide');
        } else {
            $('.mptrs_food_menu_card').each(function() {
                let itemCategory = $(this).data('category-filter');
                if (itemCategory === selectedCategory) {
                    $(this).fadeIn(300).removeClass('hide');
                } else {
                    $(this).fadeOut(300).addClass('hide');
                }
            });
        }
    });

});