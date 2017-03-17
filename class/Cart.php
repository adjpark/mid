<?php
    class Cart{
        private $items;
        private $cartID;
        private $cartConn;
        
        public function __construct($conn)
        {
            $this->cartConn = $conn;
            
            if(isset($_SESSION['cart'])){
                $this->items = $_SESSION['cart'];
            }else{
                $_SESSION['cart'] = array();
                $this->items = $_SESSION['cart'];
            }
        }
    
        public function getTotalPrice(){
            if(isset($_SESSION["cart"]["totaPrice"])){
                echo $_SESSION["cart"]["totaPrice"];
            }else{
                echo "There are no items in your cart";
            }
        }
        
        public function addProduct($data){
            $query = $this->cartConn->prepare("SELECT designer_available FROM designers WHERE id=:id");
            $query->bindParam(":id", $data["designerID"]);
            $query->execute();
            $designerRow = $query->fetch(PDO::FETCH_ASSOC);
            
            $query = $this->cartConn->prepare("SELECT * FROM cart_product WHERE designer_id = :designer_id");
            $query->bindParam(":designer_id", $data["designerID"]);
            $query->execute();
            $cartListRow = $query->fetchAll(PDO::FETCH_ASSOC);
            
            if (in_array($data["designerID"], $_SESSION['cart'])) {
                $result = "The item is in cart already.";
                return $result;
            }
            elseif (empty($cartListRow) === false) {
                $result = "The item is in cart already DATABASE.";
                return $result;
            }
            elseif($designerRow["designer_available"] == 0){
                $result = "This designer is fully booked and is not available.";
                return $result;
            }
            else{
                $query = $this->cartConn->prepare("SELECT id FROM cart WHERE user_session=:user_session");
                $query->bindParam(":user_session", $data["cartID"]);
                $query->execute();
                $cartRow = $query->fetch(PDO::FETCH_ASSOC);
            
                
                $cartInit = 1;
                $cartID = $cartRow["id"];
                $designerID = $data["designerID"];
                
                $query = $this->cartConn->prepare("INSERT INTO cart_product (quantity, cart_id, designer_id) VALUES (:quantity, :cart_id, :designer_id)");
                $query->bindParam(":quantity", $cartInit);
                $query->bindParam(":cart_id", $cartID);
                $query->bindParam(":designer_id", $designerID);
                $query->execute();
                
                array_push($_SESSION['cart'], $data["designerID"]);
                
                $result = "The item has been added to the cart.";
                return $result;
            }
            
        }
        
        public function removeProduct($data){
            $currentSession = $_SESSION["cart"];
            $currentArray = array_keys($_SESSION["cart"],$data);
            
            if(isset($currentArray) === true ){
                $temp = $currentArray[0];
                unset($_SESSION["cart"][$temp]);
            }
            
            $query = $this->cartConn->prepare("DELETE FROM cart_product WHERE designer_id = :designer_id");
            $query->bindParam(':designer_id', $data);
            $query->execute();
            
            return $data;
        }
        
        public function createNewCart(){
            $currentSession = session_id();
            $query = $this->cartConn->prepare("INSERT INTO cart (user_session) VALUES (:user_session)");
            $query->bindParam(":user_session", $currentSession);
            $query->execute();
            
            $sessionNumb = session_id();
            return $sessionNumb;
        }
        
        public function destoryCart($data){
            $query = $this->cartConn->prepare("SELECT id FROM cart WHERE user_session = :session_id");
            $query->bindParam(':session_id', $data);
            $query->execute();
            $cartRow = $query->fetch(PDO::FETCH_ASSOC);
            
            $query = $this->cartConn->prepare("DELETE FROM cart_product WHERE cart_id = :id");
            $query->bindParam(':id', $cartRow["id"]);
            $query->execute();
            
            $query = $this->cartConn->prepare("DELETE FROM cart WHERE user_session = :session_id");
            $query->bindParam(':session_id', $data);
            $query->execute();
            
            $_SESSION['cart'] = array();
            $this->items = $_SESSION['cart'];
            
            echo "Cart has been deleted";
        }
        
        public function loadCart($data){
            $query = $this->cartConn->prepare("
            SELECT * FROM cart_product
            INNER JOIN cart
                ON cart_product.cart_id = cart.id
            INNER JOIN designers
                ON cart_product.designer_id = designers.id
            WHERE user_session = :CurrentCartID
            ");
            $query->bindParam(":CurrentCartID", $data);
            $query->execute();
            $cartRow = $query->fetchAll(PDO::FETCH_ASSOC);
            
            $total = 0;
            
            foreach ($cartRow as $row){
                $subtotal = $row["quantity"] * $row["designer_price"];
                $total += $subtotal;
                echo
                "
                    <tr data-cartitem = '".$row["id"]."' >
                        <td data-th='Product'>
                            <div class='row'>
                                <div class='col-sm-2 hidden-xs'><img class='img-responsive' src='".$row["designer_pic"]."' /></div>
                                <div class='col-sm-10'>
                                    <h4 class='nomargin'>".$row["designer_Fname"]." ".$row["designer_Lname"]."</h4>
                                    <p>".$row["designer_bio"]."</p>
                                </div>
                            </div>
                        </td>
                        <td data-th='Price'>$".$row["designer_price"].".00</td>
                        <td data-th='Quantity'>
                            <input type='number' data-quantity='".$row["id"]."' class='form-control text-center' value='".$row["quantity"]."' />
                        </td>
                        <td data-th='Subtotal' class='text-center'>$".$subtotal.".00</td>
                        <td class='actions' data-th=''>
                            <button data-delete='".$row["id"]."' class='btn btn-danger btn-sm cart-delete'><i class='fa fa-trash-o'></i></button>
                            <button data-update='".$row["id"]."' class='btn btn-info btn-sm cart-update'><i class='fa fa-refresh'></i></button>
                        </td>
                    </tr>
                ";
            }
            
            $_SESSION["cart"]["totaPrice"] = $total;
        }
        
        public function updateProduct($data){
            $query   = $this->cartConn->prepare("UPDATE cart_product SET quantity = :quantity WHERE designer_id =:designer_id");
            $query->bindParam(':quantity', $data["newQuantity"]);
            $query->bindParam(':designer_id', $data["cartItem"]);
            $query->execute();
            
            echo "Quantity has been updated.";
        }
    
        public function checkOut($data){
            $query = $this->cartConn->prepare("
            SELECT * FROM cart_product
            INNER JOIN cart
                ON cart_product.cart_id = cart.id
            INNER JOIN designers
                ON cart_product.designer_id = designers.id
            WHERE user_session = :CurrentCartID
            ");
            $query->bindParam(":CurrentCartID", $data);
            $query->execute();
            $cartRow = $query->fetchAll(PDO::FETCH_ASSOC);
            
            $remainingHours = array();
            $statusArr = array("finalStatus" => false);
            $availabilityCheck = true;
            
            foreach ($cartRow as $row){
                if($row["quantity"] > $row["designer_available"]){
                    array_push($statusArr , $row["designer_Fname"]." ".$row["designer_Lname"]." cannot be checked out due to his/her available hours being ".$row["designer_available"]." hours.");
                    $availabilityCheck = false;
                }else{
                    array_push($statusArr , $row["designer_Fname"]." ".$row["designer_Lname"]." can be checked out for ".$row["quantity"]." hours.");
                    
                    $temp = $row["designer_available"] - $row["quantity"];
                    array_push($remainingHours, array("designer_id" => $row["id"], "remaining_hour" => $temp));
                }
            }
            
            if($availabilityCheck == true){
                $statusArr["finalStatus"] = true;
                for($i = 0; $i < count($remainingHours); $i++){
                    $query   = $this->cartConn->prepare("UPDATE designers SET designer_available = :designer_available WHERE id =:designer_id");
                    $query->bindParam(':designer_available', $remainingHours[$i]["remaining_hour"]);
                    $query->bindParam(':designer_id', $remainingHours[$i]["designer_id"]);
                    $query->execute();
                }
                
                //reset cart after checkout is successful
                $query = $this->cartConn->prepare("SELECT id FROM cart WHERE user_session = :session_id");
                $query->bindParam(':session_id', $data);
                $query->execute();
                $currentCartRow = $query->fetch(PDO::FETCH_ASSOC);
                
                $query = $this->cartConn->prepare("DELETE FROM cart_product WHERE cart_id = :id");
                $query->bindParam(':id', $currentCartRow["id"]);
                $query->execute();
                
                $query = $this->cartConn->prepare("DELETE FROM cart WHERE user_session = :session_id");
                $query->bindParam(':session_id', $data);
                $query->execute();
                $_SESSION['cart'] = array();
            }
            echo json_encode($statusArr);
        }
    }

$cart = new Cart($conn);
?>