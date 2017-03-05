<?php
namespace App\Controller;


use App\Models\Equipment;
use App\Models\EquipmentType;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Interop\Container\ContainerInterface;
use Doctrine\ODM\MongoDB\DocumentManager;

class EquipmentTypeController extends AbstractController{

    public function __construct(ContainerInterface $c) {
        parent::__construct($c);
    }

    // -----------------------------------------------------------------
	// GET functions
	// -----------------------------------------------------------------
	public function getAll($request, $response) {
		return $response->write("Placeholder")->withStatus(200);
	}

	public function findById($request, $response, $args) {
		return $response->write("Placeholder")->withStatus(200);
	}

	// -----------------------------------------------------------------
	// POST functions
	// -----------------------------------------------------------------

	public function create($request, $response) {
		if(is_null($request)) {
            return $response->write("Invalid request.")->withStatus(400);
        }

        if (is_null($request->getParsedBody())) {
            return $response->write("No body recieved.")->withStatus(200);
        }

        $json = $request->getParsedBody();

        // $equipmentType = new EquipmentType($json["equipment_type"]);
        $equipment_type = new EquipmentType();
        $equipment_type->setName($json['name']);

        // check if this already exists
        $find = $this->dm->getRepository(EquipmentType::class)->findOneBy(array('name' => $json["equipment_type"]));

        if ($find) {
            return $response->write("This equipment type is already in the system.")->withStatus(200);
        } else {
            $this->dm->persist($equipment_type);
            $this->dm->flush();
            return $response->write("Successfully entered new equipment type.")->withStatus(200);
        }

        return $response->write("Something went wrong, should not reach here.")->withStatus(400);
	}

    // -----------------------------------------------------------------
	// PUT functions
	// -----------------------------------------------------------------

	public function updateOne($request, $response) {
		return $response->write("Placeholder")->withStatus(200);
	}


	// -----------------------------------------------------------------
	// DELETE functions
	// -----------------------------------------------------------------

	public function delete($request, $response, $args) {
		return $response->write("Placeholder")->withStatus(200);
	}
 

}