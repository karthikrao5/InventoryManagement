<?php

namespace App\Core\Models;

class Attribute implements \JsonSerializable
{
    private $_id; // MongoId
    private $equipmentId; // MongoId
    private $equipmentTypeId; // MongoId
    private $equipmentTypeAttributeId; // MongoId

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
        return $this->equipmentId;
    }

    public function setEquipmentId($id)
    {
        $this->equipmentId = $id;
    }

    public function getEquipmentIdString()
    {
        return $this->equipmentId->{'$id'};
    }

    public function getEquipmentTypeId()
    {
        return $this->equipmentTypeId;
    }

    public function setEquipmentTypeId($id)
    {
        $this->equipmentTypeId = $id;
    }

    public function getEquipmentTypeIdString()
    {
        return $this->equipmentTypeId->{'$id'};
    }

    public function setEquipmentTypeAttributeId($id)
    {
        $this->equipmentTypeAttributeId = $id;
    }

    public function getEquipmentTypeAttributeId()
    {
        return $this->equipmentTypeAttributeId;
    }

    public function getEquipmentTypeAttributeIdString()
    {
        return $this->equipmentTypeAttributeId->{'$id'};
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
    
    public function JsonSerialize()
    {	
        return get_object_vars($this);
    }
}
