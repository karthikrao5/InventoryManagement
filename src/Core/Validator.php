<?php
namespace App\Core;
use Interop\Container\ContainerInterface;
use \Firebase\JWT\JWT;



class Validator
{
	private $c;

	public function __construct(ContainerInterface $ci) {
		$this->c = $ci;
	}

	// TODO User authentication through apache env variable
	public function getAuthUser() {
		// TODO return current authenticated user
		// $envArray = $_SERVER;

		// $userGroups = array("cos-renter" => "renter",
		// 					'cos-it-admin' => "it-admin",
		// 					'cos-sys-admin' => 'sys-admin',
		// 					"cos-prop-coord" => 'prop-coord');

		// $authenticatedUser = $envArray["REMOTE_USER"];
		// $userEmail = $envArray["REMOTE_USER_EMAIL"];

		// // go thru each user group and find one for on of our groups
		// // and set local var for it
		// foreach ($envArray as $key=>$value) {
		// 	if ($value == "some-user-group") {
		// 		$userType = $userGroups[$value];
		// 	}
		// }

		// call dao function to check if this use exists in our
		// db. If not, create one.
		// if user exists, set php global var for this user and user type

		// return array("user"=>$authenticatedUser, "user_email"=>$userEmail,
		// 			 "user_type"=>$userType);
		return array("user"=>'krao34', "user_email"=>'krao34@gatech.edu',
					 "user_type"=>'it-admin');
	}

	public function authenticateToken($token) {

		$settings = $this->c->get('settings');

		$secretKey = $settings["jwtSecretKey"];
		$encryptionAlgo = $settings['encryptionAlgo'];

		$jwt = JWT::decode($token, $secretKey, array($encryptionAlgo));

		return json_encode($jwt);
	}

	public function generateTokenForUser() {
		// gets authenticated user from CAS (apache env variable)
		$userArray = $this->getAuthUser();
		$encryptionAlgo = $this->c->get('settings')['encryptionAlgo'];

		$tokenID 	= $userArray['user'];
		$issuedAt   = time();
		$notBefore  = $issuedAt + 10;		// Token valid after 10 seconds
		$expire     = $notBefore + 300;      // Adding 5 mins
		$serverName = $this->c->get('settings')['serverName']; // Retrieve the server name from config file

		$tokenArray  = [
				'iat' => time(),
				'iss' => $serverName,
				'nbf' => $notBefore,
				'exp' => $expire,
				'data'=> [
						'userName' => $userArray['user']
					]
			];

		$generatedToken = JWT::encode(
				$tokenArray,
				$this->c->get('settings')['jwtSecretKey'],
				$encryptionAlgo
			);

		$return = ['jwt' => $generatedToken];
		return $return;
	}
}