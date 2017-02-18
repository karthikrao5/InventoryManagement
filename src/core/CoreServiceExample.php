<?php
    namespace App\core;
    
    include_once "CoreService.php";
    include_once "DAOMongoDB.php";
    
    $dao = DAOMongoDB::getInstance();
    
    $docs = $dao->getAllEquipments();
    
    print_r($docs);
?>