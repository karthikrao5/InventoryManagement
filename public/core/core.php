<?php
require 'db.php';
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

# Register REST API
$app->get('/inventory/add', 'getInventoryItems');
$app->post('/inventory/add', 'addInventory');
$app->get('/inventory',  'getItem');

// $app->get('/inventory/search/:query', 'findItem');
// $app->put('/inventory/:id', 'updateItem');
// $app->delete('/inventory/:id', 'deleteItem');

// GET all inventory objects
function getInventoryItems(Request $request, Response $response) {
    try {

        // query DB for all objects
        $result = db_getInventory();

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

// POST new inventory fields from clientside into db
function addInventory(Request $request, Response $response) {
    if (is_null($request->getParsedBody()))
    {
        $response->getBody()->write("Invalid JSON document.");
        $response->withStatus(400);
        return $response;
    }

    try {
        print_r($request->getParsedBody());
        $result = db_addEquipment($request->getParsedBody());
        print_r($result);

    } catch(PDOException $e) {
        $app->response()->setStatus(404);
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }

    
}

// GET query the db with id and return single object
// URL  /inventory
function getItem(Request $request, Response $response) {
    // $id = $request->getQueryParams('id');
    // $response->withStatus(200)
    //          ->withHeader('Content-Type', 'application/json')
    //          ->write(json_encode($id));

    try {
        $id = $request->getQueryParams();

        // search item with id
        $result = db_findItem($id);
 
        if($result) {
            $response->withStatus(200)
                     ->withHeader('Content-Type', 'application/json')
                     ->write($result);
        } else {
            throw new PDOException('No records found.');
        }
 
    } catch(PDOException $e) {
        $response->withStatus(404);
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }

}

function findItem(Request $request, Response $response, $query) {

}

function updateItem(Request $request, Response $response, $id) {

}

function deleteItem(Request $request, Response $response, $id) {

    // db_deleteItem($id)
}

// ====================== archives below this line================

// this is byung's original code. Keeping it
// for records.
# Register REST API
// $app->post($route_add_equipment, 'addEquipment');
// # REST API route
// $route_add_equipment = '/core/add-equipment';
// # API callback function
// function addEquipment (Request $request, Response $response)
// {
//     if (is_null($request->getParsedBody()))
//     {
//         $response->getBody()->write("Invalid JSON document.");
//         $response->withStatus(400);
//         return $response;
//     }
    
//     print_r($request->getParsedBody());
//     $result = db_addEquipment($request->getParsedBody());
//     print_r($result);
// }