<?php
namespace App\Controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Interop\Container\ContainerInterface;


class UserController extends AbstractController 
{
    protected $validator;
    protected $authValidator;

    public function __construct(ContainerInterface $c) {
        parent::__construct($c);
        //$this->validator = $this->ci->get('EquipmentValidator');
        $this->authValidator = $this->ci->get('AuthValidator');
    }
    
    public function find($request, $response)
    {
        if(is_null($request)) {
            return $response->write("Invalid request.")->withStatus(400);
        }

        // if(!$request->getHeader("Authorization")) {
        //     // if no header present, return unauthorized
        //     return $response->write("Forbidden")->withStatus(401); 
        // }

        // $authHeader = $request->getHeader("Authorization");
        // $authResult = $this->authValidator->decodeToken($authHeader);

        // if(!$authResult["ok"]) {
        //     // if decode does not work, return the error message and code
        //     return $response->write($authResult["msg"])->withStatus($authResult["status"]);
        // }


        $params = $request->getQueryParams();

        // if user is renter, check params to make sure they are only querying 
        // themselves, otherwise reject
        // if($this->authValidator->isRenter($authResult["data"])) {

        //     // query matches authorized user, return data
        //     if($params["username"] == $authResult["data"]["username"]) {
        $result = $this->core->getUser($params);
        
        if($result['ok'])
        {
            return $response->withJson($result)->withStatus(200);
        }
        else
        {
            return $response->withJson($result)->withStatus(404);
        }
    }
    
    public function create($request, $response) {
        // $authHeader = $request->getHeader("Authorization");
        // $token = str_replace("Bearer ", "", $authHeader[0]);
        // $result = $this->authValidator->decodeToken($token);
        
        // if(!$result["ok"]) {
        //     // decode messed up. Look into src\Core\Validator.php
        //     return $response->write($result["msg"])->withStatus($result["status"]);
        // }

        
        if(is_null($request))
        {
            return $response->write("Invalid request.")->withStatus(400);
        }

        if (is_null($request->getParsedBody()))
        {
            return $response->write("No body recieved.")->withStatus(400);
        }

        $result = $this->core->createUser($request->getParsedBody());

        if ($result["ok"]) {
            return $response->withStatus(201)->withJson($result);
        } else {
            return $response->withStatus(409)->withJson($result);
        }
    }
    
    public function update($request, $response)
    {
        if(is_null($request))
        {
            return $response->write("Invalid request.")->withStatus(400);
        }

        if (is_null($request->getParsedBody()))
        {
            return $response->write("No body recieved.")->withStatus(400);
        }

        $result = $this->core->updateUser($request->getParsedBody());

        if ($result["ok"]) {
            return $response->withStatus(200)->withJson($result);
        } else {
            return $response->withStatus(404)->withJson($result);
        }
    }
    
    public function delete($request, $response)
    {
        if(is_null($request))
        {
            return $response->write("Invalid request.")->withStatus(400);
        }

        if (is_null($request->getParsedBody()))
        {
            return $response->write("No body recieved.")->withStatus(400);
        }

        $result = $this->core->deleteUser($request->getParsedBody());

        if ($result["ok"]) {
            return $response->withStatus(200)->withJson($result);
        } else {
            return $response->withStatus(404)->withJson($result);
        }
    }
}

