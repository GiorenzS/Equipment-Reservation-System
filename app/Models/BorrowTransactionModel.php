<?php

namespace App\Models;

use CodeIgniter\Model;

class BorrowTransactionModel extends Model
{
    protected $table         = 'borrow_transactions';
    protected $primaryKey    = 'transaction_id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'user_id',
        'processed_by',
        'borrow_date',
        'expected_return_date',
        'actual_return_date',
        'status',
        'remarks',
    ];
    protected $useTimestamps = false;
}
