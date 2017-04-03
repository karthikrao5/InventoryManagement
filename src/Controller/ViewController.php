<?php

namespace App\Controller;

use Slim\Views\Twig;
use App\Models\Equipment;
use App\Models\EquipmentType;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Interop\Container\ContainerInterface;
use Doctrine\ODM\MongoDB\DocumentManager;

class ViewController extends AbstractController {

	private $view;

	public function __construct(ContainerInterface $c) {
        parent::__construct($c);
        $this->view = $this->ci->get('view');
    }

	public function index($request, $response) {
		$result = $this->core->getEquipment();
		return $this->view->render($response, "index.twig", array("data"=> $result));
	}

	// GET
	public function getEquipmentForm($request, $response) {
		return $this->view->render($response, "addEquipment.twig");
	}

	// POST
	public function postEquipmentForm($request, $response, $args) {
		$body = $request->getParsedBody();

		foreach ($body as $key => $value) {
			print_r($key.", ".$value);
			print_r("\n==============\n");
		}

		// print_r($body);
		// return null;
		$this->logger->info("Posting Equipment to db", array("name"=>$body["department_tag"]));
		return $this->view->render($response, "success.twig", array("ok" => "true", "msg" => "successfully entered equipment!"));
	}

	// GET
	public function getEquipmentTypeForm($request, $response) {
		return $this->view->render($response, "addEquipmentType.twig");
	}

	// POST
	public function postEquipmentTypeForm($request, $response, $args) {
		$body = $request->getParsedBody();
		return null;
	}

}