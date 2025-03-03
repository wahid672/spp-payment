<?php

namespace App\Controllers;

use App\Models\StudentModel;
use CodeIgniter\Controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class StudentController extends Controller
{
    protected $studentModel;

    public function __construct()
    {
        $this->studentModel = new StudentModel();
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
            return view('students/index', ['students' => []]);
        }
    }

    public function create()
    {
        try {
            return view('students/create');
        } catch (\Exception $e) {
            log_message('error', '[Students] Error loading create form: ' . $e->getMessage());
            return redirect()->to('/students')->with('error', 'Error loading create form');
        }
    }

    public function store()
    {
        try {
            $rules = [
                'name' => 'required|min_length[3]|max_length[100]',
                'class' => 'required|max_length[10]',
                'major' => 'required|max_length[50]',
                'spp_amount' => 'required|numeric|greater_than[0]',
                'parent_name' => 'required|min_length[3]|max_length[100]',
                'parent_phone' => 'required|min_length[10]|max_length[20]'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $this->db->transStart();

            // Create user account for student
            $userModel = new \App\Models\UserModel();
            $userData = [
                'name' => $this->request->getPost('name'),
                'username' => 'S' . date('Ymd') . rand(1000, 9999),
                'email' => null,
                'password' => password_hash('student123', PASSWORD_DEFAULT),
                'role' => 'siswa',
                'phone' => $this->request->getPost('parent_phone'),
            ];

            $userId = $userModel->insert($userData);

            if (!$userId) {
                throw new \Exception('Failed to create user account');
            }

            // Create student record
            $studentData = [
                'user_id' => $userId,
                'nis' => 'S' . date('Ymd') . rand(1000, 9999),
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

            return redirect()->to('/students')->with('success', 'Student added successfully. Default password: student123');

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
                'class' => 'required|max_length[10]',
                'major' => 'required|max_length[50]',
                'spp_amount' => 'required|numeric|greater_than[0]',
                'parent_name' => 'required|min_length[3]|max_length[100]',
                'parent_phone' => 'required|min_length[10]|max_length[20]'
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
            $userModel = new \App\Models\UserModel();
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
            $userModel = new \App\Models\UserModel();
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

    public function import()
    {
        try {
            $file = $this->request->getFile('student_file');
            
            if (!$file->isValid() || $file->hasMoved()) {
                throw new \Exception('Please upload a valid Excel file');
            }

            $spreadsheet = IOFactory::load($file->getTempName());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Validate header row
            $headerRow = array_shift($rows);
            $expectedHeaders = ['Name', 'NIS', 'Class', 'Major', 'SPP Amount', 'Parent Name', 'Parent Phone'];
            if ($headerRow !== $expectedHeaders) {
                throw new \Exception('Invalid file format. Please use the template provided by the export function.');
            }

            $this->db->transStart();

            $userModel = new \App\Models\UserModel();
            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($rows as $index => $row) {
                try {
                    if (!empty($row[0])) { // Check if name exists
                        // Create user account
                        $userData = [
                            'name' => $row[0],
                            'username' => 'S' . date('Ymd') . rand(1000, 9999),
                            'email' => null,
                            'password' => password_hash('student123', PASSWORD_DEFAULT),
                            'role' => 'siswa',
                            'phone' => $row[6] ?? null, // Parent phone
                        ];

                        $userId = $userModel->insert($userData);
                        if (!$userId) {
                            throw new \Exception('Failed to create user account');
                        }

                        // Create student record
                        $studentData = [
                            'user_id' => $userId,
                            'nis' => $row[1] ?? ('S' . date('Ymd') . rand(1000, 9999)),
                            'class' => $row[2],
                            'major' => $row[3],
                            'spp_amount' => $row[4],
                            'parent_name' => $row[5],
                            'parent_phone' => $row[6],
                        ];

                        if ($this->studentModel->insert($studentData)) {
                            $successCount++;
                        } else {
                            throw new \Exception('Failed to create student record');
                        }
                    }
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
                    log_message('error', 'Error importing student: ' . $e->getMessage());
                }
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Database transaction failed');
            }

            $message = "Import completed. Successfully imported {$successCount} students.";
            if ($errorCount > 0) {
                $message .= " Failed to import {$errorCount} students.";
                log_message('error', 'Import errors: ' . implode(", ", $errors));
            }

            return redirect()->to('/students')->with('success', $message);

        } catch (\Exception $e) {
            log_message('error', '[Students] Error importing data: ' . $e->getMessage());
            return redirect()->to('/students')->with('error', 'Error importing students: ' . $e->getMessage());
        }
    }

    public function export()
    {
        try {
            // Get students with user data
            $students = $this->studentModel->select('students.*, users.name')
                                         ->join('users', 'users.id = students.user_id', 'left')
                                         ->findAll();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set headers
            $sheet->setCellValue('A1', 'Name');
            $sheet->setCellValue('B1', 'NIS');
            $sheet->setCellValue('C1', 'Class');
            $sheet->setCellValue('D1', 'Major');
            $sheet->setCellValue('E1', 'SPP Amount');
            $sheet->setCellValue('F1', 'Parent Name');
            $sheet->setCellValue('G1', 'Parent Phone');

            // Style the header row
            $sheet->getStyle('A1:G1')->getFont()->setBold(true);
            $sheet->getStyle('A1:G1')->getFill()
                  ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                  ->getStartColor()->setRGB('CCCCCC');

            // Add data
            $row = 2;
            foreach ($students as $student) {
                $sheet->setCellValue('A' . $row, $student['name'] ?? 'N/A');
                $sheet->setCellValue('B' . $row, $student['nis'] ?? 'N/A');
                $sheet->setCellValue('C' . $row, $student['class'] ?? 'N/A');
                $sheet->setCellValue('D' . $row, $student['major'] ?? 'N/A');
                $sheet->setCellValue('E' . $row, $student['spp_amount'] ?? 0);
                $sheet->setCellValue('F' . $row, $student['parent_name'] ?? 'N/A');
                $sheet->setCellValue('G' . $row, $student['parent_phone'] ?? 'N/A');

                // Format SPP Amount as currency
                $sheet->getStyle('E' . $row)->getNumberFormat()
                      ->setFormatCode('#,##0');
                
                $row++;
            }

            // Auto-size columns
            foreach (range('A', 'G') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            // Add borders to all cells
            $lastRow = $row - 1;
            $sheet->getStyle('A1:G' . $lastRow)->getBorders()->getAllBorders()
                  ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            // Create Excel file
            $writer = new Xlsx($spreadsheet);
            $filename = 'students-' . date('Y-m-d-His') . '.xlsx';

            // Set headers for download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            exit();

        } catch (\Exception $e) {
            log_message('error', '[Students] Error exporting data: ' . $e->getMessage());
            return redirect()->to('/students')->with('error', 'Error exporting student data: ' . $e->getMessage());
        }
    }
}
