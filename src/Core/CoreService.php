<?php
namespace App\Core;

use App\Core\DAO;
use App\Core\Models\Attribute;
use App\Core\Medels\Equipment;
use App\Core\Models\EquipmentType;
use App\Core\Models\EquipmentTypeAttribute;

class CoreService
{
	private $dao;
	
	public function __construct()
	{
		$this->dao = new DAO();
	}
	
	public function createEquipment($requestJson)
	{
		//this is not an object.
		$result = $this->getEquipmentType(array("name" => $requestJson['equipment_type_name']));
		$equipmentTypes = $result['equipment_types'];
		
		$equipmentType = reset($equipmentTypes);
		
		$equipment = new Equipment();
		$equipment->setDepartmentTag($requestJson['department_tag']);
		$equipment->setGtTag($requestJson['gt_tag']);
		$equipment->setEquipmentTypeName($requestJson['equipment_type_name']);
		$equipment->setStatus($requestJson['status']);
		$equipment->setLoanedTo($requestJson['loaned_to']);
		$equipment->setCreatedOn($requestJson['created_on']);
		$equipment->setLastUpdated($requestJson['last_updated']);
		$equipment->setComment($requestJson['comment']);
		
		$attrs = array();
		foreach($requestJson['attributes'] as $attrJson)
		{
			$attr = new Attribute();
			$attr->setName($attrJson['name']);
			$attr->setValue($attrJson['value']);
			$attr->setEquipmentTypeId($equipmentType['_id']);
			
			foreach($equipmentType['equipment_type_attributes'] as $equipmentTypeAttr)
			{
				if($attr->getName() == $equipmentTypeAttr['name'])
				{
					$attr->setEquipmentTypeAttributeId($equipmentTypeAttr["_id"]);
				}
			}
			
			$attrs[] = $attr;
		}
		
		$equipment->setAttributes($attrs);
		$this->dao->createEquipment($equipment);
		
		return array("ok" => true, "message" => "success.");
	}
	
	public function createEquipmentType($requestJson)
	{
		$equipmentType = new EquipmentType();
		$equipmentType->setName($requestJson["name"]);

		$attributes = array();
		foreach ($requestJson['equipment_type_attributes'] as $attr) {
			$newAttr = new EquipmentTypeAttribute();
			$newAttr->setName($attr["name"]);
			$newAttr->setRequired($attr["required"]);
			$newAttr->setUnique($attr["unique"]);
			$newAttr->setDataType($attr["data_type"]);
			$newAttr->setRegex($attr["regex"]);
			$newAttr->setHelpComment($attr["help_comment"]);
			$newAttr->setEnum($attr["enum"]);
			$newAttr->setEnumValues($attr["enum_values"]);		

			$attributes[] = $newAttr;
		}
		$equipmentType->setAttributes($attributes);
		$this->dao->createEquipmentType($equipmentType);
		
		return array("ok" => true, "message" => "success.");
	}
	
	public function getEquipment($requestJson=NULL)
	{
		$searchCriteriaArr = array();
		$searchCriteriaArr['department_tag'] = $requestJson['department_tag'];
		//dev purpose code
		$equipments = $this->dao->getEquipment($searchCriteriaArr);
		
		$result = array("ok" => true, "msg" => "success.", "equipments" => $equipments);
		
		return $result;
	}
	
	public function getEquipmentType($requestJson=NULL)
	{
		$equipmentTypes = null;
		
		if(is_null($requestJson) || empty($requestJson))
		{
			$equipmentTypes = $this->dao->getEquipmentType(null);
		}
		else
		{
			$searchCriteriaArr = array();
			$searchCriteriaArr['name'] = $requestJson['name'];
			$equipmentTypes = $this->dao->getEquipmentType($searchCriteriaArr);
		}
		
		$result = array("ok" => true, "msg" => "success.", "equipment_types" => $equipmentTypes);
		
		return $result;
	}
	
	public function deleteEquipment($requestJson)
	{
		
	}
	
	public function deleteEquipmentType($requestJson)
	{
		
	}
	
	/*
	// Returns an array that contains id (on success), result, and message.
	public function addEquipment($document)
	{
		$result = 
		[
			"id" => null,
			"result" => false,
			"message" => null,
		];
		// Validator not functioning yet.
		if($this->validator->validateCreateEquipment($document))
		{
			$result["id"] = $this->dao->createEquipment($document);
			$result["result"] = true;
			$result["message"] = "Equipment " . $document["department_tag"] . " created successfully.";
			return $result;
		}
		
		$result["message"] = "Failed to create equipment with department_tag " . $document["department_tag"];
		return $result;
	}
	
	// Returns an array that contains id (on success), result, and message.
	public function updateEquipment($document)
	{
		$result = 
		[
			"equipment" => null,
			"result" => false,
			"message" => null	
		];
		
		$document['_id'] = $document['_id']->{'$id'};
		
		if($this->dao->updateEquipment($document))
		{
			$result['result'] = false;
			$result['message'] = "Invalid Equipment JSON format. " . $document;
		}
		else
		{
			$result['result'] = true;
			$result['message'] = "Successfully updated document. ID : " . $document['_id'];
		}
		
		return $result;
	}
	
	// Returns an array that contains equipment document (on success), result, and message.
	public function getEquipmentById($id)
	{
		$result = 
		[
			"equipment" => null,
			"result" => false,
			"message" => null	
		];
		
		if(!$this->validator->validateMongoIdString($id))
		{
			$result['message'] = $result['message'] = "ID : " . $id . " is in invalid MongoID format.";
			return $result;
		}
		// Assuming validator says ok.
		$result['equipment'] = $this->dao->getEquipmentById($id);
		
		if(is_null($result['equipment']))
		{
			$result['result'] = false;
			$result['message'] = "ID : " . $id . " not found in equipments collection.";
		}
		else
		{
			$result['result'] = true;
			$result['message'] = "Get equipment successful with id : " . $id;
		}
		
		return $result;
	}
	
	// Returns an array that contains equipment document (on success), result, and message.
	public function getEquipmentByDepartmentTag($departmentTag)
	{
		$result = 
		[
			"equipment" => null,
			"result" => false,
			"message" => null	
		];
		
		// Assuming validator says ok.
		$result['equipment'] = $this->dao->getEquipmentByDepartmentTag($departmentTag);
		
		if(is_null($result['equipment']))
		{
			$result['result'] = false;
			$result['message'] = "Department Tag : " . $departmentTag . " not found in equipments collection.";
		}
		else
		{
			$result['result'] = true;
			$result['message'] = "Get equipment successful with department tag : " . $departmentTag;
		}
		
		return $result;
	}
	
	// Returns all equipment documents.
	public function getAllEquipments()
	{
		return $this->dao->getAllEquipments();
	}
	
	// Returns an array that contains removed equipment document (on success), result, and message.
	public function removeEquipment($id)
	{
		$result = 
		[
			"equipment" => null,
			"result" => false,
			"message" => null	
		];
		
		if(!$this->validator->validateMongoIdString($id))
		{
			$result['message'] = $result['message'] = "ID : " . $id . " is in invalid MongoID format.";
			return $result;
		}
		
		$result['equipment'] = $this->dao->removeEquipment($id);
		
		if(is_null($result['equipment']))
		{
			$result['result'] = false;
			$result['message'] = "ID : " . $id . " not found in equipments collection.";
		}
		else
		{
			$result['result'] = true;
			$result['message'] = "Remove equipment successful with id : " . $id;
		}
		
		return $result;
	}
	
	// Returns an array that contains id (on success), result, and message.
	public function addEquipmentType($document)
	{
		throw new BadMethodCallException('Not implemented.');
	}
	
	// Returns an array that contains id (on success), result, and message.
	public function updateEquipmentType($document)
	{
		throw new BadMethodCallException('Not implemented.');
	}
	
	// Returns an array that contains id of equipment type document(on success), result, and message.
	public function addAttributeToEquipmentTypeById($id, $document)
	{
		throw new BadMethodCallException('Not implemented.');
	}
	
	// Returns an array that contains name of equipment type document(on success), result, and message.
	public function addAttributeToEquipmentTypeByName($name, $document)
	{
		throw new BadMethodCallException('Not implemented.');
	}
	
	// Returns an array that contains id of removed equipment type document(on success), result, and message.
	public function removeAttributeToEquipmentTypeById($id, $document)
	{
		throw new BadMethodCallException('Not implemented.');
	}
	
	// Returns an array that contains name of removed equipment type document(on success), result, and message.
	public function removeAttributeToEquipmentTypeByName($name, $document)
	{
		throw new BadMethodCallException('Not implemented.');
	}
	
	// Returns an array that contains equipment type document (on success), result, and message.
	public function getEquipmentTypeById($id)
	{
		throw new BadMethodCallException('Not implemented.');
	}
	
	// Returns an array that contains equipment type document (on success), result, and message.
	public function getEquipmentTypeByName($name)
	{
		throw new BadMethodCallException('Not implemented.');
	}
	
	// Returns an array that contains id of removed equipment type document (on success), result, and message.
	public function removeEquipmentType($id)
	{
		throw new BadMethodCallException('Not implemented.');
	}
	*/
}