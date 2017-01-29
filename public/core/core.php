<?php
require 'db.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

# REST API route
$route_add_equipment = '/core/add-equipment';

# API callback function
function addEquipment (Request $request, Response $response)
{
    if (is_null($request->getParsedBody())) // if parsed body is empty or null
    {
        return $response->withStatus(400); // return with error code
    }
    
	print_r($request->getParsedBody());
	$result = db_addEquipment($request->getParsedBody());
	print_r($result);
}

# Register REST API
$app->post($route_add_equipment, 'addEquipment');
