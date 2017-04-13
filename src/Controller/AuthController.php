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
	 *	Function returns JWY token with user information
	 * @return JWT string
	 */
	public function authorize($request, $response) {
		$token = $this->authValidator->generateTokenForUser();
		return json_encode($token);
	}


	// Use this to test decoding JWT
	public function testDecode($request, $response) {
		$body = $request->getParsedBody();

		$val = $this->authValidator->authenticateToken($body['jwt']);

		return json_encode(['decodedVal' => $val]);
	}

}