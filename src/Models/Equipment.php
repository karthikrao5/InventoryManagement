<?php

namespace App\Models;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use \DateTime;
use App\Models\EquipmentType;
use App\Models\Attribute;
use App\Models\Log;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @ODM\Document(db="inventorytracking")
 */
class Equipment {

    // Common fields of Equipment document.
	/** @ODM\Id */
	private $id;
    public function getId() { return $this->id; }

    // no cascade here because equipmentType will remain even if equipment mapping to it
    // is removed or modified in any way.
    /** @ODM\ReferenceOne(targetDocument="EquipmentType") */
    private $equipment_type;
    public function getEquipmentType() { return $this->equipment_type; }
    public function setEquipmentType(EquipmentType $type) { $this->equipment_type = $type; }

    /** @ODM\Field(type="string") */
    private $department_tag;
    public function getDeptTag() { return $this->department_tag; }
    public function setDeptTag($string) { $this->department_tag = $string; }
    
    /** @ODM\Field(type="string") */
    private $gt_tag;
    public function getGtTag() { return $this->gt_tag; }
    public function setGtTag($string) { $this->gt_tag = $string; }

    /** @ODM\Field(type="string") */
    private $status;
    public function getStatus() { return $this->status; }
    public function setStatus($string) { $this->status = $string; }
    
	/** @ODM\Field(type="string") */
	private $loaned_to;
    public function getLoanedTo() { return $this->loaned_to; }
    public function setLoanedTo($string) { $this->loaned_to = $string; }
    
    /** @ODM\Field(type="date") */
    private $created_on;
    
    /** @ODM\Field(type="date") */
    private $last_updated;
    public function getLastedUpdated() { return $last_updated; }
    public function setLastUpdated(DateTime $update) { $this->last_updated = $update; }
    
    /** @ODM\Field(type="string") */
    private $comment;
    public function getComment() { return $this->comment; }
    public function setComment($string) { $this->comment = $string; }

    /** @ODM\ReferenceMany(targetDocument="Attribute", mappedBy="equipment", cascade={"all"}) */
    private $attributes;

    /** #ODM\ReferenceMany(targetDocument="Log", mappedBy="equipment", cascade={"all"}) */
    private $logs;


    public function __construct() {
        $this->attributes = new ArrayCollection();
        $this->logs = new ArrayCollection();
        $time = new DateTime();
        $this->created_on = $time->getTimestamp();
    }
    
    public function getAttributes() { return $this->attributes; }
    public function addAttribute(Attribute $attr) { $this->attributes[] = $attr; }

    public function getLogs() { return $this->logs; }
    public function addLog(Log $log) { $this->logs[] = $log; }
    
}