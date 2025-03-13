jQuery(document).ready(function ($) {

    $(document).on('click',".mptrs_menuImageHolder", function() {

        let foodMenuCategory = $(this).closest('.mptrs_foodMenuContent').find('.mptrs_addedMenuordered').attr('data-menuCategory');
        let menuName = $(this).closest('.mptrs_foodMenuContent').find('.mptrs_addedMenuordered').attr('data-menuName').trim();
        let menuImg = $(this).closest('.mptrs_foodMenuContent').find('.mptrs_menuImage').attr('src').trim();
        let menuPrice = $(this).closest('.mptrs_foodMenuContent').find('.mptrs_addedMenuordered').attr('data-menuPrice').trim();
        let numPersons = $(this).closest('.mptrs_foodMenuContent').find('.mptrs_addedMenuordered').attr('data-numOfPerson').trim();

        let menuDetails = `
                            <div class="mptrs_foodMenuDetailsContent">
                                <div class="mptrs_menuDetailsImageHolder">
                                    <img class="mptrs_menuDetailsImage" src=" ${menuImg}" >
                                </div>
                                <div class="mptrs_menuDetailsInfoHolder">
                                    <div class="mptrs_topDetailsMenuInFo">
                                        <div class="mptrs_menuDetailsName">
                                            ${menuName}
                                        </div>
                                    </div>
                                    <div class="mptrs_BottomDetailsMenuInFo">
                                        <div class="mptrs_menuDetailsPrice">${menuPrice}</div>
                                        <div class="mptrs_menuDetailsPersion"><i class='fas fa-user-alt' style='font-size:14px'></i><span class="mptrs_numberOfPerson">${numPersons}</span></div>
                                    </div>
                                </div>
                            </div>
        `;
        let menuDetailePopup = `
        <div id="menuDetailsPopup" class="mptrs_popUpDetails">
            <div class="mptrs_popupDetailsContent">
                <div class="mptrs_menuDetailsHolder">${menuDetails}</div>
                <span class="mptrsPopupCloseBtn">&times;</span>
            </div>
        </div>
        `;
        $('body').append( menuDetailePopup );
    });


    $(document).on('click',".mptrs_orderOptionTab", function() {
        let tabClickedId = $(this).attr('id').trim();
        $('.mptrs_orderOptionTab').removeClass('mptrs_orderTabActive');
        $(this).addClass( 'mptrs_orderTabActive' );

        let tabHolderId = tabClickedId+'Holder';
        $("#"+tabHolderId).siblings().fadeOut();
        $("#"+tabHolderId).fadeIn();


    });

    $(document).on("click", ".mptrs_category_item",function () {
        $(".mptrs_category_item").removeClass('mptrs_active');
        $(this).addClass('mptrs_active');
        let filterValue = $(this).data("filter");
        $(".mptrs_categoryFilter").removeClass("active");
        $(this).addClass("active");

        if (filterValue === "all") {
            $(".mptrs_foodMenuContent").fadeIn();
        } else {
            $(".mptrs_foodMenuContent").hide().filter(`[data-category='${filterValue}']`).fadeIn();
        }
    });
    function category_shown(){
        let container = $(".mptrs_category_container");
        let items = $(".mptrs_category_item");
        let moreButton = $(".mptrs_more_button");
        let hiddenContainer = $(".mptrs_hidden_items");
        let availableWidth = container.width() - moreButton.outerWidth(true);
        let usedWidth = 0;

        moreButton.hide();
        hiddenContainer.hide();

        items.each(function () {
            let itemWidth = $(this).outerWidth(true);
            if (usedWidth + itemWidth <= availableWidth) {
                usedWidth += itemWidth;
            } else {
                $(this).hide();
                moreButton.show();
                hiddenContainer.append($(this).clone().show());
            }
        });

        moreButton.click(function () {
            hiddenContainer.toggle();
        });
    }
    category_shown();
    function mptrs_make_div_fixed( divIdname ){
        var header = $("#"+divIdname);
        var offset = header.offset().top;

        $(window).scroll(function() {
            if ($(window).scrollTop() > offset) {
                header.addClass("mptrs_fixed");
            } else {
                header.removeClass("mptrs_fixed");
            }
        });
    }
    // mptrs_make_div_fixed( 'mptrs_orderCardHolder' );



    function updateTotalPrice() {
        let totalPrice = 0;

        seatBooked.forEach(function (seat) {
            totalPrice += seat['price']; // Sum up prices
        });

        $("#mptrs_totalPrice").text( totalPrice );
    }

    let seatBooked = [];
    let seatBookedName = [];
    $(document).on( 'click', '.mptrs_dynamicShape', function (e) {

        e.preventDefault();
        let shapeClickedId = $(this).attr('id').trim();


        let data_tableBindIds = [];

        $(".mptrs_reservedMappedSeat").each(function () {
            let tableBindID = $(this).attr("data-tablebind"); // Get data attribute
            if (tableBindID) {
                data_tableBindIds.push(tableBindID); // Add to array
            }
        });

        if( data_tableBindIds.includes( shapeClickedId ) ){
            alert("Some seats are already selected. To book a table, please choose one where all seats are completely available.");
        }else{
            $('.mptrs_mappedSeat[data-tablebind="' + shapeClickedId + '"]').each(function() {
                let selectedSeatId = $(this).attr("id");
                let selectedSeatname = $(this).attr('data-seat-num');
                if (seatBooked.includes(selectedSeatId)) {
                    seatBooked = seatBooked.filter(seat => seat !== selectedSeatId); // Remove if exists
                    $("#"+selectedSeatId).children().css('background-color', 'rgb(52, 152, 219)');
                } else {
                    seatBooked.push(selectedSeatId);
                    $("#"+selectedSeatId).children().css('background-color', '#cacd1e');
                }

                if (seatBookedName.includes(selectedSeatname)) {
                    seatBookedName = seatBookedName.filter(seatName => seatName !== selectedSeatname); // Remove if exists
                } else {
                    seatBookedName.push(selectedSeatname);
                }

                // console.log( seatBookedName );

            });
        }

    });

    $(document).on( 'click', '.mptrs_mappedSeat', function (e) {
        e.preventDefault();
        const seatId = $(this).attr('id');
        const price = $(this).data('price');
        const seatNum = $(this).attr('data-seat-num');

        if (seatBooked.includes(seatId)) {
            seatBooked = seatBooked.filter(seat => seat !== seatId);
            $("#"+seatId).children().css('background-color', 'rgb(52, 152, 219)');
        } else {
            seatBooked.push(seatId);
            $("#"+seatId).children().css('background-color', '#cacd1e');
        }

        if (seatBookedName.includes(seatNum)) {
            seatBookedName = seatBookedName.filter(seatName => seatName !== seatNum); // Remove if exists
        } else {
            seatBookedName.push(seatNum);
        }

        if( seatBooked.length > 0 ){
            $("#mptrs_selectedSeatInfoHolder").show();
        }else{
            $("#mptrs_selectedSeatInfoHolder").hide();
        }
        $('#info').text(`Seat ID: ${seatId}, Price: $${price}, Seat number: ${seatNum}`);
        let todayDate = new Date().toISOString().split('T')[0];
        const time = 10;
        // updateTotalPrice();

        // $(this).css('background-color', '#cacd1e');

       /* let disabledDates = ["2025-02-20", "2025-02-25", "2025-02-26"];
        let disabledValues = [2, 7, 6];
        $('#mptrs-timepicker').empty();
        $('#mptrs-datepicker').empty();
        mptrs_datePicker( disabledDates );
        mptrs_timePicker( disabledValues );

        $('#info').text(`Seat ID: ${seatId}, Price: $${price}, Seat number: ${seatNum}`);
        let selectedSeat = `<tr>
                                       <td>Chair</td>
                                       <td>${seatNum}</td>
                                       <td>${price}</td>
                                       <td class="mptrs_removeSelectedSeat" id="mptrsRemoveSeat_${seatId}">Delete</td>
                                   </tr>`;
        $("#mptrs_selectedSeatInfo").append( selectedSeat );*/
    });


    let disabledDates = ["2025-03-20", "2025-03-25", "2025-02-26"];
    mptrs_datePicker( disabledDates );
    $(document).on('click',".mptrs_OrderPlaceBtn_old",function () {
        let orderPostClickedId = $(this).attr('id').trim();
        let orderPostId = orderPostClickedId.split('-');
        orderPostId = orderPostId[1];
        let order_time = $('.mptrs_time_button.active').data('time');
        let order_date = $("#mptrs_date").val().trim();

        let bookedSeats =  JSON.stringify( seatBooked );
        // let seatBookedName =  JSON.stringify( seatBookedName );

        $.ajax({
            url: mptrs_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'mptrs_set_order',
                nonce: mptrs_ajax.nonce,
                order_date: order_date,
                orderPostId: orderPostId,
                order_time: order_time,
                bookedSeats: bookedSeats,
            },
            dataType: 'json',
            success: function (response) {
                console.log('Success:', response);
                seatBooked = [];
                seatBookedName = [];
                alert( response.data.message);
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', status, error);
            }
        });

    });

    $(document).on('click',".mptrs_dineInOrderPlaceBtn",function () {

        let orderId = $(this).attr('id').trim();
        let mptrs_totalPrices = $("#mptrs_totalPrice").val().trim();
        let mptrs_order_time = $('.mptrs_time_button.active').data('time');
        let mptrs_order_date = $("#mptrs_date").val().trim();
        let postId = $("#mptrs_getPost").val().trim();

        let seats = '';
        let mptrs_locations = '';
        let mptrs_location = [];
        let mptrs_orderType = '';

        if( orderId === 'mptrs_dineInOrderPlaceBtn' ){
            seats = JSON.stringify( seatBooked );
            mptrs_orderType = 'dine_in';
        }else if( orderId === 'mptrs_deliveryOrderPlaceBtn' ){
            let mptrs_Location = $("#mptrsLocation").val().trim();
            let mptrs_StreetAddress = $("#mptrsStreetAddress").val().trim();
            mptrs_location.push( mptrs_Location, mptrs_StreetAddress);
            mptrs_locations = JSON.stringify( mptrs_location );
            // console.log( mptrs_location );

            mptrs_orderType = 'delivery';
        }else{
            mptrs_orderType = 'take_away';
        }


        let button = $(this);
        let post_id = postId;
        let menu = JSON.stringify( addToCartData ) ;
        let bookedSeatName =  JSON.stringify( seatBookedName );
        let quantity = 300; // Total quantity

        $.ajax({
            type: 'POST',
            url: mptrs_ajax.ajax_url,
            data: {
                action: 'mptrs_add_food_items_to_cart',
                post_id: post_id,
                mptrs_orderType: mptrs_orderType,
                menu: menu ,
                seats: seats,
                mptrs_locations: mptrs_locations,
                bookedSeatName: bookedSeatName,
                price: mptrs_totalPrices,
                quantity: quantity,
                mptrs_order_date: mptrs_order_date,
                mptrs_order_time: mptrs_order_time,
            },
            beforeSend: function () {
                button.text('Adding...');
            },
            success: function (response) {
                if (response.success) {
                    button.text('Added to Cart ✅');
                    setTimeout(  function () {
                        button.text('Process Checkout')
                    },1000);

                    window.location.href = mptrs_ajax.site_url+'/checkout/';
                } else {
                    alert(response.data);
                    button.text('Add to Cart');
                }
            }
        });
    });


    function mptrs_datePicker( disabledDates ) {
        $(".mptrs_DatePickerContainer").fadeIn();
        $("#mptrs_date").datepicker({
            dateFormat: "yy-mm-dd",
            changeMonth: true,
            changeYear: true,
            minDate: 0,
            maxDate: "+1Y",
            beforeShowDay: function (date) {
                let formattedDate = $.datepicker.formatDate("yy-mm-dd", date);
                if (disabledDates.includes(formattedDate)) {
                    return [false, "mptrs-disabled-date", "Not Available"]; // Disable and add tooltip
                }
                return [true, ""];
            }
        });
        $(".mptrs_calendarIcon").click(function () {
            $("#mptrs_date").focus();
        });
    }

    function mptrs_timePicker( disabledValues ){
        $(".mptrs_timePickerContainer").fadeIn();
        let timePicker = $("#mptrs-timepicker");
        // let disabledValues = [1, 5, 6]; // Values to be disabled
        timePicker.append(`<option value="0">select time</option>`);
        for (let i = 1; i <= 12; i++) {
            let isDisabled = disabledValues.includes(i) ? "disabled" : "";
            timePicker.append(`<option value="${i}" ${isDisabled}>${i} AM</option>`);
        }
        for (let i = 1; i <= 12; i++) {
            let value = i + 12;
            let isDisabled = disabledValues.includes(value) ? "disabled" : "";
            timePicker.append(`<option value="${value}" ${isDisabled}>${i} PM</option>`);
        }
    }

    $(document).on( 'click', '.mptrs_removeSelectedSeat', function ( e ) {
        let removeSeatClickedId = $(this).attr( 'id' );
        let removedSeatId = removeSeatClickedId.split('_');

        let removeSeatId = removedSeatId[1];
        seatBooked = removeDataFromArray( seatBooked, removeSeatId );
        $("#"+removeSeatId).css('background-color', '');
        $(this).parent().fadeOut();
        if( seatBooked.length === 0 ){
            $("#mptrs_selectedSeatInfoHolder").hide();
        }
        updateTotalPrice();

    });
    $(document).on( 'click', '.mptrs_orderBtn', function ( e ) {
        e.preventDefault();
        let orderClickedId = $(this).attr('id');
        // orderClickedId = orderClickedId.slice('-');
        let idParts = orderClickedId.split('-');
        let postId = idParts[1];
        // alert(postId);
        /*$.ajax({
            url: mptrs_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'mptrs_set_order',
                nonce: mptrs_ajax.nonce,
            },
            dataType: 'json',
            success: function (response) {
                console.log('Success:', response);
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', status, error);
            }
        });*/

    });

    // Adjust #seat-grid's margin if needed
    function adjustGridMargin() {
        let leastLeft = Infinity;

        $(".mptrs_mappedSeat").each(function () {
            const leftValue = parseInt($(this).css("left"), 10); // Parse "left" value
            if (leftValue < leastLeft) {
                leastLeft = leftValue;
            }
        });

        if (leastLeft < 0) {
            $("#seat-grid").css({
                "position": "relative",
                "margin-left": Math.abs(leastLeft) + "px"
            });
        }
    }

    // Call the function to adjust the margin
    adjustGridMargin();
    function removeDataFromArray(seatArray, seatIdToRemove) {
        return seatArray.filter(seat => seat.seatId !== seatIdToRemove);
    }

    $(".mptrs_toggleBtn").click(function() {
        var content = $(".mptrs_restaurantDes");
        if (content.hasClass("expanded")) {
            content.removeClass("expanded");
            $(this).text("See More");
        } else {
            content.addClass("expanded");
            $(this).text("See Less");
        }
    });

    const timeContainer = $(".mptrs_time_container");

    const timeSlots = {
        '11': "11:00 AM",
        '12': "12:00 PM",
        '13': "01:00 PM",
        '14': "02:00 PM",
        '15': "03:00 PM",
        '16': "04:00 PM",
        '17': "05:00 PM",
        '18': "06:00 PM",
        '19': "07:00 PM",
        '20': "08:00 PM",
        '21': "09:00 PM",
        '22': "10:00 PM"
    };
    
    $.each( timeSlots, function ( key, time ) {
        let button = $("<button>")
            .addClass("mptrs_time_button")
            .text(time)
            .attr("data-time", key );

        timeContainer.append(button);
    })

    $(document).on( 'click', '.mptrs_button', function () {
        $(".mptrs_time_container").css("display", "flex");
    });


    $(document).on('click',".mptrsPopupCloseBtn",function () {
        $("#menuDetailsPopup").fadeOut();
        $("#menuDetailsPopup").remove();
    });

    $(document).on('click',".close-btn",function () {
        $("#seatPopup").fadeOut();
        $("#mptrs_seatMapDisplay").empty();
    });


    function calculateTotal() {
        let total = 0;
        $(".mptrs_menuAddedCartItem").each(function () {
            let price = parseFloat($(this).data("price"));
            let quantity = parseInt($(this).find(".mptrs_quantity").text());
            if ( !isNaN(price) && !isNaN(quantity ) ) {
                total += price * quantity;
            }
        });
        $("#mptrs_totalPrice").val(total);
        if( total === 0 ){
            $("#mptrs_totalPriceHolder").fadeOut();
            $(".mptrs_foodOrderContentholder").fadeOut();
            $("#mptrs_orderedFoodMenuInfoHolder").fadeOut();
        }else{
            $("#mptrs_totalPriceHolder").fadeIn();
            $("#mptrs_orderedFoodMenuInfoHolder").fadeIn();
            $(".mptrs_foodOrderContentholder").fadeIn();
        }
    }
    // Increase Button Click
    $(document).on("click", ".mptrs_increase", function () {
        let clickedId = $(this).parent().attr('id').trim();
        let menuKeys = clickedId.split('-');
        let menuKey = menuKeys[1];

        let menuAddedQtyKey = 'mptrs_menuAddedQuantity-'+menuKey;
        let menuQtyKey = 'mptrs_quantity-'+menuKey;
        let quantityElem = $(this).siblings(".mptrs_quantity");
        let quantity = parseInt(quantityElem.text()) + 1;
        $("#"+menuAddedQtyKey).text(quantity);
        $("#"+menuQtyKey).text(quantity);

        addToCartData[menuKey] = quantity;

        calculateTotal();
    });

    // Decrease Button Click
    $(document).on("click", ".mptrs_decrease", function () {

        let clickedId = $(this).parent().attr('id').trim();
        let menuKeys = clickedId.split('-');
        let menuKey = menuKeys[1];

        let menuAddedQtyKey = 'mptrs_menuAddedQuantity-'+menuKey;
        let menuQtyKey = 'mptrs_quantity-'+menuKey;

        let quantityElem = $(this).siblings(".mptrs_quantity");
        let quantity = parseInt(quantityElem.text()) - 1;
        if (quantity < 1) {

            let qtyControlHolder = 'mptrs_addedQuantityControls-'+menuKey;
            let addedMenuBtn = 'mptrs_addBtn-'+menuKey;
            let addedMenuInCart = 'mptrs_menuAddedCartItem-'+menuKey;
            $("#"+qtyControlHolder).remove();
            $("#"+addedMenuInCart).remove();
            $("#"+addedMenuBtn).show();

            if (addToCartData.hasOwnProperty(menuKey)) {
                delete addToCartData[menuKey];
            }

        } else {

            addToCartData[menuKey] = quantity;

            $("#"+menuAddedQtyKey).text(quantity);
            $("#"+menuQtyKey).text(quantity);
        }

        // console.log( addToCartData );

        calculateTotal();
    });
    function mptrs_display_food_menu_for_order( food_menu_data ){
        let container = $("#mptrs_foodMenuHolder");
        $.each( food_menu_data, function (id, item) {
            let menuItem = `
                    <div class="mptrs_menuAddedCartItem" data-menuKey="${id}" data-price="${item.menuPrice}">
                        <img class="mptrs_menuImg" src="${item.menuImgUrl}" alt="${item.menuName}">
                        <div class="mptrs_menuDetails">
                            <div class="mptrs_menuName">${item.menuName}</div>
                            <div class="mptrs_menuPrice">৳ ${item.menuPrice}</div>
                        </div>
                        <button class="mptrs_addBtn">+</button>
                        <div class="mptrs_quantityControls" style="display:none;">
                            <button class="mptrs_decrease">−</button>
                            <span class="mptrs_quantity">1</span>
                            <button class="mptrs_increase">+</button>
                        </div>
                    </div>`;
            container.append(menuItem);
        });
    }

    let addToCartData = {};
    // Add Button Click
    $(document).on('click', ".mptrs_addBtn", function () {
        $(this).fadeOut();
        $("#mptrs_orderedFoodMenuInfoHolder").fadeIn();
        $("#mptrs_dineInTabHolder").fadeIn();

        let menuAddedClickedId = $(this).attr('id').trim();
        let menuAddedKeys = menuAddedClickedId.split('-');
        let menuAddedKey = menuAddedKeys[1];
        // alert( menuAddedKey );


        let addedMenu = `
            <div class="mptrs_addedQuantityControls" id="mptrs_addedQuantityControls-${menuAddedKey}">
                <button class="mptrs_decrease">−</button>
                <span class="mptrs_quantity" id="mptrs_menuAddedQuantity-${menuAddedKey}">1</span>
                <button class="mptrs_increase">+</button>
            </div>
        `;
        $(this).parent().append( addedMenu );

        let animationDiv =  $(this).parent().parent();
        let parentItem = $(this).parent();
        let foodMenuCategory = parentItem.attr('data-menuCategory');
        let menuImgUrl = parentItem.attr('data-menuImgUrl');
        let menuName = parentItem.attr('data-menuName');
        let menuPrice = parentItem.attr('data-menuPrice');
         menuPrice = parseFloat(menuPrice.replace(/[^0-9.]/g, ''));
        let numOfPerson = parentItem.attr('data-numOfPerson');
        let mptrs_CurrencySymbol = jQuery('.woocommerce-Price-currencySymbol:first').text().trim();


        let item = {
            menuImgUrl: menuImgUrl,
            menuName: menuName,
            menuPrice: menuPrice,
            mptrs_CurrencySymbol: mptrs_CurrencySymbol,
            numOfPerson: numOfPerson,
            foodMenuCategory: foodMenuCategory,
            menuAddedKey: menuAddedKey,
        };

        addToCartData[menuAddedKey] = 1;

        // Create flying effect
        let flyItem = animationDiv.clone().css({
            position: "absolute",
            top: animationDiv.offset().top,
            left: animationDiv.offset().left,
            width: animationDiv.width(),
            opacity: 1,
            zIndex: 1000
        }).appendTo("body");

        let targetOffset = $("#mptrs_orderedFoodMenuHolder").offset();

        flyItem.animate({
            top: targetOffset.top + 10,
            left: targetOffset.left + 10,
            width: "50px",
            opacity: 0
        }, 800, function () {
            flyItem.remove();
            mptrs_append_order_food_menu(item);
            calculateTotal();
        });

    });

    function mptrs_append_order_food_menu(item) {
        let container = $("#mptrs_orderedFoodMenuHolder");

        let menuItem = `
        <div class="mptrs_menuAddedCartItem" id="mptrs_menuAddedCartItem-${item.menuAddedKey}" data-id="${item.menuAddedKey}" data-price="${item.menuPrice}">
            <img class="mptrs_menuImg" src="${item.menuImgUrl}" alt="${item.menuName}">
            <div class="mptrs_menuDetails">
                <div class="mptrs_addedMenuName">${item.menuName}</div>
                <div class="mptrs_menuPrice">${item.mptrs_CurrencySymbol} ${item.menuPrice}</div>
            </div>
            
            <div class="mptrs_quantityControls" id="mptrs_quantityControls-${item.menuAddedKey}">
                <button class="mptrs_decrease">−</button>
                <span class="mptrs_quantity" id="mptrs_quantity-${item.menuAddedKey}">1</span>
                <button class="mptrs_increase">+</button>
            </div>
        </div>`;

        let $menuItem = $(menuItem);
        container.append($menuItem);
        $menuItem.hide().fadeIn(1000);
    }

    function mptrs_display_ordered_menu( get_time ){
        let menuItems = [];
        let quantities = [];
        $(".mptrs_addedMenuName").each(function () {
            let text = $(this).text().trim(); // Get text and trim spaces
            if (text) {
                menuItems.push(text); // Add to array
            }
        });
        $(".mptrs_menuAddedCartItem").each(function () {
            let parentCartItem = $(this).closest(".mptrs_menuAddedCartItem"); // Find the closest parent
            let quantity = parentCartItem.find(".mptrs_quantity").text().trim(); // Get quantity text

            quantities.push(quantity);
        });

        let tableBody = $("#mptrs_orderAddedTable tbody");
        tableBody.empty();
        for (let i = 0; i < menuItems.length; i++) {
            let row = `<tr>
                    <td>${menuItems[i]}</td>
                    <td>${quantities[i]}</td>
                </tr>`;

            tableBody.append(row);
        }
        let details = $("#mptrs_orderAddedDetails");

        let totalPrices = $("#mptrs_totalPrice").val().trim();
        // let order_time = $('.mptrs_time_button.active').data('time');
        let order_date = $("#mptrs_date").val().trim();
        let detailsRow = `<tr>
                            <td>${order_date}</td>
                            <td>${get_time}</td>
                            <td>${totalPrices}</td>
                        </tr>`;
        details.append(detailsRow);
    }


    // Handle time selection
    $(document).on('click',".mptrs_time_button", function () {

        $(".mptrs_time_button").removeClass("active");
        let get_postId =  $("#mptrs_getPost").val().trim();
        let get_time = $(this).data('time');
        let get_date = $("#mptrs_date").val().trim();
        if( !get_date ){
            alert('Select Date First!');
        }else{
            mptrs_display_ordered_menu( get_time );
            // console.log( mptrs_menuNamesPopup, mptrs_menuQuantityPopup );
            $(this).addClass("active");
            $("#seatPopup").fadeIn();
            $.ajax({
                url: mptrs_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'mptrs_get_available_seats_for_reservations',
                    nonce: mptrs_ajax.nonce,
                    get_time: get_time,
                    get_date: get_date,
                    post_id: get_postId,
                },
                dataType: 'json',
                success: function (response) {
                    $(this).addClass("active");
                    // mptrs_display_food_menu_for_order( response.data.get_food_menu )

                    seatBooked = [];
                    seatBookedName = [];
                    $("#mptrs_seatMapDisplay").append( response.data.mptrs_seat_maps );
                },
                error: function () {
                    alert( 'Error occurred');
                }
            });
        }


    });

});
