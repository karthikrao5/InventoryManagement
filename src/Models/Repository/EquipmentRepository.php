<?php

namespace App\Models\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use App\Models\Equipment;

class EquipmentRepository extends DocumentRepository 
{
    public function createEquipment($json)
    {
        $equipment = new Equipment();
        
    }
}