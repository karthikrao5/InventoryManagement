<?php

namespace App\core;

class DummyDB {

	protected $mongo;
	protected $db;
	protected $collection;

	public function __construct() {
		$mongo = new MongoClient();
		$db = $mongo->inventorytracking;
		$collection->equipments;
	}

	function addEquipment($itemToAdd) {
		$document["timestamp"] = new MongoDate(); // Add timestamp.
		$result = $collection->insert($document); // Insert given document to collection and get result array.
		return $result;	
	}

	// returns json object of all items
	function getAll() {
		// MongoCursor aka iterator of all documents in collection
		$cursor = $collection->find();
		$mongo->close();
		return json_encode(iterator_to_array($cursor));
	}

// this is how you use findItem in the controller: 

		// $id = $request->getQueryParams();
        // search item with id
        // $result = findItem($id);

	function findItem($query) {
		$id = new MongoId($query["id"]);
		$item = $collection->findOne(array("_id" => $id));
		$mongo->close();
		return json_encode($item);
	}

	function deleteItem($query) {
		$itemToDelete = findItem($query);
		$collection->remove(array( '_id' => new MongoID($itemToDelete)));

		if(! findItem($query)) {
			return ("Removed item successfully.");
		} else {
			return ("Item removal unsuccessful.");
		}
	}
}