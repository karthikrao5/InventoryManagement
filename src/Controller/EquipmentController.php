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
    protected $authValidator;
    protected $settings;

    public function __construct(ContainerInterface $c) {
        parent::__construct($c);
        $this->validator = $this->ci->get('EquipmentValidator');
        $this->authValidator = $this->ci->get('AuthValidator');
        $this->settings = $this->ci->get("settings");
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

        $authHeader = $request->getHeader("Authorization");
        $authResult = $this->authValidator->decodeToken($authHeader);
        
        if(!$result["ok"]) {
            // decode messed up. Look into src\Core\Validator.php
            return $response->write($result["msg"])->withStatus($result["status"]);
        }

        $authData = $authResult["data"];

        // if the user is a renter, only return that renter's stuff
        if($this->authValidator->isRenter($authData)) {
            // get the item and check loaned users
            $user = $this->core->getUser($authResult["data"]["username"]);
            $loanedItems = $user["current_loans"];
            return $response->withJson($loanedItems);
        } else {
            // user is anyone else, ie hook or admin, return all 
            // TESTED THIS CODE, params works don't mess with it.
            $params = $request->getQueryParams();
            // return null;
            $this->logger->debug("Equipment query params: ".json_decode($params));
            if ($params) {
                $array = $this->core->getEquipment($params);
            } else {
                $array = $this->core->getEquipment();
            }

            if($array) {
                return $response->withJson($array);
            } else {
                return $response->withStatus(404)->write("Something went wrong with the find function in EquipmentController.");
            }
        }
        return $response->write("Something went wrong in fetching equipments.")->withJson(404)
       
    }

// -----------------------------------------------------------------
// POST functions
// -----------------------------------------------------------------

    /**
     * input criteria are in the body of the request
     */
    public function create($request, $response) {

        if(is_null($request)) {
            return $response->write("Invalid request.")->withStatus(400);
        }

        if (is_null($request->getParsedBody())) {
            return $response->write("No body recieved.")->withStatus(200);
        }

        $authHeader = $request->getHeader("Authorization");
        $authResult = $this->authValidator->decodeToken($authHeader);
        
        if(!$result["ok"]) {
            // decode messed up. Look into src\Core\Validator.php
            return $response->write($result["msg"])->withStatus($result["status"]);
        }

        $authData = $authResult["data"];

        // if user is not admin or hook, do not auth
        if(!$this->authValidator->isAdminOrHook($authData)) {
            return $response->write("Forbidden.")->withStatus(403);
        }

        $result = $this->core->createEquipment($request->getParsedBody());

        if ($result["ok"]) {
            return $response->withStatus(201)->withJson($result);
        }

        return $response->write("Something went wrong.")->withStatus(404);
    }
    
// -----------------------------------------------------------------
// PUT functions
// -----------------------------------------------------------------
//  update/replace item by ID
    public function updateOne($request, $response, $args) {

        if(is_null($request)) {
            return $response->write("Invalid request.")->withStatus(400);
        }

        $authHeader = $request->getHeader("Authorization");
        $authResult = $this->authValidator->decodeToken($authHeader);
        
        if(!$result["ok"]) {
            // decode messed up. Look into src\Core\Validator.php
            return $response->write($result["msg"])->withStatus($result["status"]);
        }

        $authData = $authResult["data"];

        if(!$this->authValidator->isAdminOrHook($authData)) {
            return $response->write("Forbidden.")->withStatus(403);
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

        $authHeader = $request->getHeader("Authorization");
        $authResult = $this->authValidator->decodeToken($authHeader);
        
        if(!$result["ok"]) {
            // decode messed up. Look into src\Core\Validator.php
            return $response->write($result["msg"])->withStatus($result["status"]);
        }

        $authData = $authResult["data"];

        if(!$this->authValidator->isAdminOrHook($authData)) {
            return $response->write("Forbidden.")->withStatus(403);
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
