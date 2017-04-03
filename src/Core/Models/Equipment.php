<?php

namespace App\Core\Models;

class Equipment implements \JsonSerializable
{
    private $_id;
    private $departmentTag;
    private $gtTag;
    private $equipmentTypeId;
    private $equipmentTypeName;
    private $status;
    private $loanedTo;
    private $createdOn;
    private $lastUpdated;
    private $comments;
    private $attributes;

    public function __construct()
    {
        $this->attributes = array();
    }

    public function getId()
    {
        return $this->_id;
    }
    
    public function getIdString()
    {
        return $this->_id->{'$id'};
    }
    
    public function setId($id)
    {
        $this->_id = $id;
    }
    
    public function getEquipmentTypeId()
    {
        return $this->equipmentTypeId;
    }
    
    public function getEquipmentTypeIdString()
    {
        return $this->equipmentTypeId->{'$id'};
    }
    
    public function setEquipmentTypeId($id)
    {
        $this->equipmentTypeId = $id;
    }
    
    public function getDepartmentTag()
    {
        return $this->departmentTag;
    }
    
    public function setDepartmentTag($tag)
    {
        $this->departmentTag = $tag;
    }
    
    public function getGtTag()
    {
        return $this->gtTag;
    }
    
    public function setGtTag($tag)
    {
        $this->gtTag = $tag;
    }
    
    public function getEquipmentTypeName()
    {
        return $this->equipmentTypeName;
    }
    
    public function setEquipmentTypeName($tag)
    {
        $this->equipmentTypeName = $tag;
    }
    
    public function getStatus()
    {
        return $this->status;
    }
    
    public function setStatus($status)
    {
        $this->status = $status;
    }
    
    public function getLoanedTo()
    {
        return $this->loanedTo;
    }
    
    public function setLoanedTo($loanedTo)
    {
        $this->loanedTo = $loanedTo;
    }
    
    public function getCreatedOn()
    {
        return $this->createdOn;
    }
    
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;
    }
    
    public function getLastUpdated()
    {
        return $this->lastUpdated;
    }
    
    public function setLastUpdated($lastUpdated)
    {
        $this->lastUpdated = $lastUpdated;
    }
    
    public function getComments()
    {
        return $this->comments;
    }
    
    public function setComments($comments)
    {
        $this->comments = $comments;
    }
    
    public function getAttributes()
    {
        return $this->attributes;
    }
    
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }
    
    public function addAttribute($attribute)
    {
        $this->attributes[] = $attribute;
    }
    
    public function jsonSerialize()
    {	
        $attrs = array();
		
		foreach($this->attributes as $attr)
		{
			$attrs[] = $attr->jsonSerialize();
		}
		
        $vars = get_object_vars($this);
		$vars['attributes'] = $attrs;
		
		return $vars;
    }
}