<?php

namespace App\Models;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(db="inventorytracking")
 */
class Attribute {


	/**
	 * @ORM\Id
	 */
	public $id;

	// no cascade here because removing or modifying an attribute
	// should not affect the equipment that maps to it. 
	/** @ODM\ReferenceOne(targetDocument="Equipment") */
	public $equipment_id;

	/** @ODM\Field(type="string") */
	public $key;

	/** @ORM\Field(type="string") */
	public $value;


	
	
}