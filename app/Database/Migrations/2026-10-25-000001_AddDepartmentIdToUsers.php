<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDepartmentIdToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'department_id' => [
                'type'     => 'INT',
                'null'     => true,
                'after'    => 'role',
            ],
        ]);

        $this->forge->addForeignKey(
            'department_id',
            'departments',
            'department_id',
            'CASCADE',
            'SET NULL'
        );
        $this->forge->processIndexes('users');
    }

    public function down()
    {
        $this->forge->dropForeignKey('users', 'users_department_id_foreign');
        $this->forge->dropColumn('users', 'department_id');
    }
}
