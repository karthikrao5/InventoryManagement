<?php

use App\Models\Equipment;

// Back-end routes
// Do not modify codes below.
// require_once 'core/CoreService.php';
use \App\core\CoreService as CoreService;

// http://www.restapitutorial.com/lessons/httpmethods.html
// REST API routes

$app->get('/', 'ViewController:index')->setName("root");

$app->get('/add-equipment', 'ViewController:getEquipmentForm')->setName("get-add-equipment");
$app->post("/add-equipment", 'ViewController:postEquipmentForm');

$app->get('/add-equipment-type', 'ViewController:getEquipmentTypeForm')->setName("get-add-equipment-type");
$app->post('/add-equipment-type', 'ViewController:postEquipmentTypeForm');

$app->get("/show-all-equipmenttypes", "ViewController:showAllEquipmentTypes")->setName("all-equipment-types");

$app->get("/edit-equipment[/{params:.*}]", "ViewController:editEquipment")->setName("edit-equipment");
$app->put("/edit-equipment", "ViewController:updateEquipment");


$app->delete('/delete-equipment[/{params:.*}]', 'ViewController:deleteEquipment')->setName("delete-equipment");

$app->get("/test", function($request, $response) {
    return $this->view->render($response, "hp.html", array());
});

// $app->get('/home', function($request, $response) {
//     // $this->logger->info("reached /home");
//     return $this->view->render($response, 'template.html');
// });

// $app->get('/equipment', function($request, $response) {
//     // $this->logger->info("reached /home");
//     return $this->view->render($response, 'hp.html', array(data => getAll()));
// });


// $app->get('/equipment/{id}', function($request, $response) {
//     // $this->logger->info("reached /home");
//     $id = $request->getAttribute('id');
//     $core = CoreService::getInstance();
//     $result = $core->getEquipmentById($request->getAttribute('id'));
//     // $json_response = $response->withJson($result);
//     $data =  $result['equipment'];

$app->get('/home', function($request, $response) {
    return $app->get("view")->render($response, "hp.html");
});
//     return $this->view->render($response, 'equipmentpage.html', array(data => $data));
// });
$app->get('/addequipment', function($request, $response) {
    // $this->logger->info("reached /home");
    return $this->view->render($response, 'addequipment.html');
});
$app->get('/addequipmenttype', function($request, $response) {
    // $this->logger->info("reached /home");
    return $this->view->render($response, 'addequipmenttype.html');
});


// Backend routes.
$app->group('/v1', function() {
    // equipment routes
    $this->group('/equipments', function() {
        $this->post('', 'EquipmentController:create');

        $this->get('[/{params:.*}]', 'EquipmentController:find');

        $this->put('', 'EquipmentController:updateOne');

        $this->delete('', 'EquipmentController:delete');
    });

    // equipment types routes
    $this->group('/equipmenttypes', function() {
        $this->post('', 'EquipmentTypeController:create');

        $this->get('[/{params:.*}]', 'EquipmentTypeController:find');

        $this->delete('', 'EquipmentTypeController:delete');

        $this->put('', 'EquipmentTypeController:updateOne');
    });
});