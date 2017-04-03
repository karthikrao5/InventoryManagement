<?php

include_once 'CoreService.php';
include_once 'DAO.php';
include_once 'Models/EquipmentType.php';
include_once 'Models/EquipmentTypeAttribute.php';
include_once 'Models/Attribute.php';
include_once 'Models/Equipment.php';

use App\Core\CoreService;
use App\Core\Models\EquipmentType;
use App\Core\Models\EquipmentTypeAttribute;

$jsonStr = '{
    "name" : "laptop",
    "comments" : "Generic laptop.",
    "equipment_type_attributes": [
        {
            "name": "serial_number",
            "required": true,
            "unique": false,
            "data_type" : "string",
            "regex" : "[A-Z][0-9]+",
            "help_comment" : "Use uppercase.",
            "enum" : false,
            "enum_values" : [
            ]
        },
        {
            "name": "screen",
            "required": true,
            "unique": false,
            "data_type" : "string",
            "regex" : null,
            "help_comment" : null,
            "enum" : true,
            "enum_values" : [
                "1080p", "2k", "4k"
            ]
        }
    ]
}';

$coreService = new CoreService();
//$jsonArr = json_decode($jsonStr, true);
//$equipmentType = $coreService->createEquipmentType($jsonArr);

//print_r($equipmentType);

print_r($coreService->getEquipmentType(null));