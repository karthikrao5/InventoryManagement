<?php

use App\Models\Equipment;

// Back-end routes
// Do not modify codes below.
// require_once 'core/CoreService.php';
use \App\core\CoreService as CoreService;

// http://www.restapitutorial.com/lessons/httpmethods.html
// REST API routes


// this is the angular route to pull the index page.
$app->get('/', function($request, $response) {
    $this->view->render($response, 'index.html');
});

// Backend routes.
$app->group('/v1', function() {

    $this->group('/auth', function() {
        $this->post('', 'AuthController:authorize');
        $this->post('/decode', 'AuthController:testDecode');
    });

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

    // log routes
    $this->group('/logs', function() {
        $this->get('[/{params:.*}]', 'LogController:find');
    });

    // user routes
    $this->group('/users', function() {
        $this->post('', 'UserController:create');

        $this->get('[/{params:.*}]', 'UserController:find');

        $this->delete('', 'UserController:delete');

        $this->put('', 'UserController:update');
    });

    // loan routes
    $this->group('/loans', function() {
        $this->post('', 'LoanController:create');

        $this->get('[/{params:.*}]', 'LoanController:get');

        $this->delete('', 'LoanController:delete');

        $this->put('', 'LoanController:update');
    });
});
