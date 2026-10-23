<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table            = 'equipment_categories';
    protected $primaryKey       = 'category_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'category_name',
        'description',
    ];

    // This table has no timestamp columns
    protected $useTimestamps = false;

    protected $validationRules = [
        'category_name' => 'required|max_length[100]|is_unique[equipment_categories.category_name,category_id,{category_id}]',
        'description'   => 'permit_empty|max_length[255]',
    ];
}
