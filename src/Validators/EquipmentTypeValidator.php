<?php

namespace App\Validators;

use Interop\Container\ContainerInterface;

class EquipmentTypeValidator extends AbstractValidator {

    public function __construct(ContainerInterface $ci) {
            parent::__construct($ci);
    }
    
    public function isEquipmentTypeExist($name)
    {
        return $this->core->getEquipmentType(array('name' => $name))['ok'];
    }
    
    private function isValidRegex($regex)
    {
        return (preg_match($regex, null) === false);
    }

    public function isValidCreateJSON($json)
    {
        $result = array();
        $result['ok'] = false;
        $result['msg'] = null;

        //check if all common attributes are present.
        if(!isset($json['name']))
        {
            $result['msg'] = "Field 'name' of Equipment Type is missing or unset.";
            return $result;
        }
        if(!isset($json['equipment_type_attributes']) || empty($json['equipment_type_attributes'])) 
        {
            $result['msg'] = "Field 'equipment_type_attributes' of Equipment Type is missing or empty.";
            return $result;
        }

        //check equipment type attributes
        $attributes = $json['equipment_type_attributes'];

        //check attributes and name collision
        $attr_names = array();
        foreach($attributes as $attribute)
        {
            $result = $this->validateAttribute($attribute);
            if(!$result['ok']) {return $result;}

            if(in_array($attribute['name'], $attr_names))
            {
                $result['ok'] = false;
                $result['msg'] = "Name collision on Equipment Type Attribute. '".$attribute['name']."'.";
                return $result;
            }
            else
            {
                $attr_names[] = $attribute['name'];
            }
        }

        $result['ok'] = true;
        return $result;
    }

    private function validateAttribute($attribute)
    {
        $result = array();
        $result['ok'] = false;
        $result['msg'] = null;

        //check if all attributes are present. syntax check
        if(!isset($attribute['name'])) 
        {
            $result['msg'] = "Field 'name' of Equipmment Type Attribute is missing or unset.";
            return $result;
        }

        if(!isset($attribute['required'])) 
        {
            $result['msg'] = "Field 'required' of Equipmment Type Attribute is missing or unset on '".$attribute['name']."'.";
            return $result;
        }

        if(!isset($attribute['unique'])) 
        {
            $result['msg'] = "Field 'unique' of Equipmment Type Attribute is missing or unset on '".$attribute['name']."'.";
            return $result;
        }

        if(!isset($attribute['data_type'])) 
        {
            $result['msg'] = "Field 'data_type' of Equipmment Type Attribute is missing or unset on '".$attribute['name']."'.";
            return $result;
        }

        if(!array_key_exists('regex', $attribute)) 
        {
            $result['msg'] = "Field 'regex' of Equipmment Type Attribute is missing on '".$attribute['name']."'.";
            return $result;
        }
        else
        {
            if(isset($attribute['regex']))
            {
                if(!$this->isValidRegex($attribute['regex']))
                {
                    $result['msg'] = "Field 'regex' contains invalid regular expression string.";
                    return $result;
                }
            }
        }

        if(!array_key_exists('help_comment', $attribute)) 
        {
            $result['msg'] = "Field 'help_comment' of Equipmment Type Attribute is missing on '".$attribute['name']."'.";
            return $result;
        }

        if(!isset($attribute['enum'])) 
        {
            $result['msg'] = "Field 'enum' of Equipmment Type Attribute is missing or unset on '".$attribute['name']."'.";
            return $result;
        }

        if(!array_key_exists('enum_values', $attribute)) 
        {
            $result['msg'] = "Field 'enum_values' of Equipmment Type Attribute is missing or unset on '".$attribute['name']."'.";
            return $result;
        }

        //semantics check. This doesn't check against database.
        //if unique is set to true, required must be set to true.
        if($attribute['unique'] && !$attribute['required']) 
        {
            $result['msg'] = "Field 'required' must be set to true when 'unique' is true on '".$attribute['name']."'.";
            return $result;
        }

        //if enum is set to true, enum_values array cannot be empty.
        if($attribute['enum'] && empty($attribute['enum_values'])) 
        {
            $result['msg'] = "Field 'enum_values' of Equipmment Type Attribute cannot be null or empty when 'enum' is true on '".$attribute['name']."'.";
            return $result;
        }

        $result['ok'] = true;
        return $result;
    }
}