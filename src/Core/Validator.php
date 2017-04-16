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
	public function getAuthToken($input=null) {
		if ($input["isHook"]) {
			// generate a token whose body is hookname and email address
			return $this->generateTokenForHook($input);
		} else {
			// input is not a hook, generate regular user token
			return $this->generateTokenForUser();
		}

		// should not get here
		return null;
	}

	/**
	 * @return array(isHook, hookname, username) otherwise return 
	 */
	public function decodeToken($token) {


		$settings = $this->c->get('settings');

		$secretKey = $settings["jwtSecretKey"];
		$encryptionAlgo = $settings['encryptionAlgo'];

		try {
			$jwt = JWT::decode($token, $secretKey, array($encryptionAlgo));
			return json_encode($jwt);
		} catch(\Firebase\JWT\JWT\ExpiredException $e) {
			return $response->write("Expired Token")->withStatus(401);
		}
	}

	/**
	 * @return JSON with key "jwt" and value with the jwt string
	 */
	public function generateTokenForHook($data) {
		$settings = $this->get('settings');

		$encryptionAlgo = $settings['encryptionAlgo'];

		$tokenID 	= $data['hookname'];	 
		$issuedAt   = time();				 // current time
		$notBefore  = $issuedAt + 10;		 // Token valid after 10 seconds
		$expire     = $notBefore + 300;      // expires after 5 minutes
		$serverName = $settings['serverName']; // Retrieve the server name from config file

		$tokenArray  = [
				'iat' => time(),
				'iss' => $serverName,
				'nbf' => $notBefore,
				'exp' => $expire,
				'data'=> [
						'hookname' => $data['hookname'],
						'username' => $data['username']
					]
			];

		$generatedToken = JWT::encode(
				$tokenArray,
				$settings['jwtSecretKey'],
				$encryptionAlgo
			);

		$return = ['jwt' => $generatedToken];
		return $return;
	}

	/**
	 * @return JSON with key "jwt" and value with the jwt string
	 */
	public function generateTokenForUser() {
		// gets authenticated user from CAS (apache env variable)
		// $userArray = $this->getAuthUser();
		$userArray = null;
		$envArray = $_SERVER;
		$settings = $this->get('settings');

		// if the authenticated user is part of the CAS user gruup
		// give authorization
		foreach ($envArray as $key=>$value) {
			if ($value == $settings['CAS-group-name']) {
				$userArray["user"] = $envArray["REMOTE_USER"];
				$$userArray["user_email"] = $envArray["REMOTE_USER_EMAIL"];
			}
		}

		$encryptionAlgo = $settings['encryptionAlgo'];

		$tokenID 	= $userArray['user'];	 
		$issuedAt   = time();				 							// current time
		$notBefore  = $issuedAt + 10;		 							// Token valid after 10 seconds
		$expire     = $notBefore + $settings["token-expiration-time"];  // expires after 5 minutes (default) Edit in settings.php
		$serverName = $settings['serverName']; 							// Retrieve the server name from config file

		$tokenArray  = [
				'iat' => time(),
				'iss' => $serverName,
				'nbf' => $notBefore,
				'exp' => $expire,
				'data'=> [
						'user_name' => $userArray['user'],
						'user_email' => $userArray['user_email']
					]
			];

		$generatedToken = JWT::encode(
				$tokenArray,
				$settings['jwtSecretKey'],
				$encryptionAlgo
			);

		$return = ['jwt' => $generatedToken];
		return $return;
	}

}