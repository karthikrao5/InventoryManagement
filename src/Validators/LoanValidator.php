<?php

namespace App\Validators;

use Interop\Container\ContainerInterface;

class LoanValidator extends AbstractValidator 
{
    public function __construct(ContainerInterface $ci) {
            parent::__construct($ci);
    }
}
