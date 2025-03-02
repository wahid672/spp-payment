<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentModel extends Model
{
    protected $table = 'students';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'user_id',
        'nis',
        'class',
        'major',
        'spp_amount',
        'parent_name',
        'parent_phone',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'user_id' => 'required|integer|is_unique[students.user_id,id,{id}]',
        'nis' => 'required|min_length[5]|max_length[20]|is_unique[students.nis,id,{id}]',
        'class' => 'required|max_length[10]',
        'major' => 'required|max_length[50]',
        'spp_amount' => 'required|numeric',
        'parent_name' => 'required|max_length[100]',
        'parent_phone' => 'required|min_length[10]|max_length[20]',
    ];

    protected $validationMessages = [
        'user_id' => [
            'required' => 'User ID harus diisi',
            'integer' => 'User ID harus berupa angka',
            'is_unique' => 'User ID sudah digunakan',
        ],
        'nis' => [
            'required' => 'NIS harus diisi',
            'min_length' => 'NIS minimal 5 karakter',
            'max_length' => 'NIS maksimal 20 karakter',
            'is_unique' => 'NIS sudah digunakan',
        ],
        'class' => [
            'required' => 'Kelas harus diisi',
            'max_length' => 'Kelas maksimal 10 karakter',
        ],
        'major' => [
            'required' => 'Jurusan harus diisi',
            'max_length' => 'Jurusan maksimal 50 karakter',
        ],
        'spp_amount' => [
            'required' => 'Nominal SPP harus diisi',
            'numeric' => 'Nominal SPP harus berupa angka',
        ],
        'parent_name' => [
            'required' => 'Nama orang tua harus diisi',
            'max_length' => 'Nama orang tua maksimal 100 karakter',
        ],
        'parent_phone' => [
            'required' => 'Nomor telepon orang tua harus diisi',
            'min_length' => 'Nomor telepon minimal 10 karakter',
            'max_length' => 'Nomor telepon maksimal 20 karakter',
        ],
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Relationships
    public function user()
    {
        return $this->belongsTo('App\Models\UserModel', 'user_id', 'id');
    }

    public function payments()
    {
        return $this->hasMany('App\Models\PaymentModel', 'student_id', 'id');
    }

    // Custom Methods
    public function findWithUser($id = null)
    {
        $builder = $this->builder();
        $builder->select('students.*, users.name, users.email, users.phone');
        $builder->join('users', 'users.id = students.user_id');
        
        if ($id !== null) {
            return $builder->where('students.id', $id)->get()->getRowArray();
        }
        
        return $builder->get()->getResultArray();
    }

    public function getUnpaidMonths($studentId, $year = null)
    {
        if ($year === null) {
            $year = date('Y');
        }

        $builder = $this->db->table('payments');
        $builder->select('payment_month');
        $builder->where('student_id', $studentId);
        $builder->where('status', 'success');
        $builder->where('YEAR(payment_date)', $year);
        $paidMonths = $builder->get()->getResultArray();

        $paidMonthsList = array_column($paidMonths, 'payment_month');
        $allMonths = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $month = sprintf("%s-%02d", $year, $i);
            if (!in_array($month, $paidMonthsList)) {
                $allMonths[] = $month;
            }
        }

        return $allMonths;
    }

    public function getTotalUnpaidAmount($studentId)
    {
        $student = $this->find($studentId);
        if (!$student) {
            return 0;
        }

        $unpaidMonths = $this->getUnpaidMonths($studentId);
        return count($unpaidMonths) * $student['spp_amount'];
    }
}
