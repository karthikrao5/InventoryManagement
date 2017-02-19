<?php
    namespace App\core;
    
    include_once "CoreService.php";
    include_once "DAOMongoDB.php";
    
    $dao = DAOMongoDB::getInstance();
    
    $arr = array(
        'department_tag' => "Test!",
        'some_attribute' => 5
    );
    
    $dao->createEquipment($arr);
    
    print_r($arr);
    
    $arr['some_attribute'] = 7;
    $arr['new_attribute'] = "new";
    $arr['_id'] = $arr['_id']->{'$id'};
    
    print_r($arr);
    
    $docs = $dao->updateEquipment($arr);
    
    print_r($docs);
?>