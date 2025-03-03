<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdatePaymentMonthFormat extends Migration
{
    public function up()
    {
        // Update existing records to ensure payment_month is in YYYY-MM format
        $this->db->query("
            UPDATE payments 
            SET payment_month = strftime('%Y-%m', payment_date) 
            WHERE payment_month NOT LIKE '____-__'
        ");
    }

    public function down()
    {
        // No need for down migration as this is a data format correction
    }
}
