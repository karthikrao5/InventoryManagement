<?php
	interface IService
	{	
		// Returns an array that contains id (on success), result, and message.
		public function addEquipment(array $document);
		// Returns an array that contains id (on success), result, and message.
		public function updateEquipment(array $document);
		// Returns an array that contains equipment document (on success), result, and message.
		public function getEquipmentById($id);
		// Returns an array that contains equipment document (on success), result, and message.
		public function getEquipmentByDepartmentTag($departmentTag);
		// Returns an array that contains removed id (on success), result, and message.
		public function removeEquipment($id);
		// Returns an array that contains id (on success), result, and message.
		public function addEquipmentType(array $document);
		// Returns an array that contains id (on success), result, and message.
		public function updateEquipmentType(array $document);
		// Returns an array that contains id of equipment type document(on success), result, and message.
		public function addAttributeToEquipmentTypeById($id, array $document);
		// Returns an array that contains name of equipment type document(on success), result, and message.
		public function addAttributeToEquipmentTypeByName($name, array $document);
		// Returns an array that contains id of removed equipment type document(on success), result, and message.
		public function removeAttributeToEquipmentTypeById($id, array $document);
		// Returns an array that contains name of removed equipment type document(on success), result, and message.
		public function removeAttributeToEquipmentTypeByName($name, array $document);
		// Returns an array that contains equipment type document (on success), result, and message.
		public function getEquipmentTypeById($id);
		// Returns an array that contains equipment type document (on success), result, and message.
		public function getEquipmentTypeByName($name);
		// Returns an array that contains id of removed equipment type document (on success), result, and message.
		public function removeEquipmentType($id);
	}
?>