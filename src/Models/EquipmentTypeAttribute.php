<?php

namespace App\Models;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/** @ODM\EmbeddedDocument */
class EquipmentTypeAttribute {

	/** @ODM\Field(type="string") */
	public $name;
	public function getName() { return $this->name; }
	public function setName($newName) { $this->name = $newName; }

	/** ODM\Bool */
	public $required;
	public function isRequired() { return $this->required; }
	public function setRequired($bool) { $this->required = $bool; }

	/** @ODM\Bool */
	public $unique;
	public function isUnique() { return $this->unique; }
	public function setUnique($bool) { $this->unique = $bool; }

	/** @ODM\Field(type="string") */
	public $data_type;
	public function getDataType() { return $this->data_type; }
	public function setDataType($string) { $this->data_type = $string; }

	/** @ODM\Field(type="string") */
	public $regex;
	public function getRegex() { return $this->regex; }
	public function setRegex($string) { $this->regex = $regex; }

	/** @ODM\Field(type="string") */
	public $help_comment;
	public function getHelpComment() { return $this->help_comment; }
	public function setHelpComment($string) { $this->help_comment = $string; }	
}