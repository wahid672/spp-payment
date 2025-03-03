<?php

namespace App\Controllers;

use App\Models\PaymentModel;
use App\Models\StudentModel;
use CodeIgniter\Controller;
use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PaymentController extends Controller
{
    protected $paymentModel;
    protected $studentModel;

    public function __construct()
    {
        $this->paymentModel = new PaymentModel();
        $this->studentModel = new StudentModel();
    }

    public function index()
    {
        $filter = [
            'class' => $this->request->getGet('class'),
            'month' => $this->request->getGet('month'),
            'status' => $this->request->getGet('status'),
        ];

        $data = [
            'payments' => $this->paymentModel->getFilteredPayments($filter),
            'filter' => $filter
        ];

        return view('payments/index', $data);
    }

    public function create()
    {
        $data['students'] = $this->studentModel->findAll();
        return view('payments/create', $data);
    }

    public function store()
    {
        try {
            $rules = [
                'student_id' => 'required|numeric',
                'amount' => 'required|numeric',
                'payment_date' => 'required|valid_date',
                'payment_month' => 'required|numeric|less_than_equal_to[12]|greater_than[0]',
                'payment_year' => 'required|numeric|exact_length[4]',
                'payment_method' => 'required',
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $student = $this->studentModel->find($this->request->getPost('student_id'));
            
            // Format payment_month as YYYY-MM
            $payment_month = sprintf('%04d-%02d', 
                $this->request->getPost('payment_year'),
                $this->request->getPost('payment_month')
            );

            // Check if payment already exists for this month/year
            $existingPayment = $this->paymentModel
                ->where('student_id', $this->request->getPost('student_id'))
                ->where('payment_month', $payment_month)
                ->where('status', 'success')
                ->first();

            if ($existingPayment) {
                return redirect()->back()->withInput()->with('error', 'Payment for this month has already been recorded');
            }

            $this->paymentModel->save([
                'student_id' => $this->request->getPost('student_id'),
                'amount' => $this->request->getPost('amount'),
                'payment_date' => $this->request->getPost('payment_date'),
                'payment_month' => $payment_month,
                'payment_method' => $this->request->getPost('payment_method'),
                'status' => 'success',
                'notes' => $this->request->getPost('notes'),
            ]);

        } catch (\Exception $e) {
            log_message('error', '[Payment] Error creating payment: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'An error occurred while processing the payment');
        }

        // Send WhatsApp notification
        $this->sendPaymentNotification($student, $this->request->getPost('amount'));

        return redirect()->to('/payments')->with('success', 'Payment recorded successfully');
    }

    public function monthlyReport()
    {
        $month = $this->request->getGet('month') ?? date('n');
        $year = $this->request->getGet('year') ?? date('Y');

        $data = [
            'report' => $this->paymentModel->getMonthlyReport($month, $year),
            'month' => $month,
            'year' => $year
        ];

        return view('payments/monthly_report', $data);
    }

    public function annualReport()
    {
        $year = $this->request->getGet('year') ?? date('Y');
        
        $data = [
            'report' => $this->paymentModel->getAnnualReport($year),
            'year' => $year
        ];

        return view('payments/annual_report', $data);
    }

    public function unpaidStudents()
    {
        $month = $this->request->getGet('month') ?? date('n');
        $year = $this->request->getGet('year') ?? date('Y');

        $data = [
            'students' => $this->paymentModel->getUnpaidStudents($month, $year),
            'month' => $month,
            'year' => $year
        ];

        return view('payments/unpaid_students', $data);
    }

    public function exportReport($type)
    {
        $month = $this->request->getGet('month') ?? date('n');
        $year = $this->request->getGet('year') ?? date('Y');

        switch ($type) {
            case 'monthly':
                $data = $this->paymentModel->getMonthlyReport($month, $year);
                $filename = "monthly_report_{$year}_{$month}";
                break;
            case 'annual':
                $data = $this->paymentModel->getAnnualReport($year);
                $filename = "annual_report_{$year}";
                break;
            case 'unpaid':
                $data = $this->paymentModel->getUnpaidStudents($month, $year);
                $filename = "unpaid_students_{$year}_{$month}";
                break;
            default:
                return redirect()->back()->with('error', 'Invalid report type');
        }

        $format = $this->request->getGet('format') ?? 'xlsx';
        
        if ($format === 'pdf') {
            return $this->generatePDF($data, $type, $filename);
        } else {
            return $this->generateExcel($data, $type, $filename);
        }
    }

    protected function generatePDF($data, $type, $filename)
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);

        $dompdf = new Dompdf($options);
        
        // Load the appropriate view based on report type
        $html = view("payments/reports/{$type}_pdf", ['data' => $data]);
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        return $dompdf->stream("{$filename}.pdf", ['Attachment' => true]);
    }

    protected function generateExcel($data, $type, $filename)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers and data based on report type
        switch ($type) {
            case 'monthly':
                $this->setMonthlyReportHeaders($sheet);
                $this->setMonthlyReportData($sheet, $data);
                break;
            case 'annual':
                $this->setAnnualReportHeaders($sheet);
                $this->setAnnualReportData($sheet, $data);
                break;
            case 'unpaid':
                $this->setUnpaidStudentsHeaders($sheet);
                $this->setUnpaidStudentsData($sheet, $data);
                break;
        }

        $writer = new Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit();
    }

    protected function setMonthlyReportHeaders($sheet)
    {
        $sheet->setCellValue('A1', 'Monthly Payment Report');
        $sheet->setCellValue('A3', 'Total Successful Payments');
        $sheet->setCellValue('A4', 'Total Pending Payments');
        $sheet->setCellValue('A5', 'Total Failed Payments');
        $sheet->setCellValue('A6', 'Total Students');
    }

    protected function setMonthlyReportData($sheet, $data)
    {
        $sheet->setCellValue('B3', $data['total_success']);
        $sheet->setCellValue('B4', $data['total_pending']);
        $sheet->setCellValue('B5', $data['total_failed']);
        $sheet->setCellValue('B6', $data['total_students']);
    }

    protected function setAnnualReportHeaders($sheet)
    {
        $sheet->setCellValue('A1', 'Month');
        $sheet->setCellValue('B1', 'Total Payments');
        $sheet->setCellValue('C1', 'Total Students');
    }

    protected function setAnnualReportData($sheet, $data)
    {
        $row = 2;
        foreach ($data as $record) {
            $sheet->setCellValue('A' . $row, date('F', mktime(0, 0, 0, $record['payment_month'], 1)));
            $sheet->setCellValue('B' . $row, $record['total_success']);
            $sheet->setCellValue('C' . $row, $record['total_students']);
            $row++;
        }
    }

    protected function setUnpaidStudentsHeaders($sheet)
    {
        $sheet->setCellValue('A1', 'Student Name');
        $sheet->setCellValue('B1', 'Class');
        $sheet->setCellValue('C1', 'Parent Name');
        $sheet->setCellValue('D1', 'Parent Phone');
    }

    protected function setUnpaidStudentsData($sheet, $data)
    {
        $row = 2;
        foreach ($data as $student) {
            $sheet->setCellValue('A' . $row, $student['name']);
            $sheet->setCellValue('B' . $row, $student['class']);
            $sheet->setCellValue('C' . $row, $student['parent_name']);
            $sheet->setCellValue('D' . $row, $student['parent_phone']);
            $row++;
        }
    }

    public function receipt($id)
    {
        $payment = $this->paymentModel->getPaymentWithStudent($id);
        
        if (!$payment) {
            return redirect()->to('/payments')->with('error', 'Payment not found');
        }

        $data['payment'] = $payment;

        // Initialize Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);

        $dompdf = new Dompdf($options);
        
        // Load the receipt view
        $html = view('payments/receipt', $data);
        
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        
        // Set paper size
        $dompdf->setPaper('A4', 'portrait');
        
        // Render the PDF
        $dompdf->render();
        
        // Output the generated PDF
        $dompdf->stream("payment-receipt-{$id}.pdf", ['Attachment' => false]);
    }

    protected function sendPaymentNotification($student, $amount)
    {
        $apiKey = getenv('WANOTIF_API_KEY');
        $sender = getenv('WANOTIF_SENDER');
        
        if (!$apiKey || !$sender) {
            log_message('error', 'WhatsApp notification configuration missing');
            return;
        }

        $message = "Pembayaran SPP telah diterima:\n"
                . "Nama: {$student['name']}\n"
                . "Kelas: {$student['class']}\n"
                . "Jumlah: Rp " . number_format($amount, 0, ',', '.') . "\n"
                . "Tanggal: " . date('d/m/Y H:i');

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://app.wanotif.web.id/send-message',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => [
                'api_key' => $apiKey,
                'sender' => $sender,
                'number' => $student['parent_phone'],
                'message' => $message
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        if ($response === false) {
            log_message('error', 'Failed to send WhatsApp notification: ' . curl_error($curl));
        }
    }

    public function history()
    {
        $studentId = $this->request->getGet('student_id');
        
        if (!$studentId) {
            return redirect()->to('/payments')->with('error', 'Student ID is required');
        }

        $data = [
            'student' => $this->studentModel->find($studentId),
            'payments' => $this->paymentModel->where('student_id', $studentId)->findAll()
        ];

        return view('payments/history', $data);
    }

    public function tripayCallback()
    {
        $json = $this->request->getJSON();
        
        if (!$json) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid JSON payload']);
        }

        // Verify callback signature
        $callbackSignature = $this->request->getHeader('X-Callback-Signature');
        $privateKey = getenv('TRIPAY_PRIVATE_KEY');

        $signature = hash_hmac('sha256', $json, $privateKey);

        if ($signature !== $callbackSignature) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid signature']);
        }

        // Update payment status
        $merchantRef = $json->merchant_ref;
        $status = strtolower($json->status);

        $payment = $this->paymentModel->where('transaction_id', $merchantRef)->first();
        
        if (!$payment) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Payment not found']);
        }

        $this->paymentModel->update($payment['id'], ['status' => $status]);

        if ($status === 'success') {
            $student = $this->studentModel->find($payment['student_id']);
            $this->sendPaymentNotification($student, $payment['amount']);
        }

        return $this->response->setJSON(['success' => true]);
    }
}
