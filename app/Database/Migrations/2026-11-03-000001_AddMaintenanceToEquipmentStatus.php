<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMaintenanceToEquipmentStatus extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('equipment', [
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['AVAILABLE', 'IN_USE', 'RESERVED', 'MAINTENANCE'],
                'default'    => 'AVAILABLE',
                'null'       => false,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('equipment', [
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['AVAILABLE', 'IN_USE', 'RESERVED'],
                'default'    => 'AVAILABLE',
                'null'       => false,
            ],
        ]);
    }
}

