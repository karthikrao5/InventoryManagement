<?php

namespace App\Core;

use \MongoClient;
use \MongoId;

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

		return array('ok' => true, 'msg' => null, 'updated' => $equipmentType);
	}

	public function createEquipmentTypeAttribute($equipmentTypeAttribute)
	{
		$mongo = new MongoClient(DAO::$connectionString);
		$equipmentTypesAttributes = $mongo->inventorytracking->equipmenttypeattributes;
		$equipmentTypesAttributes->insert($equipmentTypeAttribute);
		$mongo->close();

		return $equipmentTypeAttribute;
	}

	public function createEquipment($equipment)
	{
		$mongo = new MongoClient(DAO::$connectionString);
		$equipments = $mongo->inventorytracking->equipments;
		$equipments->insert($equipment);

		$attributes = array(); //one with '_id's
		$attributeIds = array(); //only '_id's
		foreach($equipment['attributes'] as $attribute)
		{
			$attribute['equipment_id'] = $equipment['_id'];
			$updatedAttribute = $this->createEquipmentAttribute($attribute);
			$attributes[] = $updatedAttribute;
			$attributeIds[] = $updatedAttribute['_id'];
		}

		$equipment['attributes'] = $attributeIds;
		$equipments->update(array("_id" => $equipment['_id']),
			array('$set' => $equipment));
		$mongo->close();

		$equipment['attributes'] = $attributes;

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
		//change this behavior later
		$mongo = new MongoClient(DAO::$connectionString);
		$equipments = $mongo->inventorytracking->equipments;

		$result = null;
		if(is_null($searchCriteria) || empty($searchCriteria))
		{
			$result = iterator_to_array($equipments->find());
		} else {
			$result = iterator_to_array($equipments->find($searchCriteria));
		}

		$mongo->close();

		return $result;
	}

	public function getEquipmentType($searchCriteria=null)
	{
		//change this behavior later
		$mongo = new MongoClient(DAO::$connectionString);
		$equipmentTypes = $mongo->inventorytracking->equipmenttypes;

		$result = null;
		if(is_null($searchCriteria) || empty($searchCriteria))
		{
			$result = iterator_to_array($equipmenttypes->find());
		} else {
			$result = iterator_to_array($equipmenttypes->find($searchCriteria));
		}

		$mongo->close();
		return $result;
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

		$attributes = $equipmentType['equipment_type_attributes'];
		$attributes[] = $equipmentTypeAttribute;

		$result = $equipmentTypes->update(array('_id' => $equipmentType['_id']),
			array('$set' => array('equipment_type_attributes' => $attributes)));

		$mongo->close();

		return $result;
	}

	public function removeEquipmentTypeAttribute($equipmentType, $equipmentTypeAttribute)
	{
		$mongo = new MongoClient(DAO::$connectionString);
		$equipmentTypes = $mongo->inventorytracking->equipmenttypes;

		$attributes = $equipmentType['equipment_type_attributes'];

		foreach($attributes as $key => $attribute)
		{
			if($attribute['name'] == $equipmentTypeAttribute['name'])
			{
				unset($attributes[$key]);
				break;
			}
		}

		$result = $equipmentTypes->update(array('_id' => $equipmentType['_id']),
			array('$set' => array('equipment_type_attributes' => $attributes)));

		$mongo->close();

		return $result;
	}

	public function updateEquipmentType($equipmentTypeOld, $equipmentTypeNew)
	{
		$mongo = new MongoClient(DAO::$connectionString);
		$equipmentTypes = $mongo->inventorytracking->equipmenttypes;
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

		$attributeIds = array();
		foreach($equipment['attributes'] as $attr)
		{
			$attributeIds[] = $attr['_id'];
		}

		$attributeIds[] = $updatedAttribute['_id'];
		$temp = $equipment['attributes'];

		$equipment['attributes'] = $attributeIds;
		$equipments->update(array('_id' => $equipment['_id']),
			array('$set' => $equipment));

		$equipment['attributes'] = $temp;
		$equipment['attributes'][] = $updatedAttribute;

		return $equipment;
	}

	public function removeEquipmentAttribute($equipment, $attribute)
	{
		$mongo = new MongoClient(DAO::$connectionString);
		$equipments = $mongo->inventorytracking->equipments;
		$equipmentAttributes = $mongo->inventorytracking->equipmentattributes;

		$attributes = $equipment['attributes'];

		foreach($attributes as $key => $attrId)
		{
			if($attribute['_id'] == $attrId)
			{
				unset($attributes[$key]);
				break;
			}
		}

		$equipment['attributes'] = $attributes;
		$equipments->update(array('_id' => $equipment['_id']),
			array('$set' => $equipment));
		$equipmentAttributes->remove(array('_id' => $attribute['_id']));

		return $equipment;
	}

	public function updateEquipmentAttriubte($attributeOld, $attributeNew)
	{
		$mongo = new MongoClient(DAO::$connectionString);
		$equipmentAttributes = $mongo->inventorytracking->equipmentattributes;

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
