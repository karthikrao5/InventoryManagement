<?php

namespace App\Core;

use \MongoClient;
use \MongoId;

class DAO
{
	private static $connectionString = null; // Null is equivalent to "mongodb://localhost:27017".
	
	// EquipmentType object as parameter.
	public function createEquipmentType($equipmentType)
	{
		$array = $equipmentType->jsonSerialize();
		unset($array['_id']);
		
		$mongo = new MongoClient(DAO::$connectionString);
		$equipmentTypes = $mongo->inventorytracking->equipmenttypes;
		$equipmentTypes->insert($array);
		
		$equipmentType->setId($array['_id']);
		
		foreach($equipmentType->getAttributes() as $equipmentTypeAttr)
		{
			$equipmentTypeAttr->setEquipmentTypeId($equipmentType->getId());
			$id = $this->createEquipmentTypeAttribute($equipmentTypeAttr);
		}
		
		$array = $equipmentType->jsonSerialize();
		$equipmentTypes->update(array("_id" => $equipmentType->getId()), $array);
		
		$mongo->close();
		
		return $array['_id'];
	}
	
	public function createEquipmentTypeAttribute($equipmentTypeAttribute)
	{
		$array = $equipmentTypeAttribute->jsonSerialize();
		unset($array['_id']);
		
		$mongo = new MongoClient(DAO::$connectionString);
		$equipmentTypesAttributes = $mongo->inventorytracking->equipmenttypeattributes;
		$equipmentTypesAttributes->insert($array);
		$mongo->close();
		
		$equipmentTypeAttribute->setId($array['_id']);
		
		return $array['_id'];
	}
	
	public function createEquipment($equipment)
	{
		$array = $equipment->jsonSerialize();
		unset($array['_id']);
		
		$mongo = new MongoClient(DAO::$connectionString);
		$equipments = $mongo->inventorytracking->equipments;
		$equipments->insert($array);
		
		$equipment->setId($array['_id']);
		
		foreach($equipment->getAttributes() as $equipmentAttr)
		{
			$equipmentAttr->setEquipmentId($equipment->getId());
			$id = $this->createEquipmentAttribute($equipmentAttr);
		}
		
		$array = $equipment->jsonSerialize();
		$equipments->update(array("_id" => $equipment->getId()), $array);
		
		$mongo->close();
		
		return $array['_id'];
	}
	
	public function createEquipmentAttribute($attribute)
	{
		$array = $attribute->jsonSerialize();
		unset($array['_id']);
		
		$mongo = new MongoClient(DAO::$connectionString);
		$attributes = $mongo->inventorytracking->equipmentattributes;
		$attributes->insert($array);
		$mongo->close();
		
		$attribute->setId($array['_id']);
		
		return $array['_id'];
	}
	
	public function getEquipment($searchCriteria)
	{
		//change this behavior later
		$mongo = new MongoClient(DAO::$connectionString);
		$equipments = $mongo->inventorytracking->equipments;

		if(is_null($searchCriteria)) 
		{
			return iterator_to_array($equipments->find());
		}

		$cursor = $equipments->find($searchCriteria);
		$mongo->close();
		
		$array = array();
		
		foreach($cursor as $equipment)
		{
			$array[] = $equipment;
		}
		
		return $array;
	}
	
	public function getEquipmentType($searchCriteria)
	{
		//change this behavior later
		$mongo = new MongoClient(DAO::$connectionString);
		$equipmenttypes = $mongo->inventorytracking->equipmenttypes;

		if(is_null($searchCriteria)) 
		{
			return iterator_to_array($equipmenttypes->find());
		}

		$cursor = $equipmenttypes->find($searchCriteria);
		$mongo->close();
		
		$array = array();
		
		foreach($cursor as $equipmentType)
		{
			$array[] = $equipmentType;
		}
		
		return $array;
	}
}
