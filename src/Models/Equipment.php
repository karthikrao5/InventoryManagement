<?php

namespace App\Models;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use \DateTime;

/**
 * @ODM\Document(db="inventorytracking")
 */
class Equipment {

    // Common fields of Equipment document.
	/**
	 * @ODM\Id
	 */
	public $id;

    /** @ODM\Field(type="string") */
    public $department_tag;
    public function setDept($string) {
        $this->department_tag = $string;
    }
    
    /** @ODM\Field(type="string") */
    public $gt_tag;
    public function setGT($string) {
        $this->gt_tag = $string;
    }
    
    /** @ODM\ReferenceOne(targetDocument="EquipmentType", storeAs="id", cascade={"persist"}) */
    public $equipment_type;
    public function setEquipmentType(EquipmentType $type) {
        $this->equipment_type = $type;
    }
    
    /** @ODM\Field(type="string") */
    public $status;
    public function setStatus($string) {
        $this->status = $string;
    }
    
	/** @ODM\Field(type="string") */
	public $loaned_to;
    public function setLoaner($string) {
        $this->loaned_to = $string;
    }
    
    /**
	 * @ODM\Field(type="date")
	 */
    public $created_on;
    
    /**
	 * @ODM\Field(type="date")
	 */
    public $last_updated;
    
     
    /** @ODM\Field(type="string") */
    public $comment;
    public function setComment($string) {
        $this->comment = $string;
    }

    /** @ODM\ReferenceMany(targetDocument="Attribute", mappedBy="attribute") */
    public $attributes = array();
    public function addAttributeToEquipment($attr) {
        $this->attributes[] = $attr;
    }

    // public function __construct() {
    //     $date = new DateTime(null, new DateTimeZone('Eastern/New_York'));
    // }
    
}