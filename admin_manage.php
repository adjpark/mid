<?php
    require_once('./lib/PHPTAL-1.3.0/PHPTAL.php');
    require_once('./class/Admin.php');

    try {
        if($admin->loggedin() == ""){
            $admin->redirect('index.php');
        }
        $adminRow = $admin->information($_SESSION['admin']);
        
        //-------------------PHPTAL--------------
        $template = new PHPTAL('admin_manage.xhtml');
        $template->page_title = "My Interior Designer - Admin Manage";
        $template -> adminInfo = $adminRow;
        echo $template->execute();
    }
    catch (Exception $e){
        echo $e;
    }
?>
