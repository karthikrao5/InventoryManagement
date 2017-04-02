<?php

namespace App\Models\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use App\Models\Equipment;

class EquipmentRepository extends DocumentRepository {


	/**
     * @return Cursor of all equipments and their reference arraya
     */
	public function getAllEquipment() {
		$cursor = $this->createQueryBuilder(Equipment::class)->hydrate(false)->
					field("attributes")->prime(true)->
					field("logs")->prime(true)->
					field("equipment")->prime(true)
					->getQuery()->execute();

		return $cursor;

		// $all = array();

		// foreach ($cursor as $equipment) {
		// 	$triple = array();
				
		// 	$triple[] = $equipment;
		// 	$attributes = array();
		// 	$logs = array();

		// 	foreach ($equipment->getAttributes() as $attr) {
		// 		$attributes[] = $attr;
		// 	}

		// 	foreach ($equipment->getLogs() as $log) {
		// 		$logs[] = $log;
		// 	}

		// 	$triple[] = $attributes;
		// 	$triple[] = $logs;
		// 	$all[] = $triple;
		// }
		// return $all;
	}

	public function findByParams(array $params) {
		$qb = $this->createQueryBuilder(Equipment::class)->hydrate(false)->
					field("attributes")->prime(true)->
					field("logs")->prime(true)->getQuery()->execute();
		return $qb;
		// foreach ($params as $key => $value) {
		// 	$qb->field()
		// }

	}
}