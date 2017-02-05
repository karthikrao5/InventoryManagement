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
    
    public function createEquipment ($collection, $document)
    {
        $equipments = $this->mongo->inventorytracking->equipments;
        $result = $equipments->insert($equipment);
        return $equipment['_id']->{'$id'};
    }
    
    public function retrieveDocument ($collection, $query)
    {
        return null;
    }
    
    public function updateDocument ($collection, $filter, $keyvaluepairs)
    {
        return null;
    }
    
    public function deleteDocument ($id)
    {
        return null;
    }
}
?>
