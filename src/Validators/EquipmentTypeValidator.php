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
	
	//This function checks if given string (not array) is in json format.
	public function isJSON($json)
	{
		json_decode($json);
		return (json_last_error() == JSON_ERROR_NONE);
	}
	
	public function validateJSON($json)
	{
		$result = array();
		$result['ok'] = false;
		$result['msg'] = null;
		
		//check if all common attributes are present.
		if(!isset($json['name']))
		{
			$result['msg'] = "Attribute 'name' of Equipment Type is missing or unset.";
			return $result;
		}
		if(!isset($json['equipment_type_attributes']) || empty($json['equipment_type_attributes'])) 
		{
			$result['msg'] = "Attribute 'equipment_type_attributes' of Equipment Type is missing or empty.";
			return $result;
		}
		
		//check equipment type attributes
		$attributes = $json['equipment_type_attributes'];
		
		//check attributes and name collision
		$attr_names = array();
		foreach($attributes as $attribute)
		{
			$result = $this->validateAttribute($attribute);
			if(!$result['ok']) {return $result;}
			
			if(in_array($attribute['name'], $attr_names))
			{
				$result['ok'] = false;
				$result['msg'] = "Name collision on Equipment Type Attribute. '".$attribute['name']."'.";
				return $result;
			}
			else
			{
				$attr_names[] = $attribute['name'];
			}
		}
		
		$result['ok'] = true;
		return $result;
	}
	
	public function validateAttribute($attribute)
	{
		$result = array();
		$result['ok'] = false;
		$result['msg'] = null;
		
		//check if all attributes are present. syntax check
		if(!isset($attribute['name'])) 
		{
			$result['msg'] = "Attribute 'name' of Equipmment Type Attribute is missing or unset.";
			return $result;
		}
		
		if(!isset($attribute['required'])) 
		{
			$result['msg'] = "Attribute 'required' of Equipmment Type Attribute is missing or unset on '".$attribute['name']."'.";
			return $result;
		}
		
		if(!isset($attribute['unique'])) 
		{
			$result['msg'] = "Attribute 'unique' of Equipmment Type Attribute is missing or unset on '".$attribute['name']."'.";
			return $result;
		}
		
		if(!isset($attribute['data_type'])) 
		{
			$result['msg'] = "Attribute 'data_type' of Equipmment Type Attribute is missing or unset on '".$attribute['name']."'.";
			return $result;
		}
		
		if(!array_key_exists('regex', $attribute)) 
		{
			$result['msg'] = "Attribute 'regex' of Equipmment Type Attribute is missing on '".$attribute['name']."'.";
			return $result;
		}
		
		if(!array_key_exists('help_comment', $attribute)) 
		{
			$result['msg'] = "Attribute 'help_comment' of Equipmment Type Attribute is missing on '".$attribute['name']."'.";
			return $result;
		}
		
		if(!isset($attribute['enum'])) 
		{
			$result['msg'] = "Attribute 'enum' of Equipmment Type Attribute is missing or unset on '".$attribute['name']."'.";
			return $result;
		}
		
		if(!array_key_exists('enum_values', $attribute)) 
		{
			$result['msg'] = "Attribute 'enum_values' of Equipmment Type Attribute is missing or unset on '".$attribute['name']."'.";
			return $result;
		}
		
		//semantics check. This doesn't check against database.
		//if unique is set to true, required must be set to true.
		if($attribute['unique'] && !$attribute['required']) 
		{
			$result['msg'] = "Attribute 'required' must be set to true when 'unique' is true on '".$attribute['name']."'.";
			return $result;
		}
		
		//if enum is set to true, enum_values array cannot be empty.
		if($attribute['enum'] && empty($attribute['enum_values'])) 
		{
			$result['msg'] = "Attribute 'enum_values' of Equipmment Type Attribute cannot be null or empty when 'enum' is true on '".$attribute['name']."'.";
			return $result;
		}
		
		$result['ok'] = true;
		return $result;
	}
}

?>