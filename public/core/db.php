<?php
// connect
// no constructor parameter connects to localhost with default port
$mongo = new MongoClient();

// select a database
$db = $mongo->inventorytracking;

// select a collection (table)
$collection = $db->equipments;

// add a record
$document = array
(
	"type" => "laptop",
	"cost" => "1234.56",
	"timestamp" => new MongoDate()
);
$collection->insert($document);

// find everything in the collection
$cursor = $collection->find();

// iterate through the results
foreach ($cursor as $document)
{
	print_r($document);
}
?>
