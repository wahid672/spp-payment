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
        try {
            $builder = $this->select('payments.*, students.name as student_name, students.class')
                           ->join('students', 'students.id = payments.student_id', 'left');

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

            if (!empty($filter['limit'])) {
                $builder->limit($filter['limit']);
            }

            if (!empty($filter['orderBy'])) {
                $parts = explode(' ', $filter['orderBy']);
                $column = $parts[0];
                $direction = $parts[1] ?? 'ASC';
                $builder->orderBy($column, $direction);
            } else {
                // Default order by latest payment date
                $builder->orderBy('payments.payment_date', 'DESC');
            }

            return $builder->findAll();
        } catch (\Exception $e) {
            log_message('error', '[Payment] Error getting filtered payments: ' . $e->getMessage());
            return [];
        }
    }

    public function getPaymentWithStudent($id)
    {
        try {
            $result = $this->select('payments.*, students.name as student_name, students.class')
                          ->join('students', 'students.id = payments.student_id', 'left')
                          ->where('payments.id', $id)
                          ->first();

            if (!$result) {
                log_message('error', '[Payment] Payment not found: ' . $id);
                return null;
            }

            // Ensure numeric values are properly cast
            $result['amount'] = (float)$result['amount'];
            $result['student_id'] = (int)$result['student_id'];
            
            return $result;
        } catch (\Exception $e) {
            log_message('error', '[Payment] Error getting payment with student: ' . $e->getMessage());
            return null;
        }
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
        
        // Get all paid student IDs for the month
        $paid_students = $this->select('student_id')
                            ->where('payment_month', $payment_month)
                            ->where('status', 'success')
                            ->findAll();
        
        $paid_student_ids = array_column($paid_students, 'student_id');
        
        // If no payments, return all students
        if (empty($paid_student_ids)) {
            return $this->db->table('students')->get()->getResultArray();
        }

        return $this->db->table('students')
                       ->whereNotIn('id', $paid_student_ids)
                       ->get()
                       ->getResultArray();
    }

    public function getDashboardStats()
    {
        try {
            return [
                'total_today' => (int)($this->select('COALESCE(SUM(amount), 0) as total')
                                     ->where('date(payment_date)', date('Y-m-d'))
                                     ->where('status', 'success')
                                     ->first()['total'] ?? 0),
                'total_month' => (int)($this->select('COALESCE(SUM(amount), 0) as total')
                                     ->where('payment_month', date('Y-m'))
                                     ->where('status', 'success')
                                     ->first()['total'] ?? 0),
                'total_year' => (int)($this->select('COALESCE(SUM(amount), 0) as total')
                                    ->where('substr(payment_month, 1, 4)', date('Y'))
                                    ->where('status', 'success')
                                    ->first()['total'] ?? 0),
                'pending_payments' => (int)($this->select('COUNT(*) as total')
                                         ->where('status', 'pending')
                                         ->first()['total'] ?? 0)
            ];
        } catch (\Exception $e) {
            log_message('error', '[Payment] Error getting dashboard stats: ' . $e->getMessage());
            return [
                'total_today' => 0,
                'total_month' => 0,
                'total_year' => 0,
                'pending_payments' => 0
            ];
        }
    }

    public function getPaymentTrends($months = 12)
    {
        try {
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
                
                $result = $this->select('COALESCE(SUM(amount), 0) as total')
                              ->where('payment_month', $payment_month)
                              ->where('status', 'success')
                              ->first();

                $trends[] = [
                    'month' => date('F', mktime(0, 0, 0, $month, 1)),
                    'year' => $year,
                    'total' => (int)($result['total'] ?? 0)
                ];
            }

            return array_reverse($trends);
        } catch (\Exception $e) {
            log_message('error', '[Payment] Error getting payment trends: ' . $e->getMessage());
            return array_fill(0, $months, [
                'month' => '',
                'year' => '',
                'total' => 0
            ]);
        }
    }
}
