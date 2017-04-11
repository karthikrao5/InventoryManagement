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
			$envArray = $_SERVER;

			$userGroups = array("cos-renter" => "renter",
								'cos-it-admin' => "it-admin",
								'cos-sys-admin' => 'sys-admin',
								"cos-prop-coord" => 'prop-coord');

			$authenticatedUser = $envArray["REMOTE_USER"];
			$userEmail = $envArray["REMOTE_USER_EMAIL"];

			// go thru each user group and find one for on of our groups
			// and set local var for it
			foreach ($envArray as $key=>$value) {
				if ($value == "some-user-group") {
					$userType = $userGroups[$value];
				}
			}

			// call dao function to check if this use exists in our
			// db. If not, create one.
			// if user exists, set php global var for this user and user type

			return array("user"=>$authenticatedUser, "user_email"=>$userEmail,
						 "user_type"=>$userType);
		}


		public function checkKey($someKey) {
			// call Dao function to check API collection for this key. if it exists,
			// return true. otherwise false
			return false;
		}

		public function isAccessible($userType, $functionName) {
			// check if the user type can access this function name.
			if ($userType == "renter") {
				return false;
			} else if ($userType == "it-admin") {
				return false;
			} else if ($userType = "sys-admin") {
				return false;
			} else if ($userType == "prop-coord") {
				return false;
			}
			return false;
		}
	}
?>