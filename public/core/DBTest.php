<?php
    require 'DAOMongoDB.php';
    
    $db = DAOMongoDB::getInstance();
    
    $equipment = array(
        "test" => "Created in DBTest.php"
    );
    
    $result = $db->createDocument("inventorytracking", "equipments", $equipment);
    
    print_r($result);
?>