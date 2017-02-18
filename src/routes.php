<?php

// dummy routes for front-end testing
// $app->group('/dummy', function() {
	// $this->get('/inventory', 'DummyController:getAll');
// });

$app->get('/inventory', function($request, $response) {
	try {
        // query DB for all objects
	    $result = getAll();


        if($result) {
        	return $response->withJson($result);	

        } else {
            throw new PDOException("No records found.");
        }

    } catch(PDOException $e) {
        $response->withStatus(404)
                 ->write('{"error":{"text":'. $e->getMessage() .'}}');
    }
});

$app->post('/inventory', function($request, $response) {
	if (is_null($request->getParsedBody()))
    {
        $response->getBody()->write("Invalid JSON document.");
        $response->withStatus(400);
        return $response;
    }

    try {
    	addItem($request->getParsedBody());
    	print_r($request->getParsedBody());
    	// foreach($request->getParsedBody() as $item) {
    	// 	addItem($item["item"], 'equipments');
    	// 	addItem($item['itemtype'], 'equipmenttypes');
    	// }
    } catch(PDOException $e) {
        $response()->setStatus(404);
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }

});

// REST API routes
// $app->group('/v1', function() {
// 	$this->group('/inventory', function() {
// 		$this->get('', 'ApiController:getAll');
// 	});
// });


// Front-end routes


// test routes (ignore these)
$app->get('/', 'HomeController:index');

$app->get('/home', function($request, $response) {
	// $this->logger->info("reached /home");
	return $this->view->render($response, 'template.html');
});

$app->get('/equipment', function($request, $response) {
	// $this->logger->info("reached /home");
	return $this->view->render($response, 'hp.html', array(data => getAll()));
});

$app->get('/equipment/{id}', function($request, $response) {
	// $this->logger->info("reached /home");
	$id = $request->getAttribute('id');

	return $this->view->render($response, 'equipmentpage.html', array(data => getAll(), id => $id));
});



function addItem($itemToAdd) {
	$mongo = new MongoClient();
	$db = $mongo->inventorytracking;
	$collection = $db->equipments;

	$itemToAdd["created_on"] = new MongoDate(); // Add timestamp.
	$itemToAdd["last_updated"] = new MongoDate();
	$result = $collection->insert($itemToAdd, array('w' => 1)); // Insert given document to collection and get result array.

	if ($result) {
		return $result;	
	} else {
		return "Error inserting to db.";
	}
	
}

// returns json object of all items
function getAll() {
	$mongo = new MongoClient();
	$db = $mongo->inventorytracking;
	$collection = $db->equipments;
	// MongoCursor aka iterator of all documents in collection
	$cursor = $collection->find();
	if ($cursor) {
		return iterator_to_array($cursor);
	}
	return null;
}

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


