<?php

namespace App\Validators;

use Interop\Container\ContainerInterface;

abstract class AbstractValidator
{
    protected $dm;

    protected $ci;

    protected $logger;

    protected $core;

    public function __construct(ContainerInterface $ci) {
        $this->ci = $ci;
        $this->logger = $ci->get('logger');
    }

    public function setCore($core)
    {
        $this->core = $core;
    }

    public function isMongoIdString($string)
    {
        return preg_match('/^[a-f\d]{24}$/i', $string);
    }

    public function isMongoIdObject($object)
    {
        return $object instanceof \MongoId;
    }
}
