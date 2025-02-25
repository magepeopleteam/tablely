jQuery(document).ready(function ($) {

    function updateTotalPrice() {
        let totalPrice = 0;

        seatBooked.forEach(function (seat) {
            totalPrice += seat['price']; // Sum up prices
        });

        $("#mptrs_totalPrice").text( totalPrice );
    }

    let seatBooked = [];
    $('.mptrs_mappedSeat').on('click', function () {
        const seatId = $(this).attr('id');
        const price = $(this).data('price');
        const seatNum = $(this).data('seat-num');
        let todayDate = new Date().toISOString().split('T')[0];
        const time = 10;
        seatBooked.push({seatId, todayDate, time, price });
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

    function mptrs_datePicker( disabledDates ) {
        $(".mptrs_DatePickerContainer").fadeIn();
        $("#mptrs-datepicker").datepicker({
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
        $(".mptrs-calendar-icon").click(function () {
            $("#mptrs-datepicker").focus();
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
    function mptrs_load_seat_maps(){
        let postId = 123;

        $.ajax({
            url: mptrs_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'set_your_Action',
                nonce: mptrs_ajax .nonce,
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    let planData = response.data;
                    let planSeats = planData.seat_data || [];
                    let planSeatTexts = planData.seat_text_data || [];
                    let dynamicShapes = planData.dynamic_shapes || [];

                    let leastLeft = Math.min(...planSeats.map(s => parseInt(s.left)));
                    let leastTop = Math.min(...planSeats.map(s => parseInt(s.top)));

                    let seatGrid = $("#mptrs_seatGrid");
                    seatGrid.css("height", leastTop + 200 + "px");


                    dynamicShapes.forEach(shape => {
                        let shapeDiv = `<div class="mptrs_dynamicShape" style="
                        left: ${shape.textLeft - leastLeft}px;
                        top: ${shape.textTop - leastTop}px;
                        width: ${shape.width}px;
                        height: ${shape.height}px;
                        background-color: ${shape.backgroundColor};
                        border-radius: ${shape.borderRadius};
                        clip-path: ${shape.clipPath};
                        transform: rotate(${shape.shapeRotateDeg}deg);
                    "></div>`;
                        seatGrid.append(shapeDiv);
                    });


                    planSeatTexts.forEach(text => {
                        let textDiv = `<div class="mptrs_dynamicTextWrapper" style="
                        left: ${text.textLeft - leastLeft}px;
                        top: ${text.textTop - leastTop}px;
                        transform: rotate(${text.textRotateDeg}deg);
                    ">
                        <span class="mptrs_dynamicText" style="
                            color: ${text.color}; font-size: ${text.fontSize}px; cursor: pointer;">
                            ${text.text}
                        </span>
                    </div>`;
                        seatGrid.append(textDiv);
                    });

                    // 🔹 সিট যোগ করা
                    planSeats.forEach(seat => {
                        let seatDiv = `<div class="mptrs_mappedSeat" id="seat-${seat.id}" 
                        data-price="${seat.price}" data-seat-num="${seat.seat_number}" style="
                        width: ${seat.width}px;
                        height: ${seat.height}px;
                        left: ${seat.left - leastLeft}px;
                        top: ${seat.top - leastTop}px;
                        border-radius: ${seat.border_radius};
                        transform: rotate(${seat.data_degree}deg);
                        background-color: ${seat.color};
                    " title="Price: $${seat.price}">
                        <div class="mptrs_mappedSeatInfo">
                            <span class="mptrs_seatNumber">${seat.seat_number}</span>
                        </div>
                    </div>`;
                        seatGrid.append(seatDiv);
                    });
                }
            }
        });
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

});
