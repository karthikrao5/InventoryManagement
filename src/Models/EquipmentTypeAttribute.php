<?php

namespace App\Models;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ODM\EmbeddedDocument
 */
class EquipmentTypeAttribute
{
	/** @ODM\Field(type="string") */
	public $name;
	
	/** @ODM\Field(type="boolean") */
	public $required;
	
	/** @ODM\Field(type="boolean") */
	public $unique;
	
	/** @ODM\Field(type="string") */
	public $data_type;
	
	/** @ODM\Field(type="string", nullable=true) */
	public $regex;
	
	/** @ODM\Field(type="string", nullable=true) */
	public $help_comment;
	
	/** @ODM\Field(type="boolean") */
	public $enum;
	
	/** @ODM\Field(type="collection", nullable=true) */
	public $enum_values;
	
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