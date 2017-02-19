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
    public function createEquipment(Request $request, Response $response) {
        
        if(is_null($request)) {
            return $response->write("Invalid request.")->withStatus(400);
        }
        $dm = $this->get('dm');

        $json = $request->getParsedBody();
        $equipment = new Equipment();
        $equipment.setDept($json['department_tag']);
        $equipment.setGT($json['gt_tag']);
        $equipment.setEquipmentType($json['equipment_type']);
        $equipment.setStatus($json['status']);
        $equipment.setLoaner($json['loaned_to']);
        $equipment.comment($json['comment']);

        if(!is_null($equipment)) {
            $dm->persist($equipment);
            $dm->flush();
            return $response->write("Successfully entered.")->setStatus(200);
        }
    }
}