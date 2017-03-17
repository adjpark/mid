<?php
class Admin
{
    private $adminConn;
    
    //Grab connection from Database.php
    function __construct($conn) {
        $this->adminConn = $conn;
    }
    
    //Admin Register
    public function register($fName, $lName, $email, $password)
    {
        try {
            $salt     = "SIjiw9123snw";
            $password = md5($password . $salt);
            $query    = $this->adminConn->prepare("INSERT INTO admins (adminFName, adminLName, adminEmail, adminPassword) VALUES (:adminFName, :adminLName, :adminEmail, :adminPassword)");
            $query->bindParam(':adminFName', $fName);
            $query->bindParam(':adminLName', $lName);
            $query->bindParam(':adminEmail', $email);
            $query->bindParam(':adminPassword', $password);
            $query->execute();
            
            return $query;
        }
        catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    //Admin login
    public function login($email, $password)
    {
        try {
            $salt  = "SIjiw9123snw";
            $query = $this->adminConn->prepare("SELECT * FROM admins WHERE adminEmail=:adminEmail");
            $query->bindParam(':adminEmail', $email);
            $query->execute();
            $adminRow = $query->fetch(PDO::FETCH_ASSOC);
            
            if ($query->rowCount() > 0) {
                if ($adminRow["adminPassword"] == md5($password . $salt)) {
                    $_SESSION['admin'] = $adminRow["id"];
                    return true;
                } else {
                    return false;
                }
            }
        }
        catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    //Admin update
    public function update($newFname, $newLname, $newEmail, $newPass, $confirmPass, $oldPass, $adminRow)
    {
        try {
            $salt    = "SIjiw9123snw";
            $newPass = md5($newPass . $salt);
            $query   = $this->adminConn->prepare("UPDATE admins SET adminFName = :adminFName, adminLName = :adminLName, adminEmail = :adminEmail, adminPassword = :adminPassword WHERE id =:id");
            $query->bindParam(':adminFName', $newFname);
            $query->bindParam(':adminLName', $newLname);
            $query->bindParam(':adminEmail', $newEmail);
            $query->bindParam(':adminPassword', $newPass);
            $query->bindParam(':id', $adminRow["id"]);
            $query->execute();
            return $query;
        }
        catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    //Admin Information
    public function information($session)
    {
        $statement = $this->adminConn->prepare("SELECT * FROM admins WHERE id = ?");
        $statement->execute(array($session));
        $adminInfo = $statement->fetchAll(PDO::FETCH_ASSOC);
        $simplifiedInfo =  $adminInfo[0];
        return $simplifiedInfo;
    }
    
    //Admin login Status
    public function loggedin()
    {
        if (isset($_SESSION['admin'])) {
            return true;
        }
    }
    
    //Admin redirect
    public function redirect($url)
    {
        header("Location: $url");
        exit;
    }
}

require_once('./class/Database.php');
$admin = new Admin($conn);
?>