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
?>
