<?php
    require_once('./class/Admin.php');
    require_once('./class/AdminDesigner.php');

    try{
        if($admin->loggedin() == ""){
            $admin->redirect('index.php');
        }
        
        $adminRow = $admin->information($_SESSION['admin']);
         
        if($_POST["submitType"] == "add_designer"){
            echo $designerAdmin->add($_POST,$_FILES, $adminRow); 
        }
        if($_POST["submitType"] == "delete_designer"){
            echo $designerAdmin->delete($_POST);
        }
        if($_POST["submitType"] == "edit_designer"){
            echo $designerAdmin->edit($_POST);
        }
        if($_POST["submitType"] == "reset_hour"){
            echo $designerAdmin->resetHour($_POST);
        }
        if($_POST["submitType"] == "edit_designer_submit"){
            echo $designerAdmin->editSubmit($_POST,$_FILES, $adminRow);
        }
        if($_POST["submitType"] == "load_info"){
            echo $designerAdmin->show($adminRow);
        }
  
    }
    catch (Exception $e){
        echo $e;
    }
    
?>