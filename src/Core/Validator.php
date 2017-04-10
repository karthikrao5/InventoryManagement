<?php
    namespace App\Core;
	
	class Validator
	{
		private $c;

		public funciton __construct($ci) {
			$this->c = $ci;
		}
		// TODO User authentication through apache env variable

		public function getAuthUser() {
			// TODO return current authenticated user
		}


		public function checkKey($someKey) {
			return false;
		}
	}
?>