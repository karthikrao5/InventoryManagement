<?php

require 'iDAO.php';

class DAOMongoDB implements iDAO
{
    private static $dao = null;
    private static $connectionString = null; // Null is equivalent to "mongodb://localhost:27017".
    
    public static function getInstance()
    {
        if(DAOMongoDB::$dao == null)
        {
            DAOMongoDB::$dao = new DAOMongoDB(DAOMongoDB::$connectionString);
        }
        
        return DAOMongoDB::$dao;
    }
    
    private $mongo = null;
    
    private function __construct($connectionString)
    {
        $this->mongo = new MongoClient($connectionString);
    }
    
    public function createDocument ($databaseStr, $collectionStr, $document)
    {
        $collection = $this->mongo->$databaseStr->$collectionStr;
        $result = $collection->insert($document);
        return $document['_id']->{'$id'};
    }
    
    public function retrieveDocument ($databaseStr, $collectionStr, $query)
    {
        return null;
    }
    
    public function updateDocument ($databaseStr, $collectionStr, $filter, $keyvaluepairs)
    {
        return null;
    }
    
    public function deleteDocument ($databaseStr, $collectionStr, $id)
    {
        return null;
    }
}
?>
