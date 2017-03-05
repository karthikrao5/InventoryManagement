<?php

namespace App\Validators;

use Doctrine\ODM\MongoDB\DocumentManager;
use Interop\Container\ContainerInterface;

abstract class AbstractValidator {

	protected $dm;

	protected $ci;

	protected $logger;

	public function __construct(ContainerInterface $ci) {
		$this->ci = $ci;
		$this->dm = $ci->get('dm');
		$this->logger = $ci->get('logger');
	}

}