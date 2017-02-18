<?php
    namespace App\core;
    
    include "CoreService.php";
    
    $equipment = array(
        "test" => "Created in DBTest.php",
		"department_tag" => "MATH-1234",
    );
	
	$core = CoreService::getInstance();
    
    $id = "58a7c7c87f8b9abb1cab050e";
    
    $result = $core->getEquipmentById($id);
    
    print_r($result);
?>