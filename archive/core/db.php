<?php
// This function inserts given document to the equipments collection and returns result array.
// Result array contains n, ok, err, errmsg.
function db_addEquipment($document)
{
	$mongo = new MongoClient(); // Connect to localhost with default port.
	$db = $mongo->inventorytracking; // Select database inventorytracking.
	$collection = $db->equipments; // Select collection equipments.

	$document["timestamp"] = new MongoDate(); // Add timestamp.
	$result = $collection->insert($document); // Insert given document to collection and get result array.
	$mongo->close(); // Close open connection.
	return $result;		
}

function db_getInventory() {
	$mongo = new MongoClient(); // Connect to localhost with default port.
	$db = $mongo->inventorytracking; // Select database inventorytracking.
	$collection = $db->equipments; // Select collection equipments.

	// MongoCursor aka iterator of all documents in collection
	$cursor = $collection->find();
	$mongo->close();
	return json_encode(iterator_to_array($cursor));
}

function db_findItem($query) {
	$mongo = new MongoClient(); // Connect to localhost with default port.
	$db = $mongo->inventorytracking; // Select database inventorytracking.
	$collection = $db->equipments; // Select collection equipments.

	$id = new MongoId($query["id"]);

	$item = $collection->findOne(array("_id" => $id));
	$mongo->close();
	return json_encode($item);
}

// function db_deleteItem($item) {
// 	$mongo = new MongoClient(); // Connect to localhost with default port.
// 	$db = $mongo->inventorytracking; // Select database inventorytracking.
// 	$collection = $db->equipments; // Select collection equipments.

// 	// // first decode the json item
// 	// $obj = json_decode($item);

// 	$id = "_".$item

// 	// search by _id
// 	$removeMe = $collection->findOne($obj->{$id});

// 	// remove queried item
// 	$collection->remove($removeMe);
// }
?>
