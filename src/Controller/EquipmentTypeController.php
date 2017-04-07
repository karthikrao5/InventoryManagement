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

	protected $validator;

    private $rm;

    public function __construct(ContainerInterface $c) {
        parent::__construct($c);
		$this->validator = $this->ci->get('EquipmentTypeValidator');

        $this->rm = $this->ci->get('rm');
        $this->rm->setRepo(EquipmentType::class);
    }

    // -----------------------------------------------------------------
	// GET functions
	// -----------------------------------------------------------------
	public function find($request, $response) {

        if(is_null($request)) {
            return $response->write("Invalid request.")->withStatus(400);
        }
        // TESTED THIS CODE, params works don't mess with it.
        $params = $request->getQueryParams();
        if ($params) {
            $array = $this->core->getEquipmentType($params);
        } else {
            $array = $this->core->getEquipmentType();
        }

        // return null;

        if($array) {
            return $response->withJson($array);
        } else {
            return $response->withStatus(404)->write("Something went wrong with the find function in EquipmentTypeController.");
        }

		// if(is_null($request)) {
  //           return $response->write("Invalid request.")->withStatus(400);
  //       }

  //       $params = $request->getQueryParams();

  //       if (empty($params)) {
		// 	$cursor = $this->dm->createQueryBuilder(EquipmentType::class)
		// 		->hydrate(false)
		// 		->getQuery()
		// 		->execute();

		// 	return $response->withJson(iterator_to_array($cursor));
  //       }

  //       // $returnValue = $this->rm->findAllByCriteria($params);
  //       $returnValue = $this->dm->getRepository(EquipmentType::class)->findBy($params);

  //       if ($returnValue) {
  //           // 200 status
  //           return $response->withJson($returnValue);
  //       }

  //       return $response->withStatus(404)->write("No equipment by those params.");
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

		// if(is_null($request))
		// {
  //           return $response->write("Invalid request.")->withStatus(400);
  //       }

  //       if (is_null($request->getParsedBody()))
		// {
  //           return $response->write("No body recieved.")->withStatus(400);
  //       }

  //       $json = $request->getParsedBody();

		// $validationResult = $this->validator->validateJSON($json);

		// if(!$validationResult['ok'])
		// {
		// 	return $response->write('Invalid JSON given. '.$validationResult['msg'])->withStatus(400);
		// }

		// //check if this already exists
  //       $find = $this->dm->getRepository(EquipmentType::class)->findOneBy(array('name' => $json['name']));

		// if ($find)
		// {
  //           return $response->write("This equipment type is already in the system.")->withStatus(400);
  //       }

		// $equipmentType = $this->createEquipmentTypeObj($json);
		// $this->dm->persist($equipmentType);
		// $this->dm->flush();

		// return $response->write("Successfully created new equipment type '".$json['name']."'.")->withStatus(200);
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

	// -----------------------------------------------------------------
	// Private helper functions below
	// -----------------------------------------------------------------

	private function createEquipmentTypeObj($json)
	{
		$equipmentType = new EquipmentType();
		$equipmentType->setName($json['name']);

		foreach($json['equipment_type_attributes'] as $json_attr)
		{
			$attribute = $this->createEquipmentTypeAttributeObj($json_attr);
			$equipmentType->addEquipmentTypeAttribute($attribute);
		}

		return $equipmentType;
	}

	private function createEquipmentTypeAttributeObj($json)
	{
		$attribute = new EquipmentTypeAttribute();

		$attribute->setName($json['name']);
		$attribute->setRequired($json['required']);
		$attribute->setUnique($json['unique']);
		$attribute->setDataType($json['data_type']);
		$attribute->setRegex($json['regex']);
		$attribute->setHelpComment($json['help_comment']);
		$attribute->setEnum($json['enum']);
		$attribute->setEnumValues($json['enum_values']);

		return $attribute;
	}


}
