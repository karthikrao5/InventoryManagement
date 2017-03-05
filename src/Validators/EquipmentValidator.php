<?php

namespace App\Validators;

use Doctrine\ODM\MongoDB\DocumentManager;
use Interop\Container\ContainerInterface;
use App\Models\Equipment;

class EquipmentValidator extends AbstractValidator {

	public function __construct(ContainerInterface $ci) {
		parent::__construct($ci);
	}

	public function validateID($id) {
		if ($id instanceof \MongoDB\BSON\ObjectID || preg_match('/^[a-f\d]{24}$/i', $id)) {
			return true;
		}
		return false;
	}
}