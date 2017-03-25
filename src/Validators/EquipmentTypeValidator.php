<?php

namespace App\Validators;

use Doctrine\ODM\MongoDB\DocumentManager;
use Interop\Container\ContainerInterface;
use App\Models\EquipmentType;

class EquipmentTypeValidator extends AbstractValidator {

	public function __construct(ContainerInterface $ci) {
		parent::__construct($ci);
	}

	public function validateID($id) {
		if ($id instanceof \MongoDB\BSON\ObjectID || preg_match('/^[a-f\d]{24}$/i', $id)) {
			return true;
		}
		return false;
	}
	
	public function isJSON($json)
	{
		json_decode($json);
		return (json_last_error() == JSON_ERROR_NONE);
	}
	
	public function validateJSON($json)
	{
		//check if all common attributes are present.
		if(!isset($json['name'])) {return false;}
		if(!isset($json['equipment_type_attributes']) || 
			empty($json['equipment_type_attributes'])) {return false;}
		
		//check equipment type attributes
		$attributes = $json['equipment_type_attributes'];
		
		foreach($attributes as $attribute)
		{
			if(!$this->validateAttribute($attribute)) {return false;}
		}
		
		return true;
	}
	
	public function validateAttribute($attribute)
	{
		//check if all attributes are present. syntax check
		if(!isset($attribute['name'])) {return false;}
		if(!isset($attribute['required'])) {return false;}
		if(!isset($attribute['unique'])) {return false;}
		if(!isset($attribute['data_type'])) {return false;}
		if(!array_key_exists('regex', $attribute)) {return false;}
		if(!array_key_exists('help_comment', $attribute)) {return false;}
		if(!isset($attribute['enum'])) {return false;}
		if(!isset($attribute['enum_values'])) {return false;}
		
		//semantics check. This doesn't check against database.
		//if unique is set to true, required must be set to true.
		if($attribute['unique'] && !$attribute['required']) {return false;}
		
		//if enum is set to true, enum_values array cannot be empty.
		if($attribute['enum'] && empty($attribute['enum_values'])) {return false;}
		
		return true;
	}
}

?>