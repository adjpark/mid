$(document).ready(function () {
    /*------------------Nav bar active------------------*/
    $("#navHome").addClass("active");

    /*------------------Data for Popup------------------*/
    var i = 0;
    $('.popupAnchor').each(function () {
        i++;
        var popupOpen = 'popup-' + i;
        $(this).attr('data-popup-open', popupOpen);
    });

    i = 0;
    $('.popup').each(function () {
        i++;
        var popup = 'popup-' + i;
        $(this).attr('data-popup', popup);
    });

    i = 0;
    $('.popup-close').each(function () {
        i++;
        var popupClose = 'popup-' + i;
        $(this).attr('data-popup-close', popupClose);
    });

    i = 0;
    $('.popup-close-text').each(function () {
        i++;
        var popupClose = 'popup-' + i;
        $(this).attr('data-popup-close', popupClose);
    });
    /*------------------End Data for Popup------------------*/

    /*------------------Popup click functions------------------*/
    $('[data-popup-open]').on('click', function (e) {
        var targeted_popup_class = jQuery(this).attr('data-popup-open');
        $('[data-popup="' + targeted_popup_class + '"]').fadeIn(350);

        e.preventDefault();
    });

    $('[data-popup-close]').on('click', function (e) {
        var targeted_popup_class = jQuery(this).attr('data-popup-close');
        $('[data-popup="' + targeted_popup_class + '"]').fadeOut(350);
        $(".cartNotification").html("");
        e.preventDefault();
    });
    /*------------------End Popup click functions------------------*/

    
    /*------------------Create local storage if it doesnt exists------------------*/
    if(localStorage.getItem("CartID") === null){
        $.ajax({
            type: "POST",
            url: "cart_manage.php",
            data: {
                methodType: "cart-create"
            },
            error: function (request) {
                console.log(request.responseText);
            },
            success: function (resp) {
                localStorage.setItem("CartID", resp);
            }
        });
    }
    /*------------------End Cart local storage------------------*/
    
    
    /*------------------Add to cart------------------*/
    $('[data-designer-id]').on('click', function (e) {
        $.ajax({
            type: "POST",
            url: "cart_manage.php",
            data: {
                methodType: "cart-add",
                data : {
                    designerID: $(this).data('designer-id'),
                    cartID: localStorage.getItem("CartID")
                }
            },
            error: function (request) {
                console.log(request.responseText);
            },
            success: function (resp) {
               $(".cartNotification").html(resp);
            }
        });
    });
    /*------------------End Add to cart------------------*/
});