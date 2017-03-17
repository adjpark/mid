<?php
require_once('./lib/PHPTAL-1.3.0/PHPTAL.php');
require_once('./class/Database.php');
require_once('./class/AdminDesigner.php');
require_once('./class/Designer.php');
require_once('./class/Cart.php');


try{    
    $designerRow = $designerAdmin->listDesigners();
    $designerObjectArr = array();
    
    foreach ($designerRow as $row){
        $designerW = json_decode($row["designer_work"]);
        $designerWArr = array();
        
        for($i = 0; $i < count($designerW); $i++){
            array_push($designerWArr, $designerW[$i]);
        }
        
        array_push($designerObjectArr, new Designer(
                $row["id"], 
                $row["designer_Fname"], 
                $row["designer_Lname"],
                $row["designer_bio"], 
                $row["designer_hours"], 
                $row["designer_price"],
                $row["designer_pic"], 
                $designerWArr
        ));
    }

    
    //-------------------PHPTAL--------------
    $template = new PHPTAL('home.xhtml');
    $template->page_title = "My Interior Designer - Home";
    $template->designers= $designerObjectArr;
    echo $template->execute();   
}
catch(Exception $e){
    echo $e;
}
?>
