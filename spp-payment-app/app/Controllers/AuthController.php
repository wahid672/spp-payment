<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\StudentModel;

class AuthController extends BaseController
{
    protected $userModel;
    protected $studentModel;
    protected $session;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->studentModel = new StudentModel();
        $this->session = session();
    }

    public function login()
    {
        if ($this->session->has('user_id')) {
            return redirect()->to('/dashboard');
        }
        return view('auth/login');
    }

    public function authenticate()
    {
        // Debug log
        log_message('debug', 'Authentication attempt started');
        log_message('debug', 'POST data: ' . json_encode($this->request->getPost()));

        // Validate input
        $rules = [
            'username' => 'required',
            'password' => 'required'
        ];

        if (!$this->validate($rules)) {
            log_message('debug', 'Validation failed: ' . json_encode($this->validator->getErrors()));
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Try finding user by username first
        $user = $this->userModel->where('username', $username)->first();
        
        // If not found, try by email
        if (!$user) {
            $user = $this->userModel->where('email', $username)->first();
        }

        if (!$user) {
            log_message('debug', 'User not found: ' . $username);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Username atau password salah');
        }

        log_message('debug', 'User found: ' . json_encode($user));

        // Verify password
        if (!password_verify($password, $user['password'])) {
            log_message('debug', 'Password verification failed for user: ' . $username);
            log_message('debug', 'Provided password: ' . $password);
            log_message('debug', 'Stored hash: ' . $user['password']);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Username atau password salah');
        }

        // Set session data
        $sessionData = [
            'user_id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'name' => $user['name'],
            'role' => $user['role'],
            'logged_in' => true
        ];

        // If user is a student, get student data
        if ($user['role'] === 'siswa') {
            $student = $this->studentModel->where('user_id', $user['id'])->first();
            if ($student) {
                $sessionData['student_id'] = $student['id'];
                $sessionData['nis'] = $student['nis'];
                $sessionData['class'] = $student['class'];
                $sessionData['major'] = $student['major'];
            }
        }

        $this->session->set($sessionData);

        // Log successful login
        log_message('info', "User {$user['username']} logged in successfully");
        log_message('debug', 'Session data set: ' . json_encode($sessionData));

        return redirect()->to('/dashboard')
            ->with('success', 'Login berhasil');
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('/auth/login')
            ->with('success', 'Logout berhasil');
    }

    public function forgotPassword()
    {
        return view('auth/forgot_password');
    }

    public function resetPassword()
    {
        $rules = [
            'email' => 'required|valid_email'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');
        $user = $this->userModel->where('email', $email)->first();

        if (!$user) {
            return redirect()->back()
                ->with('error', 'Email tidak ditemukan');
        }

        // Generate reset token
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Store token in session (in production, should be stored in database)
        $this->session->set('reset_token', [
            'token' => $token,
            'email' => $email,
            'expiry' => $expiry
        ]);

        // Send reset email (implement email sending in production)
        $resetLink = base_url("auth/reset-password/{$token}");
        log_message('info', "Password reset requested for {$email}. Reset link: {$resetLink}");

        return redirect()->back()
            ->with('success', 'Instruksi reset password telah dikirim ke email Anda');
    }

    public function showResetForm($token)
    {
        $resetToken = $this->session->get('reset_token');

        if (!$resetToken || 
            $resetToken['token'] !== $token || 
            strtotime($resetToken['expiry']) < time()) {
            return redirect()->to('/auth/forgot-password')
                ->with('error', 'Token reset password tidak valid atau sudah kadaluarsa');
        }

        return view('auth/reset_password', ['token' => $token]);
    }

    public function updatePassword($token)
    {
        $resetToken = $this->session->get('reset_token');

        if (!$resetToken || 
            $resetToken['token'] !== $token || 
            strtotime($resetToken['expiry']) < time()) {
            return redirect()->to('/auth/forgot-password')
                ->with('error', 'Token reset password tidak valid atau sudah kadaluarsa');
        }

        $rules = [
            'password' => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $user = $this->userModel->where('email', $resetToken['email'])->first();
        
        if (!$user) {
            return redirect()->to('/auth/forgot-password')
                ->with('error', 'User tidak ditemukan');
        }

        // Update password
        $this->userModel->update($user['id'], [
            'password' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT)
        ]);

        // Clear reset token
        $this->session->remove('reset_token');

        return redirect()->to('/auth/login')
            ->with('success', 'Password berhasil diubah. Silakan login dengan password baru.');
    }
}
