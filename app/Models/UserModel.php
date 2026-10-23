<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'user_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'username',
        'password',
        'role',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
    protected $deletedField  = '';

    protected $validationRules = [
        'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username,user_id,{user_id}]',
        'password' => 'required|min_length[8]',
        'role'     => 'permit_empty|in_list[ADMIN,STUDENT]',
    ];

    protected $validationMessages = [
        'username' => [
            'is_unique' => 'This username is already taken.',
        ],
    ];

    // Hash password before insert/update
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data): array
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }

        return $data;
    }
}
