<?php

namespace App\Validators;

use Interop\Container\ContainerInterface;

class EquipmentValidator extends AbstractValidator {

	public function __construct(ContainerInterface $ci) {
		parent::__construct($ci);
	}

	public function validateID($id) {
		if ($id instanceof \MongoDB\BSON\ObjectID || preg_match('/^[a-f\d]{24}$/i', $id)) {
			return true;
		}
		return false;
	}

	// param $json is a string, not php array
	public function isJSON($json) {
		json_decode($json);
		return (json_last_error() == JSON_ERROR_NONE);
	}

	public function validateJSON($json) {
		$result = array();
		$result['ok'] = false;
		$result['msg'] = null;

		if(!isset($json['department_tag'])) {
			$result['msg'] = "Attribute 'department_tag' of Equipment is missing or unset.";
			return $result;
		}

		if(isset($json["attributes"])) {
			$attributes = $json["attributes"];

			$validateAttr = $this->validateAttributes($attributes);

			if(!$validateAttr["ok"]) {
				return $validateAttr;
			}
		}

		// TODO
		
		// if(isset($json["logs"])) {
		// 	$logsArray - $json["logs"];

		// 	$validateLogs = $this->validateLogs($logsArray);

		// 	if(!$validateLogs["ok"]) {
		// 		return $validateLogs;
		// 	}
		// }
		
	}

	public function validateAttributes($attributes) {
		$result = array();
		$result['ok'] = false;
		$result['msg'] = null;

		if(!array_key_exists($attribute['equipment_id'], $attributes)) {
			$result['msg'] = "Attribute 'equipment_id' of Equipmment Attribute is missing or unset.";
			return $result;
		}
		if(!array_key_exists($attribute['equipment_type_id'], $attributes)) {
			$result['msg'] = "Attribute 'equipment_type_id' of Equipmment Attribute is missing or unset.";
			return $result;
		}
		if(!array_key_exists($attribute['name'], $attributes)) {
			$result['msg'] = "Attribute 'name' of Equipmment Attribute is missing or unset.";
			return $result;
		}

		if(!array_key_exists($attribute['value'], $attributes)) {
			$result['msg'] = "Attribute 'value' of Equipmment Attribute is missing or unset.";
			return $result;
		}

		$result['ok'] = true;
		return $result;
	}

}

?>