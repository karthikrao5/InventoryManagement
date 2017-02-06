<?php

// REST API routes

// get all inventory Items
$app->get('/inventory/add', '');

// Add new item to inventory
$app->post('/inventory/add', '');

// Query item from DB with ObjectID 
// example: /inventory?id=23485729345a
$app->get('/inventory',  App\Controller:getAll);



// Front-end routes
