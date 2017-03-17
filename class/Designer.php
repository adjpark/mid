<?php
    class Designer
    {
        public $id;
        public $fName;
        public $lName;
        public $bio;
        public $hours;
        public $price;
        public $plink;
        public $wlink;
    
        function __construct($id, $fName, $lName, $bio, $hours, $price, $plink, $wlink){
            $this->id=$id;
            $this->fName=$fName;
            $this->lName=$lName;
            $this->bio=$bio;
            $this->hours=$hours;
            $this->price=$price;
            $this->plink=$plink;
            $this->wlink=$wlink;
        }
    }
?>