<?php

namespace App\Core\Models;

class Equipment implements \JsonSerializable
{
    private $_id;
    private $department_tag;
    private $gt_tag;
    private $equipment_type_id;
    private $equipment_type_name;
    private $status;
    private $loaned_to;
    private $created_on;
    private $last_updated;
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
        return $this->equipment_type_id;
    }

    public function getEquipmentTypeIdString()
    {
        return $this->equipment_type_id->{'$id'};
    }

    public function setEquipmentTypeId($id)
    {
        $this->equipment_type_id = $id;
    }

    public function getDepartmentTag()
    {
        return $this->department_tag;
    }

    public function setDepartmentTag($tag)
    {
        $this->department_tag = $tag;
    }

    public function getGtTag()
    {
        return $this->gt_tag;
    }

    public function setGtTag($tag)
    {
        $this->gt_tag = $tag;
    }

    public function getEquipmentTypeName()
    {
        return $this->equipment_type_name;
    }

    public function setEquipmentTypeName($tag)
    {
        $this->equipment_type_name = $tag;
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
        return $this->loaned_to;
    }

    public function setLoanedTo($loanedTo)
    {
        $this->loaned_to = $loanedTo;
    }

    public function getCreatedOn()
    {
        return $this->created_on;
    }

    public function setCreatedOn($createdOn)
    {
        $this->created_on = $createdOn;
    }

    public function getLastUpdated()
    {
        return $this->last_updated;
    }

    public function setLastUpdated($lastUpdated)
    {
        $this->last_updated = $lastUpdated;
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
