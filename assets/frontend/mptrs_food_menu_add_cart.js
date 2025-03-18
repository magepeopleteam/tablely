jQuery(document).ready(function($) {

    let menuKey = "b6b3d81c6f0d"; // Example key
    let selectedMenu = mptrs_food_menu.find(item => item[menuKey]);


    $(document).on('click', ".mptrs_addBtn", function () {
        let menuAddedClickedId = $(this).attr('id').trim();
        let menuAddedKeys = menuAddedClickedId.split('-');
        let menuAddedKey = menuAddedKeys[1];
        // alert( menuAddedKey );
        let menuItem = selectedMenu[menuAddedKey];

        let menuHtml = `
    <div class="mptrs_menuContainer">
        <div class="mptrs_menuImageWrapper">
            <img class="mptrs_menuImage" src="${menuItem.menuImgUrl}" alt="${menuItem.menuName}">
        </div>
        <div class="mptrs_menuContent">
            <h2 class="mptrs_menuTitle">${menuItem.menuName}</h2>
            <p class="mptrs_menuCategory">Category: ${menuItem.menuCategory}</p>
            <p class="mptrs_menuPrice">Price: $${menuItem.menuPrice}</p>
            <p class="mptrs_menuDescription">${menuItem.menuDescription}</p>
        </div>
    </div>`;

        menuItem.variations.forEach(variation => {
            menuHtml += `<div class="mptrs_variationCategory">${variation.category}</div>`;
            menuHtml += `<div class="mptrs_variationOptions">`;

            variation.items.forEach(item => {
                let inputType = variation.radioOrCheckbox === "single" ? "radio" : "checkbox";
                let inputName = variation.radioOrCheckbox === "single" ? variation.category : `${variation.category}[]`;
                menuHtml += `
            <label class="mptrs_variationLabel">
                <input class="mptrs_variationInput" type="${inputType}" name="${inputName}" value="${item.name}">
                <span class="mptrs_variationText">${item.name} (+$${item.price})</span>
            </label>`;
            });

            menuHtml += `</div>`;
        });


        let aa = `<div id="pa-food-menu-popup" class="pa-popup">
                <div class="pa-popup-content">
                    <span id="pa-close-menu" class="pa-close">&times;</span>
                    <h2>Food Menu</h2>
                    ${menuHtml}
                </div>
            </div>`

        $('body').append(aa);
    });

    $(document).on( 'click', ".pa-close",function() {
        $("#pa-food-menu-popup").fadeOut();
        $("#pa-food-menu-popup").empty();
        $("#pa-food-menu-popup").remove();
    });

});