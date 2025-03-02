<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = [
        'username',
        'email',
        'password',
        'role',
        'name',
        'phone'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username,id,{id}]',
        'email' => 'required|valid_email|max_length[100]|is_unique[users.email,id,{id}]',
        'password' => 'required|min_length[6]',
        'role' => 'required|in_list[admin,bendahara,siswa]',
        'name' => 'required|max_length[100]',
        'phone' => 'permit_empty|max_length[20]'
    ];

    protected $validationMessages = [
        'username' => [
            'required' => 'Username harus diisi',
            'min_length' => 'Username minimal 3 karakter',
            'max_length' => 'Username maksimal 50 karakter',
            'is_unique' => 'Username sudah digunakan'
        ],
        'email' => [
            'required' => 'Email harus diisi',
            'valid_email' => 'Email tidak valid',
            'max_length' => 'Email maksimal 100 karakter',
            'is_unique' => 'Email sudah digunakan'
        ],
        'password' => [
            'required' => 'Password harus diisi',
            'min_length' => 'Password minimal 6 karakter'
        ],
        'role' => [
            'required' => 'Role harus diisi',
            'in_list' => 'Role tidak valid'
        ],
        'name' => [
            'required' => 'Nama harus diisi',
            'max_length' => 'Nama maksimal 100 karakter'
        ],
        'phone' => [
            'max_length' => 'Nomor telepon maksimal 20 karakter'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (!isset($data['data']['password'])) {
            return $data;
        }

        $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_BCRYPT);
        return $data;
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public function findByUsernameOrEmail(string $login)
    {
        return $this->where('username', $login)
                    ->orWhere('email', $login)
                    ->first();
    }

    public function findWithStudent($userId)
    {
        $user = $this->find($userId);
        if (!$user) {
            return null;
        }

        if ($user['role'] === 'siswa') {
            $studentModel = new StudentModel();
            $student = $studentModel->where('user_id', $userId)->first();
            if ($student) {
                $user['student'] = $student;
            }
        }

        return $user;
    }
}
