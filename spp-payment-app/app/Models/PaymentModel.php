<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table = 'payments';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'student_id',
        'amount',
        'payment_date',
        'payment_month',
        'payment_method',
        'status',
        'notes',
        'transaction_id'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getFilteredPayments($filter = [])
    {
        $builder = $this->select('payments.*, students.name as student_name, students.class')
                       ->join('students', 'students.id = payments.student_id');

        // Apply filters
        if (!empty($filter['class'])) {
            $builder->where('students.class', $filter['class']);
        }

        if (!empty($filter['month'])) {
            $builder->where('payments.payment_month', $filter['month']);
        }

        if (!empty($filter['status'])) {
            $builder->where('payments.status', $filter['status']);
        }

        // Order by latest payment date
        $builder->orderBy('payments.payment_date', 'DESC');

        return $builder->findAll();
    }

    public function getPaymentWithStudent($id)
    {
        return $this->select('payments.*, students.name as student_name, students.class')
                    ->join('students', 'students.id = payments.student_id')
                    ->where('payments.id', $id)
                    ->first();
    }

    public function getStudentPayments($studentId)
    {
        return $this->where('student_id', $studentId)
                    ->orderBy('payment_month DESC')
                    ->findAll();
    }

    public function getMonthlyReport($month, $year)
    {
        $payment_month = sprintf('%04d-%02d', $year, $month);
        return $this->select('
                SUM(CASE WHEN status = "success" THEN amount ELSE 0 END) as total_success,
                SUM(CASE WHEN status = "pending" THEN amount ELSE 0 END) as total_pending,
                SUM(CASE WHEN status = "failed" THEN amount ELSE 0 END) as total_failed,
                COUNT(DISTINCT student_id) as total_students
            ')
            ->where('payment_month', $payment_month)
            ->first();
    }

    public function getAnnualReport($year)
    {
        return $this->select('
                payment_month,
                SUM(CASE WHEN status = "success" THEN amount ELSE 0 END) as total_success,
                COUNT(DISTINCT student_id) as total_students
            ')
            ->where('substr(payment_month, 1, 4)', $year)
            ->where('status', 'success')
            ->groupBy('payment_month')
            ->orderBy('payment_month', 'ASC')
            ->findAll();
    }

    public function getUnpaidStudents($month, $year)
    {
        $payment_month = sprintf('%04d-%02d', $year, $month);
        $subquery = $this->select('student_id')
                        ->where('payment_month', $payment_month)
                        ->where('status', 'success');

        return $this->db->table('students')
                       ->whereNotIn('id', $subquery)
                       ->get()
                       ->getResultArray();
    }

    public function getDashboardStats()
    {
        $currentMonth = date('n');
        $currentYear = date('Y');

        return [
            'total_today' => $this->select('SUM(amount) as total')
                                 ->where('date(payment_date)', date('Y-m-d'))
                                 ->where('status', 'success')
                                 ->first()['total'] ?? 0,
            'total_month' => $this->select('SUM(amount) as total')
                                 ->where('payment_month', date('Y-m'))
                                 ->where('status', 'success')
                                 ->first()['total'] ?? 0,
            'total_year' => $this->select('SUM(amount) as total')
                                ->where('substr(payment_month, 1, 4)', date('Y'))
                                ->where('status', 'success')
                                ->first()['total'] ?? 0,
            'pending_payments' => $this->select('COUNT(*) as total')
                                     ->where('status', 'pending')
                                     ->first()['total'] ?? 0
        ];
    }

    public function getPaymentTrends($months = 12)
    {
        $currentMonth = date('n');
        $currentYear = date('Y');

        $trends = [];
        for ($i = 0; $i < $months; $i++) {
            $month = $currentMonth - $i;
            $year = $currentYear;

            if ($month <= 0) {
                $month += 12;
                $year--;
            }

            $payment_month = sprintf('%04d-%02d', $year, $month);
            
            $total = $this->select('SUM(amount) as total')
                         ->where('payment_month', $payment_month)
                         ->where('status', 'success')
                         ->first()['total'] ?? 0;

            $trends[] = [
                'month' => date('F', mktime(0, 0, 0, $month, 1)),
                'year' => $year,
                'total' => $total
            ];
        }

        return array_reverse($trends);
    }
}
