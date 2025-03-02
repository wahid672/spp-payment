<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'key' => 'school_name',
                'value' => 'SMK Negeri 1 Example',
                'group' => 'general',
                'description' => 'Nama sekolah yang akan ditampilkan di aplikasi',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'school_address',
                'value' => 'Jl. Example No. 123, Kota Example',
                'group' => 'general',
                'description' => 'Alamat lengkap sekolah',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'school_phone',
                'value' => '08123456789',
                'group' => 'general',
                'description' => 'Nomor telepon sekolah',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'academic_year',
                'value' => '2024/2025',
                'group' => 'academic',
                'description' => 'Tahun ajaran aktif',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'wanotif_api_key',
                'value' => 'your-wanotif-api-key',
                'group' => 'integration',
                'description' => 'API Key untuk integrasi WhatsApp Notification',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'wanotif_sender',
                'value' => '628123456789',
                'group' => 'integration',
                'description' => 'Nomor pengirim WhatsApp',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'tripay_api_key',
                'value' => 'your-tripay-api-key',
                'group' => 'integration',
                'description' => 'API Key untuk integrasi Tripay',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'tripay_private_key',
                'value' => 'your-tripay-private-key',
                'group' => 'integration',
                'description' => 'Private Key untuk integrasi Tripay',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'tripay_merchant_code',
                'value' => 'your-merchant-code',
                'group' => 'integration',
                'description' => 'Kode merchant Tripay',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'payment_notification_template',
                'value' => 'Pembayaran SPP untuk {student_name} bulan {month} sebesar Rp {amount} telah berhasil.',
                'group' => 'notification',
                'description' => 'Template notifikasi WhatsApp untuk pembayaran berhasil',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'reminder_notification_template',
                'value' => 'Reminder: SPP untuk {student_name} bulan {month} sebesar Rp {amount} belum dibayar.',
                'group' => 'notification',
                'description' => 'Template notifikasi WhatsApp untuk pengingat pembayaran',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('settings')->insertBatch($data);
    }
}
