<?php
namespace App\Core;

use App\Core\DAO;
use Interop\Container\ContainerInterface;

class CoreService
{
    private $dao;
    private $logger;
    private $container;
    private $equipmentValidator;
    private $equipmentTypeValidator;

    public function __construct(ContainerInterface $c)
    {
        $this->dao = new DAO();
        $this->container = $c;
        $this->logger = $c->get("logger");
        $this->equipmentValidator = $c->get("EquipmentValidator");
        $this->equipmentTypeValidator = $c->get('EquipmentTypeValidator');
    }
    
    /*
     * Log function (CUD operations are not allowed.)
     */
    
    public function getLog($requestJson)
    {
        $result = array('ok' => false, 'msg' => null, 'n' => 0,'logs' => null);
        
        $logs = $this->dao->getLog($requestJson);
        
        if(is_null($logs) || empty($logs))
        {
            $result['msg'] = 'Log not found.';
        }
        else
        {
            $result['ok'] = true;
            $result['msg'] = "Successfully found logs.";
            $result['n'] = count($logs);
            $result['logs'] = $logs;
        }
        
        return $result;
    }

    /*
     * User functions
     */

    public function createUser($requestJson)
    {
        $result = array('ok' => false, 'msg' => null, 'user' => null);
        
        $user = $this->dao->createUser($requestJson);
        
        if(is_null($user))
        {
            $result['msg'] = "Failed to create a user.";
        }
        else
        {
            $result['ok'] = true;
            $result['msg'] = "Successfully created user.";
            $result['user'] = $user;
        }
        
        return $result;
    }

    public function getUser($requestJson)
    {
        $result = array('ok' => false, 'msg' => null, 'users' => null);
        
        $users = $this->dao->getUser($requestJson);
        
        if(is_null($users) || empty($users))
        {
            $result['msg'] = "Failed to find users.";
        }
        else
        {
            $result['ok'] = true;
            $result['msg'] = "Successfully found users.";
            $result['users'] = $users;
            $result['n'] = count($users);
        }
        
        return $result;
    }

    public function updateUser($requestJson)
    {
        return $this->dao->updateUser($requestJson['_id'], $requestJson['updateValues']);
    }

    public function deleteUser($requestJson)
    {
        return $this->dao->removeUser($requestJson['_id']);
    }

    /*
     * Loan functions
     */

    public function createLoan($requestJson)
    {
        // Creating loan automatically adds this loan to user's current loans
        return $this->dao->createLoan($loan);
    }

    public function getLoan($requestJson)
    {
        return $this->dao->getLoan($requestJson);
    }

    public function updateLoan($requestJson)
    {
        return $this->dao->updateLoan($requestJson['_id'], $updateValues);
    }

    public function deleteLoan($requestJson)
    {
        return $this->dao->deleteLoan($requestJson['_id']);
    }
    
    /*
     * Equipment functions
     */

    public function createEquipment($requestJson)
    {
        $returnArray = array('ok' => false, 'msg' => null, 'equipment' => null);

        $result = $this->getEquipmentType(array('name' => $requestJson['equipment_type_name']));

        if(!$result['ok'])
        {
            $returnArray['ok'] = false;
            $returnArray['msg'] = "EquipmentType '".$requestJson['equipment_type_name']."' not found.";
            return $returnArray;
        }

        $updated = $this->dao->createEquipment($requestJson, $result['equipment_types'][0]);

        return array("ok" => true, "message" => "Successfully created Equipment '".$requestJson['department_tag']."' !",
                'equipment' => $updated);
    }

    public function getEquipment($requestJson=NULL)
    {
        $result = array('ok' => false, 'msg' => null, 'n' => 0, 'equipments' => null);

        $equipments = $this->dao->getEquipment($requestJson);

        if(is_null($equipments) || empty($equipments))
        {
            $result['msg'] = "Equipment not found with given search criteria.";
            return $result;
        }
        else
        {
            $result['ok'] = true;
            $result['msg'] = "Successfully found Equipments.";
            $result['n'] = count($equipments);
            $result['equipments'] = $equipments;
            return $result;
        }
    }

    public function updateEquipment($requestJson)
    {
        $result = array("ok" => false, "msg" => null, "updated_equipment" => null);

        if(isset($requestJson['update_equipment']) && !empty($requestJson['update_equipment']))
        {
            $result = $this->dao->updateEquipment($requestJson['_id'], $requestJson['update_equipment']);
        }

        if(isset($requestJson['update_equipment_attributes']) && !empty($requestJson['update_equipment_attributes']))
        {
            foreach ($requestJson['update_equipment_attributes'] as $updateTarget)
            {
                $result = $this->dao->updateEquipmentAttriubte($updateTarget['_id'], $updateTarget);
            }
        }

        if(isset($requestJson['add_equipment_attributes']) && !empty($requestJson['add_equipment_attributes']))
        {
            foreach($requestJson['add_equipment_attributes'] as $newAttribute)
            {
                $result = $this->dao->addEquipmentAttribute($requestJson['_id'], $newAttribute);
            }
        }

        if(isset($requestJson['remove_equipment_attributes']) && !empty($requestJson['remove_equipment_attributes']))
        {
            foreach($requestJson['remove_equipment_attributes'] as $removeTarget)
            {
                $result = $this->dao->removeEquipmentAttribute($requestJson['_id'], $removeTarget);
            }
        }

        return $result;
    }

    public function deleteEquipment($requestJson)
    {
        $result = array("ok" => false, "msg" => null);

        if(is_null($requestJson) || empty($requestJson))
        {
            $result['msg'] = "Json is empty or null.";
            return $result;
        }

        $daoResult = $this->dao->deleteEquipment($requestJson['ids']);

        $result['ok'] = $daoResult['ok'];
        $result['n'] = $daoResult['n'];

        return $result;
    }
    
    /*
     * EquipmentType functions
     */

    public function createEquipmentType($requestJson)
    {
        // $result = $this->equipmentTypeValidator->validateJSON($requestJson);

        // if(!$result['ok']) {
        //  $returnArray['ok'] = false;
        //  $resultArray['msg'] = $result['msg'];
        //  return $resultArray;
        // }

        $added = $this->dao->createEquipmentType($requestJson, $result['equipment_type'][0]);

        return array("ok" => true, "message" => "Successfully created EquipmentType '".$requestJson['name']."' !",
                'equipment_type' => $added);
    }

    public function getEquipmentType($requestJson=NULL)
    {
        $result = array('ok' => false, 'msg' => null, 'n' => 0, 'equipment_types' => null);

        $equipmentTypes = $this->dao->getEquipmentType($requestJson);

        if(is_null($equipmentTypes) || empty($equipmentTypes))
        {
            $result['msg'] = "Equipment Type not found with given search criteria.";
            return $result;
        }
        else
        {
            $result['ok'] = true;
            $result['msg'] = "Successfully found Equipment Types.";
            $result['n'] = count($equipmentTypes);
            $result['equipment_types'] = $equipmentTypes;
            return $result;
        }
    }
    
    public function updateEquipmentType($requestJson)
    {
        $result = array("ok" => false, "msg" => null, "updated_equipment_type" => null);

        //do not trust DAO in terms of semantics.
        //update equipment type document itself (not its attributes).
        if(isset($requestJson['update_equipment_type']))
        {
            $result = $this->dao->updateEquipmentType($requestJson['_id'], $requestJson['update_equipment_type']);
        }

        if(isset($requestJson['update_equipment_type_attributes']) && !empty($requestJson['update_equipment_type_attributes']))
        {
            foreach($requestJson['update_equipment_type_attributes'] as $updateTarget)
            {
                $result = $this->dao->updateEquipmentTypeAttribute($updateTarget['_id'], $updateTarget);
            }
        }

        if(isset($requestJson['add_equipment_type_attributes']) && !empty($requestJson['add_equipment_type_attributes']))
        {
            foreach($requestJson['add_equipment_type_attributes'] as $newAttribute)
            {
                $result = $this->dao->addEquipmentTypeAttribute($requestJson['_id'], $newAttribute);
            }
        }

        if(isset($requestJson['remove_equipment_type_attributes']) && !empty($requestJson['remove_equipment_type_attributes']))
        {
            foreach($requestJson['remove_equipment_type_attributes'] as $removeTarget)
            {
                $result = $this->dao->removeEquipmentTypeAttribute($requestJson['_id'], $removeTarget);
            }
        }

        return $result;
    }

    public function deleteEquipmentType($requestJson)
    {
        $result = array("ok" => false, "msg" => null);

        if(is_null($requestJson) || empty($requestJson))
        {
            $result['msg'] = "Json is empty or null.";
            return $result;
        }

        $daoResult = $this->dao->deleteEquipmentType($requestJson['ids']);

        $result['ok'] = $daoResult['ok'];
        $result['n'] = $daoResult['n'];

        return $result;
    }
}