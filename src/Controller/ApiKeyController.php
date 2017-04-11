<?php
namespace App\Controller;


use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Interop\Container\ContainerInterface;

class ApiKeyController extends AbstractController {

	public function __construct(ContainerInterface $c) {
		parent::__construct($c);
	}


	// POST
	public function generateNewKey($request, $response, $args) {

		$body = $request->getParsedBody();

        if(is_null($request)) {
            return $response->write("Invalid request.")->withStatus(400);
        }

		
		$params = $request->getQueryParams();

		if ($body) {
			return $response->withJson($body);
		} else if ($params) {
			return $response->withJson($params);
		}

		// should not reach here.
		return $response->write("No input recieved!")->withStatus(200);
	}

	// GET
	public function getKeyByName($request, $response, $args) {
		$body = $request->getParsedBody();

        if(is_null($request)) {
            return $response->write("Invalid request.")->withStatus(400);
        }

        $params = $request->getQueryParams();

		if ($body) {
			return $response->withJson($body);
		} else if ($params) {
			return $response->withJson($params);
		}

		return $response->write("Something went wrong with getting your API Key.");
	}

}