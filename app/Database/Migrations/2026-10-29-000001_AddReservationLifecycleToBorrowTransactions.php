<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddReservationLifecycleToBorrowTransactions extends Migration
{
    public function up()
    {
        $this->forge->addColumn('borrow_transactions', [
            'reservation_id' => [
                'type'  => 'INT',
                'null'  => true,
                'after' => 'processed_by',
            ],
        ]);
        $this->forge->addForeignKey('reservation_id', 'reservations', 'reservation_id', 'CASCADE', 'SET NULL');
        $this->forge->processIndexes('borrow_transactions');

        $this->db->query("ALTER TABLE reservations MODIFY status ENUM('PENDING', 'APPROVED', 'CHECKED_OUT', 'RETURNED', 'REJECTED', 'CANCELLED') NOT NULL DEFAULT 'PENDING'");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE reservations MODIFY status ENUM('PENDING', 'APPROVED', 'REJECTED', 'CANCELLED') NOT NULL DEFAULT 'PENDING'");
        $this->forge->dropForeignKey('borrow_transactions', 'borrow_transactions_reservation_id_foreign');
        $this->forge->dropColumn('borrow_transactions', 'reservation_id');
    }
}
