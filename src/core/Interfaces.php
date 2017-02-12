<?php
    namespace App\core;
	// This file contains all interfaces used in core.
	
	// This is Service object interface.
	// This should be the only functions that non-core scripts should use.
	// More functions will be added as project progresses and function signatures will rarely modified/removed.
	// Using objects other than implementing this inteface can break your code easily and it is your responsibility to fix them.
	interface IService
	{	
		// Returns an array that contains id (on success), result, and message.
		public function addEquipment($document);
		// Returns an array that contains id (on success), result, and message.
		public function updateEquipment($document);
		// Returns an array that contains equipment document (on success), result, and message.
		public function getEquipmentById($id);
		// Returns an array that contains equipment document (on success), result, and message.
		public function getEquipmentByDepartmentTag($departmentTag);
		// Returns an array that contains removed id (on success), result, and message.
		public function removeEquipment($id);
		// Returns an array that contains id (on success), result, and message.
		public function addEquipmentType($document);
		// Returns an array that contains id (on success), result, and message.
		public function updateEquipmentType($document);
		// Returns an array that contains id of equipment type document(on success), result, and message.
		public function addAttributeToEquipmentTypeById($id, $document);
		// Returns an array that contains name of equipment type document(on success), result, and message.
		public function addAttributeToEquipmentTypeByName($name, $document);
		// Returns an array that contains id of removed equipment type document(on success), result, and message.
		public function removeAttributeToEquipmentTypeById($id, $document);
		// Returns an array that contains name of removed equipment type document(on success), result, and message.
		public function removeAttributeToEquipmentTypeByName($name, $document);
		// Returns an array that contains equipment type document (on success), result, and message.
		public function getEquipmentTypeById($id);
		// Returns an array that contains equipment type document (on success), result, and message.
		public function getEquipmentTypeByName($name);
		// Returns an array that contains id of removed equipment type document (on success), result, and message.
		public function removeEquipmentType($id);
	}
	
	// This is DAO (Data Access Object) interface.
    // It interfaces actual database module and service module.
    // Service module can use this interface to access database.
	// These functions should not be called directly by non-core modules.
	// Functions defined in this interface will often be removed and modified. It is your responsibility to fix if anything breaks.
    interface IDAO
    {
        public function createEquipment($document); // Returns id of equipment document.
		public function updateEquipment($document); // Returns id of equipment document.
		public function getEquipmentById($id); // Returns equipment document.
		public function removeEquipment($id); // Returns result array.
    }
	
	// This is Validator interface.
	// These functions should not be called directly by non-core modules.
	// Functions defined in this interface will often be removed and modified. It is your responsibility to fix if anything breaks.
	interface IValidator
	{
		public function validateCreateEquipment($document);
		public function validateUpdateEquipment($document);
	}
?>