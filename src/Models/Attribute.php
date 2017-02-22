<?php

namespace App\Models;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(db="inventorytracking")
 */
class Attribute {


	/**
	 * @ORM\Id
	 */
	public $id;

	
}