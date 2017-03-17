<?php
require_once('./lib/PHPTAL-1.3.0/PHPTAL.php');
require_once('./class/Admin.php');

try {
    //Login status
    if($admin->loggedin() != ""){
        $admin->redirect('admin_profile.php');
    }
    
    //Login form
    if (isset($_POST['btn-login'])) {
        $email        = trim($_POST['adminE']);
        $password     = trim($_POST['adminP']);
        
        if($admin->login($email,$password))
         {
          $admin->redirect('admin_profile.php');
         }
         else
         {
          $error = "Wrong login information";
         } 
    }
    
    //Register form
    if (isset($_POST['btn-register'])) {
        $fName    = trim($_POST['registerFNAME']);
        $lName    = trim($_POST['registerLNAME']);
        $email    = trim($_POST['registerEMAIL']);
        $password = trim($_POST['registerPASS']);
     
        $query = $conn->prepare("SELECT adminEmail FROM admins WHERE adminEmail=:adminEmail");
         $query->bindParam(':adminEmail', $email);
         $row=$query->fetch(PDO::FETCH_ASSOC);
    
         if($row['adminEmail']==$email) {
            $error = "sorry username already taken !";
         }
         else
         {
            if($admin->register($fName,$lName,$email,$password)) 
            {
                $admin->redirect('index.php');
            }
         }
    }
    
    //-------------------PHPTAL--------------
    $template = new PHPTAL('index.xhtml');
    $template->page_title = "My Interior Designer";
    echo $template->execute();
}
catch (Exception $e) {
    echo $e;
}
?>