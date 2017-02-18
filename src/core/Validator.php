<?php
    namespace App\core;
	
	class Validator implements IValidator
	{
		private $dao;
		
		public function __constructor(IDAO $dao)
		{
			$this->dao = $dao;
		}
		
		public function validateCreateEquipment($document)
		{
			return true;
		}
		
		public function validateUpdateEquipment($document)
		{
			throw new BadMethodCallException('Not implemented.');
		}
        
        // MongoId string must be 24 characters long.
        public function validateMongoIdString($id)
        {
            return preg_match('/^[0-9a-zA-Z]{24}$/', $id) == 1;
        }
	}
?>