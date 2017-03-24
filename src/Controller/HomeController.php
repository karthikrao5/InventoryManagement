<?php

namespace App\Controller;

use Slim\Views\Twig;
use App\Models\Equipment;
use App\Models\EquipmentType;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Interop\Container\ContainerInterface;
use Doctrine\ODM\MongoDB\DocumentManager;

class HomeController extends AbstractController {

	private $view;
	private $rm;

	public function __construct(ContainerInterface $c) {
        parent::__construct($c);
        $this->view = $this->ci->get('view');

        $this->rm = $this->ci->get('rm');
        $this->rm->setRepo(Equipment::class);
    }

	public function index($request, $response) {

		$data = $this->rm->getAllInCollection();

		// print_r(json_encode($data));
		// return null;
		return $this->view->render($response, 'hp.html', array(data => $data));
	}

}