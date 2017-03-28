<?php

namespace App\Models;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Common\Collections\ArrayCollection;
use App\Models\EquipmentTypeAttribute;

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

	// leave the cascade to none right now. might set it to cascade=REMOVE
	// so it removes all the Equipments with this equipmentType
	/** @ODM\ReferenceOne(targetDocument="Equipment") */
	public $equipment_id;
	
	// cascade ALL means any change to EquipmentType's object is cascaded to all
	// references to other IDs. so removing EquipmentType from DocumentManager
	// also removes all of the mapped EquipmentTypeAttributes

	/** @ODM\EmbedMany(targetDocument="EquipmentTypeAttribute", strategy="addToSet") */
	public $equipment_type_attributes;
	
	public function __construct()
	{
		$this->equipment_type_attributes = new ArrayCollection();
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function addEquipmentTypeAttribute(EquipmentTypeAttribute $newAttr) {
		$this->equipment_type_attributes->add($newAttr);
		// $this->equipment_type_attributes[] = $newAttr;

	}
}
