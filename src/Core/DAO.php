<?php

namespace App\Core;

use \MongoClient;
use \MongoId;

// I/O is done with PHP arrays.
// All functions expect and return fully joined PHP arrays.
class DAO
{
	private static $connectionString = null; // Null is equivalent to "mongodb://localhost:27017".

	public function createEquipmentType($equipmentType)
	{
		$mongo = new MongoClient(DAO::$connectionString);
		$equipmentTypes = $mongo->inventorytracking->equipmenttypes;
		$result = $equipmentTypes->insert($equipmentType);

		$attributes = array(); //one with '_id's
		$attributeIds = array(); //only '_id's
		foreach($equipmentType['equipment_type_attributes'] as $attribute)
		{
			$attribute['equipment_type_id'] = $equipmentType['_id'];
			$updatedAttribute = $this->createEquipmentTypeAttribute($attribute);
			$attributes[] = $updatedAttribute;
			$attributeIds[] = $updatedAttribute['_id'];
		}

		$equipmentType['equipment_type_attributes'] = $attributeIds;
		$equipmentTypes->update(array('_id' => $equipmentType['_id']),
			array('$set' => $equipmentType));
		$mongo->close();

		$equipmentType['equipment_type_attributes'] = $attributes;

		return $equipmentType;
	}

	public function createEquipmentTypeAttribute($equipmentTypeAttribute)
	{
		$mongo = new MongoClient(DAO::$connectionString);
		$equipmentTypesAttributes = $mongo->inventorytracking->equipmenttypeattributes;
		$equipmentTypesAttributes->insert($equipmentTypeAttribute);
		$mongo->close();

		return $equipmentTypeAttribute;
	}

	// It is the CoreService's responsibility to find and pass in proper EquipmentType
	public function createEquipment($equipment, $equipmentType)
	{
		$mongo = new MongoClient(DAO::$connectionString);
		$equipments = $mongo->inventorytracking->equipments;

		// set equipment type id to equipment
		$equipment['equipment_type_id'] = $equipmentType['_id'];
		$equipments->insert($equipment);

		// set ids to equipment attributes
		foreach($equipmentType['equipment_type_attributes'] as $equipTypeAttr)
		{
			foreach($equipment['attributes'] as $equipAttr)
			{
				if($equipTypeAttr['name'] == $equipAttr['name'])
				{
					$equipAttr['equipment_type_attribute_id'] = $equipTypeAttr['_id'];
					$equipAttr['equipment_type_id'] = $equipmentType['_id'];
					$equipAttr['equipment_id'] = $equipment['_id'];
				}
			}
		}

		$attributes = array(); //one with '_id's
		$attributeIds = array(); //only '_id's
		foreach($equipment['attributes'] as $attribute)
		{
			$updatedAttribute = $this->createEquipmentAttribute($attribute);
			$attributes[] = $updatedAttribute;
			$attributeIds[] = $updatedAttribute['_id'];
		}

		$equipment['attributes'] = $attributes;

		$equipments->update(array("_id" => $equipment['_id']),
			array('attributes' => $attributeIds));
		$mongo->close();

		return $equipment;
	}

	public function createEquipmentAttribute($attribute)
	{
		$mongo = new MongoClient(DAO::$connectionString);
		$attributes = $mongo->inventorytracking->equipmentattributes;
		$attributes->insert($attribute);
		$mongo->close();

		return $attribute;
	}

	public function getEquipment($searchCriteria=null)
	{
		$mongo = new MongoClient(DAO::$connectionString);
		$equipments = $mongo->inventorytracking->equipments;
		$attributes = $mongo->inventorytracking->equipmentattributes;

		$result = null;
		if(is_null($searchCriteria) || empty($searchCriteria))
		{
			$result = iterator_to_array($equipments->find());
		} else {
			if(isset($searchCriteria['_id']))
			{
				$searchCriteria['_id'] = new MongoId($searchCriteria['_id']);
			}

			$result = iterator_to_array($equipments->find($searchCriteria));
		}

		if(!is_null($result) && !empty($result))
		{
			foreach($result as $equipment)
			{
				$this->joinEquipment($equipment, $attributes);
			}
		}

		$mongo->close();

		return $result;
	}

	private function joinEquipment(&$equipment, $attributes)
	{
		$attrs =  iterator_to_array($attributes->find(array('equipment_id' => $equipment['_id'])));
		$equipment['attributes'] = $attrs;
	}

	public function getEquipmentType($searchCriteria=null)
	{
		//change this behavior later
		$mongo = new MongoClient(DAO::$connectionString);
		$equipmentTypes = $mongo->inventorytracking->equipmenttypes;

		$result = null;
		if(is_null($searchCriteria) || empty($searchCriteria))
		{
			$result = iterator_to_array($equipmentTypes->find());
		} else {
			if(isset($searchCriteria['_id']))
			{
				$searchCriteria['_id'] = new MongoId($searchCriteria['_id']);
			}

			$result = iterator_to_array($equipmentTypes->find($searchCriteria));
		}

		$newArr = array();
		if(!is_null($result) && !empty($result))
		{
			foreach($result as $equipmentType)
			{
				$newArr[] = $this->joinEquipmentType($equipmentType);
			}
		}

		$mongo->close();
		return $newArr;
	}

	private function joinEquipmentType($equipmentType)
	{
		$mongo = new MongoClient(DAO::$connectionString);
		$attrsDB = $mongo->inventorytracking->equipmenttypeattributes;

		//This is an associative array, which doesn't convert to JSON array.
		$attrs =  iterator_to_array($attrsDB->find(array('equipment_type_id' => $equipmentType['_id'])));
		$array = array();

		foreach($attrs as $attr)
		{
			$array[] = $attr;
		}

		$equipmentType['equipment_type_attributes'] = $array;
		$mongo->close();
		return $equipmentType;
	}

	public function deleteEquipment($equipmentIds)
	{
		$mongoIdArr = array();

		foreach($equipmentIds as $idStr)
		{
			$mongoIdArr[] = new MongoId($idStr);
		}

		$mongo = new MongoClient(DAO::$connectionString);
		$equipments = $mongo->inventorytracking->equipments;
		$attributes = $mongo->inventorytracking->equipmentattributes;

		$result = $attributes->remove(array('equipment_id' => array('$in' => $mongoIdArr)));
		$result = $equipments->remove(array('_id' => array('$in' => $mongoIdArr)));

		$mongo->close();

		return $result;
	}

	public function deleteEquipmentType($equipmentTypeIds)
	{
		$mongoIdArr = array();

		foreach($equipmentTypeIds as $idStr)
		{
			$mongoIdArr[] = new MongoId($idStr);
		}

		$mongo = new MongoClient(DAO::$connectionString);
		$equipmentTypes = $mongo->inventorytracking->equipmenttypes;
		$equipmentTypeAttributes = $mongo->inventorytracking->equipmenttypeattributes;

		$result = $equipmentTypeAttributes->remove(array('equipment_type_id' => array( '$in' => $mongoIdArr)));
		$result = $equipmentTypes->remove(array('_id' => array( '$in' => $mongoIdArr)));

		$mongo->close();

		return $result;
	}

	public function addEquipmentTypeAttribute($equipmentType, $equipmentTypeAttribute)
	{
		$mongo = new MongoClient(DAO::$connectionString);
		$equipmentTypes = $mongo->inventorytracking->equipmenttypes;

		$equipmentTypeAttribute['equipment_type_id'] = $equipmentType['_id'];
		$updatedAttribute = $this->createEquipmentTypeAttribute($equipmentTypeAttribute);

		$attributeIds = array();

		foreach($equipmentType['equipment_type_attributes'] as $attr)
		{
			$attributeIds = $attr['_id'];
		}
		$attributeIds[] = $updatedAttribute['_id'];

		$equipmentType['equipment_type_attributes'][] = $updatedAttribute;

		$result = $equipmentTypes->update(array('_id' => $equipmentType['_id']),
			array('$set' => array('equipment_type_attributes' => $attributeIds)));

		$mongo->close();

		return $equipmentType;
	}

	public function removeEquipmentTypeAttribute($equipmentType, $equipmentTypeAttribute)
	{
		$mongo = new MongoClient(DAO::$connectionString);
		$equipmentTypes = $mongo->inventorytracking->equipmenttypes;
		$equipmentTypeAttributes = $mongo->inventorytracking->equipmenttypeattributes;

		foreach($equipmentType['equipment_type_attributes'] as $key => $attribute)
		{
			if($attribute['name'] == $equipmentTypeAttribute['name'])
			{
				unset($equipmentType['equipment_type_attributes'][$key]);
				break;
			}
		}

		$attributeIds = array();
		foreach ($equipmentType['equipment_type_attributes'] as $attr)
		{
			$attributeIds[] = $attr['_id'];
		}

		$equipmentTypes->update(array('_id' => $equipmentType['_id']),
			array('$set' => array('equipment_type_attributes' => $attributeIds)));

		$mongo->close();

		return $equipmentType;
	}

	public function updateEquipmentType($equipmentTypeOld, $equipmentTypeNew)
	{
		$mongo = new MongoClient(DAO::$connectionString);
		$equipmentTypes = $mongo->inventorytracking->equipmenttypes;

		unset($equipmentTypeNew['_id']);

		$result = $equipmentTypes->update(array('_id' => $equipmentTypeOld['_id']),
			array('$set' => array('name' => $equipmentTypeNew['name'])));

		$mongo->close();

		return $result;
	}

	public function updateEquipmentTypeAttribute($attributeOld, $attributeNew)
	{
		$mongo = new MongoClient(DAO::$connectionString);
		$equipmentTypeAttributes = $mongo->inventorytracking->equipmenttypeattributes;

		unset($attributeNew['_id']);

		$result = $equipmentTypeAttributes->update(array('_id' => $attributeOld['_id']),
			array('$set' => $attributeNew));

		$mongo->close();

		return $result;
	}

	public function updateEquipment($equipmentOld, $equipmentNew)
	{
		$mongo = new MongoClient(DAO::$connectionString);
		$equipments = $mongo->inventorytracking->equipments;
		unset($equipmentNew['_id']);
		$result = $equipments->update(array('_id' => $equipmentOld['_id']),
			array('$set' => $equipmentNew));

		$mongo->close();

		return $result;
	}

	public function addEquipmentAttribute($equipment, $attribute, $equipmentType, $equipmentTypeAttribute)
	{
		$mongo = new MongoClient(DAO::$connectionString);
		$equipments = $mongo->inventorytracking->equipments;

		$attribute['equipment_id'] = $equipment['_id'];
		$attribute['equipment_type_id'] = $equipmentType['_id'];
		$attribute['equipment_type_attribute_id'] = $equipmentTypeAttribute['_id'];

		$updatedAttribute = $this->createEquipmentAttribute($attribute);
		$equipment['attributes'][] = $updatedAttribute;

		$attributeIds = array();
		foreach($equipment['attributes'] as $attr)
		{
			$attributeIds[] = $attr['_id'];
		}

		$equipments->update(array('_id' => $equipment['_id']),
			array('attributes' => $attributeIds));

		return $equipment;
	}

	public function removeEquipmentAttribute($equipment, $attribute)
	{
		$mongo = new MongoClient(DAO::$connectionString);
		$equipments = $mongo->inventorytracking->equipments;
		$equipmentAttributes = $mongo->inventorytracking->equipmentattributes;

		$attrIds = array();
		foreach($equipment['attributes'] as $key => $attr)
		{
			if($attr['_id'] == $attribute['_id'])
			{
				unset($equipment['attributes'][$key]);
				break;
			}
			else
			{
				$attrIds[] = $attr['_id'];
			}
		}

		$equipments->update(array('_id' => $equipment['_id']),
			array('attributes' => $attrIds));
		$equipmentAttributes->remove(array('_id' => $attribute['_id']));

		return $equipment;
	}

	public function updateEquipmentAttriubte($attributeOld, $attributeNew)
	{
		$mongo = new MongoClient(DAO::$connectionString);
		$equipmentAttributes = $mongo->inventorytracking->equipmentattributes;

		unset($attributeNew['_id']);

		$equipmentAttributes->update(array('_id' => $attributeOld['_id']),
			array('$set' => $attributeNew));
	}

	public function getEquipmentAttribute($searchCriteria=null)
	{
		$mongo = new MongoClient(DAO::$connectionString);
		$equipmentAttributes = $mongo->inventorytracking->equipmentattributes;

		$result = null;
		if(is_null($searchCriteria) || empty($searchCriteria))
		{
			$result = iterator_to_array($equipmentAttributes->find());
		}
		else
		{
			$result = iterator_to_array($equipmentAttributes->find($searchCriteria));
		}

		return $result;
	}

	public function getEquipmentTypeAttribute($searchCriteria)
	{
		$mongo = new MongoClient(DAO::$connectionString);
		$equipmentTypeAttributes = $mongo->inventorytracking->equipmenttypeattributes;

		$result = null;
		if(is_null($searchCriteria) || empty($searchCriteria))
		{
			$result = iterator_to_array($equipmentTypeAttributes->find());
		}
		else
		{
			$result = iterator_to_array($equipmentTypeAttributes->find($searchCriteria));
		}

		return $result;
	}
}
