<?php
namespace App\Controller;


use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Interop\Container\ContainerInterface;
use \Firebase\JWT\JWT;


class AuthController extends AbstractController {

	private $authValidator;

	public function __construct(ContainerInterface $c) {
		parent::__construct($c);
		$this->authValidator = $this->ci->get('AuthValidator');
	}

	/**
	 * /auth GET
	 *	Function returns JWY token with user information
	 * @return JWT string
	 */
	public function authorize($request, $response) {
		// if request does not have isHook, assume it is a regular 
		// single user accessing the API

		$arr = array("isHook"=>false, "hookname"=>null, "username"=>null);
		$body = $request->getParsedBody();

		if (!$body) {
			// if body is not there, generate user token and return
			$token = $this->authValidator->getAuthToken();
			return $response->withJson($token);
		} else {
			if ($body["isHook"] && $body["hook_name"]) {
				$arr["isHook"] = true;
				$arr["hook_name"] = $body["hook_name"];
				if($body["user_name"]) {
					$arr["user_name"] = $body["user_name"];
				}

				// pass in array of 3 things for hook. if all 3 are unset,
				// assume user is authenticating and access apache env var
				// for CAS information

				// isHook : boolean
				// hookame : string
				// username : optional
				$token = $this->authValidator->getAuthToken($arr);
				// $returnArray = array("jwt"=> json_encode($token));
				return $response->withJson($token);
			}
		}

		// should not get here
		return $response->write("something went wrong")->withStatus(404);

		
	}


	// Use this to test decoding JWT
	public function testDecode($request, $response) {
		$body = $request->getParsedBody();

		$val = $this->authValidator->decodeToken($body['jwt']);

		if ($val["status"] == 401) {
			return $response->write($val["msg"])->withStatus($val["status"]);
		}
		if ($val["ok"]) {
			// return $response->withJson($val["msg"]);
			return $response->withJson($val["data"]);
		} else {
			return $response->withJson($val["msg"])->withStatus($val["status"]);
		}
	}
}
