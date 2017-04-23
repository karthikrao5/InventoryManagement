<?php
namespace App\Controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Interop\Container\ContainerInterface;

class EquipmentTypeController extends AbstractController{

    protected $validator;
    private $rm;

    public function __construct(ContainerInterface $c) {
        parent::__construct($c);
	$this->validator = $this->ci->get('EquipmentTypeValidator');
    }

    // -----------------------------------------------------------------
    // GET functions
    // -----------------------------------------------------------------
    public function find($request, $response)
    {
        if(is_null($request))
        {
            return $response->write("Invalid request.")->withStatus(400);
        }

        // TESTED THIS CODE, params works don't mess with it.
        $params = $request->getQueryParams();
        if ($params) {
            $array = $this->core->getEquipmentType($params);
        } else {
            $array = $this->core->getEquipmentType();
        }

        if($array) {
            return $response->withJson($array)->withStatus(200);
        } else {
            return $response->withStatus(404)->withJson($array);
        }
    }

    // -----------------------------------------------------------------
    // POST functions
    // -----------------------------------------------------------------

    public function create($request, $response) {
        if(is_null($request))
        {
            return $response->write("Invalid request.")->withStatus(400);
        }

        if (is_null($request->getParsedBody()))
        {
            return $response->write("No body recieved.")->withStatus(400);
        }

        $result = $this->core->createEquipmentType($request->getParsedBody());

        if ($result["ok"]) {
            return $response->withStatus(201)->withJson($result);
        } else {
            return $response->withStatus(409)->withJson($result);
        }
    }


    // -----------------------------------------------------------------
    // PUT functions
    // -----------------------------------------------------------------

    public function updateOne($request, $response) {
        $result = $this->core->updateEquipmentType($request->getParsedBody());

        if ($result["ok"]) {
            return $response->withStatus(200)->withJson($result);
        } else {
            return $response->withStatus(404)->withJson($result);
        }
    }


    // -----------------------------------------------------------------
    // DELETE functions
    // -----------------------------------------------------------------

    public function delete($request, $response, $args) {
        if(is_null($request))
        {
            return $response->write("Invalid request.")->withStatus(400);
        }

        if (is_null($request->getParsedBody()))
        {
            return $response->write("No body recieved.")->withStatus(400);
        }

        $result = $this->core->deleteEquipmentType($request->getParsedBody());

        if ($result["ok"]) {
            return $response->withStatus(200)->withJson($result);
        } else {
            return $response->withStatus(404)->withJson($result);
        }
    }
}
