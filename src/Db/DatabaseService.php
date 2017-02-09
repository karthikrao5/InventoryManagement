<?php

namespace App\db;

public class DatabaseService {

	private $collection;

	public function __construct($config) {
		$mongo = new MongoClient();
		$db = $mongo->inventorytracking;
		$collection = $db->equipments;
		return $collection;
	}

	/**
	 *get all items in inventory
	 * @return a MongoCursor
	 */
	public function db_getAll() {
		$cursor = $collection->find();
		// $jsonDoc = json_encode(iterator_to_array($cursor));
		return $cursor;
	}


	/**
	 *get all items in inventory
	 * @return object in php array
	 */
	public function db_findItem($query) {
		$id = new MongoId($query["id"]);
		$item = $collection->findOne(array("_id" => $id));
		return $item;
	}
}