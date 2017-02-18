<?php

namespace App\Models;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(db="inventorytracking", repositoryClass='App\Models\Repository\EquipmentRepository)
 */
class Equipment {

    // Common fields of Equipment document.
	/**
	 * @ODM\Id
	 */
	private $id;

    /**
	 * @ReferenceOne(targetDocument="EquipmentType")
	 */
    private $department_tag;
    
    /**
	 * @ODM\Field(type="string")
	 */
    private $gt_tag;
    
    /**
	 * @ODM\Field(type="string")
	 */
    private $equipment_type;
    
    /**
	 * @ODM\Field(type="string")
	 */
    private $status;
    
	/**
	 * @ODM\Field(type="string")
	 */
	private $loaned_to;
    
    /**
	 * @ODM\Field(type="timestamp")
	 */
    private $created_on;
    
    /**
	 * @ODM\Field(type="timestamp")
	 */
    private $last_updated;
    
    /**
	 * @ODM\Field(type="string")
	 */
    private $comment;
    
    /**
	 * @ODM\Field(type="hash")
     * Key as attribute name.
     * Value as attribute value.
     * Equipment type specific attributes.
	 */
    private $attributes;

    /**
	 * @ReferenceMany(targetDocument="Log")
	 */
    private $logs = array();
    
    // Setters
    public function setDepartmentTag($tag)
    {
        $this->department_tag = $tag;
    }
    
    public function setGTTag($tag)
    {
        $this->gt_tag = $tag;
    }
    
    public function setEquipmentType($type)
    {
        $this->equipment_type = $type;
    }
    
    public function setAttribute($key, $value)
    {
        $attribute[$key] = $value;
    }
    
    public function appendLog($log)
    {
        $log_id = $log['id'];
        $logs[$log_id] = $log;
    }
    
    // Getters
    public 
    
    
}