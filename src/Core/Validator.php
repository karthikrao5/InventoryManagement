<?php
namespace App\Core;
use Interop\Container\ContainerInterface;
use \Firebase\JWT\JWT;



class Validator
{
	private $c;
	private $logger;


	public function __construct(ContainerInterface $ci) {
		$this->c = $ci;
		$this->logger = $ci->get('logger');
	}

	public function getCASUser() {
		return array("user"=>"krao34", "user_email"=>"krao34@gmail.com");
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
	 * @return array(isHook, hook_name, user_name) otherwise return error triplet:
	 *					array("ok"=>boolean, "msg"=>somemessage, "status"=>HTTP code)
	 */
	public function decodeToken($token) {

		if(!$token) {
			return array("ok"=>false, "msg"=>"no token recieved.", "status"=>404);
		}


		$settings = $this->c->get('settings');

		$secretKey = $settings["jwtSecretKey"];
		$encryptionAlgo = $settings['encryptionAlgo'];
		$jwt = null;

		try {
			$jwt = JWT::decode($token, $secretKey, array($encryptionAlgo));

		} catch(\Firebase\JWT\ExpiredException $e) {
			return array("ok"=>false, "msg"=>"expired token", "status"=>401);
		} catch(\Firebase\JWT\SignatureInvalidException $e) {
			return array("ok"=>false, "msg"=>"token tampered with. notifying...","status"=>401);
		} catch (\Firebase\JWT\UnexpectedValueException $e) {
			return array("ok"=>false, "msg"=>"invalid token", "status"=>401);
		} catch (\Firebase\JWT\BeforeValidException $e) {
			return array("ok"=>false, "msg"=>"token not yet valid. try again in a few seconds", "status"=>401);
		}

		$returnArray = null;
		
		// $data = json_decode($jwt->data, true);

		// return array("ok"=>true, "msg"=>$jwt->data, "status"=>200);
// 
		if ($jwt->data->hook_name) {
			$this->logger->debug("Decoding token for hook ".$jwt->data->hook_name.".");
			$returnArray["isHook"] = true;
			$returnArray["hook_name"] = $jwt->data->hook_name;
			$returnArray["user_name"] = $jwt->data->user_name;
		
			$result = array("ok"=>true, "msg"=>"Successful hook token decoded.", "data"=>$returnArray, "status"=>200);
			return $result;
		} else {
			if($jwt->data->user_name) {
				$this->logger->debug("Decoding token for user ".$jwt->data->user_name.".");

				$return = array("user"=>$jwt->data->user_name, 
							    "user_email"=>$jwt->data->user_email);

				return array("ok"=>true, "msg"=>"Successful user token decoded.", "data"=>$return, "status"=>200);
			} else {
				// should not get down here. make sure of it. still untested (april 17th 2017)
				return array("ok"=>false,"msg"=>"something went wrong with decode", "status"=>404);
			}
			
		}
	}

	/**
	 * @return JSON with key "jwt" and value with the jwt string
	 */
	public function generateTokenForHook($data) {
		$settings = $this->c->get('settings');

		$username = null;
		if($data["user_name"]) {
			$username = $data["user_name"];
		}

		$encryptionAlgo = $settings['encryptionAlgo'];

		$tokenID 	= $data['hook_name'];	 
		$issuedAt   = time();				 // current time
		$notBefore  = $issuedAt + 1;		 // Token valid after 1 seconds
		$expire     = $notBefore + 300;      // expires after 5 minutes
		$serverName = $settings['serverName']; // Retrieve the server name from config file

		$tokenArray  = [
				'iat' => time(),
				'iss' => $serverName,
				'nbf' => $notBefore,
				'exp' => $expire,
				'data'=> [
						'hook_name' => $data['hook_name'],
						'user_name' => $username
					]
			];

		$generatedToken = JWT::encode(
				$tokenArray,
				$settings['jwtSecretKey'],
				$encryptionAlgo
			);
		$this->logger->info("Generating JWT for HOOK: ".$data["hook_name"].". Token: ".$generatedToken);
		$return = ['jwt' => $generatedToken];
		return $return;
	}

	/**
	 * @return JSON with key "jwt" and value with the jwt string
	 */
	public function generateTokenForUser() {
		// gets authenticated user from CAS (apache env variable)
		// $userArray = $this->getAuthUser();
		$settings = $this->c->get('settings');
		$userArray = null;
		// $envArray = $_SERVER;
		

		// // if the authenticated user is part of the CAS user gruup
		// // give authorization
		// foreach ($envArray as $key=>$value) {
		// 	if ($value == $settings['CAS-group-name']) {
		// 		$userArray["user"] = $envArray["REMOTE_USER"];
		// 		$$userArray["user_email"] = $envArray["REMOTE_USER_EMAIL"];
		// 	}
		// }
		// ====================test code=======================

		$userArray["user"] = "krao34";
		$userArray["user_email"] = "krao34@gmail.com";
		// ====================================================

		$encryptionAlgo = $settings['encryptionAlgo'];

		$tokenID 	= $userArray['user'];	 
		$issuedAt   = time();				 							// current time
		$notBefore  = $issuedAt + 1;		 							// Token valid after 1 seconds
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
		$this->logger->info("Generating JWT for USER: ".$userArray["user"].". Token: ".$generatedToken);
		$return = ['jwt' => $generatedToken];
		return $return;
	}

}