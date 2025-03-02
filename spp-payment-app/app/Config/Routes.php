<?php

namespace Config;

// Create a new instance of our RouteCollection class.
use CodeIgniter\Router\RouteCollection;

$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// Authentication Routes
$routes->group('auth', function ($routes) {
    $routes->get('login', 'AuthController::login');
    $routes->post('authenticate', 'AuthController::authenticate');
    $routes->get('logout', 'AuthController::logout');
    $routes->get('forgot-password', 'AuthController::forgotPassword');
    $routes->post('reset-password', 'AuthController::resetPassword');
    $routes->get('reset-password/(:segment)', 'AuthController::showResetForm/$1');
    $routes->post('update-password/(:segment)', 'AuthController::updatePassword/$1');
});

// Dashboard Routes (Protected)
$routes->group('dashboard', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'DashboardController::index');
});

// Student Routes (Protected)
$routes->group('students', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'StudentController::index');
    $routes->get('create', 'StudentController::create', ['filter' => 'auth:admin,bendahara']);
    $routes->post('store', 'StudentController::store', ['filter' => 'auth:admin,bendahara']);
    $routes->get('edit/(:num)', 'StudentController::edit/$1', ['filter' => 'auth:admin,bendahara']);
    $routes->post('update/(:num)', 'StudentController::update/$1', ['filter' => 'auth:admin,bendahara']);
    $routes->get('delete/(:num)', 'StudentController::delete/$1', ['filter' => 'auth:admin']);
    $routes->get('profile', 'StudentController::profile', ['filter' => 'auth:siswa']);
});

// Payment Routes (Protected)
$routes->group('payments', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'PaymentController::index');
    $routes->get('create', 'PaymentController::create', ['filter' => 'auth:admin,bendahara']);
    $routes->post('store', 'PaymentController::store', ['filter' => 'auth:admin,bendahara']);
    $routes->get('verify/(:num)', 'PaymentController::verify/$1', ['filter' => 'auth:admin,bendahara']);
    $routes->post('confirm/(:num)', 'PaymentController::confirm/$1', ['filter' => 'auth:admin,bendahara']);
    $routes->get('history', 'PaymentController::history');
    $routes->get('receipt/(:num)', 'PaymentController::receipt/$1');
    
    // Additional payment routes
    $routes->get('monthly-report', 'PaymentController::monthlyReport', ['filter' => 'auth:admin,bendahara']);
    $routes->get('annual-report', 'PaymentController::annualReport', ['filter' => 'auth:admin,bendahara']);
    $routes->get('unpaid', 'PaymentController::unpaidStudents', ['filter' => 'auth:admin,bendahara']);
    $routes->get('export-report/(:segment)', 'PaymentController::exportReport/$1', ['filter' => 'auth:admin,bendahara']);
    
    // Payment Gateway Callback
    $routes->post('callback', 'PaymentController::tripayCallback');
});

// Report Routes (Protected - Admin & Bendahara Only)
$routes->group('reports', ['filter' => 'auth:admin,bendahara'], function ($routes) {
    $routes->get('/', 'ReportController::index');
    $routes->get('daily', 'ReportController::daily');
    $routes->get('monthly', 'ReportController::monthly');
    $routes->get('annual', 'ReportController::annual');
    $routes->get('export/(:segment)', 'ReportController::export/$1');
});

// Settings Routes (Protected - Admin Only)
$routes->group('settings', ['filter' => 'auth:admin'], function ($routes) {
    $routes->get('/', 'SettingController::index');
    $routes->post('update', 'SettingController::update');
    $routes->get('backup', 'SettingController::backup');
    $routes->post('restore', 'SettingController::restore');
});

// Profile Routes (Protected)
$routes->group('profile', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'ProfileController::index');
    $routes->post('update', 'ProfileController::update');
    $routes->post('change-password', 'ProfileController::changePassword');
});

// API Routes for Tripay Callback
$routes->group('api', function ($routes) {
    $routes->post('payment/callback', 'ApiController::paymentCallback');
});

$routes->group('students', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'StudentController::index');
    $routes->get('create', 'StudentController::create');
    $routes->post('store', 'StudentController::store');
    $routes->get('edit/(:num)', 'StudentController::edit/$1');
    $routes->post('update/(:num)', 'StudentController::update/$1');
    $routes->post('delete/(:num)', 'StudentController::delete/$1');
    $routes->get('export', 'StudentController::export');
    $routes->post('import', 'StudentController::import');
});
$routes->get('/', function () {
    return redirect()->to('/auth/login');
});

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
