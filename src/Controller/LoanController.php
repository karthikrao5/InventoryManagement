<?php
namespace App\Controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Interop\Container\ContainerInterface;


class LoanController extends AbstractController 
{
    protected $validator;
    protected $authValidator;

    public function __construct(ContainerInterface $c) {
        parent::__construct($c);
        //$this->validator = $this->ci->get('EquipmentValidator');
        //$this->authValidator = $this->ci->get('AuthValidator');
    }
    
    public function create($request, $response)
    {
        if(is_null($request))
        {
            return $response->write("Invalid request.")->withStatus(400);
        }

        if (is_null($request->getParsedBody()))
        {
            return $response->write("No body recieved.")->withStatus(400);
        }
        
        $result = $this->core->createLoan($request->getParsedBody());
        
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
        
        $result = $this->core->updateLoan($request->getParsedBody());
        
        if ($result["ok"]) {
            return $response->withStatus(200)->withJson($result);
        } else {
            return $response->withStatus(404)->withJson($result);
        }
    }
    
    public function get($request, $response)
    {
        if(is_null($request)) {
            return $response->write("Invalid request.")->withStatus(400);
        }

        //$user = $authValidator->getAuthUser();
        // $this->authValidator->isAccessible($user["user_type"], )

        // TESTED THIS CODE, params works don't mess with it.
        // Nested search not supported.
        $params = $request->getQueryParams();
        if ($params) {
            $result = $this->core->getLoan($params);
        } else {
            $result = $this->core->getLoan();
        }

        if($result['ok']) {
            return $response->withJson($result)->withStatus(200);
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

        $result = $this->core->deleteLoan($request->getParsedBody());

        if ($result["ok"]) {
            return $response->withStatus(200)->withJson($result);
        } else {
            return $response->withStatus(404)->withJson($result);
        }
    }
}
