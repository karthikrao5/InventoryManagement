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

	public function getEquipment($searchCriteria=null)
	{
		//change this behavior later
		$mongo = new MongoClient(DAO::$connectionString);
		$equipments = $mongo->inventorytracking->equipments;

		if(is_null($searchCriteria) || !$searchCriteria)
		{
			return iterator_to_array($equipments->find());
		} else {
			$cursor = $equipments->find($searchCriteria);
			$array = array();

			foreach($cursor as $equipment)
			{
				$array[] = $equipment;
			}
			$mongo->close();
			return $array;
		}
		// should not get here
		return null;
	}

	public function getEquipmentType($searchCriteria)
	{
		//change this behavior later
		$mongo = new MongoClient(DAO::$connectionString);
		$equipmenttypes = $mongo->inventorytracking->equipmenttypes;

		if(is_null($searchCriteria))
		{
			return iterator_to_array($equipmenttypes->find());
		} else {
			$cursor = $equipmenttypes->find($searchCriteria);
			$mongo->close();

			$array = array();

			foreach($cursor as $equipmentType)
			{
				$array[] = $equipmentType;
			}

			return $array;
		}

		return null;
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

		$mongo = new mongoclient(DAO::$connectionString);
		$equipmentTypes = $mongo->inventorytracking->equipmenttypes;
		$equipmentTypeAttributes = $mongo->inventorytracking->equipmenttypeattributes;

		//$result = $equipmentTypeAttributes->remove(array('equipment_type_id' => array( '$in' => $equipmentTypeIds)));
		$result = $equipmentTypeAttributes->remove(array('equipment_type_id' => array( '$in' => $mongoIdArr)));
		$result = $equipmentTypes->remove(array('_id' => array( '$in' => $mongoIdArr)));

		$mongo->close();

		return $result;
	}

	public function addEquipmentTypeAttribute()
	{

	}

	public function removeEquipmentTypeAttribute()
	{

	}

	public function updateEquipmentType()
	{

	}

	public function updateEquipmentTypeAttribute()
	{

	}

	public function updateEquipment()
	{

	}

	public function addEquipmentAttribute()
	{

	}

	public function removeEquipmentAttribute()
	{

	}

	public function updateEquipmentAttriubte()
	{

	}
}
