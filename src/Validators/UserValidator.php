<?php

namespace App\Validators;

use Interop\Container\ContainerInterface;

class UserValidator extends AbstractValidator 
{
    public function __construct(ContainerInterface $ci) {
            parent::__construct($ci);
    }
    
    public function isMongoIdString($string)
    {
        return preg_match('/^[a-f\d]{24}$/i', $string);
    }
    
    public function isMongoIdObject($object)
    {
        return $object instanceof \MongoId;
    }
    
    public function isUsernameExist($json)
    {
        return $this->core->getUser(array('username' => $json['username']))['ok'];
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

