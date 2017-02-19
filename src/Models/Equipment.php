<?php

namespace App\Models;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(db="inventorytracking")
 */
class Equipment {

    // Common fields of Equipment document.
	/**
	 * @ODM\Id
	 */
	private $id;

    /**
	 * ReferenceOne(targetDocument="EquipmentType")
     * @ODM\Field(type="string")
	 */
    private $department_tag;
    public function setDept($string) {
        $this->department_tag = $string;
    }
    
    /**
	 * @ODM\Field(type="string")
	 */
    private $gt_tag;
    public function setGT($string) {
        $this->gt_tag = $string;
    }
    
    /**
	 * @ODM\Field(type="string")
	 */
    private $equipment_type;
    public function setEquipmentType($string) {
        $this->equipment_type = $string;
    }
    
    /**
	 * @ODM\Field(type="string")
	 */
    private $status;
    public function setStatus($string) {
        $this->status = $string;
    }
    
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
    public function setComment($string) {
        $this->comment = $string;
    }
    
  //   *
	 // * @ODM\Field(type="hash")
  //    * Key as attribute name.
  //    * Value as attribute value.
  //    * Equipment type specific attributes.
	 
  //   private $attributes;

  //   *
	 // * @ReferenceMany(targetDocument="Log")
	 
  //   private $logs = array();
    
    
    // public function setEquipmentType($type)
    // {
    //     $this->equipment_type = $type;
    // }
    
    // public function setAttribute($key, $value)
    // {
    //     $attribute[$key] = $value;
    // }

    public function setLoaner($string) {
        $this->loaned_to = $string;
    }
    
    // public function appendLog($log)
    // {
    //     $log_id = $log['id'];
    //     $logs[$log_id] = $log;
    // }
    
    // Getters
    public function getLoaner() {
        return $this->loaned_to;
    }
    
    
}