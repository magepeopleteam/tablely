jQuery(document).ready( function ( $ ) {

    const wrapper = $('.mptrs_restaurant_wrapper');
    const container = wrapper.find('.mptrs_restaurant_container');
    const colCount = wrapper.data('columns') || 3;
    container.css('--mptrs-columns', colCount);
    $('.mptrs_restaurant_card').each(function(i){
        let card = $(this);
        setTimeout(()=> card.addClass('show'), i * 150);
    });

    $(document).on('click','.mptrs_restaurant_btn_grid', function(){
        $(this).addClass('active');
        $('.mptrs_restaurant_btn_list').removeClass('active');
        container.removeClass('list-view').addClass('grid-view');

        $(".mptrs_restaurant_desc").fadeOut();
    });

    $(document).on( 'click', '.mptrs_restaurant_btn_list', function(){
        $(this).addClass('active');
        $('.mptrs_restaurant_btn_grid').removeClass('active');
        container.removeClass('grid-view').addClass('list-view');
        $(".mptrs_restaurant_desc").fadeIn();
    });

    $(document).on('click', '.mptrs_restaurant_city', function() {
        let selectedCategory = $(this).data('city');

        $('.mptrs_restaurant_city').removeClass('active');
        $(this).addClass('active');

        // Filter logic
        if (selectedCategory === 'all') {
            $('.mptrs_restaurant_card').fadeIn(300).removeClass('hide');
        } else {
            $('.mptrs_restaurant_card').each(function() {
                let itemCategory = $(this).data('city-filter');
                if (itemCategory === selectedCategory) {
                    $(this).fadeIn(300).removeClass('hide');
                } else {
                    $(this).fadeOut(300).addClass('hide');
                }
            });
        }
    });


    const mptrs_resPerPage = parseInt($("[name='mptrs_display_restaurant_count']").val(), 10) || 6; // default 6 if empty
    let mptrs_resCurrentCount = 0;
    const mptrs_resCards = $('.mptrs_restaurant_card');
    const totalCount = mptrs_resCards.length;

    mptrs_resCards.hide();
    mptrs_resCards.slice(0, mptrs_resPerPage).show();
    mptrs_resCurrentCount = mptrs_resPerPage;
    $('.mptrs_restaurant_load_more_btn').on('click', function() {
        console.log('Current:', mptrs_resCurrentCount, 'PerPage:', mptrs_resPerPage);

        mptrs_resCards
            .slice(mptrs_resCurrentCount, mptrs_resCurrentCount + mptrs_resPerPage)
            .slideDown();

        mptrs_resCurrentCount += mptrs_resPerPage;

        if (mptrs_resCurrentCount >= totalCount) {
            $(this).fadeOut();
        }
    });

});