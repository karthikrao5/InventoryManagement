<?php
namespace App\Controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use App\Models\Equipment;
use Interop\Container\ContainerInterface;

class ApiController extends AbstractController{

    public function __construct(ContainerInterface $c) {
        parent::__construct($c);
    }


// -----------------------------------------------------------------
// GET functions
// -----------------------------------------------------------------

    /**
     * @return JSON document with all items
     */
    public function getAll(Request $request, Response $response) {
        $var = $this->dm->getRepository(Equipment::class)->findAll();
        return $response->withJson($var);
    }

    /**
     * @param $item is a json with some fields
     * @return 
     */
    // public function addItem($item) {

    // }


// -----------------------------------------------------------------
// POST functions
// -----------------------------------------------------------------

    /**
     * 
     */
    public function createEquipment(Request $request, Response $response)
    {
        $json = $request->getParsedBody();
        
    }
}