<?php
namespace App\Core;
use Interop\Container\ContainerInterface;
use \Firebase\JWT\JWT;



class Validator
{
	private $c;
	private $logger;
	private $core;
	private $settings;


	public function __construct(ContainerInterface $ci) {
		$this->c = $ci;
		$this->logger = $ci->get('logger');
		$this->core = $ci->get("core");
		$this->settings = $ci->get("settings");
	}

	private function getTokenFromHeader($authHeader) {
		if(!$authHeader) {
			return null;
		}
		$token = str_replace("Bearer ", "", $authHeader[0]);
		return $token;
	}

	public function isRenter($userArray) {
		// if user is not CAS group or hook, they are renter
		if ($userArray["group"] != $this->settings["CAS-group-name"]
			|| !$userArray["hookname"]) {
			return true;
		}
		return false;
	}

	public function isAdminOrHook($userArray) {
		// if user is CAS group or hook
		if ($userArray["group"] == $this->settings["CAS-group-name"]
			|| $userArray["hookname"]) {
			return true;
		}
		return false;
	}

	/**
	 * Check apache environment variable for authenticated user
	 * @return CAS user group as array("user", "email", "group")
	 */
	public function getCASUser() {

		// // if the authenticated user is part of the CAS user gruup
		// // give authorization

		// $envArray = $_SERVER;
		// foreach ($envArray as $key=>$value) {
		// 	if ($value == $settings['CAS-group-name']) {
		// 		$userArray["user"] = $envArray["REMOTE_USER"];
		// 		$$userArray["user_email"] = $envArray["REMOTE_USER_EMAIL"];
		// 	}
		// }


		// ====================test code=======================
		$userArray = null;

		if($this->settings["test-renter"]) {
			// test renter
			// $userArray["username"] = "bkang61";
			// $userArray["email"] = "bkang61@gatech.edu";
			// $userArray["group"] = null;

			// not in users (aka forbidden user)
			$userArray["username"] = "krao34";
			$userArray["email"] = "krao34@gatech.edu";
			$userArray["group"] = null;

		} else {
			// test IT admin (Not in Users table, but has CAS group for itadmin)
			$userArray["username"] = "krao34";
			$userArray["email"] = "krao34@gmail.com";
			$userArray["group"] = $this->settings["CAS-group-name"];
		}
		
		// ====================================================
		return $userArray;
	}

	/**
	 * @param PHP array: ["isHook", "hookname", "username"]
	 * @return array with ok signal, message and data
	 */
	public function getAuthToken($hookInfo=null) {

		if ($hookInfo["isHook"]) {
			// generate a token whose body is hookname and email address
			return $this->generateTokenForHook($hookInfo);
		} else {
			// Get authenticated user
			$userArray = $this->getCASUser();

			if ($userArray["group"] == $this->settings["CAS-group-name"]) {

				$this->logger->info("Created token for: ". $this->settings["CAS-group-name"] . "with username: " . $userArray["username"].".");

				$token = $this->generateTokenForUser($userArray);

				return array("ok"=>true,"msg"=>"Successfully created token for".$this->settings["CAS-group-name"].".", "jwt"=>$token);
			}

			// check if authenticated user is a renter
			$checkUser = $this->core->getUser(array("username" => $userArray["username"]));

			$this->logger->debug("Checking for user: ".$userArray["username"].".");
			$this->logger->debug("CoreService getUser returned: ".$checkUser["users"][0].".");


			// if authenticated user is in user collection (a renter), authorize
			if($checkUser["users"][0]["username"] == $userArray["username"]) {
				$token = $this->generateTokenForUser($userArray);

				$this->logger->info("Created token for user: ".$userArray["username"].".");
				return array("ok"=>true, "msg"=>"Successfully created token.", "jwt"=>$token);
			} else {

				$this->logger->info("User: ".$userArray["username"]." not found in User collection.");
				return array("ok"=>false, "msg"=>"User not in database. Please contact your IT admin to be registered with the service.", "jwt"=>null);
			}
		}

		// should not get here
		return null;
	}

	/**
	 * @param request Header array
	 * @return array(isHook, hook_name, user_name) otherwise return error triplet:
	 *					array("ok"=>boolean, "msg"=>somemessage, "status"=>HTTP code)
	 */
	public function decodeToken($authHeader) {

		// get JWT string from header
		$token = $this->getTokenFromHeader($authHeader);

		if(!$token) {
			return array("ok"=>false, "msg"=>"no token recieved.", "status"=>404);
		}

		$secretKey = $this->settings["jwtSecretKey"];
		$encryptionAlgo = $this->settings['encryptionAlgo'];
		$jwt = null;

		try {
			$jwt = JWT::decode($token, $secretKey, array($encryptionAlgo));

		} catch(\Firebase\JWT\ExpiredException $e) {
			return array("ok"=>false, "msg"=>"expired token", "status"=>403);
		} catch(\Firebase\JWT\SignatureInvalidException $e) {
			return array("ok"=>false, "msg"=>"token tampered with. notifying...","status"=>403);
		} catch (\Firebase\JWT\UnexpectedValueException $e) {
			return array("ok"=>false, "msg"=>"invalid token", "status"=>403);
		} catch (\Firebase\JWT\BeforeValidException $e) {
			return array("ok"=>false, "msg"=>"token not yet valid. try again in a few seconds", "status"=>403);
		}

		$returnArray = null;
		
		// $data = json_decode($jwt->data, true);

		return array("ok"=>true, "msg"=>$jwt->data);
// 
		if ($jwt->data->hookname) {
			$this->logger->debug("Decoding token for hook ".$jwt->data->hookname.".");
			$returnArray["isHook"] = true;
			$returnArray["hookname"] = $jwt->data->hookname;
			$returnArray["username"] = $jwt->data->username;
		
			$this->logger->debug("Decoding token for hook ".$jwt->data->hookname.".");
			$result = array("ok"=>true, "msg"=>"Successful hook token decoded.", "data"=>$returnArray);
			return $result;
		} else {
			if($jwt->data->username) {

				$return = array("user"=>$jwt->data->username, 
							    "email"=>$jwt->data->email,
							    "group"=>$jwt->data->group);

				$this->logger->debug("Decoding token for user ".$jwt->data->username.".");
				return array("ok"=>true, "msg"=>"Successful user token decoded.", "data"=>$return);
			} else {
				// should not get down here. make sure of it. still untested (april 17th 2017)
				return array("ok"=>false,"msg"=>"something went wrong with decode", "data"=>null, "status"=>500);
			}
			
		}
	}

	/**
	 * @param PHP array of ["hookname", "username"]
	 * @return JWT token string
	 */
	public function generateTokenForHook($data) {

		$username = null;
		if($data["username"]) {
			$username = $data["username"];
		}

		$encryptionAlgo = $this->settings['encryptionAlgo'];

		$tokenID 	= $data['hookname'];	 
		$issuedAt   = time();											// current time
		$notBefore  = $issuedAt + 1;		 							// Token valid after 1 seconds
		$expire     = $notBefore + $this->settings["token-expiration-time"];	// expires after 5 minutes
		$serverName = $this->settings['serverName']; 							// Retrieve the server name from config file

		$tokenArray  = [
				'iat' => time(),
				'iss' => $serverName,
				'nbf' => $notBefore,
				'exp' => $expire,
				'data'=> [
						'hookname' => $data['hookname'],
						'username' => $username
					]
			];

		$generatedToken = JWT::encode(
				$tokenArray,
				$this->settings['jwtSecretKey'],
				$encryptionAlgo
			);
		$this->logger->info("Generating JWT for HOOK: ".$data["hookname"].". Token: ".$generatedToken);
		$return = $generatedToken;
		return $return;
	}

	/**
	 * @param PHP array of ["username", "email", "group"]
	 * @return JWT token string
	 */
	public function generateTokenForUser($userArray) {

		// userArray will have three fields
		// 1. username
		// 2. email
		// 3. group
		
		$encryptionAlgo = $this->settings['encryptionAlgo'];

		$tokenID 	= $userArray['username'];	 
		$issuedAt   = time();				 							// current time
		$notBefore  = $issuedAt + 1;		 							// Token valid after 1 seconds
		$expire     = $notBefore + $this->settings["token-expiration-time"];  // expires after 5 minutes (default) Edit in settings.php
		$serverName = $this->settings['serverName']; 							// Retrieve the server name from config file

		$tokenArray  = [
				'iat' => time(),
				'iss' => $serverName,
				'nbf' => $notBefore,
				'exp' => $expire,
				'data'=> [
						'username' => $userArray['username'],
						'email' => $userArray['email'],
						'group' => $userArray['group']
					]
			];

		$generatedToken = JWT::encode(
				$tokenArray,
				$this->settings['jwtSecretKey'],
				$encryptionAlgo
			);
		$this->logger->info("Generating JWT for USER: ".$userArray["username"].". Of group: ". $userArray["group"]. ". Token: ".$generatedToken);

		$return = $generatedToken;
		return $return;
	}

}