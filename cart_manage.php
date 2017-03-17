<?php
require_once('./class/Database.php');
require_once('./class/Cart.php');

    try{
        if($_POST["methodType"] == "cart-update") {
            echo $cart->updateProduct($_POST["data"]); 
        }
        if($_POST["methodType"] == "cart-create") {
            echo $cart->createNewCart();  
        }
        if($_POST["methodType"] == "cart-add") {
            echo $cart->addProduct($_POST["data"]);  
        }
        if($_POST["methodType"] == "cart-reset") {
            echo $cart->destoryCart($_POST["cartID"]);  
        }
        if($_POST["methodType"] == "cart-delete") {
            echo $cart->removeProduct($_POST["cartItem"]); 
        }
        if($_POST["methodType"] == "cart-load") {
            echo $cart->loadCart($_POST["cartID"]);  
        }
        if($_POST["methodType"] == "cart-totalprice") {
            echo $cart->getTotalPrice();  
        }
        if($_POST["methodType"] == "cart-checkout") {
            echo $cart->checkOut($_POST["cartID"]);  
        }
    }
    catch (Exception $e){
        echo $e;
    }
?>