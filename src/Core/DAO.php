<?php

namespace App\Core;

use \MongoClient;
use \MongoId;
use \MongoDate;

// I/O is done with PHP arrays.
// All functions expect and return fully joined PHP arrays.
class DAO
{
    private static $connectionString = null; // Null is equivalent to "mongodb://localhost:27017".

    /*
     * Loan related functions.
     */
    
    public function createLoan($loan)
    {
        $mongo = new MongoClient(DAO::$connectionString);
        $loans = $mongo->inventorytracking->loans;
        
        $loan['loaned_date'] = new MongoDate();
        $loan['due_date'] = new MongoDate(strtotime($loan['due_date']));
        $loan['is_return'] = false;
        $loan['logs'] = array();
        
        foreach($loan['equipments'] as $key => $value)
        {
            if(!($value instanceof MongoId))
            {
                $loan['equipments'][$key] = new MongoId($value);
            }
        }
        
        $result = $loans->insert($loan);
        $this->addCurrentLoanToUser($this->getUserId($loan['username']), $loan['_id']);
        
        foreach($loan['equipments'] as $equipmentId)
        {
            $this->updateEquipment($equipmentId, array('loaned_to' => $loan['username'], 'status' => "loaned"));
        }
        
        $log = $this->createLog();
        $log['reference_id'] = $loan['_id'];
        $log['document_type'] = "loan";
        $log['action_type'] = "create";
        $log['action_by'] = "hardcodedweb";
        $log['action_via'] = "hardcodedweb";
        $this->updateLog($log);
        $this->addLogToLoan($loan['_id'], $log['_id']);
        
        return $this->getLoan(array('_id' => $loan['_id']))[0];
    }
    
    private function getUserId($username)
    {
        $result = $this->getUser(array('username' => $username));
        
        return $result[0]['_id'];
    }
    
    public function getLoan($searchCriteria=null)
    {
        $mongo = new MongoClient(DAO::$connectionString);
        $loans = $mongo->inventorytracking->loans;
        
        $result = null;
        if(is_null($searchCriteria) || empty($searchCriteria))
        {
            //search all
            //probably not a good idea in terms of performance.
            $result = iterator_to_array($loans->find());
        }
        else
        {
            if(isset($searchCriteria['_id']) && !is_array($searchCriteria['_id']))
            {
                if(!($searchCriteria['_id'] instanceof MongoId))
                {
                    $searchCriteria['_id'] = new MongoId($searchCriteria['_id']);
                }
            }
            
            $result = iterator_to_array($loans->find($searchCriteria));
        }
        $mongo->close();
        
        if(!is_null($result) && !empty($result))
        {            
            $joinedLoans = array();
            foreach($result as $loan)
            {
                $loan['loaned_date'] = date('Y-m-d H:i:s', $loan['loaned_date']->sec);
                $loan['due_date'] = date('Y-m-d H:i:s', $loan['due_date']->sec);
                $joinedLoans[] = $this->joinLoan($loan);
            }
            
            return $joinedLoans;
        }
        
        return $result;
    }
    
    private function joinLoan($loan)
    {
        if(is_null($loan['equipments']) || empty($loan['equipments']))
        {
            $loan['equipments'] = array();
        }
        else
        {
            foreach($loan['equipments'] as $key => $equipmentId)
            {
                $loan['equipments'][$key] = new MongoId($equipmentId);
            }
            
            $equipments = $this->getEquipment(array('_id' => array('$in' => $loan['equipments']))); 
            $loan['equipments'] = $equipments;
        }
        
        $logs = $this->getLog(array('reference_id' => $loan['_id']));
        $loan['logs'] = $logs;
        
        return $loan;
    }
    
    public function updateLoan($loanId, $updateValues)
    {
        if(!($loanId instanceof MongoId))
        {
            $loanId = new MongoId($loanId);
        }
        
        unset($updateValues['_id']);
        
        if($updateValues['loaned_date'])
        {
            $updateValues['loaned_date'] = new MongoDate(strtotime($updateValues['loaned_date']));
        }
        
        if($updateValues['due_date'])
        {
            $updateValues['due_date'] = new MongoDate(strtotime($updateValues['due_date']));
        }
        
        $mongo = new MongoClient(DAO::$connectionString);
        $loans = $mongo->inventorytracking->loans;
        
        // if is_returned = true, change status and loaned_to values of equipments in this loan.
        if(isset($updateValues['is_return']) && $updateValues['is_return'])
        {
            $loan = $loans->findOne(array('_id' => $loanId));
            
            foreach($loan['equipments'] as $equipmentId)
            {
                $this->updateEquipment($equipmentId, array('status' => 'inventory', 'loaned_to' => null));
            }
            
            $userId = $this->getUserId($loan['username']);
            
            $this->removeCurrentLoanFromUser($userId, $loan['_id']);
            $this->addPastLoanToUser($userId, $loan['_id']);
        }
        
        $result = $loans->update(array('_id' => $loanId),
            array('$set' => $updateValues));
        
        $loan = $loans->findOne(array('_id' => $loanId));
        $mongo->close();
        
        $log = $this->createLog();
        $log['reference_id'] = $loanId;
        $log['document_type'] = "loan";
        $log['action_type'] = "edit";
        $log['action_by'] = "hardcodedweb";
        $log['action_via'] = "hardcodedweb";
        
        foreach($updateValues as $key => $value)
        {
            $log['changes'][] = (object)array("field_name" => $key, "old_value" => $loan[$key] , "new_value" => $value);
        }
        
        $result = $this->updateLog($log);
        $result = $this->addLogToLoan($loanId, $log['_id']);
        
        return $result;
    }
    
    public function addEquipmentToLoan($loanId, $equipmentId)
    {
        if(!($loanId instanceof MongoId))
        {
            $loanId = new MongoId($loanId);
        }
        
        if(!($equipmentId instanceof MongoId))
        {
            $equipmentId = new MongoId($equipmentId);
        }
        
        $mongo = new MongoClient(DAO::$connectionString);
        $loans = $mongo->inventorytracking->loans;
        
        $loan = $loans->findOne(array('_id' => $loanId));
        $equipmentsPrev = $loan['equipments'];
        
        $result = $loans->update(array('_id' => $loanId),
            array('$addToSet' => array('equipments' => $equipmentId)));
        
        $loan['equipments'][] = $loanId;
        
        $log = $this->createLog();
        $log['reference_id'] = $loanId;
        $log['document_type'] = "loan";
        $log['action_type'] = "edit";
        $log['action_by'] = "hardcodedweb";
        $log['action_via'] = "hardcodedweb";
        $log['changes'][] = (object)array('field_name' => 'equipments', 'old_value' => $equipmentsPrev, 'new_value' => $loan['equipments']);
        $this->updateLog($log);
        
        $result = $loans->update(array('_id' => $loanId),
            array('$addToSet' => array('logs' => $log['_id'])));
        $mongo->close();
        
        return $result;
    }
    
    public function removeEquipmentFromLoan($loanId, $equipmentId)
    {
        if(!($loanId instanceof MongoId))
        {
            $loanId = new MongoId($loanId);
        }
        
        if(!($equipmentId instanceof MongoId))
        {
            $equipmentId = new MongoId($equipmentId);
        }
        
        $mongo = new MongoClient(DAO::$connectionString);
        $loans = $mongo->inventorytracking->loans;
        
        $loan = $loans->findOne(array('_id' => $loanId));
        $equipmentsPrev = $loan['equipments'];
        
        $result = $loans->update(array('_id' => $loanId),
            array('$pull' => array('equipments' => $equipmentId)));
        
        foreach($loan['equipments'] as $key => $value)
        {
            if($value->{'$id'} == $equipmentId->{'$id'})
            {
                unset($loan['equipments'][$key]);
                $loan['equipments'] = array_values($loan['equipments']);
                break;
            }
        }
              
        $log = $this->createLog();
        $log['reference_id'] = $loanId;
        $log['document_type'] = "loan";
        $log['action_type'] = "edit";
        $log['action_by'] = "hardcodedweb";
        $log['action_via'] = "hardcodedweb";
        $log['changes'][] = (object)array('field_name' => 'equipments', 'old_value' => $equipmentsPrev, 'new_value' => $loan['equipments']);
        $this->updateLog($log);
        
        $result = $loans->update(array('_id' => $loanId),
            array('$addToSet' => array('logs' => $log['_id'])));
        
        $mongo->close();
        
        return $result;
    }
    
    public function deleteLoan($id)
    {
        if(!($id instanceof MongoId))
        {
            $id = new MongoId($id);
        }
        
        $mongo = new MongoClient(DAO::$connectionString);
        $loans = $mongo->inventorytracking->loans;
        
        $result = $loans->remove(array('_id' => $id));
        
        if($result['n'] == 0)
        {
            return $result;
        }
        
        $mongo->close();
        
        $log = $this->createLog();
        $log['reference_id'] = $id;
        $log['document_type'] = "loan";
        $log['action_type'] = "remove";
        $log['action_by'] = "hardcodedweb";
        $log['action_via'] = "hardcodedweb";
        $this->updateLog($log);
        
        return $result;
    }
    
    private function addLogToLoan($loanId, $logId)
    {
        if(!($loanId instanceof MongoId))
        {
            $loanId = new MongoId($loanId);
        }
        
        if(!($logId instanceof MongoId))
        {
            $logId = new MongoId($logId);
        }
        
        $mongo = new MongoClient(DAO::$connectionString);
        $loans = $mongo->inventorytracking->loans;
        
        $result = $loans->update(array('_id' => $loanId),
            array('$addToSet' => array('logs' => $logId)));
        
        $mongo->close();
        
        return $result;
    }
    
    /*
     *  User related functions.
     */
    
    public function createUser($user)
    {
        $mongo = new MongoClient(DAO::$connectionString);
        $users = $mongo->inventorytracking->users;
        
        $user['logs'] = array();
        $result = $users->insert($user);
        $mongo->close();
        
        $log = $this->createLog();
        $log['reference_id'] = $user['_id'];
        $log['document_type'] = "user";
        $log['action_type'] = "create";
        $log['action_by'] = "hardcodedweb";
        $log['action_via'] = "hardcodedweb";
        $this->updateLog($log);
        
        $this->addLogToUser($user['_id'], $log['_id']);
        
        return $this->getUser(array('_id' => $user['_id']))[0];
    }
    
    private function addLogToUser($userId, $logId)
    {
        if(!($userId instanceof MongoId))
        {
            $userId = new MongoId($userId);
        }
        
        if(!($logId instanceof MongoId))
        {
            $logId = new MongoId($logId);
        }
        
        $mongo = new MongoClient(DAO::$connectionString);
        $users = $mongo->inventorytracking->users;
        
        $result = $users->update(array('_id' => $userId),
            array('$addToSet' => array('logs' => $logId)));
        
        $mongo->close();
        return $result;
    }

    public function getUser($searchCriteria=null)
    {
        $mongo = new MongoClient(DAO::$connectionString);
        $users = $mongo->inventorytracking->users;
        
        $result = null;
        if(is_null($searchCriteria) || empty($searchCriteria))
        {
            //search all
            //probably not a good idea in terms of performance.
            $result = iterator_to_array($users->find());
        }
        else
        {
            if(isset($searchCriteria['_id']))
            {
                if(!($searchCriteria['_id'] instanceof MongoId))
                {
                    $searchCriteria['_id'] = new MongoId($searchCriteria['_id']);
                }
            }
            
            $result = iterator_to_array($users->find($searchCriteria));
        }
        $mongo->close();
        
        if(!is_null($result) && !empty($result))
        {            
            $joinedUsers = array();
            foreach($result as $user)
            {
                $joinedUsers[] = $this->joinUser($user);
            }
            
            return $joinedUsers;
        }
        
        return $result;
    }
    
    //join logs and loans
    private function joinUser($user)
    {
        $mongo = new MongoClient(DAO::$connectionString);
        
        $logs = $this->getLog(array('reference_id' => $user['_id']));
        
        $user['logs'] = $logs;
        
        $currentLoans = $this->getLoan(array('_id' => array('$in' => $user['current_loans'])));
        $user['current_loans'] = $currentLoans;
        
        $pastLoans = $this->getLoan(array('_id' => array('$in' => $user['past_loans'])));
        $user['past_loans'] = $pastLoans;
        
        return $user;
    }
    
    public function updateUser($id, $updateValues)
    {
        if(!($id instanceof MongoId))
        {
            $id = new MongoId($id);
        }
        unset($updateValues['_id']);
        
        $mongo = new MongoClient(DAO::$connectionString);
        $users = $mongo->inventorytracking->users;
        
        $user = $users->findOne(array('_id' => $id));
        $result = $users->update(array('_id' => $id),
                array('$set' => $updateValues));
        
        $log = $this->createLog();
        $log['reference_id'] = $user['_id'];
        $log['document_type'] = "user";
        $log['action_type'] = "edit";
        $log['action_by'] = "hardcodedweb";
        $log['action_via'] = "hardcodedweb";
        
        foreach($updateValues as $key => $value)
        {
            $log['changes'][] = (object)array('field_name' => $key, 'old_value' => $user[$key], 'new_value' => $value);
        }
        
        $this->updateLog($log);
        $this->addLogToUser($id, $log['_id']);
        
        return $result;
    }
    
    public function addCurrentLoanToUser($userId, $loanId)
    {
        if(!($userId instanceof MongoId))
        {
            $userId = new MongoId($userId);
        }
        
        if(!($loanId instanceof MongoId))
        {
            $loanId = new MongoId($loanId);
        }
        
        $mongo = new MongoClient(DAO::$connectionString);
        $users = $mongo->inventorytracking->users;
        
        $user = $users->findOne(array('_id' => $userId));
        $currentLoanPrev = $user['current_loans'];
        
        $result = $users->update(array('_id' => $userId),
            array('$addToSet' => array('current_loans' => $loanId)));
        $mongo->close();
        
        $user['current_loans'][] = $loanId;
        
        $log = $this->createLog();
        $log['reference_id'] = $userId;
        $log['document_type'] = "user";
        $log['action_type'] = "edit";
        $log['action_by'] = "hardcodedweb";
        $log['action_via'] = "hardcodedweb";
        $log['changes'][] = (object)array('field_name' => 'current_loans', 'old_value' => $currentLoanPrev, 'new_value' => $user['current_loans']);
        $this->updateLog($log);
        $this->addLogToUser($userId, $log['_id']);
        
        return $result;
    }
    
    public function addPastLoanToUser($userId, $loanId)
    {
        if(!($userId instanceof MongoId))
        {
            $userId = new MongoId($userId);
        }
        
        if(!($loanId instanceof MongoId))
        {
            $loanId = new MongoId($loanId);
        }
        
        $mongo = new MongoClient(DAO::$connectionString);
        $users = $mongo->inventorytracking->users;
        
        $user = $users->findOne(array('_id' => $userId));
        $pastLoanPrev = $user['past_loans'];
        
        $result = $users->update(array('_id' => $userId),
            array('$addToSet' => array('past_loans' => $loanId)));
        $user = $users->findOne(array('_id' => $userId));
        $mongo->close();
        
        $user['past_loans'][] = $loanId;
        
        $log = $this->createLog();
        $log['reference_id'] = $userId;
        $log['document_type'] = "user";
        $log['action_type'] = "edit";
        $log['action_by'] = "hardcodedweb";
        $log['action_via'] = "hardcodedweb";
        $log['changes'][] = (object)array('field_name' => 'past_loans', 'old_value' => $pastLoanPrev, 'new_value' => $user['past_loans']);
        $this->updateLog($log);
        $this->addLogToUser($userId, $log['_id']);
        
        return $result;
    }
    
    public function removeCurrentLoanFromUser($userId, $loanId)
    {
        if(!($userId instanceof MongoId))
        {
            $userId = new MongoId($userId);
        }
        
        if(!($loanId instanceof MongoId))
        {
            $loanId = new MongoId($loanId);
        }
        
        $mongo = new MongoClient(DAO::$connectionString);
        $users = $mongo->inventorytracking->users;
        
        $user = $users->findOne(array('_id' => $userId));
        $currentLoanPrev = $user['current_loans'];
        
        $result = $users->update(array('_id' => $userId),
            array('$pull' => array('current_loans' => $loanId)));
        
        $user = $users->findOne(array('_id' => $userId));
        
        $mongo->close();
        
        $log = $this->createLog();
        $log['reference_id'] = $userId;
        $log['document_type'] = "user";
        $log['action_type'] = "edit";
        $log['action_by'] = "hardcodedweb";
        $log['action_via'] = "hardcodedweb";
        $log['changes'][] = (object)array('field_name' => 'current_loans', 'old_value' => $currentLoanPrev, 'new_value' => $user['current_loans']);
        $this->updateLog($log);
        $this->addLogToUser($userId, $log['_id']);
        
        return $result;
    }
    
    public function removePastLoanFromUser($userId, $loanId)
    {
        if(!($userId instanceof MongoId))
        {
            $userId = new MongoId($userId);
        }
        
        if(!($loanId instanceof MongoId))
        {
            $loanId = new MongoId($loanId);
        }
        
        $mongo = new MongoClient(DAO::$connectionString);
        $users = $mongo->inventorytracking->users;
        
        $user = $users->findOne(array('_id' => $userId));
        $currentLoanPrev = $user['past_loans'];
        
        $result = $users->update(array('_id' => $userId),
            array('$pull' => array('past_loans' => $loanId)));
        
        $user = $users->findOne(array('_id' => $userId));
        $mongo->close();
        
        $log = $this->createLog();
        $log['reference_id'] = $userId;
        $log['document_type'] = "user";
        $log['action_type'] = "edit";
        $log['action_by'] = "hardcodedweb";
        $log['action_via'] = "hardcodedweb";
        $log['changes'][] = (object)array('field_name' => 'past_loans', 'old_value' => $currentLoanPrev, 'new_value' => $user['past_loans']);
        $this->updateLog($log);
        $this->addLogToUser($userId, $log['_id']);
        
        return $result;
    }
    
    public function removeUser($id)
    {
        if(!($id instanceof MongoId))
        {
            $id = new MongoId($id);
        }
        
        $mongo = new MongoClient(DAO::$connectionString);
        $users = $mongo->inventorytracking->users;
        
        $log = $this->createLog();
        $log['reference_id'] = $user['_id'];
        $log['document_type'] = "user";
        $log['action_type'] = "remove";
        $log['action_by'] = "hardcodedweb";
        $log['action_via'] = "hardcodedweb";
        $this->updateLog($log);
        
        $result = $users->remove(array('_id' => $id));
        $mongo->close();
        
        return $result;
    }
    
    public function findUserIdByUserName($username)
    {
        return $this->getUser(array('username' => $username))[0]['_id'];
    }
    
    /*
     * Log related functions.
     */
    
    public function getLog($searchCriteria=null)
    {
        $mongo = new MongoClient(DAO::$connectionString);
        $logs = $mongo->inventorytracking->logs;
        
        $result = null;
        if(is_null($searchCriteria) || empty($searchCriteria))
        {
            //search all
            //probably not a good idea in terms of performance.
            $result = iterator_to_array($logs->find());
        }
        else
        {
            if(isset($searchCriteria['_id']))
            {
                if(!($searchCriteria['_id'] instanceof MongoId))
                {
                    $searchCriteria['_id'] = new MongoId($searchCriteria['_id']);
                }
            }
            
            $result = iterator_to_array($logs->find($searchCriteria));
        }
        $mongo->close();
        
        if(!is_null($result) && !empty($result))
        {
            $result = array_values($result);
        }
        
        foreach($result as $key => $value)
        {
            $result[$key]['timestamp'] = date('Y-m-d H:i:s', $value['timestamp']->sec);
        }
        
        return $result;
    }

    // Returns a log array with '_id' as mongo id object.
    private function createLog()
    {
        $log = array();
        $log['reference_id'] = null;
        $log['document_type'] = null;
        $log['action_type'] = null;
        $log['timestamp'] = new MongoDate();
        $log['action_by'] = null;
        $log['action_via'] = null;
        $log['changes'] = array();

        $mongo = new MongoClient(DAO::$connectionString);
        $logs = $mongo->inventorytracking->logs;
        $result = $logs->insert($log);
        $mongo->close();

        return $log;
    }

    private function updateLog($log)
    {
        $mongo = new MongoClient(DAO::$connectionString);
        $logs = $mongo->inventorytracking->logs;

        $result = $logs->update(array('_id' => $log['_id']),
            array('$set' => $log));

        $mongo->close();
        return $result;
    }
    
    /*
     * Equipment related functions.
     */

    // Create
    
    // It is the CoreService's responsibility to find and pass in proper EquipmentType
    public function createEquipment($equipment, $equipmentType)
    {
        $mongo = new MongoClient(DAO::$connectionString);
        $equipments = $mongo->inventorytracking->equipments;

        // set equipment type id to equipment
        $equipment['equipment_type_id'] = $equipmentType['_id'];
        $equipment['created_on'] = new MongoDate();
        $equipment['last_updated'] = new MongoDate();
        $equipment['logs'] = array();
        $equipments->insert($equipment);

        // set ids to equipment attributes
        foreach($equipmentType['equipment_type_attributes'] as $equipTypeAttr)
        {
            foreach($equipment['attributes'] as &$equipAttr)
            {
                if($equipTypeAttr['name'] == $equipAttr['name'])
                {
                    $equipAttr['equipment_type_attribute_id'] = $equipTypeAttr['_id'];
                    $equipAttr['equipment_type_id'] = $equipmentType['_id'];
                    $equipAttr['equipment_id'] = $equipment['_id'];
                }
            }
        }

        $attributes = array(); //one with '_id's
        $attributeIds = array(); //only '_id's
        foreach($equipment['attributes'] as $attribute)
        {
                $updatedAttribute = $this->createEquipmentAttribute($attribute);
                $attributes[] = $updatedAttribute;
                $attributeIds[] = $updatedAttribute['_id'];
        }

        $equipment['attributes'] = $attributes;
        $equipments->update(array("_id" => $equipment['_id']),
                array('$set'=> array('attributes' => $attributeIds)));
        $mongo->close();
        
        $log = $this->createLog();
        $log['reference_id'] = $equipment['_id'];
        $log['document_type'] = "equipment";
        $log['action_by'] = "some_user";
        $log['action_via'] = "hard coded web";
        $log['action_type'] = "create";
        
        $result = $this->updateLog($log);
        
        $result = $this->addLogToEquipment($equipment['_id'], $log['_id']);
        
        $result = $this->getEquipment(array('_id' => $equipment['_id']));

        return $result[0];
    }

    public function createEquipmentAttribute($attribute)
    {
        $mongo = new MongoClient(DAO::$connectionString);
        $attributes = $mongo->inventorytracking->equipmentattributes;
        $attribute['logs'] = array();
        $attributes->insert($attribute);
        $mongo->close();
        
        $log = $this->createLog();
        $log['reference_id'] = $attribute['_id'];
        $log['document_type'] = "equipment_attribute";
        $log['action_by'] = "some_user";
        $log['action_via'] = "hard coded web";
        $log['action_type'] = "create";
        $result = $this->updateLog($log);
        
        $result = $this->addLogToEquipmentAttribute($attribute['_id'], $log['_id']);

        return $attribute;
    }
    
    private function addLogToEquipmentAttribute($attributeId, $logId)
    {
        $mongo = new MongoClient(DAO::$connectionString);
        $equipmentAttributes = $mongo->inventorytracking->equipmentattributes;

        $result = $equipmentAttributes->update(array('_id' => $attributeId),
                    array('$addToSet' => array('logs' => $logId)));
        $mongo->close();
        return $result;
    }
    
    private function addLogToEquipment($equipmentId, $logId)
    {
        $mongo = new MongoClient(DAO::$connectionString);
        $equipments = $mongo->inventorytracking->equipments;

        $result = $equipments->update(array('_id' => $equipmentId),
                    array('$addToSet' => array('logs' => $logId)));
        $mongo->close();
        return $result;
    }
    
    // Read

    public function getEquipment($searchCriteria=null)
    {
        $mongo = new MongoClient(DAO::$connectionString);
        $equipments = $mongo->inventorytracking->equipments;

        $result = null;
        if(is_null($searchCriteria) || empty($searchCriteria))
        {
            $result = iterator_to_array($equipments->find());
        } 
        else 
        {
            if(isset($searchCriteria['_id']) && !is_array($searchCriteria['_id']))
            {
                if(!($searchCriteria['_id'] instanceof MongoId))
                {
                    $searchCriteria['_id'] = new MongoId($searchCriteria['_id']);
                }
            }

            $result = iterator_to_array($equipments->find($searchCriteria));
        }
        $mongo->close();

        $newArr = array();
        if(!is_null($result) && !empty($result))
        {
            foreach($result as $equipment)
            {
                $equipment['created_on'] = date('Y-m-d H:i:s', $equipment['created_on']->sec);
                $equipment['last_updated'] = date('Y-m-d H:i:s', $equipment['last_updated']->sec);
                $newArr[] = $this->joinEquipment($equipment);
            }
        }
        
        return $newArr;
    }

    private function joinEquipment($equipment)
    {
        $mongo = new MongoClient(DAO::$connectionString);
        $attributes = $mongo->inventorytracking->equipmentattributes;
        $logsDB = $mongo->inventorytracking->logs;

        //This is an associative array, which doesn't convert to JSON array.
        $attrResult =  $this->getEquipmentAttribute(array('equipment_id' => $equipment['_id']));
        $attrs = $attrResult['equipment_attributes'];
        $array = array();

        foreach($attrs as $attr)
        {
            $array[] = $attr;
        }

        $equipment['attributes'] = $array;

        $logs = iterator_to_array($logsDB->find(array('reference_id' => $equipment['_id'])));
        $array = array();

        foreach($logs as $log)
        {
            $log['timestamp'] = date('Y-m-d H:i:s', $log['timestamp']->sec);
            $array[] = $log;
        }

        $equipment['logs'] = $array;

        $mongo->close();
        return $equipment;
    }
    
    public function getEquipmentAttribute($searchCriteria=null)
    {
        $mongo = new MongoClient(DAO::$connectionString);
        $equipmentAttributesCol = $mongo->inventorytracking->equipmentattributes;
        
        $result = array('ok' => false, 'msg' => null, 'n' => 0,'equipment_attributes' => null);
        
        if(is_null($searchCriteria) || empty($searchCriteria))
        {
            //search all
            $attrs = iterator_to_array($equipmentAttributesCol->find());
            
            if(is_null($attrs) || empty($attrs))
            {
                $result['msg'] = "Equipment Attribute Collection is empty.";
                return $result;
            }
            
            $result['equipment_attributes'] = $attrs;
        }
        else
        {
            if(isset($searchCriteria['_id']))
            {
                if(!($searchCriteria['_id'] instanceof MongoId))
                {
                    $searchCriteria['_id'] = new MongoId($searchCriteria['_id']);
                }
            }
            
            //search specific
            $attrs = iterator_to_array($equipmentAttributesCol->find($searchCriteria));
            
            if(is_null($attrs) || empty($attrs))
            {
                $result['msg'] = "Equipment Attributes not found with given search criteria.";
                $result['search_criteria'] = $searchCriteria;
                return $result;
            }
            
            $result['equipment_attributes'] = $attrs;
        }
        $mongo->close();
        
        $joinedAttributesArr = array();
        foreach($result['equipment_attributes'] as $attr)
        {
            $joinedAttributesArr[] = $this->joinEquipmentAttributeLog($attr);
        }
        
        $result['equipment_attributes'] = $joinedAttributesArr;
        $result['ok'] = true;
        $result['msg'] = "Successfully fetched Equipment Attributes with given search criteria.";
        $result['n'] = count($joinedAttributesArr);
        
        return $result;
    }
    
    private function joinEquipmentAttributeLog($attribute)
    {
        $mongo = new MongoClient(DAO::$connectionString);
        $logsCol = $mongo->inventorytracking->logs;
        
        $joinedLogsArr = array();
        foreach($attribute['logs'] as $logId)
        {
            $log = $logsCol->findOne(array('_id' => $logId));
            $log['timestamp'] = date('Y-m-d H:i:s', $log['timestamp']->sec);
            $joinedLogsArr[] = $log;
        }
        
        $mongo->close();
        $attribute['logs'] = $joinedLogsArr;
        
        return $attribute;
    }
    
    // Update
 
    public function updateEquipment($id, $updateValues)
    {
        unset($updateValues['_id']); // To avoid updating equipment id.
        
        // Create mongo id object if given id is not.
        if(!($id instanceof MongoId))
        {
            $id = new MongoId($id);
        }
        
        $log = $this->createLog();
        
        $mongo = new MongoClient(DAO::$connectionString);
        $equipments = $mongo->inventorytracking->equipments;
        
        $equipment = $equipments->findOne(array('_id' => $id));

        $result = $equipments->update(array('_id' => $id),
                array('$set' => $updateValues));
        $mongo->close();
        
        $result = $this->addLogToEquipment($id, $log['_id']);

        // set changes in the log
        $log['reference_id'] = $id;
        $log['document_type'] = "equipment";
        $log['action_by'] = "some_user";
        $log['action_via'] = "hard coded web";
        $log['action_type'] = "edit";
        
        foreach($updateValues as $key => $value)
        {
            $temp = array('field_name' => $key, "old_value" => $equipment[$key], "new_value" => $value);
            $log['changes'][] = (object)$temp; 
        }

        $this->updateLog($log);
        $this->updateEquipmentLastUpdated($id);

        return $result;
    }
    
    private function updateEquipmentLastUpdated($id)
    {
        if(!($id instanceof MongoId))
        {
            $id = new MongoId($id);
        }
        
        $mongo = new MongoClient(DAO::$connectionString);
        $equipments = $mongo->inventorytracking->equipments;
        
        $result = $equipments->update(array('_id' => $id),
                array('$currentDate' => array('last_updated' => true)));
        
        $mongo->close();
        
        return $result;
    }

    public function updateEquipmentAttriubte($id, $updateValues)
    {
        unset($updateValues['_id']);
        
        if(!($id instanceof MongoId))
        {
            $id = new MongoId($id);
        }

        $log = $this->createLog(); 

        $attribute = $this->getEquipmentAttribute(array('_id' => $id))['equipment_attributes'][0];
        
        $mongo = new MongoClient(DAO::$connectionString);
        $equipmentAttributes = $mongo->inventorytracking->equipmentattributes;
        $result = $equipmentAttributes->update(array('_id' => $id),
                array('$set' => $updateValues));
        $mongo->close();
        
        $result = $this->addLogToEquipmentAttribute($id, $log['_id']);

        // set changes in the log
        $log['reference_id'] = $id;
        $log['document_type'] = "equipment_attribute";
        $log['action_by'] = "some_user";
        $log['action_via'] = "hard coded web";
        $log['action_type'] = "edit";
        
        foreach($updateValues as $key => $value)
        {
            $temp = array('field_name' => $key, "old_value" => $attribute[$key], "new_value" => $value);
            $log['changes'][] = (object)$temp; 
        }
        
        $this->updateLog($log);
        $this->updateEquipmentLastUpdated($attribute['equipment_id']);

        return $result;
    }
    
    public function addEquipmentAttribute($equipmentId, $attribute)
    {
        if(!($equipmentId instanceof MongoId))
        {
            $equipmentId = new MongoId($equipmentId);
        }
        
        $equipment = $this->getEquipment(array('_id' => $equipmentId))[0];
        $equipmentType = $this->getEquipmentType(array('_id' => $equipment['equipment_type_id']))[0];

        //setting reference ids on attribute.
        $attribute['equipment_id'] = $equipment['_id'];
        $attribute['equipment_type_id'] = $equipmentType['_id'];

        foreach($equipmentType['equipment_type_attributes'] as $equipmentTypeAttr)
        {
            if($attribute['name'] == $equipmentTypeAttr['name'])
            {
                $attribute['equipment_type_attribute_id'] = $equipmentTypeAttr['_id'];
                break;
            }
        }

        $updatedAttribute = $this->createEquipmentAttribute($attribute);
        $attrRefArray = $this->getEquipmentAttributesArray($equipmentId);
        $attrRefArrayPrev = $attrRefArray;

        $attrRefArray[] = $updatedAttribute['_id'];
        $result = $this->updateEquipmentAttributesArray($equipmentId, $attrRefArray);
        
        //log for adding new attribute to equipment document.
        $log = $this->createLog();
        $log['reference_id'] = $equipmentId;
        $log['document_type'] = "equipment";
        $log['action_by'] = "some_user";
        $log['action_via'] = "hard coded web";
        $log['action_type'] = "edit";
        
        $log['changes'][] = (object)array('field_name' => "attributes", "old_value" => $attrRefArrayPrev, "new_value" => $attrRefArray);
        $this->updateLog($log);
        
        $result = $this->addLogToEquipment($equipmentId, $log['_id']);
        $this->updateEquipmentLastUpdated($equipmentId);

        return $result;
    }

    public function removeEquipmentAttribute($equipmentId, $attributeId)
    {
        if(!($equipmentId instanceof MongoId))
        {
            $equipmentId = new MongoId($equipmentId);
        }
        
        if(!($attributeId instanceof MongoId))
        {
            $attributeId = new MongoId($attributeId);
        }
        
        $mongo = new MongoClient(DAO::$connectionString);
        $equipmentAttributes = $mongo->inventorytracking->equipmentattributes;
        $result = $equipmentAttributes->remove(array('_id' => $attributeId));
        $mongo->close();
        
        $log = $this->createLog();
        $log['reference_id'] = $equipmentId;
        $log['document_type'] = "equipment";
        $log['action_by'] = "some_user";
        $log['action_via'] = "hard coded web";
        $log['action_type'] = "edit";

        $attrRefArray = $this->getEquipmentAttributesArray($equipmentId);
        $attrRefArrayPrev = $attrRefArray;
        foreach($attrRefArray as $key => $value)
        {
            if($value->{'$id'} == $attributeId)
            {
                unset($attrRefArray[$key]);
                $attrRefArray = array_values($attrRefArray); //rebasing array index.
                break;
            }
        }
        
        $log['changes'][] = (object)array('field_name' => "attributes", "old_value" => $attrRefArrayPrev, "new_value" => $attrRefArray);
        $this->updateLog($log);

        $result = $this->updateEquipmentAttributesArray($equipmentId, $attrRefArray);
        $result = $this->addLogToEquipment($equipmentId, $log['_id']);
        
        // Create a log for deleting this equipment attribute document.
        $log = $this->createLog();
        $log['reference_id'] = $attributeId;
        $log['document_type'] = "equipment_attribute";
        $log['action_by'] = "some_user";
        $log['action_via'] = "hard coded web";
        $log['action_type'] = "remove";
        $this->updateLog($log);
        
        $this->updateEquipmentLastUpdated($equipmentId);

        return $result;
    }

    private function getEquipmentAttributesArray($mongoId)
    {
        $mongo = new MongoClient(DAO::$connectionString);
        $equipments = $mongo->inventorytracking->equipments;
        $equipment = $equipments->findOne(array('_id' => $mongoId));
        $mongo->close();

        return $equipment['attributes'];
    }

    private function updateEquipmentAttributesArray($mongoId, $array)
    {
        $mongo = new MongoClient(DAO::$connectionString);
        $equipments = $mongo->inventorytracking->equipments;
        $result = $equipments->update(array('_id' => $mongoId),
                array('$set' => array('attributes' => $array)));

        $mongo->close();
        return $result;
    }
    
    // Delete

    public function deleteEquipment($equipmentId)
    {
        if(!($equipmentId instanceof MongoId))
        {
            $equipmentId = new MongoId($equipmentId);
        }

        $mongo = new MongoClient(DAO::$connectionString);
        $equipments = $mongo->inventorytracking->equipments;
        $attributes = $mongo->inventorytracking->equipmentattributes;
        
        //get all ids to make logs
        $targetAttrs = iterator_to_array($attributes->find(array('equipment_id' => $equipmentId)));
        $targetAttrIds = array();
        
        foreach($targetAttrs as $attr)
        {
            $targetAttrIds[] = $attr['_id'];
            
            //create remove log for each attribute
            $log = $this->createLog();
            $log['reference_id'] = $attr['_id'];
            $log['document_type'] = "equipment_attribute";
            $log['action_by'] = "some user";
            $log['action_via'] = "hard coded web";
            $log['action_type'] = "remove";
            $this->updateLog($log);
        }

        $result = $attributes->remove(array('_id' => array('$in' => $targetAttrIds)));
        
        foreach($equipmentIds as $equipmentId)
        {
            //create remove log for each equipment type document.
            $log = $this->createLog();
            $log['reference_id'] = $equipmentId;
            $log['document_type'] = "equipment";
            $log['action_by'] = "some user";
            $log['action_via'] = "hard coded web";
            $log['action_type'] = "remove";
            $this->updateLog($log);
        }
        
        $result = $equipments->remove(array('_id' => $equipmentId));

        $mongo->close();

        return $result;
    }
    
    /*
     * Equipment Type related functions.
     */
    
    // Create
       
    public function createEquipmentType($equipmentType)
    {
        $mongo = new MongoClient(DAO::$connectionString);
        $equipmentTypes = $mongo->inventorytracking->equipmenttypes;
        $equipmentType['logs'] = array();
        $result = $equipmentTypes->insert($equipmentType);

        $attributes = array(); //one with '_id's
        $attributeIds = array(); //only '_id's
        foreach($equipmentType['equipment_type_attributes'] as $attribute)
        {
            $attribute['equipment_type_id'] = $equipmentType['_id'];
            $updatedAttribute = $this->createEquipmentTypeAttribute($attribute);
            $attributes[] = $updatedAttribute;
            $attributeIds[] = $updatedAttribute['_id'];
        }

        $equipmentType['equipment_type_attributes'] = $attributeIds;
        $equipmentTypes->update(array('_id' => $equipmentType['_id']),
                array('$set' => $equipmentType));
        $mongo->close();

        $log = $this->createLog();
        $log['reference_id'] = $equipmentType['_id'];
        $log['document_type'] = "equipment_type";
        $log['action_by'] = "some_user";
        $log['action_via'] = "hard coded web";
        $log['action_type'] = "create";
        
        $result = $this->updateLog($log);

        $result = $this->addLogToEquipmentType($equipmentType['_id'], $log['_id']);

        $result = $this->getEquipmentType(array('_id' => $equipmentType['_id']));

        return $result[0];
    }

    public function createEquipmentTypeAttribute($equipmentTypeAttribute)
    {
        $mongo = new MongoClient(DAO::$connectionString);
        $equipmentTypesAttributes = $mongo->inventorytracking->equipmenttypeattributes;
        $equipmentTypeAttribute['logs'] = array();
        $equipmentTypesAttributes->insert($equipmentTypeAttribute);
        $mongo->close();

        $log = $this->createLog();
        $log['reference_id'] = $equipmentTypeAttribute['_id'];
        $log['document_type'] = "equipment_type_attribute";
        $log['action_by'] = "some_user";
        $log['action_via'] = "hard coded web";
        $log['action_type'] = "create";
        $result = $this->updateLog($log);

        $result = $this->addLogToEquipmentTypeAttribute($equipmentTypeAttribute['_id'], $log['_id']);

        return $equipmentTypeAttribute;
    }
    
    // Read
    
    public function getEquipmentType($searchCriteria=null)
    {
        $mongo = new MongoClient(DAO::$connectionString);
        $equipmentTypes = $mongo->inventorytracking->equipmenttypes;

        $result = null;
        if(is_null($searchCriteria) || empty($searchCriteria))
        {
            $result = iterator_to_array($equipmentTypes->find());
        } 
        else 
        {
            if(isset($searchCriteria['_id']))
            {
                if(!($searchCriteria['_id'] instanceof MongoId))
                {
                    $searchCriteria['_id'] = new MongoId($searchCriteria['_id']);
                }
            }

            $result = iterator_to_array($equipmentTypes->find($searchCriteria));
        }
        $mongo->close();

        $newArr = array();
        if(!is_null($result) && !empty($result))
        {
            foreach($result as $equipmentType)
            {
                $newArr[] = $this->joinEquipmentType($equipmentType);
            }
        }

        return $newArr;
    }

    private function joinEquipmentType($equipmentType)
    {
        $mongo = new MongoClient(DAO::$connectionString);
        $logsDB = $mongo->inventorytracking->logs;

        //This is an associative array, which doesn't convert to JSON array.
        $attrsResult = $this->getEquipmentTypeAttribute(array('equipment_type_id' => $equipmentType['_id']));
        $attrs = $attrsResult['equipment_type_attributes'];
        $array = array();

        foreach($attrs as $attr)
        {
            $array[] = $attr;
        }

        $equipmentType['equipment_type_attributes'] = $array;

        $logs = iterator_to_array($logsDB->find(array('reference_id' => $equipmentType['_id'])));
        $array = array();

        foreach($logs as $log)
        {
            $log['timestamp'] = date('Y-m-d H:i:s', $log['timestamp']->sec);
            $array[] = $log;
        }

        $equipmentType['logs'] = $array;

        $mongo->close();
        return $equipmentType;
    }

    public function getEquipmentTypeAttribute($searchCriteria=null)
    {
        $mongo = new MongoClient(DAO::$connectionString);
        $equipmentTypeAttributesCol = $mongo->inventorytracking->equipmenttypeattributes;
        
        $result = array('ok' => false, 'msg' => null, 'n' => 0,'equipment_type_attributes' => null);
        
        if(is_null($searchCriteria) || empty($searchCriteria))
        {
            //search all
            $attrs = iterator_to_array($equipmentTypeAttributesCol->find());
            
            if(is_null($attrs) || empty($attrs))
            {
                $result['msg'] = "Equipment Type Attribute Collection is empty.";
                return $result;
            }
            
            $result['equipment_type_attributes'] = $attrs;
        }
        else
        {
            if(isset($searchCriteria['_id']))
            {
                if(!($searchCriteria['_id'] instanceof MongoId))
                {
                    $searchCriteria['_id'] = new MongoId($searchCriteria['_id']);
                }
            }
            
            //search specific
            $attrs = iterator_to_array($equipmentTypeAttributesCol->find($searchCriteria));
            
            if(is_null($attrs) || empty($attrs))
            {
                $result['msg'] = "Equipment Type Attributes not found with given search criteria.";
                $result['search_criteria'] = $searchCriteria;
                return $result;
            }
            
            $result['equipment_type_attributes'] = $attrs;
        }
        $mongo->close();
        
        $joinedAttributesArr = array();
        foreach($result['equipment_type_attributes'] as $attr)
        {
            $joinedAttributesArr[] = $this->joinEquipmentTypeAttributeLog($attr);
        }
        
        $result['equipment_type_attributes'] = $joinedAttributesArr;
        $result['ok'] = true;
        $result['msg'] = "Successfully fetched Equipment Type Attributes with given search criteria.";
        $result['n'] = count($joinedAttributesArr);
        
        return $result;
    }
    
    private function joinEquipmentTypeAttributeLog($attribute)
    {
        $mongo = new MongoClient(DAO::$connectionString);
        $logsCol = $mongo->inventorytracking->logs;
        
        $joinedLogsArr = array();
        foreach($attribute['logs'] as $logId)
        {
            $log = $logsCol->findOne(array('_id' => $logId));
            $log['timestamp'] = date('Y-m-d H:i:s', $log['timestamp']->sec);
            $joinedLogsArr[] = $log;
        }
        
        $mongo->close();
        $attribute['logs'] = $joinedLogsArr;
        
        return $attribute;
    }
    
    // Update
    
    public function updateEquipmentTypeAttribute($id, $updateValues)
    {
        unset($updateValues['_id']);
        
        if(!($id instanceof MongoId))
        {
            $id = new MongoId($id);
        }

        $log = $this->createLog(); 
        
        $mongo = new MongoClient(DAO::$connectionString);
        $equipmentTypeAttributes = $mongo->inventorytracking->equipmenttypeattributes;

        $equipmentTypeAttribute = $equipmentTypeAttributes->findOne(array('_id' => $id));
        
        $result = $equipmentTypeAttributes->update(array('_id' => $id),
                array('$set' => $updateValues));
        
        $result = $equipmentTypeAttributes->update(array('_id' => $id),
                array('$addToSet' => array('logs' => $log['_id'])));
        
        $mongo->close();
        
        // set changes in the log
        $log['reference_id'] = $id;
        $log['document_type'] = "equipment_type_attribute";
        $log['action_by'] = "some_user";
        $log['action_via'] = "hard coded web";
        $log['action_type'] = "edit";
        
        foreach($updateValues as $key => $value)
        {
            $temp = array('field_name' => $key, "old_value" => $equipmentTypeAttribute[$key], "new_value" => $value);
            $log['changes'][] = (object)$temp; 
        }
        
        $this->updateLog($log);
        
        //if name is changing, update all equipment attributes with this name.
        if(isset($updateValues['name']))
        {
            $equipmentAttributes = $this->getEquipmentAttribute(array('equipment_type_attribute_id' => $id));
            
            foreach($equipmentAttributes['equipment_attributes'] as $equipmentAttribute)
            {
                $this->updateEquipmentAttriubte($equipmentAttribute['_id'], array('name' => $updateValues['name']));
            }
        }

        return $result;
    }
    
    public function addEquipmentTypeAttribute($equipmentTypeId, $equipmentTypeAttribute)
    {
        $equipmentTypeAttribute['equipment_type_id'] = new MongoId($equipmentTypeId);
        $updatedAttribute = $this->createEquipmentTypeAttribute($equipmentTypeAttribute);

        //Adding new equipment type to attribute.
        //Therefore this log belongs to equipment type document.
        $log = $this->createLog();
        $log['reference_id'] = new MongoId($equipmentTypeId);
        $log['document_type'] = "equipment_type";
        $log['action_by'] = "some_user";
        $log['action_via'] = "hard coded web";
        $log['action_type'] = "edit";

        $attrRefArray = $this->getEquipmentTypeAttributesArray(new MongoId($equipmentTypeId));
        $attrRefArrayPrev = $attrRefArray;
        $attrRefArray[] = $updatedAttribute['_id'];
        $result = $this->updateEquipmentTypeAttributesArray(new MongoId($equipmentTypeId), $attrRefArray);

        $log['changes'][] = (object)array('field_name' => "equipment_type_attributes", "old_value" => $attrRefArrayPrev, "new_value" => $attrRefArray);
        $this->updateLog($log);

        $result = $this->addLogToEquipmentType(new MongoId($equipmentTypeId), $log['_id']);

        return $result;
    }

    //pass mongo id objects
    private function addLogToEquipmentType($equipmentTypeId, $logId)
    {
        $mongo = new MongoClient(DAO::$connectionString);
        $equipmentTypes = $mongo->inventorytracking->equipmenttypes;

        $result = $equipmentTypes->update(array('_id' => $equipmentTypeId),
                    array('$addToSet' => array('logs' => $logId)));
        $mongo->close();
        return $result;
    }

    //pass mongo id objects
    private function addLogToEquipmentTypeAttribute($equipmentTypeAttributeId, $logId)
    {
        $mongo = new MongoClient(DAO::$connectionString);
        $equipmentTypeAttributes = $mongo->inventorytracking->equipmenttypeattributes;

        $result = $equipmentTypeAttributes->update(array('_id' => $equipmentTypeAttributeId),
                    array('$addToSet' => array('logs' => $logId)));
        $mongo->close();
        return $result;
    }

    //returns array of references
    private function getEquipmentTypeAttributesArray($mongoId)
    {
        $mongo = new MongoClient(DAO::$connectionString);
        $equipmentTypes = $mongo->inventorytracking->equipmenttypes;

        $equipmentType = $equipmentTypes->findOne(array('_id' => $mongoId));
        $mongo->close();

        return $equipmentType['equipment_type_attributes'];
    }

    private function updateEquipmentTypeAttributesArray($mongoId, $array)
    {
        $mongo = new MongoClient(DAO::$connectionString);
        $equipmentTypes = $mongo->inventorytracking->equipmenttypes;

        $result = $equipmentTypes->update(array('_id' => $mongoId),
                array('$set' => array('equipment_type_attributes' => $array)));

        $mongo->close();
        return $result;
    }

    public function removeEquipmentTypeAttribute($equipmentTypeId, $equipmentTypeAttributeId)
    {
        if(!($equipmentTypeId instanceof MongoId))
        {
            $equipmentTypeId = new MongoId($equipmentTypeId);
        }
        
        if(!($equipmentTypeAttributeId instanceof MongoId))
        {
            $equipmentTypeAttributeId = new MongoId($equipmentTypeAttributeId);
        }
        
        $mongo = new MongoClient(DAO::$connectionString);
        $equipmentTypeAttributes = $mongo->inventorytracking->equipmenttypeattributes;
        $equipmentTypeAttributes->remove(array('_id' => $equipmentTypeAttributeId));
        $mongo->close();

        //Adding new equipment type to attribute.
        //Therefore this log belongs to equipment type document.
        $log = $this->createLog();
        $log['reference_id'] = $equipmentTypeId;
        $log['document_type'] = "equipment_type";
        $log['action_by'] = "some_user";
        $log['action_via'] = "hard coded web";
        $log['action_type'] = "edit";

        $attrRefArray = $this->getEquipmentTypeAttributesArray($equipmentTypeId);
        $attrRefArrayPrev = $attrRefArray;
        foreach($attrRefArray as $key => $value)
        {
            if($value->{'$id'} == $equipmentTypeAttributeId)
            {
                unset($attrRefArray[$key]);
                $attrRefArray = array_values($attrRefArray); //rebasing array index.
                break;
            }
        }

        $log['changes'][] = (object)array('field_name' => "equipment_type_attributes", "old_value" => $attrRefArrayPrev, "new_value" => $attrRefArray);
        $this->updateLog($log);
        
        $result = $this->addLogToEquipmentType($equipmentTypeId, $log['_id']);

        $result = $this->updateEquipmentTypeAttributesArray($equipmentTypeId, $attrRefArray);

        // Create a log for deleting this equipment type attribute document.
        $log = $this->createLog();
        $log['reference_id'] = $equipmentTypeAttributeId;
        $log['document_type'] = "equipment_type_attribute";
        $log['action_by'] = "some_user";
        $log['action_via'] = "hard coded web";
        $log['action_type'] = "remove";
        
        $this->updateLog($log);
        
        return $result;
    }

    public function updateEquipmentType($id, $updateValues)
    {
        unset($updateValues['_id']); // To avoid updating equipment type document's id.
        
        // Create mongo id object if given id is not.
        if(!($id instanceof MongoId))
        {
            $id = new MongoId($id);
        }
        
        $log = $this->createLog(); 

        $mongo = new MongoClient(DAO::$connectionString);
        $equipmentTypes = $mongo->inventorytracking->equipmenttypes;
        
        $equipmentType = $equipmentTypes->findOne(array('_id' => $id));
        
        $result = $equipmentTypes->update(array('_id' => $id),
                array('$set' => $updateValues));

        $result = $equipmentTypes->update(array('_id' => $id),
                array('$addToSet' => array('logs' => $log['_id'])));
        
        $mongo->close();
        
        // set changes in the log
        $log['reference_id'] = $id;
        $log['document_type'] = "equipment_type";
        $log['action_by'] = "some_user";
        $log['action_via'] = "hard coded web";
        $log['action_type'] = "edit";
        
        foreach($updateValues as $key => $value)
        {
            $temp = array('field_name' => $key, "old_value" => $equipmentType[$key], "new_value" => $value);
            $log['changes'][] = (object)$temp; 
        }

        $this->updateLog($log);
        
        if(isset($updateValues['name']))
        {
            $equipments = $this->getEquipment(array('equipment_type_id' => $id));
            
            foreach($equipments as $equipment)
            {
                $this->updateEquipment($equipment['_id'], array('equipment_type_name' => $updateValues['name']));
            }
        }

        return $result;
    }
    
    // Delete
    
    public function deleteEquipmentType($equipmentTypeId)
    {
        if(!($equipmentTypeId instanceof MongoId))
        {
            $equipmentTypeId = new MongoId($value);
        }

        $mongo = new MongoClient(DAO::$connectionString);
        $equipmentTypes = $mongo->inventorytracking->equipmenttypes;
        $equipmentTypeAttributes = $mongo->inventorytracking->equipmenttypeattributes;
        $equipments = $mongo->inventorytracking->equipments;
        
        $result = array('ok' => false, 'msg' => null);
        
        //check if any equipments are using this equipment type.
        $foundEquipments = iterator_to_array($equipments->find(array('equipment_type_id' => $equipmentTypeId)));
        if(!empty($foundEquipments))
        {
            $result['msg'] = "Equipment Type cannot be deleted due to existing equipments.";
            return $result;
        }
        
        //get all ids to make logs
        $targetAttrs = iterator_to_array($equipmentTypeAttributes->find(array('equipment_type_id' => $equipmentTypeId)));
        $targetAttrIds = array();
        
        foreach($targetAttrs as $attr)
        {
            $targetAttrIds[] = $attr['_id'];
            
            //create remove log for each attribute
            $log = $this->createLog();
            $log['reference_id'] = $attr['_id'];
            $log['document_type'] = "equipment_type_attribute";
            $log['action_by'] = "some user";
            $log['action_via'] = "hard coded web";
            $log['action_type'] = "remove";
            $this->updateLog($log);
        }
        
        $result = $equipmentTypeAttributes->remove(array('_id' => array( '$in' => $targetAttrIds)));
        
        //create remove log for each equipment type document.
        $log = $this->createLog();
        $log['reference_id'] = $equipmentTypeId;
        $log['document_type'] = "equipment_type";
        $log['action_by'] = "some user";
        $log['action_via'] = "hard coded web";
        $log['action_type'] = "remove";
        $this->updateLog($log);
        
        $result = $equipmentTypes->remove(array('_id' => $equipmentTypeId));

        $mongo->close();

        return $result;
    }    
}
