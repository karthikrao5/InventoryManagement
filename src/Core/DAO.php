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
                    foreach($equipment['attributes'] as &$equipAttr)
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
			array('$set'=> array('attributes' => $attributeIds)));

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

		$newArr = array();
		if(!is_null($result) && !empty($result))
		{
			foreach($result as $equipment)
			{
				$newArr[] = $this->joinEquipment($equipment);
			}
		}

		$mongo->close();
		return $newArr;
	}

	private function joinEquipment($equipment)
	{
		$mongo = new MongoClient(DAO::$connectionString);
		$attributes = $mongo->inventorytracking->equipmentattributes;

		//This is an associative array, which doesn't convert to JSON array.
		$attrs =  iterator_to_array($attributes->find(array('equipment_id' => $equipment['_id'])));
		$array = array();

		foreach($attrs as $attr)
		{
			$array[] = $attr;
		}

		$equipment['attributes'] = $array;
		$mongo->close();
		return $equipment;
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

	public function addEquipmentTypeAttribute($equipmentTypeId, $equipmentTypeAttribute)
	{
		$equipmentTypeAttribute['equipment_type_id'] = new MongoId($equipmentTypeId);
		$updatedAttribute = $this->createEquipmentTypeAttribute($equipmentTypeAttribute);

		$attrRefArray = $this->getEquipmentTypeAttributesArray(new MongoId($equipmentTypeId));
		$attrRefArray[] = $updatedAttribute['_id'];

		$result = $this->updateEquipmentTypeAttributesArray(new MongoId($equipmentTypeId), $attrRefArray);

		return $result;
	}

	//returns array of references
	private function getEquipmentTypeAttributesArray($mongoId)
	{
		$mongo = new MongoClient(DAO::$connectionString);
		$equipmentTypes = $mongo->inventorytracking->equipmenttypes;

		$equipmentType = $equipmentTypes->findOne(array('_id' => $mongoId));
		$mongo->close();

		return $equipmentType['equipment_type_attributes'];
	}

	private function updateEquipmentTypeAttributesArray($mongoId, $array)
	{
		$mongo = new MongoClient(DAO::$connectionString);
		$equipmentTypes = $mongo->inventorytracking->equipmenttypes;

		$result = $equipmentTypes->update(array('_id' => $mongoId),
			array('$set' => array('equipment_type_attributes' => $array)));

		$mongo->close();
		return $result;
	}

	public function removeEquipmentTypeAttribute($equipmentTypeId, $equipmentTypeAttributeId)
	{
		$mongo = new MongoClient(DAO::$connectionString);
		$equipmentTypeAttributes = $mongo->inventorytracking->equipmenttypeattributes;
                $equipmentTypeAttributes->remove(array('_id' => new MongoId($equipmentTypeAttributeId)));
                $mongo->close();

		$attrRefArray = $this->getEquipmentTypeAttributesArray(new MongoId($equipmentTypeId));

		foreach($attrRefArray as $key => $value)
		{
			if($value->{'$id'} == $equipmentTypeAttributeId)
			{
				unset($attrRefArray[$key]);
				break;
			}
		}

		$result = $this->updateEquipmentTypeAttributesArray(new MongoId($equipmentTypeId), $attrRefArray);

		return $result;
	}

	//expects id as a string, not mongo id.
	public function updateEquipmentType($id, $updateValues)
	{
		$mongo = new MongoClient(DAO::$connectionString);
		$equipmentTypes = $mongo->inventorytracking->equipmenttypes;

		unset($updateValues['_id']);

		$result = $equipmentTypes->update(array('_id' => new MongoId($id)),
			array('$set' => $updateValues));

		$mongo->close();

		return $result;
	}

	public function updateEquipmentTypeAttribute($id, $updateValues)
	{
		$mongo = new MongoClient(DAO::$connectionString);
		$equipmentTypeAttributes = $mongo->inventorytracking->equipmenttypeattributes;

		unset($updateValues['_id']);

		$result = $equipmentTypeAttributes->update(array('_id' => new MongoId($id)),
			array('$set' => $updateValues));

		$mongo->close();

		return $result;
	}

	public function updateEquipment($id, $updateValues)
	{
		$mongo = new MongoClient(DAO::$connectionString);
		$equipments = $mongo->inventorytracking->equipments;
		
                $result = $equipments->update(array('_id' => new MongoId($id)),
                        array('$set' => $updateValues));

		$mongo->close();

		return $result;
	}

	public function addEquipmentAttribute($equipmentId, $attribute)
	{
		$equipment = $this->getEquipment(array('_id' => $equipmentId))[0];
                $equipmentType = $this->getEquipmentType(array('_id' => $equipment['equipment_type_id']))[0];
                
		$attribute['equipment_id'] = $equipment['_id'];
		$attribute['equipment_type_id'] = $equipmentType['_id'];
                
                foreach($equipmentType['equipment_type_attributes'] as $equipmentTypeAttr)
                {
                    if($attribute['name'] == $equipmentTypeAttr['name'])
                    {
                        $attribute['equipment_type_attribute_id'] = $equipmentTypeAttr['_id'];
                        break;
                    }
                }

		$updatedAttribute = $this->createEquipmentAttribute($attribute);
                $attrRefArray = $this->getEquipmentAttributesArray(new MongoId($equipmentId));
                
                $attrRefArray[] = $updatedAttribute['_id'];
                $result = $this->updateEquipmentAttributesArray(new MongoId($equipmentId), $attrRefArray);
                
		return $result;
	}

	public function removeEquipmentAttribute($equipmentId, $attributeId)
	{
		$mongo = new MongoClient(DAO::$connectionString);
		$equipmentAttributes = $mongo->inventorytracking->equipmentattributes;
                $result = $equipmentAttributes->remove(array('_id' => new MongoId($attributeId)));
                $mongo->close();

                $attrRefArray = $this->getEquipmentAttributesArray(new MongoId($equipmentId));
                
                foreach($attrRefArray as $key => $value)
                {
                    if($value->{'$id'} == $attributeId)
                    {
                        unset($attrRefArray[$key]);
                        break;
                    }
                }
                
                $result = $this->updateEquipmentAttributesArray(new MongoId($equipmentId), $attrRefArray);

		return $result;
	}

	public function updateEquipmentAttriubte($id, $updateValues)
	{
		$mongo = new MongoClient(DAO::$connectionString);
		$equipmentAttributes = $mongo->inventorytracking->equipmentattributes;

		unset($updateValues['_id']);

		$result = $equipmentAttributes->update(array('_id' => new MongoId($id)),
			array('$set' => $updateValues));
                
                $mongo->close();
                
                return $result;
	}
        
        private function getEquipmentAttributesArray($mongoId)
        {
            $mongo = new MongoClient(DAO::$connectionString);
            $equipments = $mongo->inventorytracking->equipments;
            $equipment = $equipments->findOne(array('_id' => $mongoId));
            $mongo->close();
            
            return $equipment['attributes'];
        }
        
        private function updateEquipmentAttributesArray($mongoId, $array)
        {
            $mongo = new MongoClient(DAO::$connectionString);
            $equipments = $mongo->inventorytracking->equipments;
            $result = $equipments->update(array('_id' => $mongoId),
                    array('$set' => array('attributes' => $array)));
            
            $mongo->close();
            return $result;
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
