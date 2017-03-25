<?php
namespace App\Controller;


use App\Models\Equipment;
use App\Models\EquipmentType;
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
		if(is_null($request)) 
		{
            return $response->write("Invalid request.")->withStatus(400);
        }

        if (is_null($request->getParsedBody())) 
		{
            return $response->write("No body recieved.")->withStatus(400);
        }

        $json = $request->getParsedBody();
		
		if(!$this->validator->validateJSON($json))
		{
			return $response->write('Invalid JSON given.')->withStatus(400);
		}
		
		//check if this already exists
        $find = $this->dm->getRepository(EquipmentType::class)->findOneBy(array('name' => $json['name']));
		
		if ($find) 
		{
            return $response->write("This equipment type is already in the system.")->withStatus(400);
        }
		
		$equipmentType = new EquipmentType();
		$equipmentType->setName($json['name']);
		
		$this->dm->persist($equipmentType);
		$this->dm->flush();
		
		return $response->write("Successfully entered new equipment type '".$json['name']."'")->withStatus(200);
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