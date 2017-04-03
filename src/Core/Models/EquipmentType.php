<?php

namespace App\Core\Models;

class EquipmentType implements \JsonSerializable
{
    private $_id;
    private $name;
    private $equipment_type_attributes;

    public function __construct()
    {
        $this->equipment_type_attributes = array();
    }

    public function setId($id) {$this->_id = $id;}
    public function getId() {return $this->_id;}
	public function getIdString() {return $this->_id->{'$id'};}

    public function setName($name) {$this->name = $name;}
    public function getName() {return $this->name;}

    public function addAttribute($attr)
    {
        $this->equipment_type_attributes[] = $attr;
    }

    public function setAttributes($attrs) {$this->equipment_type_attributes = $attrs;}
    public function getAttributes() {return $this->equipment_type_attributes;}

    public function jsonSerialize()
    {
		$attrs = array();
        print_r("afasdfknja");
		foreach($this->equipment_type_attributes as $attr)
		{
            print_r($attr);
			$attrs[] = $attr->jsonSerialize();
		}
		
        $vars = get_object_vars($this);
		$vars['equipment_type_attributes'] = $attrs;
		
		return $vars;
    }
}

