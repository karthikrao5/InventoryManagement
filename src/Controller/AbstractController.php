<?php

namespace App\Controller;

use Interop\Container\ContainerInterface;

abstract class AbstractController {

	/**
	 *	ContainerInterface
	 */
	protected $ci;

	/**
	 * Monolog
	 */
	protected $logger;

	protected $core;

	protected $view;

	public function __construct(ContainerInterface $c) {
		$this->ci = $c;
		$this->logger = $c->get('logger');
		$this->core = $c->get("core");
		$this->view = $c->get("view");
	}
}