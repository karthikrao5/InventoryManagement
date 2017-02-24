<?php

namespace App\Models;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;


/**
 * @ODM\Document(db="inventorytracking")
 */
class EquipmentType {

	/**
	 * @ODM\Id
	 */
	public $id;

	/** @ODM\Field(type="string") */
	public $name;
	public function setName($string) {
		$this->name = $string;
	}


	public function __construct($name) {
		$this->name = $name;
	}





}