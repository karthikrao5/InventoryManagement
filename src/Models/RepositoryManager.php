<?php

namespace App\Models;

use Interop\Container\ContainerInterface;
use Doctrine\ODM\MongoDB\DocumentManager;


class RepositoryManager {

	/*
	 * Interop\Container\ContainerInterface instance
	 */
	private $ci;

	/*
	 * Doctrine\ODM\MongoDB\DocumentManager instance
	 */
	private $dm;

	/**
	 * Doctrine\ODM\MongoDB\DocumentRepository instance
	 */
	private $repo;


	private $col;

	public function __construct(ContainerInterface $c) {
		$this->ci = $c;
		$this->dm = $c->get('dm');
	}

	/**
	 * @param class object for Model such as Equipment::class or EquipmentType::class
	 */
	public function setRepo($repoName) {
		$this->repo = $this->dm->getRepository($repoName);
	}

	/**
     * Get all document in a collection
     *
     * @param int $limit How many documents to return.
     * @param int $offset Offset from which to start listing documents.
     *
     * @return \Doctrine\MongoDB\Collection
     */ 
	public function getAllInCollection() {
		// findBy( array $criteria, array $sort = null, integer|null $limit = null, integer|null $skip = null )
		return $this->repo->findAll();
	}

	/**
     * Find all documents according a criteria
     *
     * @param  array  $criteria
     * @return \Doctrine\MongoDB\Collection
     */
	public function findAllByCriteria($criteria = array(), $limit = null, $offset = null) {

        return $this->repo->findBy($criteria, null, $limit, $offset);
	}

	/**
	 * Find one document by ID
	 * @param int $id is the mongoID
	 * @return mongoDB document
	 */

	public function findOneByField($field, $value) {
		return $this->repo->findOneBy(array($field => $value));
	}

	/**
	 * save a new document to this collection
	 */
	public function save($document) {
		$this->dm->persist($document);
		$this->dm->flush();
	}

	public function update() {
		$this->dm->flush();
	}

}