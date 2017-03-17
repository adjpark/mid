$(document).ready(function () {
    $("#navCart").addClass("active");

    if (localStorage.getItem("CartID") === null) {
        console.log("No active cart");
    } else {
        $.ajax({
            type: "POST",
            url: "cart_manage.php",
            dataType: "html",
            data: {
                methodType: "cart-load",
                cartID: localStorage.getItem("CartID")
            },
            error: function (request) {
                console.log(request.responseText);
            },
            success: function (resp) {
                $("tbody").append(resp);

                $.ajax({
                    type: "POST",
                    url: "cart_manage.php",
                    data: {
                        methodType: "cart-totalprice"
                    },
                    error: function (request) {
                        console.log(request.responseText);
                    },
                    success: function (resp) {
                        $("#totalPrice").html("Total $" + resp + ".00");
                    }
                });
            }
        });
    }

    $("tbody").on("click", ".cart-delete", function () {
        $.ajax({
            type: "POST",
            url: "cart_manage.php",
            data: {
                methodType: "cart-delete",
                cartItem: $(this).data("delete")
            },
            error: function (request) {
                console.log(request.responseText);
            },
            success: function (resp) {
                $("tr[data-cartitem=" + resp + "]").remove();
            }
        });
    });

    $("tbody").on("click", ".cart-update", function () {
        var newQuantity = $("input[data-quantity='" + $(this).data("update") + "']");
        $.ajax({
            type: "POST",
            url: "cart_manage.php",
            data: {
                methodType: "cart-update",
                data: {
                    cartItem: $(this).data("update"),
                    newQuantity: newQuantity.val(),
                }
            },
            error: function (request) {
                console.log(request.responseText);
            },
            success: function (resp) {
                $.ajax({
                    type: "POST",
                    url: "cart_manage.php",
                    dataType: "html",
                    data: {
                        methodType: "cart-load",
                        cartID: localStorage.getItem("CartID")
                    },
                    error: function (request) {
                        console.log(request.responseText);
                    },
                    success: function (resp) {
                        $("tbody").html("");
                        $("tbody").append(resp);

                        $.ajax({
                            type: "POST",
                            url: "cart_manage.php",
                            data: {
                                methodType: "cart-totalprice"
                            },
                            error: function (request) {
                                console.log(request.responseText);
                            },
                            success: function (resp) {
                                $("#totalPrice").html("Total $" + resp + ".00");
                            }
                        });
                    }
                });
            }
        });
    });

    $('#cart-checkout').on('click', function (e) {
        $.ajax({
            type: "POST",
            url: "cart_manage.php",
            dataType: "json",
            data: {
                methodType: "cart-checkout",
                cartID: localStorage.getItem("CartID")
            },
            error: function (request) {
                console.log(request.responseText);
            },
            success: function (resp) {
                $("#cart-checkout-msg").html("");
                
                if(resp.finalStatus == true){
                    for(var i =0; i < Object.keys(resp).length-1; i++){
                        $("#cart-checkout-msg").append("<p>"+resp[i]+"</p>")
                    }
                    $("#cart-checkout-status").html("Your designers have been checked out and booked. Thank you for using My Interior Designer.")
                    $("tbody").html("");
                    localStorage.removeItem('CartID');
                }else if(resp.finalStatus == false){
                    for(var i =0; i < Object.keys(resp).length-1; i++){
                        $("#cart-checkout-msg").append("<p>"+resp[i]+"</p>")
                    }
                    $("#cart-checkout-status").html("Your transaction has not been approved due to above reasons.")
                }
            }
        });
    });

    $('#cart-reset').on('click', function (e) {
        $.ajax({
            type: "POST",
            url: "cart_manage.php",
            data: {
                methodType: "cart-reset",
                cartID: localStorage.getItem("CartID")
            },
            error: function (request) {
                console.log(request.responseText);
            },
            success: function (resp) {
                localStorage.removeItem('CartID');

            }
        });
    });
});