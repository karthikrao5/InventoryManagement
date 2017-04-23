<?php
namespace App\Controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Interop\Container\ContainerInterface;

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

        // $authHeader = $request->getHeader("Authorization");

        // if (!$authHeader) {
        //     return $response->write("No auth header.")->withStatus(401);
        // }

        // $authResult = $this->authValidator->decodeToken($authHeader);

        // if(!$authResult["ok"]) {
        //     // decode messed up. Look into src\Core\Validator.php
        //     return $response->write($authResult["msg"])->withStatus($authResult["status"]);
        // }



        // $authData = $authResult["data"];

        

        // $this->logger->debug("authData in equipmentcontroller line 50: ".$authData["username"]);

        // // if the user is a renter, only return that renter's stuff
        // if($this->authValidator->isRenter($authData)) {

        //     // print_r($authData);
        //     // return null;

        //     // get the item and check loaned users
        //     // $user = $this->core->getUser(array("username" => $authData["username"]));
        //     // $loanedItems = $user["current_loans"];
        //     $loanedItems = $this->core->getLoan(array("username"=>$authData["username"]));
        //     return $response->withJson($loanedItems);


        // }
        // if($this->authValidator->isAdminOrHook($authData)) {
            
            // user is anyone else, ie hook or admin, return all 
            $params = $request->getQueryParams();

            // $this->logger->debug("Equipment query params: ".json_decode($params));
            if ($params) {
                $array = $this->core->getEquipment($params);
            } else {
                $array = $this->core->getEquipment();
            }

            if($array) {
                return $response->withJson($array)->withStatus(200);
            } else {
                return $response->withStatus(404)->withJson($array);
            }
        // }
        return $response->write("Something went wrong in fetching equipments.")->withJson(404);
       
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
            return $response->write("No body recieved.")->withStatus(400);
        }

        // $authHeader = $request->getHeader("Authorization");
        // if (!$authHeader) {
        //     return $response->write("No auth header.")->withStatus(401);
        // }

        // $authResult = $this->authValidator->decodeToken($authHeader);

        // // this will only go bad from jwt exceptions or missing token from authheader etc
        // if(!$authResult["ok"]) {
        //     // decode messed up. Look into src\Core\Validator.php
        //     return $response->write($authResult["msg"])->withStatus($authResult["status"]);
        // }
        
        
        // if(!$result["ok"]) {
        //     // decode messed up. Look into src\Core\Validator.php
        //     return $response->write($result["msg"])->withStatus($result["status"]);
        // }
         

        // $authData = $authResult["data"];

        // // if user is not admin or hook, do not auth
        // if(!$this->authValidator->isAdminOrHook($authData)) {
        //     return $response->write($authData["username"]." is not authorized to create equipments.")->withStatus(403);
        // }

        // if($this->authValidator->isRenter($authData)) {
        //     return $response->write("You are forbidden.")->withStatus(403);
        // }

        $result = $this->core->createEquipment($request->getParsedBody());

        if ($result["ok"]) {
            return $response->withStatus(201)->withJson($result);
        } else {
            return $response->withStatus(409)->withJson($result);
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

        // $authHeader = $request->getHeader("Authorization");
        // $authResult = $this->authValidator->decodeToken($authHeader);
        
        // if(!$result["ok"]) {
        //     // decode messed up. Look into src\Core\Validator.php
        //     return $response->write($result["msg"])->withStatus($result["status"]);
        // }

        // $authData = $authResult["data"];

        // if(!$this->authValidator->isAdminOrHook($authData)) {
        //     return $response->write("Forbidden.")->withStatus(403);
        // }

        if (is_null($request->getParsedBody())) {
            return $response->write("No body recieved.")->withStatus(400);
        }

        $result = $this->core->updateEquipment($request->getParsedBody());
        
        if($result['ok'])
        {
            return $response->withJson($result)->withStatus(200);
        }
        else
        {
            return $response->withJson($result)->withStatus(404);
        }
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
        
        return $response->withJson($result);

        if($result['ok'])
        {
            return $response->withJson($result)->withStatus(200);
        }
        else
        {
            return $response->withJson($result)->withStatus(404);
        }
    }
}
