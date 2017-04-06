<?php

namespace App\Core\Models;

class Attribute implements \JsonSerializable
{
    private $_id; // MongoId
    private $equipment_id; // MongoId
    private $equipment_type_id; // MongoId
    private $equipment_type_attribute_id; // MongoId

    private $name;
    private $value;

    // Returns mongo id object.
    public function getId()
    {
        return $this->_id;
    }

    public function setId($id)
    {
        $this->_id = $id;
    }

    public function getIdString()
    {
        return $this->_id->{'$id'};
    }

    public function getEquipmentId()
    {
        return $this->equipment_id;
    }

    public function setEquipmentId($id)
    {
        $this->equipment_id = $id;
    }

    public function getEquipmentIdString()
    {
        return $this->equipment_id->{'$id'};
    }

    public function getEquipmentTypeId()
    {
        return $this->equipment_type_id;
    }

    public function setEquipmentTypeId($id)
    {
        $this->equipment_type_id = $id;
    }

    public function getEquipmentTypeIdString()
    {
        return $this->equipment_type_id->{'$id'};
    }

    public function setEquipmentTypeAttributeId($id)
    {
        $this->equipment_type_attribute_id = $id;
    }

    public function getEquipmentTypeAttributeId()
    {
        return $this->equipment_type_attribute_id;
    }

    public function getEquipmentTypeAttributeIdString()
    {
        return $this->equipment_type_attribute_id->{'$id'};
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
