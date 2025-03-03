<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialDataSeeder extends Seeder
{
    public function run()
    {
        // Create admin user
        $users = [
            [
                'username' => 'admin',
                'email' => 'admin@example.com',
                'password' => password_hash('admin123', PASSWORD_BCRYPT),
                'role' => 'admin',
                'name' => 'Administrator',
                'phone' => '08123456789',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'username' => 'bendahara',
                'email' => 'bendahara@example.com',
                'password' => password_hash('bendahara123', PASSWORD_BCRYPT),
                'role' => 'bendahara',
                'name' => 'Bendahara Sekolah',
                'phone' => '08234567890',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Insert users
        $this->db->table('users')->insertBatch($users);

        // Create sample student users and their profiles
        $studentUsers = [
            [
                'username' => 'student1',
                'email' => 'student1@example.com',
                'password' => password_hash('student123', PASSWORD_BCRYPT),
                'role' => 'siswa',
                'name' => 'John Doe',
                'phone' => '08345678901',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'username' => 'student2',
                'email' => 'student2@example.com',
                'password' => password_hash('student123', PASSWORD_BCRYPT),
                'role' => 'siswa',
                'name' => 'Jane Smith',
                'phone' => '08456789012',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Insert student users
        $this->db->table('users')->insertBatch($studentUsers);

        // Get the inserted student user IDs
        $student1 = $this->db->table('users')
            ->where('username', 'student1')
            ->get()
            ->getRowArray();

        $student2 = $this->db->table('users')
            ->where('username', 'student2')
            ->get()
            ->getRowArray();

        // Create student profiles
        $students = [
            [
                'user_id' => $student1['id'],
                'nis' => '2024001',
                'class' => 'X',
                'major' => 'RPL',
                'spp_amount' => 500000,
                'parent_name' => 'Mr. Doe',
                'parent_phone' => '08567890123',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id' => $student2['id'],
                'nis' => '2024002',
                'class' => 'X',
                'major' => 'TKJ',
                'spp_amount' => 500000,
                'parent_name' => 'Mr. Smith',
                'parent_phone' => '08678901234',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Insert student profiles
        $this->db->table('students')->insertBatch($students);

        // Create sample payments
        $student1Profile = $this->db->table('students')
            ->where('user_id', $student1['id'])
            ->get()
            ->getRowArray();

        $payments = [
            [
                'student_id' => $student1Profile['id'],
                'payment_date' => date('Y-m-d'),
                'payment_month' => date('Y-m'), // Already in correct YYYY-MM format
                'amount' => 500000,
                'payment_method' => 'cash',
                'status' => 'success',
                'payment_type' => 'manual',
                'reference' => 'PAY-' . date('YmdHis'),
                'notes' => 'Pembayaran SPP bulan ' . date('F Y'),
                'created_by' => 2, // Bendahara user ID
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Insert payments
        $this->db->table('payments')->insertBatch($payments);

        // Create notification for the payment
        $payment = $this->db->table('payments')
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        $notifications = [
            [
                'payment_id' => $payment['id'],
                'type' => 'payment_success',
                'recipient' => '08567890123', // Parent's phone
                'message' => 'Pembayaran SPP untuk John Doe bulan ' . date('F Y') . ' sebesar Rp 500.000 telah berhasil.',
                'status' => 'sent',
                'sent_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Insert notifications
        $this->db->table('notifications')->insertBatch($notifications);
    }
}
