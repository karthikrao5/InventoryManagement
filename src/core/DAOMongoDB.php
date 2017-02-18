<?php
    namespace App\core;

    use App\core\DAOMongoDB as DAOMongoDB;
    use \MongoClient;
    use \MongoId;

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
            $equipments = $this->mongo->inventorytracking->equipments;
            $result = $equipments->findOne(array('_id' => new MongoId($id)));
            return $result;
        }
        
        public function getEquipmentByDepartmentTag($departmentTag)
        {
            $equipments = $this->mongo->inventorytracking->equipments;
            $result = $equipments->findOne(array('department_tag' => $departmentTag));
            return $result;
        }

        public function removeEquipment($id)
        {
            $equipment = $this->getEquipmentById($id);
            $equipments = $this->mongo->inventorytracking->equipments;
            
            if($equipments->remove(array('_id' => new MongoId($id))))
            {
                return $equipment; // Remove success.
            }
            else
            {
                return null; // Remove failed.
            }
        }
    }
?>
