<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

# REST API route
$route_add_equipment = '/core/add-equipment';

# API callback function
function add_equipment (Request $request, Response $response)
{
	print_r($request->getParsedBody());
}

# Register REST API
$app->post($route_add_equipment, 'add_equipment');
