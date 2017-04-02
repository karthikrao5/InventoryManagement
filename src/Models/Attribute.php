<?php

namespace App\Models;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use App\Models\Equipment;

/**
 * @ODM\Document(db="inventorytracking")
 */
class Attribute {


	/** @ODM\Id */
	public $id;
	public function getId() { return $this->id; }

	/** @ODM\Field(type="string") */
	public $key;
	public function getKey() { return $this->key; }
	public function setKey($string) { $this->key = $string; }

	/** @ODM\Field(type="string") */
	public$value;
	public function getValue() { return $this->value; }
	public function setValue($string) { $this->value = $string; }
	
}