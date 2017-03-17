<?php
    require_once('./lib/PHPTAL-1.3.0/PHPTAL.php');
    require_once('./class/Admin.php');

    try {
        if($admin->loggedin() == ""){
            $admin->redirect('index.php');
        }
   
        $adminRow = $admin->information($_SESSION['admin']);
    
        if(isset($_POST["btn-update"])){
            $newFname    = trim($_POST['newFname']);
            $newLname    = trim($_POST['newLname']);
            $newEmail    = trim($_POST['newEmail']);
            $newPass     = trim($_POST['newPass']);
            $confirmPass = trim($_POST['confirmPass']);
            $oldPass     = trim($_POST['oldPass']);
            $salt     = "SIjiw9123snw";
            
            if($newPass != $confirmPass){
                $error = "new password does not match";
            }
            elseif(md5($oldPass . $salt) != $adminRow["adminPassword"]){
                $error = "type a correct old password";
            }else{
                $admin->update($newFname,$newLname,$newEmail,$newPass,$confirmPass,$oldPass,$adminRow);
                $admin->redirect('admin_profile.php');
            }
        }
        
        //-------------------PHPTAL--------------
        $template = new PHPTAL('admin_profile.xhtml');
        $template -> adminInfo = $adminRow;
        $template->page_title = "My Interior Designer - Admin Profile";
        echo $template->execute();
    }
    catch (Exception $e){
        echo $e;
    }
?>