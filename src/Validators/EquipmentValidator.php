<?php

namespace App\Validators;

use Interop\Container\ContainerInterface;

class EquipmentValidator extends AbstractValidator {

    public function __construct(ContainerInterface $ci) {
            parent::__construct($ci);
    }
    
    public function isDepartmentTagExist($tag)
    {
        return $this->core->getEquipment(array('department_tag' => $tag))['ok'];
    }
    
    public function isGtTagExist($tag)
    {
        return $this->core->getEquipment(array('gt_tag' => $tag))['ok'];
    }
    
    public function getIdByDepartmentTag($tag)
    {   
        $result = $this->core->getEquipment(array('department_tag' => $tag));
        
        if($result['ok'])
        {
            $result = array('ok' => true, '_id' => $result['equipments'][0]['_id']);
        }
        
        return $result;
    }
    
    public function getIdByGtTag($tag)
    {
        $result = $this->core->getEquipment(array('gt_tag' => $tag));
        
        if($result['ok'])
        {
            $result = array('ok' => true, '_id' => $result['equipments'][0]['_id']);
        }
        
        return $result;
    }

    public function isValidDeleteJSON($json)
    {
        $result = array('ok' => false, 'msg' => null);
        
        if(isset($json['_id']))
        {
            if(!$this->isMongoIdString($json['_id']))
            {
                $result['msg'] = "Field '_id' contains invalid ID string.";
                return $result;
            }
        }
        else if(isset($json['department_tag']))
        {
            //Do nothing
        }
        else if(isset($json['gt_tag']))
        {
            //Do nothing
        }
        else 
        {
            $result['msg'] = "At least one unique identifier ('_id', 'department_tag', 'gt_tag') must be present in the request JSON.";
            return $result;
        }
    }
    
    public function isEquipmentTypeExist($name)
    {
        $dao = $this->core->getDao();
        
        return $this->core->getEquipmentType(array('name' => $name))['ok'];
    }

    public function isValidCreateJSON($json)
    {
        $result = array('ok' => false, 'msg' => null);
        
        if(!isset($json['department_tag']))
        {
            $result['msg'] = "Field 'department_tag' must be present in request JSON.";
            return $result;
        }
        
        if(!array_key_exists("gt_tag", $json))
        {
            $result['msg'] = "Field 'gt_tag' must be present in request JSON but value can be null.";
            return $result;
        }
        
        if(!isset($json['equipment_type_name']))
        {
            $result['msg'] = "Field 'equipment_type_name' must be present in request JSON.";
            return $result;
        }
        else
        {
            if(!$this->isEquipmentTypeExist($name))
            {
                $result['msg'] = "Given 'equipment_type_name' not found.";
                return $result;
            }
        }
        
        if(!isset($json['status']))
        {
            $result['msg'] = "Field 'status' must be present in request JSON.";
            return $result;
        }
        else
        {
            if(!$this->isCorrectStatus($json['status']))
            {
                $result['msg'] = "Field 'status' is in invalid status.";
            }
        }
        
        if(!array_key_exists("comments", $array))
        {
            $result['msg'] = "Field 'comments' must be present in request JSON but value can be null.";
            return $result;
        }
        
        if(!isset($json['attributes']))
        {
            $result['msg'] = "Field 'attributes' must be present in request JSON.";
            return $result;
        }
        else
        {
            $equipmentType = $this->core->getEquipmentType(array('name' => $json['equipment_type_name']))['equipment_types'][0];
            $validationResult = $this->validateAttributesCreate($equipmentType['equipment_type_attributes'], $json);
        }
    }
    
    public function validateAttributesCreate($equipmentTypeAttributes, $attributes)
    {
        
    }
    
    public function isCorrectStatus($status)
    {
        return in_array($status, array("inventory", "loaned", "surplus", "trashed"));
    }
}

?>