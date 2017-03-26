<?php

namespace App\Models;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Common\Collections\ArrayCollection;

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

	/** @ODM\EmbedMany(targetDocument="EquipmentTypeAttribute") */
	public $equipment_type_attributes;
	
	public function __construct()
	{
		$this->equipment_type_attributes = array();
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function addEquipmentTypeAttribute($newAttr) {
		$this->equipment_type_attributes[] = $newAttr;
	}
}

/**
 * @ODM\EmbeddedDocument
 */
class EquipmentTypeAttribute
{
	/** @ODM\Field(type="string") */
	private $name;
	
	/** @ODM\Field(type="boolean") */
	private $required;
	
	/** @ODM\Field(type="boolean") */
	private $unique;
	
	/** @ODM\Field(type="string") */
	private $data_type;
	
	/** @ODM\Field(type="string", nullable=true) */
	private $regex;
	
	/** @ODM\Field(type="string", nullable=true) */
	private $help_comment;
	
	/** @ODM\Field(type="boolean") */
	private $enum;
	
	/** @ODM\Field(type="collection", nullable=true) */
	private $enum_values;
	
	public function __construct()
	{
		$this->enum_values = new ArrayCollection();
	}
	
	public function setName($name) {$this->name = $name;}
	public function getName() {return $this->name;}
	
	public function setRequired($required) {$this->required = $required;}
	public function getRequired() {return $this->required;}
	
	public function setUnique($unique) {$this->unique = $unique;}
	public function getUnique() {return $this->unique;}
	
	public function setDataType($dataType) {$this->data_type = $dataType;}
	public function getDataType() {return $this->data_type;}
	
	public function setRegex($regex) {$this->regex = $regex;}
	public function getRegex() {return $this->regex;}
	
	public function setHelpComment($helpComment) {$this->help_comment = $helpComment;}
	public function getHelpComment() {return $this->help_comment;}
	
	public function setEnum($enum) {$this->enum = $enum;}
	public function getEnum() {return $this->enum;}
	
	public function setEnumValues($enum_values) {$this->enum_values = $enum_values;}
	public function getEnumValues() {return $this->enum_values;}
}