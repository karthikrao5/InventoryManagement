<?php
namespace App\Controller;


use App\Models\Equipment;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Interop\Container\ContainerInterface;
use Doctrine\ODM\MongoDB\DocumentManager;


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
        $this->logger->info('Requesting all equipments.');
        $var = $this->dm->getRepository(Equipment::class)->findAll();
        return $response->withJson($var);
    }

    


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

        $json = $request->getParsedBody();

        $equipment = new Equipment();
        $equipment->setDept($json['department_tag']);
        $equipment->setGT($json['gt_tag']);
        $equipment->setEquipmentType($json['equipment_type']);
        $equipment->setStatus($json['status']);
        $equipment->setLoaner($json['loaned_to']);
        $equipment->setComment($json['comment']);

        if(!is_null($equipment)) {
            $this->dm->persist($equipment);
            $this->dm->flush();
            return $response->write("Successfully entered.")->withStatus(200);
        }
    }
}