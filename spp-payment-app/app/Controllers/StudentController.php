<?php

namespace App\Controllers;

use App\Models\StudentModel;
use App\Models\UserModel;
use CodeIgniter\Controller;

class StudentController extends BaseController
{
    protected $studentModel;
    protected $db;

    public function __construct()
    {
        $this->studentModel = new StudentModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        try {
            $data['students'] = $this->studentModel->select('students.*, users.name')
                                                  ->join('users', 'users.id = students.user_id', 'left')
                                                  ->findAll();

            if (empty($data['students'])) {
                $data['students'] = [];
            }

            return view('students/index', $data);
        } catch (\Exception $e) {
            log_message('error', '[Students] Error loading students: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading students');
        }
    }

    public function create()
    {
        try {
            helper(['form']);
            return view('students/create');
        } catch (\Exception $e) {
            log_message('error', '[Students] Error loading create form: ' . $e->getMessage());
            return redirect()->to('/students')->with('error', 'Error loading create form');
        }
    }

    public function store()
    {
        try {
            helper(['form']);

            $rules = [
                'name' => 'required|min_length[3]|max_length[100]',
                'class' => 'required|in_list[X,XI,XII]',
                'major' => 'required|in_list[RPL,TKJ,MM]',
                'spp_amount' => 'required|numeric|greater_than[0]',
                'parent_name' => 'required|min_length[3]|max_length[100]',
                'parent_phone' => 'required|min_length[10]|max_length[15]|regex_match[/^[0-9]+$/]'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $this->db->transStart();

            // Create user account
            $userModel = new UserModel();
            
            // Generate unique username
            $baseUsername = 'S' . date('Ymd');
            $counter = 1;
            $username = $baseUsername . sprintf('%04d', $counter);
            
            while ($userModel->where('username', $username)->first()) {
                $counter++;
                $username = $baseUsername . sprintf('%04d', $counter);
            }

            $userData = [
                'name' => $this->request->getPost('name'),
                'username' => $username,
                'email' => '',  // Empty string instead of null
                'password' => 'student123',  // Will be hashed by UserModel
                'role' => 'siswa',
                'phone' => $this->request->getPost('parent_phone'),
            ];

            $userId = $userModel->insert($userData);
            if (!$userId) {
                $errors = $userModel->errors();
                throw new \Exception('Failed to create user account: ' . implode(', ', $errors));
            }

            // Generate unique NIS
            $baseNis = 'S' . date('Ymd');
            $counter = 1;
            $nis = $baseNis . sprintf('%04d', $counter);
            
            while ($this->studentModel->where('nis', $nis)->first()) {
                $counter++;
                $nis = $baseNis . sprintf('%04d', $counter);
            }

            // Create student record
            $studentData = [
                'user_id' => $userId,
                'nis' => $nis,
                'class' => $this->request->getPost('class'),
                'major' => $this->request->getPost('major'),
                'spp_amount' => $this->request->getPost('spp_amount'),
                'parent_name' => $this->request->getPost('parent_name'),
                'parent_phone' => $this->request->getPost('parent_phone'),
            ];

            if (!$this->studentModel->insert($studentData)) {
                throw new \Exception('Failed to create student record');
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Failed to create student');
            }

            return redirect()->to('/students')
                           ->with('success', 'Student added successfully. Username: ' . $username . ', Password: student123');

        } catch (\Exception $e) {
            log_message('error', '[Students] Error creating student: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error creating student: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $student = $this->studentModel->select('students.*, users.name')
                                        ->join('users', 'users.id = students.user_id')
                                        ->where('students.id', $id)
                                        ->first();

            if (!$student) {
                throw new \Exception('Student not found');
            }

            return view('students/edit', ['student' => $student]);
        } catch (\Exception $e) {
            log_message('error', '[Students] Error loading edit form: ' . $e->getMessage());
            return redirect()->to('/students')->with('error', 'Error loading student data');
        }
    }

    public function update($id)
    {
        try {
            $rules = [
                'name' => 'required|min_length[3]|max_length[100]',
                'class' => 'required|in_list[X,XI,XII]',
                'major' => 'required|in_list[RPL,TKJ,MM]',
                'spp_amount' => 'required|numeric|greater_than[0]',
                'parent_name' => 'required|min_length[3]|max_length[100]',
                'parent_phone' => 'required|min_length[10]|max_length[15]|regex_match[/^[0-9]+$/]'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $student = $this->studentModel->find($id);
            if (!$student) {
                throw new \Exception('Student not found');
            }

            $this->db->transStart();

            // Update user data
            $userModel = new UserModel();
            $userData = [
                'name' => $this->request->getPost('name'),
                'phone' => $this->request->getPost('parent_phone'),
            ];

            if (!$userModel->update($student['user_id'], $userData)) {
                throw new \Exception('Failed to update user data');
            }

            // Update student data
            $studentData = [
                'class' => $this->request->getPost('class'),
                'major' => $this->request->getPost('major'),
                'spp_amount' => $this->request->getPost('spp_amount'),
                'parent_name' => $this->request->getPost('parent_name'),
                'parent_phone' => $this->request->getPost('parent_phone'),
            ];

            if (!$this->studentModel->update($id, $studentData)) {
                throw new \Exception('Failed to update student data');
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Failed to update student');
            }

            return redirect()->to('/students')->with('success', 'Student updated successfully');

        } catch (\Exception $e) {
            log_message('error', '[Students] Error updating student: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error updating student: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $student = $this->studentModel->find($id);
            if (!$student) {
                throw new \Exception('Student not found');
            }

            $this->db->transStart();

            // Delete student record
            if (!$this->studentModel->delete($id)) {
                throw new \Exception('Failed to delete student record');
            }

            // Delete associated user account
            $userModel = new UserModel();
            if (!$userModel->delete($student['user_id'])) {
                throw new \Exception('Failed to delete user account');
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Failed to delete student');
            }

            return redirect()->to('/students')->with('success', 'Student deleted successfully');

        } catch (\Exception $e) {
            log_message('error', '[Students] Error deleting student: ' . $e->getMessage());
            return redirect()->to('/students')->with('error', 'Error deleting student: ' . $e->getMessage());
        }
    }
}
