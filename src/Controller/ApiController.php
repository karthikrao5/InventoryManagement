<?php
namespace App\Controller;


use App\Models\Equipment;
use App\Models\EquipmentType;
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

    public function clearCollection($request, $response) {

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
        $equipment->setStatus($json['status']);
        $equipment->setLoaner($json['loaned_to']);
        $equipment->setComment($json['comment']);

        if(!is_null($equipment)) {
            $this->dm->persist($equipment);
            $this->dm->flush();
            return $response->write("Successfully entered new equipment.")->withStatus(200);
        }
    }

    public function createEquipmentType($request, $response) {
        if(is_null($request)) {
            return $response->write("No body. Make sure you enter some json!")->withStatus(400);
        }

        $json = $request->getParsedBody();

        $equipmentType = new EquipmentType($json["equipment_type"]);

        // check if this already exists
        $find = $this->dm->getRepository(EquipmentType::class)->findOneBy(array('name' => $json["equipment_type"]));

        if ($find) {
            return $response->write("This equipment type is already in the system.")->withStatus(200);
        } else {
            $this->dm->persist($equipmentType);
            $this->dm->flush();
            return $response->write("Successfully entered new equipment type.")->withStatus(200);
        }

        return $response->write("Something went wrong, should not reach here.")->withStatus(400);

        
    }
}