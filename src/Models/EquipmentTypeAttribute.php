<?php

namespace App\Models;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ODM\EmbeddedDocument(db="inventorytracking")
 */
class EquipmentTypeAttribute {

	/** @ODM\Id */
	public $id;

	/** @ODM\Field(type="string") */
	public $name;

	/** @ODM\Field(type="string") */
	public $unique;

	/** @ODM\Field(type="string") */
	public $data_type;

	/** @ODM\Field(type="string") */
	public $regex;

	/** @ODM\Field(type="string") */
	public $help_comment;

	public function __construct() {

	}

	public function setName($newName) {
		$this->name = $newName;
	}

	public function setHelp($newHelp)  {
		$this->help_comment = $newHelp;
	}

	public function setRegex($newRegex) {
		$this->regex = $newRegex;
	}
}