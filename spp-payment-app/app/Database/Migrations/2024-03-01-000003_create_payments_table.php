<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePaymentsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'student_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'payment_date' => [
                'type' => 'DATE',
            ],
            'payment_month' => [
                'type' => 'VARCHAR',
                'constraint' => 7, // Format: YYYY-MM
            ],
            'amount' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'payment_method' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'success', 'failed'],
                'default' => 'pending',
            ],
            'payment_type' => [
                'type' => 'ENUM',
                'constraint' => ['manual', 'tripay'],
                'default' => 'manual',
            ],
            'reference' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'tripay_reference' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('student_id', 'students', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('payments');

        // Add indexes for better performance
        $this->db->query('CREATE INDEX payments_payment_month_index ON payments(payment_month)');
        $this->db->query('CREATE INDEX payments_status_index ON payments(status)');
        $this->db->query('CREATE INDEX payments_payment_date_index ON payments(payment_date)');
    }

    public function down()
    {
        $this->forge->dropTable('payments');
    }
}
