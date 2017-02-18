<?php

namespace App\Models;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document( db="inventorytracking")
 */
class Equipment {


	/**
	 * @ODM\Id
	 */
	public $id;

	/**
	 * @ODM\Field(type="string")
	 */
	public $loaned_to;


	public function setLoaner($string) {
		$this->loaned_to = $string;
	}

	public function getLoaner() {
		return $this->loaned_to;
	}

}