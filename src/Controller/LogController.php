<?php
namespace App\Controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Interop\Container\ContainerInterface;


class LogController extends AbstractController 
{


    protected $validator;
    protected $authValidator;

    public function __construct(ContainerInterface $c) {
        parent::__construct($c);
        //$this->validator = $this->ci->get('EquipmentValidator');
        //$this->authValidator = $this->ci->get('AuthValidator');
    }
    
    public function find($request, $response)
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
            $array = $this->core->getLog($params);
        } else {
            $array = $this->core->getLog();
        }

        if($array) {
            return $response->withJson($array);
        } else {
            return $response->withStatus(404)->write("Something went wrong with the find function in LogController.");
        }
    }
}