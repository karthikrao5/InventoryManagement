<?php

use App\Models\Equipment;

// Back-end routes
// Do not modify codes below.
// require_once 'core/CoreService.php';
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








$app->get('/', function($request, $response) {
    $equipment = new Equipment();
    $equipment->setLoaner("Karthik");
    $dm = $this->get('dm');
    $dm->persist($equipment);
    $dm->flush();

});

// REST API routes
$app->group('/v1', function() {
    $this->get('/equipments', 'ApiController:getAll');

    $this->get('/testget', function($request, $response) {
        $dm = $this->get('dm');
        $var = $this->dm->getRepository(Equipment::class)->findAll();
        return $response->withJson($var);
    });
});