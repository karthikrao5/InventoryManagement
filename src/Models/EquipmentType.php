<?php

namespace App\Models;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ODM\Document(db="inventorytracking")
 */
class EquipmentType {

	/** @ODM\Id */
	public $id;

	/** @ODM\Field(type="string") */
	public $name;

	// leave the cascade to none right now. might set it to cascade=REMOVE
	// so it removes all the Equipments with this equipmentType
	/** @ODM\ReferenceOne(targetDocument="Equipment") */
	public $equipment_id;
	
	// cascade ALL means any change to EquipmentType's object is cascaded to all
	// references to other IDs. so removing EquipmentType from DocumentManager
	// also removes all of the mapped EquipmentTypeAttributes

	/** ODM\EmbedMany(targetDocument="EquipmentTypeAttribute", cascade=ALL) */
	public $equipment_type_attributes;

	public function __construct() {
		// arraycollection instead of php array. 
		// arraycollection is a doctrine wrapper around a php array
		// http://www.doctrine-project.org/api/common/2.3/class-Doctrine.Common.Collections.ArrayCollection.html

		// go to that URL for API docs on arraycollection functions
		$this->equipment_type_attributes = new ArrayCollection();
	}

	public function setName($string) {
		$this->name = $name;
	}

	public function addEquipmentTypeAttribute($newAttr) {
		// add is the ArrayCollection function to append to the array
		$this->equipment_type_attributes->add($newAttr);
	}
}