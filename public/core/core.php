<?php
require 'db.php';
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

# Register REST API
$app->get('/add', 'getInventoryItems');
$app->post('/add', 'addInventory');

// GET all inventory objects
function getInventoryItems(Request $request, Response $response) {
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
}

// POST new inventory fields from clientside into db
function addInventory(Request $request, Response $response) {
    if (is_null($request->getParsedBody()))
    {
        $response->getBody()->write("Invalid JSON document.");
        $response->withStatus(400);
        return $response;
    }

    print_r($request->getParsedBody());
    $result = db_addEquipment($request->getParsedBody());
    print_r($result);
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