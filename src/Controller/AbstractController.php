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

	public function __construct(ContainerInterface $c) {
		$this->ci = $c;
		$this->dm = $c->get('dm');
	}
}