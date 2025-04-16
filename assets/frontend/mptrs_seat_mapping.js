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
    });


    let disabledDates = ["2025-03-20", "2025-03-25", "2025-02-26"];
   /* mptrs_datePicker( disabledDates, 'mptrs_date' );
    mptrs_datePicker( disabledDates, 'mptrs_dalivery_date' );
    mptrs_datePicker( disabledDates, 'mptrs_takeaway_date' );*/

    $(document).on('click',".mptrs_dineInOrderPlaceBtn",function () {
        let orderVarDetails = {};
        let orderVarDetailsText = '';
        $(".mptrs_addedMenuOrderDetails").each(function () {
            let key = $(this).parent().parent().attr('data-id');
            orderVarDetailsText = $(this).text();
            orderVarDetails[key] = orderVarDetailsText;
        });

        let orderId = $(this).attr('id').trim();
        let mptrs_totalPrices = $("#mptrs_totalPrice").val().trim();
        mptrs_totalPrices = parseFloat(mptrs_totalPrices.replace(/[^\d.]/g, ''));
        let mptrs_order_time = mptrs_orderSettings.mptrs_orderTime;
        let mptrs_order_date = mptrs_orderSettings.mptrs_orderDate;
        let mptrs_orderType = mptrs_orderSettings.mptrs_orderType;
        let mptrs_locations = mptrs_orderSettings.mptrs_locations;
        let postId = $("#mptrs_getPost").val().trim();

        console.log(mptrs_orderSettings);

        let seats = '';
        // let mptrs_location = [];


        if( orderId === 'mptrs_dineInOrderPlaceBtn' ){
            // mptrs_order_date = $("#mptrs_date").val().trim();
            seats = JSON.stringify( seatBooked );
            // mptrs_orderType = 'dine_in';
        }else if( orderId === 'mptrs_deliveryOrderPlaceBtn' ){
            let mptrs_Location = $("#mptrsLocation").val().trim();
            let mptrs_StreetAddress = $("#mptrsStreetAddress").val().trim();
            // mptrs_location.push( mptrs_Location, mptrs_StreetAddress);
            // mptrs_locations = JSON.stringify( mptrs_location );
            // mptrs_order_date = $("#mptrs_dalivery_date").val().trim();
            // mptrs_orderType = 'delivery';
        }else{
            // mptrs_orderType = 'take_away';
            // mptrs_order_date = $("#mptrs_takeaway_date").val().trim();
        }

        let button = $(this);
        let post_id = postId;
        let menu = JSON.stringify( addToCartData );
        let orderVarDetailsStr = JSON.stringify( orderVarDetails );
        let bookedSeatName =  JSON.stringify( seatBookedName );
        let quantity = 300; // Total quantity
        let nonce = mptrs_ajax.nonce;

        $.ajax({
            type: 'POST',
            url: mptrs_ajax.ajax_url,
            data: {
                action: 'mptrs_add_food_items_to_cart',
                post_id: post_id,
                mptrs_orderType: mptrs_orderType,
                menu: menu,
                orderVarDetailsStr: orderVarDetailsStr,
                // seats: seats,
                mptrs_locations: mptrs_locations,
                // bookedSeatName: bookedSeatName,
                price: mptrs_totalPrices,
                nonce: nonce,
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


    function mptrs_datePicker( disabledDates, id ) {
        $(".mptrs_DatePickerContainer").fadeIn();
        $("#"+id).datepicker({
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
        $(document).on('click',".mptrs_calendarIcon",function () {
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

    const tableReserveTimeContainer = $(".mptrs_tableReserveTimeContainer");
    const tableReserveTimeSlots = {
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
    $.each( tableReserveTimeSlots, function ( key, time ) {
        let button = $("<button>")
            .addClass("mptrs_tableReserveTimeButton")
            .text(time)
            .attr("data-time", key );

        tableReserveTimeContainer.append(button);
    })

    $(document).on( 'click', '.mptrs_button', function () {
        $(".mptrs_time_container").css("display", "flex");
    });

    /*$(document).on( 'click', '.mptrs_findTimeButton', function () {
        $(".mptrs_tableReserveTimeContainer").css("display", "flex");
    });*/


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

        let mptrs_priceSimble = jQuery('.woocommerce-Price-currencySymbol:first').text().trim();

        // $("#mptrs_sitePriceSymble").text( mptrs_priceSimble );
        $("#mptrs_totalPrice").val( mptrs_priceSimble+ total );
        if( total === 0 ){
            $("#mptrs_totalPriceHolder").fadeOut();
            $(".mptrs_foodOrderContentholder").fadeOut();
            $("#mptrs_orderedFoodMenuInfoHolder").fadeOut();
            $("#mptrs_foodMenuAddedCart").fadeIn();
        }else{
            $("#mptrs_totalPriceHolder").fadeIn();
            $("#mptrs_orderedFoodMenuInfoHolder").fadeIn();
            $("#mptrs_foodMenuAddedCart").fadeOut();
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
    $(document).on('click', ".mptrs_addBtn_old", function () {
        $(this).fadeOut();
        $("#mptrs_orderedFoodMenuInfoHolder").fadeIn();
        $("#mptrs_foodMenuAddedCart").fadeOut();
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
        let orderVarDetails = item.mptrs_oderDetails.trim(); // Get text from element
        orderVarDetails = orderVarDetails.replace(/,\s*$/, "");
        let menuItem = `
        <div class="mptrs_menuAddedCartItem" id="mptrs_menuAddedCartItem-${item.menuAddedKey}" data-id="${item.menuAddedKey}" data-price="${item.menuPrice}">
            <img class="mptrs_menuImg" src="${item.menuImgUrl}" alt="${item.menuName}">
            <div class="mptrs_menuDetails">
                <div class="mptrs_addedMenuName">${item.menuName}</div>
                <div class="mptrs_addedMenuOrderDetails">${orderVarDetails}</div>
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
            let parentCartItem = $(this).closest(".mptrs_menuAddedCartItem");
            let quantity = parentCartItem.find(".mptrs_quantity").text().trim();

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
        totalPrices = parseFloat(totalPrices.replace(/[^\d.]/g, ''));
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

        let activeText = $('.mptrs_orderOptionTab.mptrs_orderTabActive').text().trim();
        $(".mptrs_time_button").removeClass("active");
        if( activeText === 'Dine-In' ){
            let get_postId =  $("#mptrs_getPost").val().trim();
            let get_time = $(this).data('time');
            let get_date = $("#mptrs_date").val().trim();
            if( !get_date ){
                alert('Select Date First!');
            }
            else{
                mptrs_display_ordered_menu( get_time );
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
        }else if( activeText === 'Delivery' ){
            let get_date = $("#mptrs_dalivery_date").val().trim();
            if( !get_date ){
                alert('Select Date First!');
            }else{
                $(this).addClass("active");
            }

        }else{
            let get_date = $("#mptrs_takeaway_date").val().trim();
            if( !get_date ){
                alert('Select Date First!');
            }else{
                $(this).addClass("active");
            }

        }
    });


    // Handle time selection
    let mptrs_tableReserveDate = '';
    let mptrs_tableReserveTime = '';
    let mptrs_tableReservePost = '';
    $(document).on('click',".mptrs_findSeatsButton", function () {

        $(this).text('Loading...');

        mptrs_tableReservePost =  $("#mptrs_tableReserveId").val().trim();
        mptrs_tableReserveTime = $(".mptrs_tableReserveTimeButton.active").attr('data-time').trim();
        mptrs_tableReserveDate = $("#mptrs_seatReserveDate").val().trim();

        $(this).addClass("active");
        $("#seatPopup").fadeIn();
        $("#mptrs_seatReserveMapDisplay").empty();
        $.ajax({
            url: mptrs_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'mptrs_get_available_seats_for_reservations',
                nonce: mptrs_ajax.nonce,
                get_time: mptrs_tableReserveTime,
                get_date: mptrs_tableReserveDate,
                post_id: mptrs_tableReservePost,
            },
            dataType: 'json',
            success: function (response) {
                $(this).addClass("active");
                // mptrs_display_food_menu_for_order( response.data.get_food_menu )

                seatBooked = [];
                seatBookedName = [];
                $("#mptrs_seatReserveMapDisplay").append( response.data.mptrs_seat_maps );
                $('#mptrs_findSeatsButton').text('Finds Seats');
            },
            error: function () {
                $(this).text('Finds Seats');
                $('#mptrs_findSeatsButton').text('Finds Seats');
            }
        });

    });

    $(document).on('click',".mptrs_tableReservationBackButton", function ( e ) {
        e.preventDefault();

        $("#mptrs_tableReservationButton").text('Book Now');
        $("#mptrs_tableReservationButton").css('width','100%');

        $("#mptrs_tableReservationbackButton").fadeOut();
        $("#mptrs_tableReservePersonInfo").fadeOut();
        $("#mptrs_tableReserveInfoHolder").fadeIn();
    });

    $(document).on('click',".mptrs_tableReservationButton", function ( e ) {
        e.preventDefault();

        let userAdvice = '';
            let getText = $(this).text().trim();
        if( getText === 'Book Now' ){
            $("#mptrs_tableReserveInfoHolder").fadeOut();
            $("#mptrs_tableReservePersonInfo").fadeIn();
            $("#mptrs_tableReservationbackButton").fadeIn();
            $("#mptrs_tableReservationButton").css('width','calc( 100% - 60px)');
            $(this).text('Confirm Reservation');
        }else {
            $(this).text('Booking...');
            // console.log( mptrs_tableReserveDate, mptrs_tableReserveTime, mptrs_tableReservePost );
            mptrs_tableReservePost = $("#mptrs_tableReserveId").val().trim();
            mptrs_tableReserveTime = $(".mptrs_tableReserveTimeButton.active").attr('data-time').trim();
            mptrs_tableReserveDate = $("#mptrs_seatReserveDate").val().trim();

            let userName = $("#mptrs_seatReserveName").val().trim();
            let userPhoneNum = $("#mptrs_seatReservePhone").val().trim();
            let userEmailId = $("#mptrs_seatReserveEmail").val().trim();
            userAdvice = $("#mptrs_seatReserveMessage").val().trim();
            let seatIds = JSON.stringify(seatBooked);
            let seatNames = JSON.stringify(seatBookedName);
            let occasion = $('select[name="mptrs_occasion"]').val();
            let guests = $('select[name="mptrs_guests"]').val();

            let postId = $("#mptrs_tableReserveId").val().trim();

            $.ajax({
                url: mptrs_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'mptrs_table_reservations',
                    nonce: mptrs_ajax.nonce,
                    get_time: mptrs_tableReserveTime,
                    get_date: mptrs_tableReserveDate,
                    seatIds: seatIds,
                    seatNames: seatNames,
                    occasion: occasion,
                    guests: guests,
                    userName: userName,
                    userPhoneNum: userPhoneNum,
                    userEmailId: userEmailId,
                    userAdvice: userAdvice,
                    postId: postId,
                },
                dataType: 'json',
                success: function (response) {
                    $('#mptrs_tableReservationButton').text('Confirm Reservation');
                    console.log(response);
                },
                error: function () {
                    alert('Error Occured');
                    $('#mptrs_tableReservationButton').text('Confirm Reservation');
                }
            });
        }

    });

    // Handle time selection
    $(document).on('click',".mptrs_tableReserveTimeButton", function () {

        let get_date = $("#mptrs_seatReserveDate").val().trim();
        if( !get_date ){
            alert('Select Date First!');
        }else{
            $(".mptrs_tableReserveTimeButton").removeClass("active");
            $(this).addClass('active');
            $("#mptrs_findSeatsButton").fadeIn();
            $("#mptrs_tableReserveBtnHolder").fadeIn();
        }

    });

    function mptrs_display_popup_for_order_types(){

        // console.log( mptrs_orderSettings );
        let setLocations = '';
        if( mptrs_orderSettings.hasOwnProperty( 'mptrs_locations' ) && mptrs_orderSettings.mptrs_locations ){
            setLocations = mptrs_orderSettings.mptrs_locations;
        }

        let orderTypes = `
            <div class="mptrs_popupOverlay" id="mptrs_popupOverlay">
                <div class="mptrs_popupBox">
                <span class="mptrs_closeBtn"><i class="fas fa-times"></i></span>
                <div class="mptrs_popupTitle">Your Order Settings</div>
       
                <div class="mptrs_toggleBtns">
                    <button id="mptrs_dine_in" class="mptrs_orderTypeSelect active">Delivery</button>
                    <button id="mptrs_take_away" class="mptrs_orderTypeSelect">Takeaway</button>
                    <button id="mptrs_dineInBtn" class="mptrs_orderTypeSelect">Dine-In</button>
                </div>
        
                
                <div class="mptrs_DatePickerContainer">
                    <label for="mptrs_dateDatepicker">Select pickup order date</label>
                    <input type="text" id="mptrs_dateDatepicker" class="mptrs_datepicker_input" placeholder="Select a Date">
                    <span class="mptrs_calendarIcon">&#128197;</span>
                </div>
        
                <label for="mptrs_pickupTime">Select a pickup time</label>
                <select id="mptrs_pickupTime" class="mptrs_dropdown">
                    <option>11:30am</option>
                    <option>12:00pm</option>
                    <option>12:30pm</option>
                </select>
                
                <div class="mptrs_OrderTypeLocationsContainer" style="display: block">
                    <label for="mptrs_deliveryLocation">Set Delivery Location</label>
                    <input type="text" id="mptrs_deliveryLocation" class="mptrs_deliveryLocations" value="${setLocations}" placeholder="Set Delivery Location">
                </div>
        
                <button class="mptrs_updateBtn" id="mptrs_updateorderTypeBtn">Update</button>
            </div>
        </div>`
        $('body').append( orderTypes );
        let today = $.datepicker.formatDate("dd MM yy", new Date());

        $(".mptrs_datepicker_input").datepicker({

            dateFormat: "dd MM yy",  // Formats as "10 March 2025"
            changeMonth: true,
            changeYear: true,
            minDate: 0,
            maxDate: "+1Y",
        }).val( today );
    }
    $(document).on("focus", ".mptrs_datepicker_input", function () {
        $(this).datepicker("show");
    });

    $(document).on("click", ".mptrs_calendarIcon", function () {
        $(".mptrs_datepicker_input").datepicker("show");
    });


    let mptrs_orderSettings = {};
    $(document).on( 'click',"#mptrs_updateorderTypeBtn", function (e) {
        e.preventDefault();

        let mptrs_locations = '';
        let mptrs_orderType = $(".mptrs_orderTypeSelect.active").text().trim();

        if( mptrs_orderType === 'Delivery'){
            mptrs_locations = $("#mptrs_deliveryLocation").val().trim();
        }

        let mptrs_orderDate = $("#mptrs_dateDatepicker").val().trim();
        let mptrs_orderTime = $("#mptrs_pickupTime").val().trim();
        mptrs_orderSettings = {
            mptrs_orderType, mptrs_orderDate, mptrs_orderTime, mptrs_locations
        }

        let mptrs_order_des = '';
        if( mptrs_locations ){
            mptrs_order_des = mptrs_orderType+', Order Date: '+mptrs_orderDate+', Time:'+mptrs_orderTime+', Location:'+mptrs_locations;
        }else{
            mptrs_order_des = mptrs_orderType+', Order Date: '+mptrs_orderDate+', Time:'+mptrs_orderTime;
        }
        $("#mptrs_orderTypeDates").text( mptrs_order_des );

        mptrs_close_order_type_popup();

    });

    $(document).on( 'click', '.mptrs_clearOrder', function ( e ) {
        e.preventDefault();
        $("#mptrs_orderedFoodMenuInfoHolder").fadeOut();
        $("#mptrs_foodMenuAddedCart").fadeIn();
        $("#mptrs_orderedFoodMenuHolder").empty();

        addToCartData = {};
        $('.mptrs_foodMenuContaine').find('.mptrs_addedQuantityControls').fadeOut();
        $('.mptrs_foodMenuContaine').find('.mptrs_addBtn').fadeIn(1000);

    })

    $(document).on( 'click', '.mptrs_orderTypeDatesChange', function (e) {
        e.preventDefault();
        mptrs_display_popup_for_order_types();
    })

    function mptrs_close_order_type_popup(){

        $("#mptrs_popupOverlay").fadeOut();
        $("#mptrs_popupOverlay").remove();
        $("#mptrs_popupOverlay").empty();
        $("#mptrs_addToCartPopupHolder").fadeIn();
    }

    $(document).on( 'click',".mptrs_closeBtn", function () {
        mptrs_close_order_type_popup();
    });

    $(document).on( 'click',".mptrs_toggleBtns button", function () {
        $(".mptrs_toggleBtns button").removeClass("active");
        let orderTypeText = $(this).text().trim();
        if( orderTypeText === 'Delivery' ){
            $(".mptrs_OrderTypeLocationsContainer").fadeIn();
        }else{
            $(".mptrs_OrderTypeLocationsContainer").fadeOut();
        }
        $(this).addClass("active");

    });

    function mptrs_display_add_cart_item_data( menuAddedKey, mptrs_MenuPrice, mptrs_CurrencySymbol, menuPrice, display, animationDiv, parentItem, mptrs_this ){
        // let mptrs_CurrencySymbol = jQuery('.woocommerce-Price-currencySymbol:first').text().trim();

        let menuItem = selectedMenu[menuAddedKey];
        let addOneVariation = '';

        let menuDescription = '';
        if (menuItem && menuItem.hasOwnProperty('menuDescription')) {
            menuDescription = menuItem.menuDescription;
        }

        if (menuItem.hasOwnProperty('variations') && menuItem.variations.length > 0) {
            let menuHtml = ` <div class="mptrs_addToCartPopupHolder" id="mptrs_addToCartPopupHolder" style="display: ${display}">
            <div class="mptrs_popupContainer" id="mptrs_popupContainer">
                <div class="mptrs_popupHeader">
                    <span class="mptrs_addCartmenuName">${menuItem.menuName}</span>
                    <span class="mptrs_addCartmenuPrice" id="mptrs_addCartmenuPrice">${mptrs_CurrencySymbol}${mptrs_MenuPrice}</span>
                    <span class="mptrs_popupClose">&times;</span>
                </div>
                <p class="mptrs_menuDescription">${menuDescription}</p>
            `;

            menuHtml += `<div class="mptrs_optionGroupHolder">`;
            if (menuItem && menuItem.hasOwnProperty('variations')) {
                menuItem.variations.forEach(variation => {
                    if (variation && variation.hasOwnProperty('variationOrAddOne')) {
                        addOneVariation = variation.variationOrAddOne;
                    }
                    menuHtml += `<div class="mptrs_optionGroup">`;
                    menuHtml += `<span class="mptrs_variationName">${variation.category}</span>`;

                    if (addOneVariation === 'variations') {
                        menuHtml += `
                            <div class="mptrs_optionItem">
                                <div class="mptrs_nameAcrionHolder">
                                    <input class="mptrs_variationInput" id="mptrs_variationInput" type="radio" name="variations" checked>
                                    <label for="mptrs_variationInput">Regular</label>
                                </div>
                                <span class="mptrs_price">${mptrs_CurrencySymbol}${mptrs_MenuPrice} </span>
                            </div>
                        `
                    }

                    variation.items.forEach(item => {
                        let increaseDecrease = '';
                        let inputType = variation.radioOrCheckbox === "single" ? "radio" : "checkbox";
                        let inputName = variation.radioOrCheckbox === "single" ? variation.category : `${variation.category}[]`;
                        if (addOneVariation === 'variations') {
                            increaseDecrease = '';
                        } else {
                            increaseDecrease = `
                                        <div class="mptrs_quantityControls" style="display: none">
                                            <button class="mptrs_addDecrease">-</button>
                                            <input type="text" value="1">
                                            <button class="mptrs_addIncrease">+</button>
                                        </div>`;
                        }
                        menuHtml += `
                    <div class="mptrs_optionItem">
                        <div class="mptrs_nameAcrionHolder">
                            <input class="mptrs_variationInput" id="mptrs_variationInput" type="${inputType}" name="${addOneVariation}">
                            <label for="mptrs_variationInput">${item.name}</label>           
                        </div>
                        ${increaseDecrease}
                        <span class="mptrs_price">${mptrs_CurrencySymbol}${item.price} </span>
                    </div>`;
                    });

                    menuHtml += `</div>`;
                });
            }
            menuHtml += `</div>`;
            menuHtml += ` 
                    <div class="mptrs_addToCart">
                        <div class="mptrs_addToCartTitle">Total: 
                            <span class="mptrs_addToCartText">${mptrs_CurrencySymbol}${mptrs_MenuPrice}</span>
                        </div>
                        <button class="mptrs_foodMenuAddedCart" id="${menuAddedKey}">Add to Cart</button>
                    </div>`;
            menuHtml += `</div> </div>`;
            $('body').append(menuHtml);
        }
        else {
            mptrs_this.fadeOut();
            let addedMenu = `
                    <div class="mptrs_addedQuantityControls" id="mptrs_addedQuantityControls-${menuAddedKey}">
                        <button class="mptrs_decrease">−</button>
                        <span class="mptrs_quantity" id="mptrs_menuAddedQuantity-${menuAddedKey}">1</span>
                        <button class="mptrs_increase">+</button>
                    </div>
                `;
            mptrs_this.parent().append(addedMenu);


            let foodMenuCategory = parentItem.attr('data-menuCategory');
            let menuImgUrl = parentItem.attr('data-menuImgUrl');
            let menuName = parentItem.attr('data-menuName');
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
                mptrs_oderDetails: '',
            };
            addToCartData[menuAddedKey] = 1;
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
        }
    }

    let selectedMenu = mptrs_food_menu.find(item => item);
    let menuAddedClickedId = '';
    let mptrs_displayCartPopUp = 0;
    $(document).on('click', ".mptrs_addBtn", function () {

        let mptrs_this = $(this);

        let animationDiv = $(this).parent().parent();
        let parentItem = $(this).parent();

        let mptrs_CurrencySymbol = jQuery('.woocommerce-Price-currencySymbol:first').text().trim();
        let mptrs_MenuPrice = $(this).parent().attr('data-menuprice').trim();
        mptrs_MenuPrice = parseFloat(mptrs_MenuPrice.replace(/[^\d.]/g, ''));

        let menuPrice = $(this).parent().attr('data-menuprice').trim();
        menuPrice = parseFloat(menuPrice.replace(/[^0-9.]/g, ''));

        menuAddedClickedId = $(this).attr('id').trim();
        let menuAddedKeys = menuAddedClickedId.split('-');
        let menuAddedKey = menuAddedKeys[1];

        if ( Object.keys(mptrs_orderSettings).length === 0 ) {
            mptrs_display_popup_for_order_types();
            mptrs_displayCartPopUp++;
            mptrs_display_add_cart_item_data( menuAddedKey, mptrs_MenuPrice, mptrs_CurrencySymbol, menuPrice,'none', animationDiv, parentItem, mptrs_this );
        }

        if( Object.keys(mptrs_orderSettings).length > 0 ) {
            mptrs_display_add_cart_item_data( menuAddedKey, mptrs_MenuPrice, mptrs_CurrencySymbol, menuPrice, 'flex', animationDiv, parentItem, mptrs_this );
        }

    });

    function mptrs_remove_single_menu_add_cart_popup(){
        $("#mptrs_addToCartPopupHolder").fadeOut();
        $("#mptrs_addToCartPopupHolder").remove();
        $("#mptrs_popupContainer").fadeOut();
        $("#mptrs_popupContainer").remove();
    }
    $(document).on('click',".mptrs_popupClose",function () {
        mptrs_remove_single_menu_add_cart_popup();
    });


    $(document).on('click', ".mptrs_addIncrease, .mptrs_addDecrease", function () {
        let input = $(this).siblings("input");
        let quantity = parseInt(input.val());

        if ($(this).hasClass("mptrs_addIncrease")) {
            quantity++;
        } else if (quantity > 1) {
            quantity--;
        }

        input.val(quantity);
        mptrs_updateVariationTotalPrice('increase');
    });

    $(document).on('change', "input[type=checkbox]", function () {
        if ( $(this).is(":checked") ) {
            $(this).parent().siblings('.mptrs_quantityControls').fadeIn();
        } else {
            $(this).parent().siblings('.mptrs_quantityControls').fadeOut();
        }
        mptrs_updateVariationTotalPrice("checkbox");
    });

    $(document).on('change', "input[type=radio]", function () {
        $("input[type=radio][name='" + $(this).attr("name") + "']")
            .parent().siblings('.mptrs_quantityControls').fadeOut();

        // Show only for the selected radio
        $(this).parent().siblings('.mptrs_quantityControls').fadeIn();
        mptrs_updateVariationTotalPrice("radio");
    });

    function mptrs_updateVariationTotalPrice(type) {
        let mptrs_CurrencySymbol = jQuery('.woocommerce-Price-currencySymbol:first').text().trim();

        let total = 0;
        let radioSelected = false;
        let is_variations = false;
        let radioPrice = 0;
        let currentTotal = $("#mptrs_addCartmenuPrice").text() || 0;
        currentTotal = parseFloat(currentTotal.replace(/[^0-9.]/g, ''));
        let radioName = '';

        $("input[type=radio]:checked").each(function () {
            let quantity = 0;
            let parent = $(this).closest(".mptrs_optionItem");
            radioName = parent.find("input[type=radio]").attr("name");
            if( radioName === 'variations' ){
                quantity = 1
                let price = parent.find(".mptrs_price").text().trim();
                price = parseFloat(price.replace(/[^0-9.]/g, ''));
                radioPrice += price * quantity;
                radioSelected = 'var';
                is_variations = true;
            }else{
                quantity = parseInt(parent.find("input[type=text]").val());
                let price = parent.find(".mptrs_price").text().trim();
                price = parseFloat(price.replace(/[^0-9.]/g, ''));
                radioPrice += price * quantity;
                radioSelected = 'not_var';
            }


        });

        let checkboxTotal = 0;
        $("input[type=checkbox]:checked").each(function () {
            let parent = $(this).closest(".mptrs_optionItem");
            let quantity = parseInt(parent.find("input[type=text]").val());
            let price = parent.find(".mptrs_price").text().trim();
            price = parseFloat(price.replace(/[^0-9.]/g, ''));

            checkboxTotal += price * quantity;
        });

        if ( radioSelected === 'var' ) {
            total = radioPrice + checkboxTotal;
        }else if( radioSelected === 'not_var' ){
            if( is_variations ){
                total = radioPrice + checkboxTotal;
            }else{
                total = radioPrice + checkboxTotal+currentTotal;
            }

        } else {
            total = currentTotal + checkboxTotal;
        }
        $(".mptrs_addToCart span").text( mptrs_CurrencySymbol + total.toFixed(2));
    }



    $(document).on('click', ".mptrs_foodMenuAddedCart", function () {

        let mptrs_oderDetails = '';
        $("input[type=radio]:checked").each(function () {
            let quantity = 0;
            let parent = $(this).closest(".mptrs_optionItem");
            // let quantity = parseInt(parent.find("input[type=text]").val());
            let radioName = parent.find("input[type=radio]").attr("name");
            if( radioName === 'variations' ){
                quantity = 1
            }else{
                quantity = parseInt(parent.find("input[type=text]").val());
            }
            let price = parent.find(".mptrs_price").text().trim();
            price = parseFloat(price.replace(/[^0-9.]/g, ''));
            let labelText = parent.find("label").text().trim();
            mptrs_oderDetails += labelText+'('+quantity+'), ';
        });

        // Sum all checked checkbox prices

        $("input[type=checkbox]:checked").each(function () {
            let parent = $(this).closest(".mptrs_optionItem");
            let quantity = parseInt(parent.find("input[type=text]").val());
            let price = parent.find(".mptrs_price").text().trim();
            price = parseFloat(price.replace(/[^0-9.]/g, ''));
            let labelText = parent.find("label").text().trim();
            mptrs_oderDetails += labelText+'('+quantity+'), ';

        });
        let addCartPrice =  $(".mptrs_addToCart span").text();
        addCartPrice = parseFloat( addCartPrice.replace(/[^0-9.]/g, '' ) );

        $("#mptrs_orderedFoodMenuInfoHolder").fadeIn();
        $("#mptrs_foodMenuAddedCart").fadeOut();
        $("#mptrs_dineInTabHolder").fadeIn();

        let menuAddedKey = $(this).attr('id').trim();
        let addedMenu = `
            <div class="mptrs_addedQuantityControls" id="mptrs_addedQuantityControls-${menuAddedKey}">
                <button class="mptrs_decrease">−</button>
                <span class="mptrs_quantity" id="mptrs_menuAddedQuantity-${menuAddedKey}">1</span>
                <button class="mptrs_increase">+</button>
            </div>
        `;
        $("#"+menuAddedClickedId).parent().append( addedMenu );

        let animationDiv =  $("#"+menuAddedClickedId).parent().parent();
        let parentItem = $("#"+menuAddedClickedId).parent();
        let foodMenuCategory = parentItem.attr('data-menuCategory');
        let menuImgUrl = parentItem.attr('data-menuImgUrl');
        let menuName = parentItem.attr('data-menuName');
        let menuPrice = addCartPrice;
        let numOfPerson = parentItem.attr('data-numOfPerson');
        let mptrs_CurrencySymbol = jQuery('.woocommerce-Price-currencySymbol:first').text().trim();

        mptrs_remove_single_menu_add_cart_popup();

        let item = {
            menuImgUrl: menuImgUrl,
            menuName: menuName,
            menuPrice: menuPrice,
            mptrs_CurrencySymbol: mptrs_CurrencySymbol,
            numOfPerson: numOfPerson,
            foodMenuCategory: foodMenuCategory,
            menuAddedKey: menuAddedKey,
            mptrs_oderDetails: mptrs_oderDetails,
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

        $("#"+menuAddedClickedId).fadeOut();

    });

    /*$("#mptrs_seatReserveDate").datepicker({
        dateFormat: "yy-mm-dd",
        minDate: 0
    });*/

    var disableReservedDates = ["2025-04-18", "2025-04-20", "2025-04-25"]; // your disabled dates

    $("#mptrs_seatReserveDate").datepicker({
        dateFormat: "yy-mm-dd",
        minDate: 0,
        beforeShowDay: function(date) {
            var day = date.getDay();
            var formattedDate = $.datepicker.formatDate("yy-mm-dd", date);

            // Disable weekends (Saturday=6, Sunday=0) and specific dates
            if (day === 0 || day === 6 || disableReservedDates.includes(formattedDate)) {
                return [false, "", "Unavailable"];
            }
            return [true, ""];
        }
    });

    $("#mptrs_reservation_form").submit(function(event) {
            event.preventDefault();

            let formData = {
                action: "mptrs_process_reservation",
                mptrs_nonce: $("#mptrs_nonce").val(),
                occasion: $("#mptrs_occasion").val(),
                guests: $("#mptrs_guests").val(),
                date: $("#mptrs_date").val(),
            };

            $.post("<?php echo admin_url('admin-ajax.php'); ?>", formData, function(response) {
                $("#mptrs_message").html(response);
            });
        });


});

// @author: shahadat hossain
(function($){
    $(document).on('click', '[data-popup-target]', function (e) {
        e.preventDefault();
        let target = $(this).data('popup-target');
        $(target).fadeIn(200);
    });
    $(document).on('click', '[data-popup-close]', function (e) {
        e.preventDefault();
        $(this).closest('.popup').fadeOut(200);
    });
    
})(jQuery);
   
