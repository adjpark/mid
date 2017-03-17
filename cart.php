<?php
require_once('./lib/PHPTAL-1.3.0/PHPTAL.php');
require_once('./class/Database.php');
require_once('./class/AdminDesigner.php');
require_once('./class/Designer.php');
require_once('./class/Cart.php');
    
try {
    //-------------------PHPTAL--------------
    $template = new PHPTAL('cart.xhtml');
    $template->page_title = "My Interior Designer - Cart";
    echo $template->execute(); 
}
catch (Exception $e){
    echo $e;
}
?>