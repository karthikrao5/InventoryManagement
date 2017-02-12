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
	}
?>