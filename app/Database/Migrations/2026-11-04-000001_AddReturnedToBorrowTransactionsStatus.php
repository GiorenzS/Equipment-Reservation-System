<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddReturnedToBorrowTransactionsStatus extends Migration
{
    public function up()
    {
        // Expand the status enum to include RETURNED
        $this->db->query("ALTER TABLE borrow_transactions MODIFY status ENUM('CHECKED_OUT', 'CHECKED_IN', 'RETURNED') NOT NULL DEFAULT 'CHECKED_OUT'");

        // Fix any previously-returned transactions that were stored as empty string
        // because 'RETURNED' was not in the enum at the time.
        $this->db->query("UPDATE borrow_transactions SET status = 'RETURNED' WHERE actual_return_date IS NOT NULL AND (status = '' OR status = 'CHECKED_IN')");
    }

    public function down()
    {
        $this->db->query("UPDATE borrow_transactions SET status = 'CHECKED_IN' WHERE status = 'RETURNED'");
        $this->db->query("ALTER TABLE borrow_transactions MODIFY status ENUM('CHECKED_OUT', 'CHECKED_IN') NOT NULL DEFAULT 'CHECKED_OUT'");
    }
}

