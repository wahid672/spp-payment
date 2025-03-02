<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table = 'settings';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = [
        'key',
        'value',
        'group',
        'description'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'key' => 'required|max_length[50]|is_unique[settings.key,id,{id}]',
        'value' => 'required',
        'group' => 'permit_empty|max_length[50]',
        'description' => 'permit_empty'
    ];

    protected $validationMessages = [
        'key' => [
            'required' => 'Kunci pengaturan harus diisi',
            'max_length' => 'Kunci pengaturan maksimal 50 karakter',
            'is_unique' => 'Kunci pengaturan sudah ada'
        ],
        'value' => [
            'required' => 'Nilai pengaturan harus diisi'
        ],
        'group' => [
            'max_length' => 'Grup pengaturan maksimal 50 karakter'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Cache for settings
    private static $cache = [];

    // Custom Methods
    public function get(string $key, $default = null)
    {
        if (isset(self::$cache[$key])) {
            return self::$cache[$key];
        }

        $setting = $this->where('key', $key)->first();
        if ($setting) {
            self::$cache[$key] = $setting['value'];
            return $setting['value'];
        }

        return $default;
    }

    public function setSetting(string $key, $value, ?string $group = null, ?string $description = null)
    {
        $setting = $this->where('key', $key)->first();

        $data = [
            'key' => $key,
            'value' => $value,
            'group' => $group,
            'description' => $description
        ];

        if ($setting) {
            $this->update($setting['id'], $data);
        } else {
            $this->insert($data);
        }

        // Update cache
        self::$cache[$key] = $value;

        return true;
    }

    public function getByGroup(string $group)
    {
        return $this->where('group', $group)
                    ->orderBy('key', 'ASC')
                    ->findAll();
    }

    public function getAllSettings()
    {
        $settings = $this->findAll();
        $grouped = [];

        foreach ($settings as $setting) {
            $group = $setting['group'] ?? 'general';
            if (!isset($grouped[$group])) {
                $grouped[$group] = [];
            }
            $grouped[$group][] = $setting;
        }

        return $grouped;
    }

    public function clearCache()
    {
        self::$cache = [];
    }

    // Helper methods for specific settings
    public function getSchoolInfo()
    {
        return [
            'name' => $this->get('school_name', 'Nama Sekolah'),
            'address' => $this->get('school_address', 'Alamat Sekolah'),
            'phone' => $this->get('school_phone', '08xxxxxxxxxx')
        ];
    }

    public function getWanotifConfig()
    {
        return [
            'api_key' => $this->get('wanotif_api_key'),
            'sender' => $this->get('wanotif_sender')
        ];
    }

    public function getTripayConfig()
    {
        return [
            'api_key' => $this->get('tripay_api_key'),
            'private_key' => $this->get('tripay_private_key'),
            'merchant_code' => $this->get('tripay_merchant_code')
        ];
    }

    public function getNotificationTemplates()
    {
        return [
            'payment_success' => $this->get('payment_notification_template'),
            'payment_reminder' => $this->get('reminder_notification_template')
        ];
    }

    public function getCurrentAcademicYear()
    {
        return $this->get('academic_year', date('Y') . '/' . (date('Y') + 1));
    }
}
