<?php

use App\Models\Equipment;

// Back-end routes
// Do not modify codes below.
// require_once 'core/CoreService.php';
use \App\core\CoreService as CoreService;

// http://www.restapitutorial.com/lessons/httpmethods.html
// REST API routes

$app->get('/home', 'HomeController:index');



// $app->get('/home', function($request, $response) {
//     // $this->logger->info("reached /home");
//     return $this->view->render($response, 'template.html');
// });

$app->get('/equipment', function($request, $response) {
    // $this->logger->info("reached /home");
    return $this->view->render($response, 'hp.html', array(data => getAll()));
});


$app->get('/equipment/{id}', function($request, $response) {
    // $this->logger->info("reached /home");
    $id = $request->getAttribute('id');
    $core = CoreService::getInstance();
    $result = $core->getEquipmentById($request->getAttribute('id'));
    // $json_response = $response->withJson($result);
    $data =  $result['equipment'];


    return $this->view->render($response, 'equipmentpage.html', array(data => $data));
});
$app->get('/addequipment', function($request, $response) {
    // $this->logger->info("reached /home");
    return $this->view->render($response, 'addequipment.html');
});
$app->get('/addequipmenttype', function($request, $response) {
    // $this->logger->info("reached /home");
    return $this->view->render($response, 'addequipmenttype.html');
});




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






$app->group('/v1', function() {

    // equipment routes
    $this->group('/equipments', function() {
        // CREATE
        $this->post('', 'EquipmentController:create');

        // READ single equipment
        // for info about this route, check docs https://www.slimframework.com/docs/objects/router.html#route-placeholders
        $this->get('[/{params:.*}]', 'EquipmentController:find');
        // read all equipments
        // $this->get('', 'EquipmentController:find');

        // UPDATE to update/replace item by ID
        // body is json with keys being the fields to update
        // and values being the values to update
        $this->put('/{id}', 'EquipmentController:updateOne');
        // $this->put('/', 'EquipmentController:updateCollection');

        // DESTROY
        $this->delete('/{id}', 'EquipmentController:delete');


        // add equipment type to equipment
        $this->put('/addequipmenttype/{id}', 'EquipmentController:addEqType');

    });

    // equipment types routes
    $this->group('/equipmenttypes', function() {


        // input has to be json as {"name" : "someequipmenttype"}
        // then add as many other fields. Validation will happen
        // inside controller.
        $this->post('', 'EquipmentTypeController:create');

        // $this->get('/search/{id}', 'ApiController:searchId');
    });


    $this->get('/testget', function($request, $response) {
        $dm = $this->get('dm');
        $var = $this->dm->getRepository(Equipment::class)->findAll();
        return $response->withJson($var);
    });
});

// test route to see if DM is working
// $app->get('/', function($request, $response) {
//     $equipment = new Equipment();
//     $equipment->setLoaner("Karthik");
//     $dm = $this->get('dm');
//     $dm->persist($equipment);
//     $dm->flush();
// });


// Route registrations
// Just for sprint2. This will change in sprint 3 but the way these routes working will be the same.
$app->post('/core/equipment/add', 'addEquipment');
$app->get('/core/equipment/get/by-id/{id}', 'getEquipmentById');
$app->get('/core/equipment/get/by-department-tag/{tag}', 'getEquipmentByDeptTag');
$app->delete('/core/equipment/remove/{id}', 'removeEquipment');
$app->get('/core/equipment/getall', 'getAllEquipments');
$app->post('/core/equipment/update', 'updateEquipment');

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