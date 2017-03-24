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

		// $data = $this->rm->getAllInCollection();
		$data = $this->dm->createQueryBuilder(Equipment::class)->eagerCursor(true)->getQuery()->execute();
		
		foreach ($data as $item) {
			print_r($item);
		}
		return null;

		// return $this->view->render($response, 'hp.twig', array(data => json_decode($data)));
	}

}