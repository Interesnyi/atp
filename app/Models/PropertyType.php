<?php

namespace App\Models;

use App\Core\Model;

class PropertyType extends Model
{
    protected $table = 'maslosklad_property_type';

    public function getAllTypes()
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY name";
        return $this->db->fetchAll($sql);
    }
} 