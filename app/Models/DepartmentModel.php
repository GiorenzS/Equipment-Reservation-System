<?php

namespace App\Models;

use CodeIgniter\Model;

class DepartmentModel extends Model
{
    protected $table      = 'departments';
    protected $primaryKey = 'department_id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'department_name',
    ];

    protected $useTimestamps = false;
}
