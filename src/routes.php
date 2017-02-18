<?php

use App\Models\Equipment;

$app->get('/', function($request, $response) {
	$equipment = new Equipment();
	$equipment->setLoaner("Karthik");
	$dm = $this->get('dm');
	$dm->persist($equipment);
	$dm->flush();

});

// dummy routes for front-end testing
// $app->group('/dummy', function() {
	// $this->get('/inventory', 'DummyController:getAll');
// });

// $app->get('/inventory', function($request, $response) {
// 	try {
//         // query DB for all objects
// 	    $result = getAll();


//         if($result) {
//         	return $response->withJson($result);	

//         } else {
//             throw new PDOException("No records found.");
//         }

//     } catch(PDOException $e) {
//         $response->withStatus(404)
//                  ->write('{"error":{"text":'. $e->getMessage() .'}}');
//     }
// });

// $app->post('/inventory', function($request, $response) {
// 	if (is_null($request->getParsedBody()))
//     {
//         $response->getBody()->write("Invalid JSON document.");
//         $response->withStatus(400);
//         return $response;
//     }

//     try {
//     	addItem($request->getParsedBody());
//     	print_r($request->getParsedBody());
//     	// foreach($request->getParsedBody() as $item) {
//     	// 	addItem($item["item"], 'equipments');
//     	// 	addItem($item['itemtype'], 'equipmenttypes');
//     	// }
//     } catch(PDOException $e) {
//         $response()->setStatus(404);
//         echo '{"error":{"text":'. $e->getMessage() .'}}';
//     }

// });

// // REST API routes
// // $app->group('/v1', function() {
// // 	$this->group('/inventory', function() {
// // 		$this->get('', 'ApiController:getAll');
// // 	});
// // });


// // Front-end routes


// // test routes (ignore these)
// $app->get('/', 'HomeController:index');

// $app->get('/home', function($request, $response) {
// 	// $this->logger->info("reached /home");
// 	return $this->view->render($response, 'template.html');
// });

// $app->get('/all', function($request, $response) {
// 	// $this->logger->info("reached /home");
// 	return $this->view->render($response, 'hp.html', array(data => getAll()));
// });



// function addItem($itemToAdd) {
// 	$mongo = new MongoClient();
// 	$db = $mongo->inventorytracking;
// 	$collection = $db->equipments;

// 	$itemToAdd["created_on"] = new MongoDate(); // Add timestamp.
// 	$itemToAdd["last_updated"] = new MongoDate();
// 	$result = $collection->insert($itemToAdd, array('w' => 1)); // Insert given document to collection and get result array.

// 	if ($result) {
// 		return $result;	
// 	} else {
// 		return "Error inserting to db.";
// 	}
	
// }

// // returns json object of all items
// function getAll() {
// 	$mongo = new MongoClient();
// 	$db = $mongo->inventorytracking;
// 	$collection = $db->equipments;
// 	// MongoCursor aka iterator of all documents in collection
// 	$cursor = $collection->find();
// 	if ($cursor) {
// 		return iterator_to_array($cursor);
// 	}
// 	return null;
// }

// // this is how you use findItem in the controller: 

// 	// $id = $request->getQueryParams();
//     // search item with id
//     // $result = findItem($id);

// function findItem($query) {
// 	$mongo = new MongoClient();
// 	$db = $mongo->inventorytracking;
// 	$collection->equipments;
// 	$id = new MongoId($query["id"]);
// 	$item = $collection->findOne(array("_id" => $id));
// 	$mongo->close();
// 	return json_encode($item);
// }

// function deleteItem($query) {
// 	$mongo = new MongoClient();
// 	$db = $mongo->inventorytracking;
// 	$collection->equipments;
// 	$itemToDelete = findItem($query);
// 	$collection->remove(array( '_id' => new MongoID($itemToDelete)));

// 	if(! findItem($query)) {
// 		return ("Removed item successfully.");
// 	} else {
// 		return ("Item removal unsuccessful.");
// 	}
// }


// Back-end routes
// Do not modify codes below.
require_once 'core/CoreService.php';
use \App\core\CoreService as CoreService;

// Route registrations
$app->post('/core/equipment/add', 'addEquipment');
$app->get('/core/equipment/get/by-id/{id}', 'getEquipmentById');
$app->get('/core/equipment/get/by-department-tag/{tag}', 'getEquipmentByDeptTag');
$app->delete('/core/equipment/remove/{id}', 'removeEquipment');
$app->get('/core/equipment/getall', 'getAllEquipments');

// Functions used in each route
function getAllEquipments($request, $response)
{
    $core = CoreService::getInstance();
    $result = $core->getAllEquipments();
    $json_response = $response->withJson($result);
    
    if($result['result'])
    {
        $json_response->withStatus(200);
    }
    else
    {
        $json_response->withStatus(400);
    }
    
    return $json_response;
}

function removeEquipment($request, $response)
{
    $core = CoreService::getInstance();
    $result = $core->removeEquipment($request->getAttribute('id'));
    $json_response = $response->withJson($result);
    
    if($result['result'])
    {
        $json_response->withStatus(200);
    }
    else
    {
        $json_response->withStatus(400);
    }
    
    return $json_response;
}

function getEquipmentByDeptTag($request, $response)
{
    $core = CoreService::getInstance();
    $result = $core->getEquipmentByDepartmentTag($request->getAttribute('tag'));
    $json_response = $response->withJson($result);
    
    if($result['result'])
    {
        $json_response->withStatus(200);
    }
    else
    {
        $json_response->withStatus(400);
    }
    
    return $json_response;
}

function getEquipmentById($request, $response)
{
    $core = CoreService::getInstance();
    $result = $core->getEquipmentById($request->getAttribute('id'));
    $json_response = $response->withJson($result);
    
    if($result['result'])
    {
        $json_response->withStatus(200);
    }
    else
    {
        $json_response->withStatus(400);
    }
    
    return $json_response;
}

function addEquipment($request, $response)
{
    $json = $request->getParsedBody();
    $core = CoreService::getInstance();
    $result = $core->addEquipment($json);
    
    $json_response = $response->withJson($result);
    
    if($result['result'])
    {
        $json_response->withStatus(200);
    }
    else
    {
        $json_response->withStatus(400);
    }
    
    return $json_response;
}

// Not working yet
function updateEquipment($request, $response)
{
    $json = $request->getParsedBody();
    $core = CoreService::getInstance();
    $result = $core->updateEquipment($json);
    
    $json_response = $response->withJson($result);
    
    if($result['result'])
    {
        $json_response->withStatus(200);
    }
    else
    {
        $json_response->withStatus(400);
    }
    
    return $json_response;
}

$app->post('/core/equipment/update', 'updateEquipment');