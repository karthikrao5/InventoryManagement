<?php

namespace App\Validators;

use Interop\Container\ContainerInterface;

class UserValidator extends AbstractValidator 
{
    public function __construct(ContainerInterface $ci) {
            parent::__construct($ci);
    }
    
    public function isUsernameExist($username)
    {
        return $this->core->getUser(array('username' => $username))['ok'];
    }
    
    public function isUserIdExist($id)
    {
        if(!($id instanceof \MongoId))
        {
            $id = new \MongoId($id);
        }
        
        return $this->core->getUser(array('_id' => $id))['ok'];
    }
    
    public function isValidDeleteJson($json)
    {
        $result = array('ok' => false, 'msg' => null);
        //check for all fields and its values.
        
        if(isset($json['_id']))
        { 
            if(!$this->isMongoIdString($json['_id']))
            {
                $result['msg'] = "Invalid ID string given.";
                return $result;
            }
        }
        else
        {
            if(!isset($json['username']))
            {
                $result['msg'] = "Either field '_id' or 'username' must be present in the request JSON.";
                return $result;
            }
        }
        
        $result['ok'] = true;
        return $result;
    }
    
    public function isValidUpdateJson($json)
    {
        $result = array('ok' => false, 'msg' => null);
        //check for all fields and its values.
        
        if(isset($json['_id']))
        { 
            if(!$this->isMongoIdString($json['_id']))
            {
                $result['msg'] = "Invalid ID string given.";
                return $result;
            }
        }
        else
        {
            if(!isset($json['username']))
            {
                $result['msg'] = "Either field '_id' or 'username' must be present in the request JSON.";
                return $result;
            }
        }
        
        if(isset($json['edit_user']))
        {
            if(!isset($json['edit_user']['email']))
            {
                $result['msg'] = "Field 'email' must be present and cannot be null";
                return $result;
            }
        }
        
        if(isset($json['add_current_loans']))
        {
            if(empty($json['add_current_loans']))
            {
                $result['msg'] = "Field 'add_current_loans' cannot be empty";
                return $result;
            }
            
            foreach($json['add_current_loans'] as $id)
            {
                if(!$this->isMongoIdString($id))
                {
                    $result['msg'] = "Field 'add_current_loans' contains invalid ID string.";
                    $result['invalid_id_string'] = $id;
                    return $result;
                }
            }
        }
        
        if(isset($json['add_past_loans']))
        {
            if(empty($json['add_past_loans']))
            {
                $result['msg'] = "Field 'add_past_loans' cannot be empty";
                return $result;
            }
            
            foreach($json['add_past_loans'] as $id)
            {
                if(!$this->isMongoIdString($id))
                {
                    $result['msg'] = "Field 'add_past_loans' contains invalid ID string.";
                    $result['invalid_id_string'] = $id;
                    return $result;
                }
            }
        }
        
        if(isset($json['remove_current_loans']))
        {
            if(empty($json['remove_current_loans']))
            {
                $result['msg'] = "Field 'remove_current_loans' cannot be empty";
                return $result;
            }
            
            foreach($json['remove_current_loans'] as $id)
            {
                if(!$this->isMongoIdString($id))
                {
                    $result['msg'] = "Field 'remove_current_loans' contains invalid ID string.";
                    $result['invalid_id_string'] = $id;
                    return $result;
                }
            }
        }
        
        if(isset($json['remove_current_loans']))
        {
            if(empty($json['remove_current_loans']))
            {
                $result['msg'] = "Field 'remove_current_loans' cannot be empty";
                return $result;
            }
            
            foreach($json['remove_current_loans'] as $id)
            {
                if(!$this->isMongoIdString($id))
                {
                    $result['msg'] = "Field 'add_current_loans' contains invalid ID string.";
                    $result['invalid_id_string'] = $id;
                    return $result;
                }
            }
        }
        
        $result['ok'] = true;
        $result['msg'] = "Success.";
        
        return $result;
    }
    
    public function isValidCreateJson($json)
    {
        $result = array('ok' => false, 'msg' => null);
        //check for all fields and its values.
        if(array_key_exists('_id', $json))
        { 
            $result['msg'] = "Field '_id' cannot be present.";
            return $result;
        }
        
        if(!isset($json['username']))
        {
            $result['msg'] = "Field 'username' is missing.";
            return $result;
        }
        
        if(!isset($json['email']))
        {
            $result['msg'] = "Field 'email' is missing.";
        }
        
        if(array_key_exists('current_loans', $json))
        {
            $result['msg'] = "Field 'current_loans' cannot be present.";
            return $result;
        }
        
        if(array_key_exists('past_loans', $json))
        {
            $result['msg'] = "Field 'past_loans' cannot be present.";
            return $result;
        }
        
        if(array_key_exists('logs', $json))
        {
            $result['msg'] = "Field 'logs' cannot be present.";
            return $result;
        }
        
        $result['ok'] = true;
        $result['msg'] = "Success.";
        
        return $result;
    }
}

