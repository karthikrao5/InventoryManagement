<?php

// REST API routes

// get all inventory Items
// $app->get('/inventory/add', '');

// // Add new item to inventory
// $app->post('/inventory/add', '');

// Query item from DB with ObjectID 
// example: /inventory?id=23485729345a
// $app->get('/inventory',  ApiController::class . ':getAll');



$app->get('/', 'HomeController:index');

$app->get('/home', function($request, $response) {
	// $this->logger->info("reached /home");
	return $this->view->render($response, 'home.twig');
});

// Front-end routes
