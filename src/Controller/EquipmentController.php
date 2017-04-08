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
     * @return json of document
     */
    public function find($request, $response) {


        if(is_null($request)) {
            return $response->write("Invalid request.")->withStatus(400);
        }
        // TESTED THIS CODE, params works don't mess with it.
        $params = $request->getQueryParams();
        if ($params) {
            $array = $this->core->getEquipment($params);
        } else {
            $array = $this->core->getEquipment();
        }

        // return null;

        if($array) {
            return $response->withJson($array);
        } else {
            return $response->withStatus(404)->write("Something went wrong with the find function in EquipmentController.");
        }


        // $params = $request->getQueryParams();

        // if (empty($params)) {
        //     $cursor = $this->dm->getRepository(Equipment::class)->getAllEquipment();
        //     // return $response->withStatus(200);
        //     return $response->withJson(iterator_to_array($cursor));
        // }

        // // $returnValue = $this->rm->findAllByCriteria($params);
        // $returnValue = $this->dm->getRepository(Equipment::class)->findByParams($params);

        // if ($returnValue) {
        //     // 200 status
        //     return $response->withJson($returnValue);
        // }

        // return $response->withStatus(404)->write("No equipment by those params.");
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

        $result = $this->core->createEquipment($request->getParsedBody());

        if ($result["ok"]) {
			return $response->withStatus(200)->withJson($result);
		} else {
			return $response->withStatus(400)->withJson($result);
		}
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

        $result = $this->core->updateEquipment($request->getParsedBody());
        
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

		$result = $this->core->deleteEquipment($request->getParsedBody());

		if ($result["ok"]) {
			return $response->withStatus(200)->write("Successfully deleted ".$result['n']." Equipments!");
		} else {
			return $response->withStatus(404)->write("Something went wrong, Equipments are not deleted.");
		}
    }
}
