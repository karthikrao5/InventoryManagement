<?php
	require 'Interfaces.php';
	require 'DAOMongoDB.php';
	
	public class CoreService implements IService
	{
		private static IService instance;
		
		public static IService getInstance()
		{
			if(CoreService::$instance == null)
			{
				CoreService::$instance = new CoreService();
			}
			
			return CoreService::$instance;
		}
		
		private IDAO $dao;
		
		private function __construct()
		{
			$this->dao = DAOMongoDB::getInstance();
		}
		
		// Returns an array that contains id (on success), result, and message.
		public function addEquipment($document)
		{
			// Validator not functioning yet.
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