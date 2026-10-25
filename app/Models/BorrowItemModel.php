<?php

namespace App\Models;

use CodeIgniter\Model;

class BorrowItemModel extends Model
{
    protected $table         = 'borrow_items';
    protected $primaryKey    = 'borrow_item_id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'transaction_id',
        'equipment_id',
        'condition_out',
        'condition_in',
        'returned_at',
        'remarks',
    ];
    protected $useTimestamps = false;
}
