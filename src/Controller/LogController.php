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

        $params = $request->getQueryParams();
        if ($params) {
            $array = $this->core->getLog($params);
        } else {
            $array = $this->core->getLog();
        }

        if($array) {
            return $response->withJson($array)->withStatus(200);
        } else {
            return $response->withStatus(404)->withJson($array);
        }
    }
}
