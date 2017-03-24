<?php
namespace App\Controller;


use App\Models\Equipment;
use App\Models\EquipmentType;
use App\Models\EquipmentTypeAttribute;

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
	public function find($request, $response) {
		// return $response->write("Placeholder")->withStatus(200);
		if(is_null($request)) {
            return $response->write("Invalid request.")->withStatus(400);
        }

        $params = $request->getQueryParams();

        if (empty($params)) {
            // $returnValue = $this->rm->getAllInCollection();
            $returnValue = $this->dm->createQueryBuilder(EquipmentType::class)->find();
            return $response->withJson($returnValue);
        }

        // $returnValue = $this->rm->findAllByCriteria($params);
        $returnValue = $this->dm->getRepository(EquipmentType::class)->findBy($params);

        if ($returnValue) {
            // 200 status
            return $response->withJson($returnValue);
        }

        return $response->withStatus(404)->write("No equipment by those params.");
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

        // check if this already exists
        $find = $this->dm->getRepository(EquipmentType::class)->findOneBy(array('name' => $json["name"]));

        if ($find) {
            return $response->write("This equipment type is already in the system.")->withStatus(200);
        } else {
        	// $equipmentType = new EquipmentType($json["equipment_type"]);
	        $eqType = new EquipmentType();
	        $eqType->setName($json['name']);

	        // loop thru the EquipmentTypeAttributes and create them and add them to
	        // EquipmentType
	        foreach ($json['attributes'] as $attribute) {
        		// print_r($attribute."\n");
	        	$newEqAttr = new EquipmentTypeAttribute();

	        	foreach ($attribute as $key => $value) {
		        	if($key == 'name') { $newEqAttr->setName($value); }
		        	if($key == 'required') { $newEqAttr->setRequired($value); }
		        	if($key == 'unique') { $newEqAttr->setUnique($value); }
		        	if($key == 'data_type') { $newEqAttr->setDataType($value); }
		        	if($key == 'regex') { $newEqAttr->setRegex($value); }
		        	if($key == 'help_comment') { $newEqAttr->setHelpComment($value); }
		        }
		        // print_r($newEqAttr);
		        $eqType->addEquipmentTypeAttribute($newEqAttr);
	        }

	        // print_r("=====================================");
	        // print_r($eqType->getEquipmentTypeAttributes());

	        $this->dm->persist($eqType);
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