<?php
	require 'Interfaces.php';
	
	public class Validator implements IValidator
	{
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