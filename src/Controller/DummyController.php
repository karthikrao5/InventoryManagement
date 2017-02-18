<?php


namespace App\Controller;

use \App\core\DummyDB as DummyDB;

class DummyController {

	protected $db;

	public function __construct(DummyDB $db) {
		$this->db = $db;
	}

	public function index($request, $response) {
		return "Dummy Controller index";
	}

	public function getAll($request, $response) {
		try {

	        // query DB for all objects
	        $result = $db->getInventory();

	        if($result) {
	           // construct json with collection
	            return $response->withStatus(200)
	                            ->withHeader("Content-Type", "application/json")
	                            ->write($result); 
	        } else {
	            throw new PDOException("No records found.");
	        }

	    } catch(PDOException $e) {
	        $response->withStatus(404)
	                 ->write('{"error":{"text":'. $e->getMessage() .'}}');
	    }
	}
	
}