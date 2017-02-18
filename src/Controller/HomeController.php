<?php

namespace App\Controller;

use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
// use \Psr\Http\Message\ServerRequestInterface as Request;
// use \Psr\Http\Message\ResponseInterface as Response;

class HomeController {

	protected $logger;
	protected $view;

	public function __construct(Twig $view, LoggerInterface $logger) {

		$this->logger = $logger;

		$this->view = $view;
	}

	public function index($request, $response) {
		
		 // $this->logger->info("Home page action dispatched");

		return $this->view->render($response, 'template.html');
		// return "Home Controller Index";
	}

}