<?php
namespace App\Controller;


use App\Models\Equipment;
use App\Models\EquipmentType;
use App\Models\Log;
use App\Models\Attribute;
use App\Models\EquipmentTypeAttribute;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Interop\Container\ContainerInterface;
use Doctrine\ODM\MongoDB\DocumentManager;


class EquipmentController extends AbstractController{

    protected $validator;

    // private $rm;

    public function __construct(ContainerInterface $c) {
        parent::__construct($c);
        $this->validator = $this->ci->get('EquipmentValidator');

        // $this->rm = $this->ci->get('rm');
        // $this->rm->setRepo(Equipment::class);
    }


// -----------------------------------------------------------------
// GET functions
// -----------------------------------------------------------------
    /**
     * @return json of document entry or multiple entries
     */
    public function find($request, $response) {
        if(is_null($request)) {
            return $response->write("Invalid request.")->withStatus(400);
        }

        $params = $request->getQueryParams();

        if (empty($params)) {
            // $returnValue = $this->rm->getAllInCollection();
            $returnValue = $this->dm->getRepository(Equipment::class)->findAll();
            return $response->withJson($returnValue);
        }

        // $returnValue = $this->rm->findAllByCriteria($params);
        $returnValue = $this->dm->getRepository(Equipment::class)->findBy($params);

        if ($returnValue) {
            // 200 status
            return $response->withJson($returnValue);
        }

        return $response->withStatus(404)->write("No equipment by those params.");
    }

    


// -----------------------------------------------------------------
// POST functions
// -----------------------------------------------------------------

    /**
     * 
     */
    public function create($request, $response) {
        
        if(is_null($request)) {
            return $response->write("Invalid request.")->withStatus(400);
        }

        if (is_null($request->getParsedBody())) {
            return $response->write("No body recieved.")->withStatus(200);
        }

        $json = $request->getParsedBody();

        // $findMe = $this->rm->findAllByCriteria($json);

        // search by dept tag since its unique and required
        $findMe = $this->dm->getRepository(Equipment::class)->findBy(array("department_tag" => $json['department_tag']));

        // if something returned, item exists, send 409 conflict
        if ($findMe) {
            return $response->withStatus(409)->write("This item already exists.".json_encode($findMe));
        } else {
            // TODO Validate fields. 
            // $this->ci->get("SomeValidator")->validateMe($json);

            $equipment = new Equipment();

            // look for equipment type
            $findEqType = $this->dm->getRepository(EquipmentType::class)->findOneBy(array('name' => $json['equipment_type']));

            // MUST HAVE THIS FIELD. VALIDATE THE REQUEST FOR THIS
            if(!is_null($findEqType)) {
                $equipment->setEquipmentType($findEqType);
                $equipment->setDeptTag($json['department_tag']);
                $equipment->setGtTag($json['gt_tag']);
                $equipment->setStatus($json['status']);
                $equipment->setLoanedTo($json['loaned_to']);
                $equipment->setComment($json['comment']);

                // loop thru attributes
                foreach ($json['attributes'] as $key => $value) {
                    $newAttr = new Attribute();
                    $newAttr->setKey($key);
                    $newAttr->setValue($value);
                    
                    // the referencemany in equipment.php has cascade flag
                    // so no need to persist separately

                    $equipment->addAttribute($newAttr);
                }

                // loop thru logs??
                foreach ($json['logs'] as $key => $value) {
                    $newLog = new Log();
                    $newLog->setEquipment($equipment);

                    if ($key == "action_via") { $newLog->setActionVia($value); }
                    if ($key == "action_by") { $newLog->setActionBy($value); }
                }

                if(!is_null($equipment)) {
                    $this->dm->persist($equipment);
                    $this->dm->flush();
                    return $response->write("Successfully entered new equipment.")->withStatus(200);
                }
            } else {
                // if equipment_type is not given, return error saying user must provide it
                return $response->withStatus(400)->write("Equipment type not found.");
            }
        }

        return $response->withStatus(404)->write("Something went wrong.");
    }
// -----------------------------------------------------------------
// PUT functions
// -----------------------------------------------------------------

//  update/replace item by ID
    public function updateOne($request, $response, $args) {
        if(is_null($request)) {
            return $response->write("Invalid request.")->withStatus(400);
        }

        if (is_null($request->getParsedBody())) {
            return $response->write("No body recieved.")->withStatus(200);
        }

        if(!validateID($args['id'])) {
            return $response->write("Invalid ID.")->withStatus(404);
        }

        $json = $request->getParsedBody();


        $qb = $this->dm->createQueryBuilder(Equipment::class)
                                    ->findAndUpdate()
                                    ->field('id')->equals($args['id']);

        foreach ($json as $key => $value) {
            $query = $qb->field($key)->set($value);
        }

        $qb->getQuery()->execute();

        return $response->write("Successfully updated equipment.")->withStatus(200);

    }

// -----------------------------------------------------------------
// DELETE functions
// -----------------------------------------------------------------

    public function delete($request, $response, $args) {
        if(is_null($request)) {
            return $response->write("Invalid request.")->withStatus(400);
        }

        if (is_null($request->getParsedBody())) {
            return $response->write("No body recieved.")->withStatus(200);
        }

        $this->dm->createQueryBuilder(Equipment::class)
                                    ->remove()
                                    ->field('id')->equals($args['id'])
                                    ->getQuery()
                                    ->execute();

        if (!$this->dm->getRepository(Equipment::class)->findOneBy(array('id'=>$args['id']))) {
            return $response->write("Successfully removed equipment.")->withStatus(200);
        }
        
        return $response->write("Something happened with the remove function.")->withStatus(404);
    }
}