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
        $data['students'] = $this->studentModel->findAll();
        return view('students/index', $data);
    }

    public function create()
    {
        return view('students/create');
    }

    public function store()
    {
        $rules = [
            'name' => 'required',
            'class' => 'required',
            'major' => 'required',
            'spp_amount' => 'required|numeric',
            'parent_name' => 'required',
            'parent_phone' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->studentModel->save([
            'name' => $this->request->getPost('name'),
            'class' => $this->request->getPost('class'),
            'major' => $this->request->getPost('major'),
            'spp_amount' => $this->request->getPost('spp_amount'),
            'parent_name' => $this->request->getPost('parent_name'),
            'parent_phone' => $this->request->getPost('parent_phone'),
        ]);

        return redirect()->to('/students')->with('success', 'Student added successfully');
    }

    public function edit($id)
    {
        $data['student'] = $this->studentModel->find($id);
        return view('students/edit', $data);
    }

    public function update($id)
    {
        $rules = [
            'name' => 'required',
            'class' => 'required',
            'major' => 'required',
            'spp_amount' => 'required|numeric',
            'parent_name' => 'required',
            'parent_phone' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->studentModel->update($id, [
            'name' => $this->request->getPost('name'),
            'class' => $this->request->getPost('class'),
            'major' => $this->request->getPost('major'),
            'spp_amount' => $this->request->getPost('spp_amount'),
            'parent_name' => $this->request->getPost('parent_name'),
            'parent_phone' => $this->request->getPost('parent_phone'),
        ]);

        return redirect()->to('/students')->with('success', 'Student updated successfully');
    }

    public function delete($id)
    {
        $this->studentModel->delete($id);
        return redirect()->to('/students')->with('success', 'Student deleted successfully');
    }

    public function import()
    {
        $file = $this->request->getFile('student_file');
        
        if (!$file->isValid() || $file->hasMoved()) {
            return redirect()->to('/students')->with('error', 'Please upload a valid Excel file');
        }

        try {
            $spreadsheet = IOFactory::load($file->getTempName());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Remove header row
            array_shift($rows);

            $successCount = 0;
            $errorCount = 0;

            foreach ($rows as $row) {
                try {
                    if (!empty($row[0])) { // Check if name exists
                        $data = [
                            'name' => $row[0],
                            'class' => $row[1],
                            'major' => $row[2],
                            'spp_amount' => $row[3],
                            'parent_name' => $row[4],
                            'parent_phone' => $row[5],
                        ];

                        if ($this->studentModel->save($data)) {
                            $successCount++;
                        } else {
                            $errorCount++;
                        }
                    }
                } catch (\Exception $e) {
                    $errorCount++;
                    log_message('error', 'Error importing student: ' . $e->getMessage());
                }
            }

            $message = "Import completed. Successfully imported {$successCount} students.";
            if ($errorCount > 0) {
                $message .= " Failed to import {$errorCount} students.";
            }

            return redirect()->to('/students')->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->to('/students')->with('error', 'Error processing file: ' . $e->getMessage());
        }
    }

    public function export()
    {
        $students = $this->studentModel->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'Name');
        $sheet->setCellValue('B1', 'Class');
        $sheet->setCellValue('C1', 'Major');
        $sheet->setCellValue('D1', 'SPP Amount');
        $sheet->setCellValue('E1', 'Parent Name');
        $sheet->setCellValue('F1', 'Parent Phone');

        // Style the header row
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $sheet->getStyle('A1:F1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCCC');

        // Add data
        $row = 2;
        foreach ($students as $student) {
            $sheet->setCellValue('A' . $row, $student['name']);
            $sheet->setCellValue('B' . $row, $student['class']);
            $sheet->setCellValue('C' . $row, $student['major']);
            $sheet->setCellValue('D' . $row, $student['spp_amount']);
            $sheet->setCellValue('E' . $row, $student['parent_name']);
            $sheet->setCellValue('F' . $row, $student['parent_phone']);
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Create Excel file
        $writer = new Xlsx($spreadsheet);
        $filename = 'students-' . date('Y-m-d-His') . '.xlsx';

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit();
    }
}
