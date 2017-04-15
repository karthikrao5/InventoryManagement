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
        $this->rm = $this->ci->get('rm');
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
            return $response->withJson($array);
        } else {
            return $response->withStatus(404)->write("Something went wrong with the find function in EquipmentTypeController.");
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
            return $response->withStatus(200)->withJson($result);
        } else {
            return $response->withStatus(400)->withJson($result);
        }
    }


    // -----------------------------------------------------------------
    // PUT functions
    // -----------------------------------------------------------------

    public function updateOne($request, $response) {
        $result = $this->core->updateEquipmentType($request->getParsedBody());

        return $response->withJson($result);
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
            //return $response->withStatus(200)->write("Successfully deleted EquipmentTypes.");
            return $response->withStatus(200)->write("Successfully deleted ".$result['n']." EquipmentTypes!");
        } else {
            return $response->withStatus(404)->write("Something went wrong, EquipmentType are not deleted.");
        }
    }
}
