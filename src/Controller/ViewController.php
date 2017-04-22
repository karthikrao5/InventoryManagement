<?php

namespace App\Controller;

use Slim\Views\Twig;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Interop\Container\ContainerInterface;

class ViewController extends AbstractController {

	public function __construct(ContainerInterface $c) {
        parent::__construct($c);
    }

    // GET
	// this one works. checked by karthik 04/03/17
	public function index($request, $response) {
		// return $this->view->render($response, "hp.html");
		$array = $this->core->getEquipment();
		return $this->view->render($response, "index.twig", array("data"=> $array["equipments"]));
	}

	// GET
	public function getEquipmentForm($request, $response) {
		$fields = array("department_tag" => "text",
					 	"gt_tag" => "text",
					 	"equipment_type_name" => "text",
					 	"status" => "text",
					 	"comment" => "text"
					 	);
		return $this->view->render($response, "addEquipment.twig", array("fields" => $fields));
	}

	// POST
	public function postEquipmentForm($request, $response, $args) {
		$body = $request->getParsedBody();

		// foreach ($body as $key => $value) {
		// 	print_r($key.$value);
		// }
		// $this->logger->debug("debug log");
		print_r(json_encode($body));
		return null;

		// print_r($body);
		// return null;
		// $this->logger->info("Posting Equipment to db", array("name"=>$body["department_tag"]));
		// return $this->view->render($response, "success.twig", array("ok" => "true", "msg" => "successfully entered equipment!"));
	}

	// GET
	public function getEquipmentTypeForm($request, $response) {
		$fields = array("name" => "text");
		return $this->view->render($response, "addEquipmentType.twig", array("fields"=>$fields));
	}

	// POST
	public function postEquipmentTypeForm($request, $response, $args) {
		$body = $request->getParsedBody();
		return null;
	}

	public function showAllEquipmentTypes($request, $response) {
		$array = $this->core->getEquipmentTypes();
		return $this->view->render($response, "index.twig", array("data"=> $array["equipments"]));
	}

	// public function showEquipment($request, $response) {
	// 	$equip = $this->core->getEquipment()
	// }


	// GET request to load page for the selected equipment
	public function editEquipment($request, $response) {
		// print_r(json_encode($request->getQueryParams()));
		$item = $this->core->getEquipment($request->getQueryParams());
		return $this->view->render($response, "editEquipment.twig", array("data" => $item["equipments"]));
		// return null;
	}

	// PUT function that accepts a request body as input
	public function updateEquipment($request, $response) {
		return null;
	}

	// DELETE equipment identified by query param
	public function deleteEquipment($request, $response) {
		$itemToDelete = $response->getQueryParams();
		print_r(json_encode($itemToDelete));
		return null;
	}

}
