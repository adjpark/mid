<?php
class DesignerAdmin
{
    private $designerConn;
    
    //Grab connection from Database.php
    function __construct($conn) {
        $this->designerConn = $conn;
    }
    
    //Add designers to the database
    public function add($post, $files, $adminRow)
    {
        $portrait_dir = 'uploads/portrait/';
        $work_dir     = 'uploads/work/';
        $allowExt     = array(
            'jpeg',
            'jpg',
            'png',
            'gif'
        );
        
        $fName = trim($post["designerFName"]);
        $lName = trim($post["designerLName"]);
        $bio   = trim($post["designerBio"]);
        $hours = trim($post["designerHours"]);
        $price = trim($post["designerPrice"]);
        
        $plink        = $files['designerPlink']['name'];
        $plinkImgTmp  = $files['designerPlink']['tmp_name'];
        $plinkImgSize = $files['designerPlink']['size'];
        $pPic         = "";
        
        $wlink        = $files['designerWlink']['name'];
        $wlinkImgTmp  = $files['designerWlink']['tmp_name'];
        $wlinkImgSize = $files['designerWlink']['size'];
        $wPicArr      = array();
        
        
        if (empty($fName)) {
            $error = 'First name input is empty';
        } elseif (empty($lName)) {
            $error = 'Last name input is empty';
        } elseif (empty($bio)) {
            $error = 'Biography input is empty';
        } elseif (empty($hours)) {
            $error = 'Hours input is empty';
        } elseif (empty($price)) {
            $error = 'Price input is empty';
        } else {
            $plinkImgExt = strtolower(pathinfo($plink, PATHINFO_EXTENSION));
            $portraitPic = time() . '_' . rand(1000, 9999) . '.' . $plinkImgExt;
            
            if (in_array($plinkImgExt, $allowExt)) {
                if ($plinkImgSize < 5000000) {
                    move_uploaded_file($plinkImgTmp, $portrait_dir . $portraitPic);
                    $pPic = $portrait_dir . $portraitPic;
                } else {
                    $error = 'Image is too big';
                }
            } else {
                $error = 'Please select a image file which are JPEG, JPG, PNG or GIF';
            }
            
            for ($i = 0; $i < count($files["designerWlink"]["name"]); $i++) {
                
                $wlinkImgExt = strtolower(pathinfo($wlink[$i], PATHINFO_EXTENSION));
                $workPic     = time() . '_' . rand(1000, 9999) . '.' . $wlinkImgExt;
                
                if (in_array($wlinkImgExt, $allowExt)) {
                    if ($wlinkImgSize[$i] < 5000000) {
                        move_uploaded_file($wlinkImgTmp[$i], $work_dir . $workPic);
                        array_push($wPicArr, $work_dir . $workPic);
                        
                    } else {
                        $error = 'Image is larger than 5mb';
                    }
                } else {
                    $error = 'Please select a image file which are JPEG, JPG, PNG or GIF';
                }
            }
        }
        
        if (isset($error)) {
            echo $error;
        } else {
            $adminId = $adminRow["id"];
            $workJson = json_encode($wPicArr);
            
            try {
                $query    = $this->designerConn->prepare("INSERT INTO designers (designer_Fname, designer_Lname, designer_pic, designer_bio, designer_work, designer_hours, designer_available, designer_price, admin_id) VALUES (:designer_Fname, :designer_Lname, :designer_pic, :designer_bio, :designer_work, :designer_hours, :designer_available, :designer_price, :admin_id)");
                $query->bindParam(':designer_Fname', $fName);
                $query->bindParam(':designer_Lname', $lName);
                $query->bindParam(':designer_pic', $pPic);
                $query->bindParam(':designer_bio', $bio);
                $query->bindParam(':designer_work', $workJson);
                $query->bindParam(':designer_hours', $hours);
                $query->bindParam(':designer_available', $hours);
                $query->bindParam(':designer_price', $price);
                $query->bindParam(':admin_id', $adminId);
                $query->execute();
                
                $query = $this->designerConn->prepare("SELECT * FROM designers WHERE admin_id = :admin_id");
                $query->bindParam(':admin_id', $adminId);
                $query->execute();
                $designerList = $query->fetchAll(PDO::FETCH_ASSOC);
   
                print_r($designerList);
                
                foreach ($designerList as $row)
                {
                    $tempArr = json_decode($row["designer_work"]);
                    $workPicArr = "";
                    
                    for($i = 0; $i < count($tempArr); $i++){
                        $workPicArr .= "<img src='" . $tempArr[$i] . "' />";
                    }
                   
                    echo
                    "<tr class='tableRow' data-designer='".$row["id"]."'>
                        <td>" . $row["designer_Fname"] . " " . $row["designer_Lname"] . "</td>
                        <td><img src='" . $row["designer_pic"] . "' /></td>
                        <td>" . $row["designer_bio"] . "</td>
                        <td class='center'>" . $row["designer_hours"] . "</td>
                        <td class='center'>" . $row["designer_price"] . "</td>
                        <td class='center'>" . $workPicArr . "</td>
                        <td>
                            <button class='editDesigner' data-edit='" . $row["id"] . "'>Edit</button>
                            <button class='deleteDesigner' data-delete='" . $row["id"] . "'>Delete</button>
                            <button class='resetHour' data-resethour='" . $row["id"] . "'>Reset Available Hour</button>
                        </td>
                    </tr>";
                }
            }
            catch (PDOException $e) {
                echo $e->getMessage();
            }
        }
    }
    
    //Delete designers in the database
    public function delete($post)
    {
        try{
            $designer_id = $post["id"];
            
            $query = $this->designerConn->prepare("SELECT * FROM designers WHERE id = :id");
            $query->bindParam(':id', $designer_id);
            $query->execute();
            $designerList = $query->fetchAll(PDO::FETCH_ASSOC);

            unlink($designerList[0]["designer_pic"]);
            
            $workArr = json_decode($designerList[0]["designer_work"]);
        
            for ($i = 0; $i < count($workArr); $i++){
                unlink($workArr[$i]);
            }
                
            $query = $this->designerConn->prepare("DELETE FROM designers WHERE id = :id");
            $query->bindParam(':id', $designer_id);
            $query->execute();
            
            return $designer_id;
        }
        catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    //Edit designers in the database
    public function edit($post)
    {
        $query = $this->designerConn->prepare("SELECT * FROM designers WHERE id = :id");
        $query->bindParam(':id', $post["id"]);
        $query->execute();
        $designerRow = $query->fetch(PDO::FETCH_ASSOC);
        
        echo json_encode($designerRow);
    }
    
    //Show designers in the database
    public function show($adminRow)
    {
        try{
            $adminId = $adminRow["id"];
            $query = $this->designerConn->prepare("SELECT * FROM designers WHERE admin_id = :admin_id");
            $query->bindParam(':admin_id', $adminId);
            $query->execute();
            $designerList = $query->fetchAll(PDO::FETCH_ASSOC);
   
            foreach ($designerList as $row)
            {
                $tempArr = json_decode($row["designer_work"]);
                $workPicArr = "";
                
                for($i = 0; $i < count($tempArr); $i++){
                    $workPicArr .= "<img src='" . $tempArr[$i] . "' />";
                }
                
                $lowHour = "";
                if($row["designer_available"] < 5){
                    $lowHour = "There is less than 5 hours available. Click the button to renew the time for this designer.";
                }
               
                echo
                "<tr class='tableRow' data-designer='".$row["id"]."'>
                    <td>" . $row["designer_Fname"] . " " . $row["designer_Lname"] . "</td>
                    <td><img src='" . $row["designer_pic"] . "' /></td>
                    <td>" . $row["designer_bio"] . "</td>
                    <td class='center'>" . $row["designer_hours"] . "</td>
                    <td class='center'>" . $row["designer_price"] . "</td>
                    <td class='center'>" . $workPicArr . "</td>
                    <td>
                        <button class='editDesigner' data-edit='" . $row["id"] . "'>Edit</button>
                        <button class='deleteDesigner' data-delete='" . $row["id"] . "'>Delete</button>
                        <button class='resetHour' data-resethour='" . $row["id"] . "'>Reset Available Hour</button>
                        <div class='hour-reset-notice'>".$lowHour."</div>
                    </td>
                </tr>";
            }
        }
        catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    public function listDesigners(){
        try{
            $query = $this->designerConn->prepare("SELECT * FROM designers");
            $query->execute();
            $designerList = $query->fetchAll(PDO::FETCH_ASSOC);
            return $designerList;
        }
        catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    public function editSubmit($post, $files, $adminRow){
        $portrait_dir = 'uploads/portrait/';
        $work_dir     = 'uploads/work/';
        $allowExt     = array(
            'jpeg',
            'jpg',
            'png',
            'gif'
        );
        
        //check if plink is empty
        $plink        = $files['editPlink']['name'];
        $plinkImgTmp  = $files['editPlink']['tmp_name'];
        $plinkImgSize = $files['editPlink']['size'];
        $pPic         = "";
        
        //profile picture input is not empty
        if($files['editPlink']['error'] != 4){
            $plinkImgExt = strtolower(pathinfo($plink, PATHINFO_EXTENSION));
            $portraitPic = time() . '_' . rand(1000, 9999) . '.' . $plinkImgExt;
            
            if (in_array($plinkImgExt, $allowExt)) {
                if ($plinkImgSize < 5000000) {
                    move_uploaded_file($plinkImgTmp, $portrait_dir . $portraitPic);
                    $pPic = $portrait_dir . $portraitPic;
                } else {
                    $pError = 'Image is too big';
                }
            } else {
                $pError = 'Please select a image file which are JPEG, JPG, PNG or GIF';
            }
            
            if (isset($pError)) {
                echo $pError;
            } else {
                //everything successful upload new profile picture
                $query   = $this->designerConn->prepare("UPDATE designers SET designer_pic = :designer_pic WHERE id =:designer_id");
                $query->bindParam(':designer_pic', $pPic);
                $query->bindParam(':designer_id', $post["designer_id"]);
                $query->execute();
            }
        }
        
        //check if wlink is empty
        $wlink        = $files['editWlink']['name'];
        $wlinkImgTmp  = $files['editWlink']['tmp_name'];
        $wlinkImgSize = $files['editWlink']['size'];
        $wPicArr      = array();
        
        if($files['editPlink']['error']['0'] != 4){
          for ($i = 0; $i < count($files['editWlink']['name']); $i++) {
                $wlinkImgExt = strtolower(pathinfo($wlink[$i], PATHINFO_EXTENSION));
                $workPic     = time() . '_' . rand(1000, 9999) . '.' . $wlinkImgExt;
                
                if (in_array($wlinkImgExt, $allowExt)) {
                    if ($wlinkImgSize[$i] < 5000000) {
                        move_uploaded_file($wlinkImgTmp[$i], $work_dir . $workPic);
                        array_push($wPicArr, $work_dir . $workPic);
                    } else {
                        $wError = 'Image is larger than 5mb';
                    }
                } else {
                    $wError = 'Please select a image file which are JPEG, JPG, PNG or GIF';
                }
            }
            
            if (isset($wError)) {
                echo $wError;
            } else {
                //everything successful upload new work sample picture
                $workJson = json_encode($wPicArr);
                $query   = $this->designerConn->prepare("UPDATE designers SET designer_work = :designer_work WHERE id =:designer_id");
                $query->bindParam(':designer_work', $workJson);
                $query->bindParam(':designer_id', $post["designer_id"]);
                $query->execute();
            }
        
        }
        
        $fName = trim($post["editFName"]);
        $lName = trim($post["editLName"]);
        $bio   = trim($post["editBio"]);
        $hours = trim($post["editHours"]);
        $price = trim($post["editPrice"]);
        
        if (empty($fName)) {
            $error = 'First name input is empty';
        } elseif (empty($lName)) {
            $error = 'Last name input is empty';
        } elseif (empty($bio)) {
            $error = 'Biography input is empty';
        } elseif (empty($hours)) {
            $error = 'Hours input is empty';
        } elseif (empty($price)) {
            $error = 'Price input is empty';
        }
        
        if (isset($error)) {
            echo $error;
        } else {
            $query   = $this->designerConn->prepare("UPDATE designers SET 
                                                        designer_Fname = :designer_Fname,
                                                        designer_Lname = :designer_Lname,
                                                        designer_bio = :designer_bio,
                                                        designer_hours = :designer_hours, 
                                                        designer_price = :designer_price
                                                    WHERE id =:designer_id");
            
            $query->bindParam(':designer_Fname', $fName);
            $query->bindParam(':designer_Lname', $lName);
            $query->bindParam(':designer_bio', $bio);
            $query->bindParam(':designer_hours', $hours);
            $query->bindParam(':designer_price', $price);
            $query->bindParam(':designer_id', $post["designer_id"]);
            $query->execute();
            
            $query = $this->designerConn->prepare("SELECT * FROM designers WHERE admin_id = :admin_id");
            $query->bindParam(':admin_id', $adminRow["id"]);
            $query->execute();
            $designerList = $query->fetchAll(PDO::FETCH_ASSOC);
    
            foreach ($designerList as $row)
            {
                $tempArr = json_decode($row["designer_work"]);
                $workPicArr = "";
                
                for($i = 0; $i < count($tempArr); $i++){
                    $workPicArr .= "<img src='" . $tempArr[$i] . "' />";
                }
               
                echo
                "<tr class='tableRow' data-designer='".$row["id"]."'>
                    <td>" . $row["designer_Fname"] . " " . $row["designer_Lname"] . "</td>
                    <td><img src='" . $row["designer_pic"] . "' /></td>
                    <td>" . $row["designer_bio"] . "</td>
                    <td class='center'>" . $row["designer_hours"] . "</td>
                    <td class='center'>" . $row["designer_price"] . "</td>
                    <td class='center'>" . $workPicArr . "</td>
                    <td>
                        <button class='editDesigner' data-edit='" . $row["id"] . "'>Edit</button>
                        <button class='deleteDesigner' data-delete='" . $row["id"] . "'>Delete</button>
                        <button class='resetHour' data-resethour='" . $row["id"] . "'>Reset Available Hour</button>
                    </td>
                </tr>";
            }
        }
    }
    
    public function resetHour($post){
        $query = $this->designerConn->prepare("SELECT designer_hours FROM designers WHERE id = :id");
        $query->bindParam(':id', $post["id"]);
        $query->execute();
        $designerHour = $query->fetch(PDO::FETCH_ASSOC);
        
        $query = $this->designerConn->prepare("UPDATE designers SET designer_available = :designer_available WHERE id =:id");
        $query->bindParam(':designer_available', $designerHour["designer_hours"]);
        $query->bindParam(':id', $post["id"]);
        $query->execute();
        
        echo $post["id"];
    }
}

require_once('./class/Database.php');
$designerAdmin = new DesignerAdmin($conn);
?>