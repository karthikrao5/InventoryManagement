<?php

namespace App\Controller;

use Doctrine\ODM\MongoDB\DocumentManager;
use Interop\Container\ContainerInterface;

abstract class AbstractController {


	/**
	 *	DatabaseManager
	 */
	protected $dm;

	/**
	 *	ContainerInterface
	 */
	protected $ci;

	/**
	 * Monolog
	 */
	protected $logger;

	protected $core;

	public function __construct(ContainerInterface $c) {
		$this->ci = $c;
		$this->dm = $c->get('dm');
		$this->logger = $c->get('logger');
		$this->core = $c->get("core");
	}
}