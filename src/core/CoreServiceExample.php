<?php
    namespace App\core;
    
    include "CoreService.php";
    
    $equipment = array(
        "test" => "Created in DBTest.php",
		"department_tag" => "MATH-1234",
    );
	
	$core = CoreService::getInstance();
    
    $result = $core->addEquipment($equipment);
    
    print_r($result);
?>