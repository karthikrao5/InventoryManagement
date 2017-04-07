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
		$returnArray = array('ok' => false, 'msg' => null, 'equipment' => null);
		//todo - requestJson validation
		$result = $this->getEquipmentType(array('name' => $requestJson['equipment_type_name']));

		if(!$result['ok'])
		{
			$returnArray['ok'] = false;
			$returnArray['msg'] = "EquipmentType '".$requestJson['equipment_type_name']."' not found.";
			return $returnArray;
		}

		$updated = $this->dao->createEquipment($requestJson, $result['equipment_types'][0]);

		return array("ok" => true, "message" => "Successfully created Equipment '".$requestJson['department_tag']."' !",
			'equipment' => $updated);
	}

	public function createEquipmentType($requestJson)
	{
		$updated = $this->dao->createEquipmentType($requestJson, $result['equipment_type'][0]);

		return array("ok" => true, "message" => "Successfully created EquipmentType '".$requestJson['name']."' !",
			'equipment_type' => $updated);
	}

	public function getEquipment($requestJson=NULL)
	{
		$result = array('ok' => false, 'msg' => null, 'n' => 0, 'equipments' => null);

		$equipments = $this->dao->getEquipment($requestJson);

		if(is_null($equipments) || empty($equipments))
		{
			$result['msg'] = "Equipment not found with given search criteria.";
			return $result;
		}
		else
		{
			$result['ok'] = true;
			$result['msg'] = "Successfully found Equipments.";
			$result['n'] = count($equipments);
			$result['equipments'] = $equipments;
			return $result;
		}
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
