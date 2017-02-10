<?php
    // This is DAO (Data Access Object).
    // It interfaces actual database module and service module.
    // Service module can use this interface to access database.
    
    interface IDAO
    {
        public static function getInstance(); // Returns singleton instance of the DAO.
        public function createEquipment($document); // Returns id of equipment document.
		public function updateEquipment($document); // Returns id of equipment document.
		public function getEquipmentById($id); // Returns equipment document.
		public function removeEquipment($id); // Returns result array.
    }
?>