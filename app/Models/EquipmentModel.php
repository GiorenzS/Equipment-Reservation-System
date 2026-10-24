<?php

namespace App\Models;

use CodeIgniter\Model;

class EquipmentModel extends Model
{
    protected $table            = 'equipment';
    protected $primaryKey       = 'equipment_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'equipment_code',
        'equipment_name',
        'category_id',
        'image',
        'brand',
        'model',
        'serial_number',
        'description',
        'status',
        'condition_status',
        'department_id',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
    protected $deletedField  = '';

    protected $validationRules = [
        'equipment_code' => 'required|max_length[50]|is_unique[equipment.equipment_code,equipment_id,{equipment_id}]',
        'equipment_name' => 'required|max_length[100]',
        'category_id'    => 'required|integer',
        'image'          => 'permit_empty|max_length[255]',
        'brand'          => 'permit_empty|max_length[100]',
        'model'          => 'permit_empty|max_length[100]',
        'serial_number'  => 'permit_empty|max_length[100]|is_unique[equipment.serial_number,equipment_id,{equipment_id}]',
        'description'    => 'permit_empty',
        'status'         => 'permit_empty|in_list[AVAILABLE,IN_USE,RESERVED]',
        'condition_status' => 'permit_empty|in_list[GOOD,FAIR,DAMAGED]',
        'department_id'  => 'permit_empty|integer',
    ];
}
