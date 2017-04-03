<?php

namespace App\Core\Models;

class EquipmentTypeAttribute implements \JsonSerializable
{
    private $_id;
	private $equipmentTypeId;
    private $name;
    private $required;
    private $unique;
    private $data_type;
    private $regex;
    private $helpComment;
    private $enum;
    private $enum_values = array();

    public function setId($id) {$this->_id = $id;}
    public function getId() {return $this->_id;}
	public function getIdString() {return $this->_id->{'$id'};}

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

    public function setHelpComment($helpComment) {$this->helpComment = $helpComment;}
    public function getHelpComment() {return $this->helpComment;}

    public function setEnum($enum) {$this->enum = $enum;}
    public function getEnum() {return $this->enum;}

    public function setEnumValues($enum_values) {$this->enum_values = $enum_values;}
    public function getEnumValues() {return $this->enum_values;}

    public function getEquipmentTypeId() {return $this->equipmentTypeId;}
    public function setEquipmentTypeId($equipmentTypeId) {$this->equipmentTypeId = $equipmentTypeId;}
	public function getEquipmentTypeIdString() {return $this->equipmentTypeId->{'$id'};}

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}

