<?php

namespace App\Models;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use \DateTime;

/**
 * @ODM\Document(db="inventorytracking", repositoryClass="App\Models\Repository\EquipmentRepository")
 */
class Equipment {

    // Common fields of Equipment document.
	/**
	 * @ODM\Id
	 */
	public $id;

    /** @ODM\Field(type="string") */
    public $department_tag;
    
    /** @ODM\Field(type="string") */
    public $gt_tag;

    // no cascade here because equipmentType will remain even if equipment mapping to it
    // is removed or modified in any way.
    /** @ODM\ReferenceOne(targetDocument="EquipmentType") */
    public $equipment_type;

    /** @ODM\Field(type="string") */
    public $status;
    
	/** @ODM\Field(type="string") */
	public $loaned_to;
    
    /** @ODM\Field(type="date") */
    public $created_on;
    
    /** @ODM\Field(type="date") */
    public $last_updated;
    
    /** @ODM\Field(type="string") */
    public $comment;

    /** @ODM\ReferenceMany(targetDocument="Attribute", mappedBy="attribute") */
    public $attributes = array();

    /** #ODM\ReferenceMany(targetDocument="Log", mappedBy="log") */
    public $logs = array();


    // setters
    public function setDept($string) {
        $this->department_tag = $string;
    }
    public function setComment($string) {
        $this->comment = $string;
    }
    public function setLoaner($string) {
        $this->loaned_to = $string;
    }
    public function setStatus($string) {
        $this->status = $string;
    }
    public function setEquipmentType(EquipmentType $type) {
        $this->equipment_type = $type;
    }
    public function setGT($string) {
        $this->gt_tag = $string;
    }
    public function addAttributeToEquipment($attr) {
        $this->attributes[] = $attr;
    }
    public function addLog($log) {
        
        $this->logs[] = $log;
    }
    
    
}