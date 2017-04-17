<?php

namespace App\Validators;

use Interop\Container\ContainerInterface;

abstract class AbstractValidator {

	protected $dm;

	protected $ci;

	protected $logger;
        
        protected $core;

	public function __construct(ContainerInterface $ci) {
            $this->ci = $ci;
            $this->dm = $ci->get('dm');
            $this->logger = $ci->get('logger');
	}
        
        public function setCore($core)
        {
            $this->core = $core;
        }
}