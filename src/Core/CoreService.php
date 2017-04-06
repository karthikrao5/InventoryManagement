<?php
namespace App\Core;

use App\Core\DAO;
use App\Core\Models\Attribute;
use App\Core\Models\Equipment;
use App\Core\Models\EquipmentType;
use App\Core\Models\EquipmentTypeAttribute;

use Interop\Container\ContainerInterface;


class CoreService
{
	private $dao;
	private $logger;
	private $container;

	public function __construct(ContainerInterface $c)
	{
		$this->dao = new DAO();
		$this->container = $c;
		$this->logger = $c->get("logger");
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
		$equipment->setComments($requestJson['comment']);

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

		$isDbSuccess = False;

		// if there is a search criteria, validate the fields and generate
		// the search array that mongoclient can recognize
		if ($requestJson) {
			$searchCriteriaArr = array();

			if ($requestJson["department_tag"]) {
				$searchCriteriaArr["departmentTag"] = $requestJson["department_tag"];
			} else if($requestJson["gt_tag"]) {
				$searchCriteriaArr["gtTag"] = $requestJson["gt_tag"];
			} else if($requestJson["equipment_td"]) {
				$searchCriteriaArr["equipmentId"] = $requestJson["equipment_td"];
			} else if($requestJson["equipment_type_name"]) {
				$searchCriteriaArr["equipmentTypeName"] = $requestJson["equipment_type_name"];
			} else if($requestJson["status"]) {
				$searchCriteriaArr["status"] = $requestJson["status"];
			};
			$equipments = $this->dao->getEquipment($searchCriteriaArr);
			if ($equipmentTypes) {
				$isDbSuccess = True;
			} else {
				$isDbSuccess = False;
			}

		// if no search array is given, return all equipments
		} else {
			//dev purpose code
			$equipments = $this->dao->getEquipment();
			if ($equipments) {
				$isDbSuccess = True;
			} else {
				$isDbSuccess = False;
			}
		}

		if ($isDbSuccess) {
			//$this->logger->debug("Equipment was successfully returned from DAO.php.");
			//$this->logger->error("Test");
			//$this->logger->info("test");
			$result = array("ok" => true, "msg" => "Success getting equipment", "equipments" => $equipments);
		} else {
			//$this->logger->error("Equipment was not successfully called from DAO");
			$result = array("ok" => false, "msg" => "Get equipment was not successful.", "equipments" => $equipments);
		}
		return $result;
	}


	public function getEquipmentType($requestJson=NULL)
	{
		$equipmentTypes = null;
		$isDbSuccess = False;

		if($requestJson) {
			$searchCriteriaArr = array();

			// validation of id doesnt work
			if ($requestJson["_id"]) {
				$searchCriteriaArr["_id"] = $requestJson["id"];
			} else if($requestJson["name"]) {
				$searchCriteriaArr["name"] = $requestJson["name"];
			}

			$equipmentTypes = $this->dao->getEquipmentType($searchCriteriaArr);
			if ($equipmentTypes) {
				$isDbSuccess = True;
			} else {
				$isDbSuccess = False;
			}
		} else {
			$equipmentTypes = $this->dao->getEquipmentType(null);
			if ($equipmentTypes) {
				$isDbSuccess = True;
			} else {
				$isDbSuccess = False;
			}
		}

		if ($isDbSuccess) {
			$result = array("ok" => true, "msg" => "Success getting equipment type!.", "equipment_types" => $equipmentTypes);
		} else {
			$result = array("ok" => false, "msg" => "Get equipment type was not successful.", "equipment_types" => array());
		}

		return $result;
	}

	public function deleteEquipment($requestJson)
	{
		$result = array("ok" => false, "msg" => null);

		//send in Mongoids for now

		if(is_null($requestJson) || empty($requestJson))
		{
			$result['msg'] = "Json is empty or null.";
			return $result;
		}

		$daoResult = $this->dao->deleteEquipment($requestJson['ids']);

		$result['ok'] = $daoResult['ok'];
		$result['n'] = $daoResult['n'];

		return $result;
	}

	public function deleteEquipmentType($requestJson)
	{
		$result = array("ok" => false, "msg" => null);

		//send in Mongoids for now

		if(is_null($requestJson) || empty($requestJson))
		{
			$result['msg'] = "Json is empty or null.";
			return $result;
		}

		$daoResult = $this->dao->deleteEquipmentType($requestJson['ids']);

		$result['ok'] = $daoResult['ok'];
		$result['n'] = $daoResult['n'];

		return $result;
	}
}
