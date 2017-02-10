<?php
	require_once 'Interfaces.php';
	require_once 'DAOMongoDB.php';
	require_once 'Validator.php';
	
	class CoreService implements IService
	{
		private static $instance;
		
		public static function getInstance()
		{
			if(CoreService::$instance == null)
			{
				CoreService::$instance = new CoreService();
			}
			
			return CoreService::$instance;
		}
		
		private $dao;
		private $validator;
		
		private function __construct()
		{
			$this->dao = DAOMongoDB::getInstance();
			$this->validator = new Validator($this->dao);
		}
		
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
			throw new BadMethodCallException('Not implemented.');
		}
		
		// Returns an array that contains equipment document (on success), result, and message.
		public function getEquipmentById($id)
		{
			throw new BadMethodCallException('Not implemented.');
		}
		
		// Returns an array that contains equipment document (on success), result, and message.
		public function getEquipmentByDepartmentTag($departmentTag)
		{
			throw new BadMethodCallException('Not implemented.');
		}
		
		// Returns an array that contains removed id (on success), result, and message.
		public function removeEquipment($id)
		{
			throw new BadMethodCallException('Not implemented.');
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
	}
?>