<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\SettingModel;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = ['form', 'url', 'text', 'number'];

    /**
     * Session instance
     */
    protected $session;

    /**
     * Settings Model instance
     */
    protected $settings;

    /**
     * School information
     */
    protected $schoolInfo;

    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        $this->session = \Config\Services::session();
        $this->settings = new SettingModel();

        // Load school information
        $this->schoolInfo = [
            'name' => $this->settings->get('school_name', 'SPP Payment System'),
            'address' => $this->settings->get('school_address', ''),
            'phone' => $this->settings->get('school_phone', ''),
            'academic_year' => $this->settings->get('academic_year', date('Y') . '/' . (date('Y') + 1))
        ];

        // Share common data with all views
        $this->shareCommonData();
    }

    /**
     * Share common data with all views
     */
    protected function shareCommonData()
    {
        $userData = null;
        if ($this->session->has('user_id')) {
            $userData = [
                'id' => $this->session->get('user_id'),
                'name' => $this->session->get('name'),
                'email' => $this->session->get('email'),
                'role' => $this->session->get('role'),
                'student_id' => $this->session->get('student_id'),
                'nis' => $this->session->get('nis'),
                'class' => $this->session->get('class'),
                'major' => $this->session->get('major'),
            ];
        }

        // Share data with all views
        $this->viewData = [
            'schoolInfo' => $this->schoolInfo,
            'userData' => $userData,
            'currentUrl' => current_url(),
            'request' => $this->request
        ];
    }

    /**
     * Format currency
     */
    protected function formatCurrency($amount)
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    /**
     * Format date to Indonesian format
     */
    protected function formatDate($date, $format = 'd F Y')
    {
        if (empty($date)) {
            return '-';
        }

        $timestamp = strtotime($date);
        $indonesianMonths = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $month = $indonesianMonths[date('n', $timestamp) - 1];
        return str_replace('F', $month, date($format, $timestamp));
    }

    /**
     * Send WhatsApp notification
     */
    protected function sendWhatsAppNotification($phone, $message)
    {
        $apiKey = $this->settings->get('wanotif_api_key');
        $sender = $this->settings->get('wanotif_sender');

        if (empty($apiKey) || empty($sender)) {
            log_message('error', 'WhatsApp notification settings not configured');
            return false;
        }

        // Remove any non-numeric characters from phone number
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Ensure phone number starts with country code
        if (substr($phone, 0, 2) !== '62') {
            $phone = '62' . ltrim($phone, '0');
        }

        try {
            $client = \Config\Services::curlrequest();
            $response = $client->request('POST', 'https://api.wanotif.id/v1/send', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey
                ],
                'form_params' => [
                    'sender' => $sender,
                    'recipient' => $phone,
                    'message' => $message
                ]
            ]);

            $result = json_decode($response->getBody(), true);
            
            if (isset($result['success']) && $result['success']) {
                return true;
            }

            log_message('error', 'WhatsApp notification failed: ' . json_encode($result));
            return false;
        } catch (\Exception $e) {
            log_message('error', 'WhatsApp notification error: ' . $e->getMessage());
            return false;
        }
    }
}
