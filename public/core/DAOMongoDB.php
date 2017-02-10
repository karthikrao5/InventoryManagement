<?php

require_once 'Interfaces.php';

class DAOMongoDB implements IDAO
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
    
    public function createEquipment($document)
    {
		$equipments = $this->mongo->inventorytracking->equipments;
		$result = $equipments->insert($document);
		return $document['_id']->{'$id'};
    }
	
	public function updateEquipment($document)
	{
		throw new BadMethodCallException('Not implemented.');
	}
	
	public function getEquipmentById($id)
	{
		throw new BadMethodCallException('Not implemented.');
	}
	
	public function removeEquipment($id)
	{
		throw new BadMethodCallException('Not implemented.');
	}
}
?>
