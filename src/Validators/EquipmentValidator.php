<?php

namespace App\Validators;

use Interop\Container\ContainerInterface;

class EquipmentValidator extends AbstractValidator {

    public function __construct(ContainerInterface $ci) {
            parent::__construct($ci);
    }
    
    public function isUserExist($username)
    {
        return $this->core->getUser(array('username' => $username))['ok'];
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
        
        $result['ok'] = true;
        return $result;
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
        
        if(isset($json['update_equipment']))
        {
            $allowedFields = array("department_tag", "gt_tag", "status", "loaned_to", "comments");
            
            if(isset($json['update_equipment']['department_tag']))
            {
                if($this->isDepartmentTagExist($json['update_equipment']['department_tag']))
                {
                    $result['msg'] = "Given 'department_tag' value in 'update_equipment' already exists.";
                    return $result;
                }
            }
            
            if(isset($json['update_equipment']['gt_tag']))
            {
                if($this->isGtTagExist($json['update_equipment']['gt_tag']))
                {
                    $result['msg'] = "Given 'gt_tag' value in 'update_equipment' already exists.";
                    return $result;
                }
            }
            
            if(isset($json['update_equipment']['status']))
            {
                if(!$this->isCorrectStatus($json['update_equipment']['status']))
                {
                    $result['msg'] = "Given 'status' value in 'update_equipment' is invalid.";
                    return $result;
                }
            }
            
            if(isset($json['update_equipment']['loaned_to']))
            {
                if(!$this->isUserExist($json['update_equipment']['loaned_to']))
                {
                    $result['msg'] = "Given 'loaned_to' value (username) is not found.";
                    return $result;
                }
            }
        }
        
        if(isset($json['update_equipment_attributes']))
        {
            foreach($json['update_equipment_attributes'] as $attr)
            {
                $validationResult = $this->validateAttributeUpdate($json['_id'], $attr);
                
                if(!$validationResult['ok'])
                {
                    return $validationResult;
                }
            }
        }
        
        if(isset($json['add_equipment_attributes']))
        {
            $dao = $this->core->getDao();
            $equipment = $dao->getEquipment(array('_id' => $json['_id']))['equipments'][0];
            $equipmentAttributes = $dao->getEquipmentAttribute(array('equipment_id' => $json['_id']))['equipment_attributes'];
            $equipmentTypeAttributes = $dao->getEquipmentTypeAttribute(array('equipment_type_id' => $equipment['equipment_type_id']));
            
            //check if the attribute is already present.
            foreach($equipmentAttributes as $attr)
            {
                foreach($json['add_equipment_attributes'] as $attr2)
                {
                    if($attr['name'] == $attr2['name'])
                    {
                        $result['msg'] = "Attribute name '".$attr2."' already exists.";
                        return $result;
                    }
                }
            }
            
            //check if value is correct.
            foreach($equipmentTypeAttributes as $attrType)
            {
                foreach($json['add_equipment_attributes'] as $attr)
                {
                    if($attrType['name'] == $attr['name'])
                    {
                        if($attrType['unique'])
                        {
                            if(!$this->isAttributeUnique($attrType, $attr))
                            {
                                $result['msg'] = "Given attribute value '".$attr."' is not unqiue.";
                                return $result;
                            }
                        }
                        
                        if(isset($attrType['regex']))
                        {
                            if(!preg_match($attrType['regex'], $attr['value']))
                            {
                                $result['msg'] = "Given attribute value '".$attr."' is not in regex format.";
                                return $result;
                            }
                        }
                        
                        if($attrType['enum'])
                        {
                            if(!in_array($attr['value'], $attrType['enum_values']))
                            {
                                $result['msg'] = "Given attribute value '".$attr['value']."' is not in 'enum_values' in 'add_equipment_attributes'";
                                return $result;
                            }
                        }
                    }
                }
            }
        }
        
        if(isset($json['remove_equipment_attributes']))
        {
            foreach($json['remove_equipment_attributes'] as $attrId)
            {
                if(!$this->isMongoIdString($attrId))
                {
                    $result['msg'] = "Invalid ID string '".$attrId."' given to 'remove_equipment_attributes'.";
                    return $result;
                }
                
                $dao = $this->core->getDao();
                $daoResult = $dao->getEquipmentAttribute(array('_id' => new \MongoId($attrId)));
                
                if(!$daoResult['ok'])
                {
                    $result['msg'] = "Equipment Attribute not found with given ID string.";
                    $result['id_string'] = $attrId;
                }
                
                $equipmentAttribute = $daoResult['equipment_attributes'][0];
                $equipmentTypeAttribute = $dao->getEquipmentTypeAttribute(array('_id' => $equipmentAttribute['equipment_type_attribute_id']))['equipment_type_attributes'][0];
                
                if($equipmentTypeAttribute['required'])
                {
                    $result['msg'] = "Attribute '".$equipmentAttribute['name']."' is required attribute.";
                    return $result;
                }
            }
        }
        
        $result['ok'] = true;
        
        return $result;
    }
    
    private function validateAttributeUpdate($equipmentId, $attribute)
    {
        $result = array('ok' => false, 'msg' => null);
        
        $dao = $this->core->getDao();
        if(!isset($attribute['_id']))
        {
            $daoResult = $dao->getEquipmentAttribute(array('equipment_id' => $equipmentId, 'name' => $attribute['name']));
            
            if(!$daoResult['ok'])
            {
                $result['msg'] = "Attribute name '".$attribute['name']."' not found in 'update_equipment_attributes'.";
                return $result;
            }
            
            $attribute['_id'] = $daoResult['equipment_attributes'][0]['_id'];
        }
        
        $daoResult = $dao->getEquipmentTypeAttribute(array('_id' => $attribute['equipment_type_attribute_id']));
        $equipmentTypeAttribute = $daoResult['equipment_type_attributes'][0];
        
        if(isset($equipmentTypeAttribute['regex']))
        {
            if(!preg_match($equipmentTypeAttribute['regex'], $attribute['value']))
            {
                $result['msg'] = "Attribute value '".$attribute['value']."' is not in regex format in 'update_equipment_attributes'.";
                return $result;
            }
        }
        
        if(isset($equipmentTypeAttribute['unique']))
        {
            if(!$this->isAttributeUnique($equipmentTypeAttribute, $attribute))
            {
                $result['msg'] = "Attribute value '".$attribute['value']."' is not unique in 'update_equipment_attributes'.";
                return $result;
            }
        }
        
        if($equipmentTypeAttribute['enum'])
        {
            if(!in_array($attribute['value'], $equipmentTypeAttribute['enum_values']))
            {
                $result['msg'] = "Attribute value '".$attribute['value']."' is not in enum_values array in 'update_equipment_attributes'.";
                return $result;
            }
        }
        
        $result['ok'] = true;
        
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