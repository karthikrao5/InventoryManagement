<?php
namespace App\Core;

use App\Core\DAO;
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
		//todo - requestJson validation

		$updated = $this->dao->createEquipment($requestJson);

		return array("ok" => true, "message" => "Successfully created Equipment '".$requestJson['department_tag']."' !",
			'equipment' => $updated);
	}

	public function createEquipmentType($requestJson)
	{
		$returnArray = array('ok' => false, 'msg' => null, 'equipment' => null);
		//todo - requestJson validation
		$result = $this->getEquipmentType(array('name' => $requestJson['equipment_type_name']));

		if(!$result['ok'])
		{
			$returnArray['ok'] = false;
			$returnArray['msg'] = "EquipmentType '".$requestJson['equipment_type_name']."' not found.";
			return $returnArray;
		}

		$updated = $this->dao->createEquipmentType($requestJson, $result['equipment_type'][0]);

		return array("ok" => true, "message" => "Successfully created EquipmentType '".$requestJson['name']."' !",
			'equipment' => $updated);
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
		$result = array('ok' => false, 'msg' => null, 'n' => 0, 'equipment_types' => null);

		$equipmentTypes = $this->dao->getEquipmentType($requestJson);

		if(is_null($equipmentTypes) || empty($equipmentTypes))
		{
			$result['msg'] = "Equipment Type not found with given search criteria.";
			return $result;
		}
		else
		{
			$result['ok'] = true;
			$result['msg'] = "Successfully found Equipment Types.";
			$result['n'] = count($equipmentTypes);
			$result['equipment_types'] = $equipmentTypes;
			return $result;
		}
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
