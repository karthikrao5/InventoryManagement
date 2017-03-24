<?php

namespace App\Models;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Common\Collections\ArrayCollection;
use \DateTime;

/**
 * @ODM\Document(db="inventorytracking")
 */
class Log {

	/** @ODM\Id */
	public $id; 
	public function getId() { return $this->id; }

	/** @ODM\ReferenceOne(targetDocument="Equipment", inversedBy="logs") */
	public $equipment;
	public function getEquipment() { return $this->equipment; }
	public function setEquipment(Equipment $eq) { $this->equipment = $eq; }

	/** @ODM\Field(type="date") */
	public $created_on;
	public function getCreatedOn() { return $this->created_on; }

	/** @ODM\Field(type="string") */
	public $action_by;
	public function getActionBy() { return $action_by; }
	public function setActionBy($string) { $this->action_by = $string; }

	/** @ODM\Field(type="string") */
	public $action_via;
	public function getActionVia() { return $action_via; }
	public function setActionVia($string) { $this->action_via = $string; }

	public function __construct() {
		$time = new DateTime();
		$this->created_on = $time->getTimestamp();
	}
}