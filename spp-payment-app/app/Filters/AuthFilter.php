<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        // If user is not logged in and trying to access protected routes
        if (!$session->has('user_id')) {
            return redirect()->to('/auth/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // If role restrictions are specified
        if (!empty($arguments)) {
            $userRole = $session->get('role'); // Changed from user_role to role
            
            // If user's role is not in the allowed roles
            if (!in_array($userRole, $arguments)) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing after the request
    }
}
