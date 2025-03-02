<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = [
        'payment_id',
        'type',
        'recipient',
        'message',
        'status',
        'response',
        'sent_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'payment_id' => 'required|integer',
        'type' => 'required|in_list[payment_success,payment_reminder,payment_failed]',
        'recipient' => 'required|min_length[10]|max_length[20]',
        'message' => 'required',
        'status' => 'required|in_list[pending,sent,failed]',
        'response' => 'permit_empty',
        'sent_at' => 'permit_empty|valid_date'
    ];

    protected $validationMessages = [
        'payment_id' => [
            'required' => 'ID Pembayaran harus diisi',
            'integer' => 'ID Pembayaran harus berupa angka'
        ],
        'type' => [
            'required' => 'Tipe notifikasi harus diisi',
            'in_list' => 'Tipe notifikasi tidak valid'
        ],
        'recipient' => [
            'required' => 'Nomor penerima harus diisi',
            'min_length' => 'Nomor penerima minimal 10 karakter',
            'max_length' => 'Nomor penerima maksimal 20 karakter'
        ],
        'message' => [
            'required' => 'Pesan notifikasi harus diisi'
        ],
        'status' => [
            'required' => 'Status notifikasi harus diisi',
            'in_list' => 'Status notifikasi tidak valid'
        ],
        'sent_at' => [
            'valid_date' => 'Format tanggal kirim tidak valid'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Relationships
    public function payment()
    {
        return $this->belongsTo('App\Models\PaymentModel', 'payment_id', 'id');
    }

    // Custom Methods
    public function getNotificationWithDetails($id = null)
    {
        $builder = $this->builder();
        $builder->select('
            notifications.*,
            payments.payment_month,
            payments.amount,
            students.nis,
            users.name as student_name
        ');
        $builder->join('payments', 'payments.id = notifications.payment_id');
        $builder->join('students', 'students.id = payments.student_id');
        $builder->join('users', 'users.id = students.user_id');
        
        if ($id !== null) {
            return $builder->where('notifications.id', $id)->get()->getRowArray();
        }
        
        return $builder->get()->getResultArray();
    }

    public function getPendingNotifications()
    {
        return $this->where('status', 'pending')
                    ->orderBy('created_at', 'ASC')
                    ->findAll();
    }

    public function getFailedNotifications()
    {
        return $this->where('status', 'failed')
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    public function markAsSent($id, $response = null)
    {
        $data = [
            'status' => 'sent',
            'sent_at' => date('Y-m-d H:i:s')
        ];

        if ($response !== null) {
            $data['response'] = $response;
        }

        return $this->update($id, $data);
    }

    public function markAsFailed($id, $response = null)
    {
        $data = [
            'status' => 'failed'
        ];

        if ($response !== null) {
            $data['response'] = $response;
        }

        return $this->update($id, $data);
    }

    public function createPaymentNotification($paymentId, $type, $message)
    {
        $payment = model('PaymentModel')->find($paymentId);
        if (!$payment) {
            return false;
        }

        $student = model('StudentModel')->find($payment['student_id']);
        if (!$student) {
            return false;
        }

        return $this->insert([
            'payment_id' => $paymentId,
            'type' => $type,
            'recipient' => $student['parent_phone'],
            'message' => $message,
            'status' => 'pending'
        ]);
    }
}
