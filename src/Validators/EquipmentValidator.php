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
    
    public function isValidUpdateJSON($json)
    {
        $result = array('ok' => false, 'msg' => null);
        
        if(isset($json['_id']))
        {
            if(!$this->isMongoIdString($json['_id']))
            {
                $result['msg'] = "Field '_id' has invalid ID string.";
                return $result;
            }
        }
        else if(isset($json['department_tag']))
        {
            if($this->isDepartmentTagExist($json['department_tag']))
            {
                $json['_id'] = $this->getIdByDepartmentTag($json['department_tag']);
            }
            else
            {
                $result['msg'] = "Equipment not found by given 'department_tag'.";
                return $result;
            }
        }
        else if(isset($json['gt_tag']))
        {
            if($this->isGtTagExist($json['gt_tag']))
            {
                $json['_id'] = $this->getIdByGtTag($json['gt_tag']);
            }
            else
            {
                $result['msg'] = "Equipment not found by given 'gt_tag'.";
                return $result;
            }
        }
        else
        {
            $result['msg'] = "At least one unique identifier ('_id', 'department_tag', 'gt_tag') must be present in the request JSON.";
            return $result;
        }
        
        return $result;
    }

    public function isValidCreateJSON($json)
    {
        $result = array('ok' => false, 'msg' => null);
        
        if(!isset($json['department_tag']))
        {
            $result['msg'] = "Field 'department_tag' must be present in request JSON.";
            return $result;
        }
        else
        {
            if($this->isDepartmentTagExist($json['department_tag']))
            {
                $result['msg'] = "'department_tag' value already exists.";
                return $result;
            }
        }
        
        if(!array_key_exists("gt_tag", $json))
        {
            $result['msg'] = "Field 'gt_tag' must be present in request JSON but value can be null.";
            return $result;
        }
        else if(isset($json['gt_tag']))
        {
            if($this->isGtTagExist($json['gt_tag']))
            {
                $result['msg'] = "'gt_tag' value already exists.";
                return $result;
            }
        }
        
        if(!isset($json['equipment_type_name']))
        {
            $result['msg'] = "Field 'equipment_type_name' must be present in request JSON.";
            return $result;
        }
        else
        {
            if(!$this->isEquipmentTypeExist($json['equipment_type_name']))
            {
                $result['msg'] = "Given 'equipment_type_name' not found.";
                return $result;
            }
        }
        
        if(array_key_exists("status", $json))
        {
            $result['msg'] = "Field 'status' cannot be set by user.";
            return $result;
        }
        
        /*
        else
        {
            if(!$this->isCorrectStatus($json['status']))
            {
                $result['msg'] = "Field 'status' is in invalid status.";
            }
        }
        */
        
        if(array_key_exists("loaned_to", $json))
        {
            $result['msg'] = "Field 'loaned_to' cannot be set by user.";
            return $result;
        }
        
        if(!array_key_exists("comments", $json))
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
            $validationResult = $this->validateAttributesCreate($equipmentType['equipment_type_attributes'], $json['attributes']);
            
            if(!$validationResult['ok'])
            {
                return $validationResult;
            }
        }
        
        if(array_key_exists("logs", $json))
        {
            $result['msg'] = "Field 'logs' cannot be set by user.";
            return $result;
        }
        
        $allowedFields = array("department_tag", "gt_tag", "equipment_type_name", "comments", "attributes");
        //check for invalid fields present in request JSON
        foreach($json as $key => $value)
        {
            if(!in_array($key, $allowedFields))
            {
                $result['msg'] = "Invalid field '".$key."' in request JSON.";
                return $result;
            }
        }
        
        $result['ok'] = true;
        return $result;
    }
    
    public function validateAttributesCreate($equipmentTypeAttributes, $attributes)
    {
        $result = array('ok' => false, 'msg' => null);
        
        //check for name collision
        $temp = array();
        foreach($attributes as $attr)
        {
            if(!in_array($attr['name'], $temp))
            {
                $temp[] = $attr['name'];
            }
            else
            {
                $result['msg'] = "Attribute name collision on '".$attr['name']."'.";
                return $result;
            }
        }
        
        //check for invalid attribute name
        $temp = array();
        foreach($equipmentTypeAttributes as $attr)
        {
            $temp[] = $attr['name'];
        }
        
        foreach($attributes as $attr)
        {
            if(!in_array($attr['name'], $temp))
            {
                $result['msg'] = "Invalid attribute name '".$attr['name']."' is found.";
                return $result;
            }
        }
        
        //check for value against regex and enum
        foreach($equipmentTypeAttributes as $equipmentTypeAttr)
        {
            foreach($attributes as $attr)
            {
                if($equipmentTypeAttr['name'] == $attr['name'])
                {
                    if(isset($equipmentTypeAttr['regex']))
                    {
                        if(!preg_match($equipmentTypeAttr['regex'], $attr['value']))
                        {
                            $result['msg'] = "Attribute '".$attr['name']."' has failed regex check.";
                        }
                    }
                    
                    if($equipmentTypeAttr['enum'])
                    {
                        if(!in_array($attr['value'], $equipmentTypeAttr['enum_values']))
                        {
                            $result['msg'] = "Attribute '".$attr['name']."' must have value from equipment type's enum_values.";
                        }
                    }
                }
            }
        }
        
        //gather attribute names
        $temp = array();
        foreach($attributes as $attr)
        {
            $temp[] = $attr['name'];
        }
        
        //check for missing required attributes
        foreach($equipmentTypeAttributes as $equipmentTypeAttr)
        {
            if($equipmentTypeAttr['required'] && !in_array($equipmentTypeAttr['name'], $temp))
            {
                $result['msg'] = "Equipment is missing required attribute '".$equipmentTypeAttr['name']."'.";
                return $result;
            }
        }
        
        //check for unique attributes
        foreach($equipmentTypeAttributes as $equipmentTypeAttr)
        {
            if($equipmentTypeAttr['unique'])
            {
                foreach($attributes as $attr)
                {
                    if($attr['name'] == $equipmentTypeAttr['name'])
                    {
                        if(!$this->isAttributeUnique($equipmentTypeAttr, $attr))
                        {
                            $result['msg'] = "Attribute '".$attr['name']."' has duplicate value.";
                            return $result;
                        }
                    }
                }
            }
        }
        
        $result['ok'] = true;
        return $result;
    }
    
    private function isAttributeUnique($equipmentTypeAttribute, $attribute)
    {
        $dao = $this->core->getDao();
        
        return $dao->getEquipmentAttribute(array('equipment_type_attribute_id' => $equipmentTypeAttribute['_id'], 
            'name' => $attribute['name'], 'value' => $attribute['value']))['ok'] == false;
    }
    
    public function isCorrectStatus($status)
    {
        return in_array($status, array("inventory", "loaned", "surplus", "trashed"));
    }
}

?>