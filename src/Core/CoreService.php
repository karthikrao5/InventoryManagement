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
    private $userValidator;
    private $loanValidator;

    public function __construct(ContainerInterface $c)
    {
        $this->dao = new DAO();
        $this->container = $c;
        $this->logger = $c->get("logger");
        $this->equipmentValidator = $c->get("EquipmentValidator");
        $this->equipmentValidator->setCore($this);
        $this->equipmentTypeValidator = $c->get('EquipmentTypeValidator');
        $this->equipmentTypeValidator->setCore($this);
        $this->userValidator = $c->get('UserValidator');
        $this->userValidator->setCore($this);
        $this->loanValidator = $c->get('LoanValidator');
        $this->loanValidator->setCore($this);
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
            $result['msg'] = 'Log not found with given search criteria.';
            $result['search_criteria'] = $requestJson;
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
        
        $validationResult = $this->userValidator->isValidCreateJson($requestJson);
        
        if(!$validationResult['ok'])
        {
            return $validationResult;
        }
        
        if($this->userValidator->isUsernameExist($requestJson))
        {
           $result['msg'] = "Username '".$requestJson['username']."' already exists.";
           return $result;
        }
        
        $requestJson['current_loans'] = array();
        $requestJson['past_loans'] = array();
        
        $user = $this->dao->createUser($requestJson);
        
        if(is_null($user))
        {
            $result['msg'] = "Failed to create a user.";
        }
        else
        {
            $result['ok'] = true;
            $result['msg'] = "Successfully created new user.";
            $result['user'] = $user;
        }
        
        return $result;
    }

    public function getUser($requestJson)
    {
        $result = array('ok' => false, 'msg' => null, 'users' => null);
        
        if(isset($requestJson['_id']))
        {
            if($this->userValidator->isMongoIdString($requestJson['_id']))
            {
                $requestJson['_id'] = new \MongoId($requestJson['_id']);
            }
            else 
            {
                $result['msg'] = "Invalid ID string given.";
                return $result;
            }
        }
        
        $users = $this->dao->getUser($requestJson);
        
        if(is_null($users) || empty($users))
        {
            $result['msg'] = "Failed to find user with given search criteria.";
            $result['search_criteria'] = $requestJson;
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
        $validationResult = $this->userValidator->isValidUpdateJson($requestJson);
        
        if(!$validationResult['ok'])
        {
            return $validationResult;
        }
        
        return $this->updateUserHelper($requestJson);
    }
    
    private function updateUserHelper($requestJson)
    {
        $result = array('ok' => false, 'msg' => null, 'user' => null);
        
        if(isset($requestJson['edit_user']))
        {
            $daoResult = $this->dao->updateUser($requestJson['_id'], $requestJson['edit_user']);
        }
        
        if(isset($requestJson['add_current_loans']))
        {
            foreach($requestJson['add_current_loans'] as $loanId)
            {
                $daoResult = $this->dao->addCurrentLLoanToUser($requestJson['_id'], $loanId);
            }
        }
        
        if(isset($requestJson['add_past_loans']))
        {
            foreach($requestJson['add_past_loans'] as $loanId)
            {
                $daoResult = $this->dao->addPastLoanToUser($requestJson['_id'], $loanId);
            }
        }
        
        if(isset($requestJson['remove_current_loans']))
        {
            foreach($requestJson['remove_current_loans'] as $loanId)
            {
                $daoResult = $this->dao->removeCurrentLoanFromUser($requestJson['_id'], $loanId);
                $loan = $this->getLoan(array('_id' => $loanId))['loans'][0];
                $this->dao->updateEquipment($equipmentId, array('status' => "inventory", 'loaned_to' => null));
            }
        }
        
        if(isset($requestJson['remove_past_loans']))
        {
            foreach($requestJson['remove_past_loans'] as $loanId)
            {
                $daoResult = $this->dao->removePastLoanFromUser($requestJson['_id'], $loanId);
            }
        }
        
        $result['ok'] = true;
        $result['user'] = $this->dao->getUser(array('_id' => $requestJson['_id']))[0];
        
        return $result;
    }

    public function deleteUser($requestJson)
    {
        $result = array('ok' => false, 'msg' => null);
        
        $validationResult = $this->userValidator->isValidDeleteJson($requestJson);
        
        if(!$validationResult['ok'])
        {
            return $validationResult;
        }
        
        if(isset($requestJson['_id']) && $this->userValidator->isMongoIdString($requestJson['_id']))
        {
            $daoResult = $this->dao->removeUser($requestJson['_id']);
        }
        else
        {
            if(isset($requestJson['username']))
            {
                $requestJson['_id'] = $this->dao->findUserIdByUserName($requestJson['username']);
            }
            
            $daoResult = $this->dao->removeUser($requestJson['_id']);
        }
        
        if($daoResult['n'] == 0)
        {
            $result['msg'] = "User not found.";
            return $result;
        }
        
        $result['ok'] = true;
        $result['msg'] = "Successfully deleted user.";
        
        return $result;
    }

    /*
     * Loan functions
     */

    public function createLoan($requestJson)
    {
        $result = array('ok' => false, 'msg' => null, 'loan' => null);
        
        // Creating loan automatically adds this loan to user's current loans
        $result['loan'] = $this->dao->createLoan($requestJson);
        
        $result['ok'] = true;
        $result['msg'] = "Successfully created loan.";
        
        return $result;
    }

    public function getLoan($requestJson)
    {
        $result = array('ok' => false, 'msg' => null, 'loans' => null);
        
        $loans = $this->dao->getLoan($requestJson);
        
        if(is_null($loans) || empty($loans))
        {
            $result['msg'] = 'Loan not found with given search criteria.';
            $result['search_criteria'] = $requestJson;
        }
        else
        {
            $result['ok'] = true;
            $result['msg'] = "Successfully found loans.";
            $result['n'] = count($loans);
            $result['loans'] = $loans;
        }
        
        return $result;
    }

    public function updateLoan($requestJson)
    {
        $result = array('ok' => false, 'msg' => null, 'loan' => null);
        
        if(isset($requestJson['update_loan']))
        {
            $this->dao->updateLoan($requestJson['_id'], $requestJson['update_loan']);
        }
        
        if(isset($requestJson['add_equipments']))
        {
            foreach($requestJson['add_equipments'] as $equipmentId)
            {
                $this->dao->addEquipmentToLoan($requestJson['_id'], $equipmentId);
                $loan = $this->getLoan(array('_id' => $requestJson['_id']))['loans'][0];
                $this->dao->updateEquipment($equipmentId, array('status' => "loaned", 'loaned_to' => $loan['username']));
            }
        }
        
        if(isset($requestJson['remove_equipments']))
        {
            foreach($requestJson['remove_equipments'] as $equipmentId)
            {
                $this->dao->removeEquipmentFromLoan($requestJson['_id'], $equipmentId);
                $loan = $this->getLoan(array('_id' => $requestJson['_id']))['loans'][0];
                $this->dao->updateEquipment($equipmentId, array('status' => "inventory", 'loaned_to' => null));
            }
        }
        
        $result['ok'] = true;
        $result['msg'] = "Successfully updated loan.";
        $result['loan'] = $this->getLoan(array('_id' => $requestJson['_id']))['loans'][0];
        
        return $result;
    }

    public function deleteLoan($requestJson)
    {
        $result = array('ok' => false, 'msg' => null);
        
        $daoResult = $this->dao->deleteLoan($requestJson['_id']);
        
        $result['ok'] = true;
        $result['msg'] = "Successfully deleted loan.";
        
        return $result;
    }
    
    /*
     * Equipment functions
     */

    public function createEquipment($requestJson)
    {
        $returnArray = array('ok' => false, 'msg' => null, 'equipment' => null);
        
        /*
        $validationResult = $this->equipmentValidator->isValidCreateJSON($requestJson);
        
        if(!$validationResult['ok'])
        {
            return $validationResult;
        }
         */

        $result = $this->getEquipmentType(array('name' => $requestJson['equipment_type_name']));

        if(!$result['ok'])
        {
            $returnArray['ok'] = false;
            $returnArray['msg'] = "EquipmentType '".$requestJson['equipment_type_name']."' not found.";
            return $returnArray;
        }
        
        $requestJson['status'] = "inventory";
        $requestJson['loaned_to'] = null;

        $updated = $this->dao->createEquipment($requestJson, $result['equipment_types'][0]);

        return array("ok" => true, "message" => "Successfully created Equipment '".$requestJson['department_tag']."'.",
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
        
        /*
        $validationResult = $this->equipmentValidator->isValidUpdateJSON($requestJson);
        
        if(!$validationResult['ok'])
        {
            return $validationResult;
        }
         * 
         */
        
        //get id
        if(!isset($requestJson['_id']))
        {
            if(isset($requestJson['department_tag']))
            {
                $getIdResult = $this->equipmentValidator->getIdByDepartmentTag($requestJson['department_tag']);
                if($getIdResult['ok'])
                {
                    $requestJson['_id'] = $getIdResult['_id'];
                }
                else
                {
                    $result['msg'] = "Equipment not found with given name.";
                    return $result;
                }
            }
            else if(isset($requestJson['gt_tag']))
            {
                $getIdResult = $this->equipmentValidator->getIdByGtTag($requestJson['gt_tag']);
                if($getIdResult['ok'])
                {
                    $requestJson['_id'] = $getIdResult['_id'];
                }
                else
                {
                    $result['msg'] = "Equipment not found with given name.";
                    return $result;
                }
            }
        }

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
        
        $result = array();
        $result['ok'] = true;
        $result['msg'] = "Update success.";
        $result['updated_equipment'] = $this->dao->getEquipment(array('_id' => $requestJson['_id']));

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
        
        $validationResult = $this->equipmentValidator->isValidDeleteJSON($requestJson);
        
        if(!$validationResult['ok'])
        {
            return $validationResult;
        }
        
        if(!isset($requestJson['_id']))
        {
            if(isset($requestJson['department_tag']))
            {
                $idArr = $this->equipmentValidator->getIdByDepartmentTag($requestJson['department_tag']);
                
                if($idArr['ok'])
                {
                    $requestJson['_id'] = $idArr['_id'];
                }
                else
                {
                    $result['msg'] = "Given 'department_tag' value is not found.";
                    return $result;
                }
            }
            else
            {
                $idArr = $this->equipmentValidator->getIdByGtTag($requestJson['gt_tag']);
                
                if($idArr['ok'])
                {
                    $requestJson['_id'] = $idArr['_id'];
                }
                else
                {
                    $result['msg'] = "Given 'gt_tag' value is not found.";
                    return $result;
                }
            }
        }

        $daoResult = $this->dao->deleteEquipment($requestJson['_id']);

        $result['ok'] = $daoResult['ok'];
        $result['msg'] = "Successfully deleted Equipment.";
        
        return $result;
    }
    
    /*
     * EquipmentType functions
     */

    public function createEquipmentType($requestJson)
    {
        $result = array('ok' => false, 'msg' => null);
        
        $validationResult = $this->equipmentTypeValidator->isValidCreateJson($requestJson);
        
        if(!$validationResult['ok'])
        {
            return $validationResult;
        }
        
        if($this->equipmentTypeValidator->isEquipmentTypeExist($requestJson['name']))
        {
            $result['msg'] = "Equipment Type '".$requestJson['name']."' already exists.";
            return $result;
        }
        
        $equipmentType = $this->dao->createEquipmentType($requestJson, $result['equipment_type'][0]);

        return array("ok" => true, "message" => "Successfully created EquipmentType '".$requestJson['name']."' !",
                'equipment_type' => $equipmentType);
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
        
        $validationResult = $this->equipmentTypeValidator->isValidUpdateJSON($requestJson);
        
        if(!$validationResult['ok'])
        {
            return $validationResult;
        }
        
        if(!isset($requestJson['_id']))
        {
            $getIdResult = $this->equipmentTypeValidator->getEquipmentTypeIdByName($requestJson['name']);
            
            if($getIdResult['ok'])
            {
                $requestJson['_id'] = $getIdResult['_id'];
            }
            else
            {
                $result['msg'] = "Equipment Type not found with given name.";
                return $result;
            }
        }

        //do not trust DAO in terms of semantics.
        //update equipment type document itself (not its attributes).
        if(isset($requestJson['update_equipment_type']))
        {   
            $this->dao->updateEquipmentType($requestJson['_id'], $requestJson['update_equipment_type']);
        }

        if(isset($requestJson['update_equipment_type_attributes']) && !empty($requestJson['update_equipment_type_attributes']))
        {
            foreach($requestJson['update_equipment_type_attributes'] as $updateTarget)
            {
                $this->dao->updateEquipmentTypeAttribute($updateTarget['_id'], $updateTarget);
            }
        }

        if(isset($requestJson['add_equipment_type_attributes']) && !empty($requestJson['add_equipment_type_attributes']))
        {
            foreach($requestJson['add_equipment_type_attributes'] as $newAttribute)
            {
                $this->dao->addEquipmentTypeAttribute($requestJson['_id'], $newAttribute);
            }
        }

        if(isset($requestJson['remove_equipment_type_attributes']) && !empty($requestJson['remove_equipment_type_attributes']))
        {
            foreach($requestJson['remove_equipment_type_attributes'] as $removeTarget)
            {
                $this->dao->removeEquipmentTypeAttribute($requestJson['_id'], $removeTarget);
            }
        }
        
        $result['ok'] = true;
        $result['msg'] = "Update success.";
        $result['updated_equipment_type'] = $this->dao->getEquipmentType(array('_id' => $requestJson['_id']));

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
        
        $validationResult = $this->equipmentTypeValidator->isValidDeleteJSON($requestJson);
        
        if(!$validationResult['ok'])
        {
            return $validationResult;
        }
        
        if(!isset($requestJson['_id']))
        {
            $getIdResult = $this->equipmentTypeValidator->getEquipmentTypeIdByName($requestJson['name']);
            
            if($getIdResult['ok'])
            {
                $requestJson['_id'] = $getIdResult['_id'];
            }
            else
            {
                $result['msg'] = "Equipment Type not found with given name.";
                return $result;
            }
        }

        $daoResult = $this->dao->deleteEquipmentType($requestJson['_id']);
        
        if($daoResult['ok'])
        {
            $result['ok'] = true;
            $result['msg'] = "Successfully deleted Equipment Type.";
        }
        else
        {
            $result['msg'] = $daoResult['msg'];
        }

        return $result;
    }
    
    public function getDAO()
    {
        return $this->dao;
    }
}