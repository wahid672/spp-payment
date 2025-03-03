<?php

namespace App\Controllers;

use App\Models\PaymentModel;
use App\Models\StudentModel;
use CodeIgniter\Controller;

class DashboardController extends Controller
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
        try {
            $currentMonth = date('n');
            $currentYear = date('Y');

            $data = [
                'stats' => $this->paymentModel->getDashboardStats(),
                'trends' => $this->paymentModel->getPaymentTrends(6),
                'unpaidCount' => count($this->paymentModel->getUnpaidStudents($currentMonth, $currentYear)),
                'totalStudents' => $this->studentModel->countAll(),
                'recentPayments' => $this->paymentModel->getFilteredPayments([
                    'limit' => 5,
                    'orderBy' => 'payment_date DESC'
                ]) ?: []
            ];

            return view('dashboard/index', $data);
        } catch (\Exception $e) {
            log_message('error', '[Dashboard] Error loading dashboard: ' . $e->getMessage());
            
            // Return dashboard with empty data
            return view('dashboard/index', [
                'stats' => [
                    'total_today' => 0,
                    'total_month' => 0,
                    'total_year' => 0,
                    'pending_payments' => 0
                ],
                'trends' => [],
                'unpaidCount' => 0,
                'totalStudents' => 0,
                'recentPayments' => []
            ]);
        }
    }
}
