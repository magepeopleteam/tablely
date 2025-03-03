jQuery(document).ready(function ($) {

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
    mptrs_make_div_fixed( 'mptrs_orderCardHolder' );



    function updateTotalPrice() {
        let totalPrice = 0;

        seatBooked.forEach(function (seat) {
            totalPrice += seat['price']; // Sum up prices
        });

        $("#mptrs_totalPrice").text( totalPrice );
    }

    let seatBooked = [];
    $(document).on( 'click', '.mptrs_mappedSeat', function () {
        const seatId = $(this).attr('id');
        const price = $(this).data('price');
        const seatNum = $(this).data('seat-num');
        let todayDate = new Date().toISOString().split('T')[0];
        const time = 10;
        seatBooked.push(seatId);
        if( seatBooked.length > 0 ){
            $("#mptrs_selectedSeatInfoHolder").show();
        }else{
            $("#mptrs_selectedSeatInfoHolder").hide();
        }

        updateTotalPrice();

        $(this).css('background-color', '#cacd1e');

        let disabledDates = ["2025-02-20", "2025-02-25", "2025-02-26"];
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
        $("#mptrs_selectedSeatInfo").append( selectedSeat );
    });


    let disabledDates = ["2025-03-20", "2025-03-25", "2025-02-26"];
    mptrs_datePicker( disabledDates );
    $(document).on('click',".mptrs_OrderPlaceBtn",function () {
        let orderPostClickedId = $(this).attr('id').trim();
        let orderPostId = orderPostClickedId.split('-');
        orderPostId = orderPostId[1];
        let order_time = $('.mptrs_time_button.active').data('time');
        let order_date = $("#mptrs_date").val().trim();

        let bookedSeats =  JSON.stringify( seatBooked );
        // console.log( order_time );

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
                alert( response.data.message);
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', status, error);
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
        console.log( seatBooked );

    });
    $(document).on( 'click', '.mptrs_orderBtn', function ( e ) {
        e.preventDefault();
        let orderClickedId = $(this).attr('id');
        // orderClickedId = orderClickedId.slice('-');
        let idParts = orderClickedId.split('-');
        let postId = idParts[1];
        // alert(postId);
        console.log( seatBooked );
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
        console.log( time );
        let button = $("<button>")
            .addClass("mptrs_time_button")
            .text(time)
            .attr("data-time", key );

        timeContainer.append(button);
    })

    $(document).on( 'click', '.mptrs_button', function () {
        $(".mptrs_time_container").css("display", "flex");

    });


    $(document).on('click',".close-btn",function () {
        $("#seatPopup").fadeOut();
        $("#mptrs_seatMapDisplay").empty();
    });

    // Handle time selection
    $(".mptrs_time_button").on("click", function () {
        $(".mptrs_time_button").removeClass("active");
        $(this).addClass("active");
        $("#seatPopup").fadeIn();
        let get_postId =  $("#mptrs_getPost").val().trim();
        let get_time = $(this).data('time');
        let get_date = $("#mptrs_date").val().trim();
        if( get_time === '' ){
            alert('Select Date First!');
        }else{
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
                    $("#mptrs_seatMapDisplay").append( response.data.mptrs_seat_maps );
                },
                error: function () {
                    alert( 'Error occurred');
                }
            });
        }


    });

});
