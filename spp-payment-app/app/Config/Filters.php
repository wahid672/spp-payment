<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\SecureHeaders;
use App\Filters\AuthFilter;

class Filters extends BaseConfig
{
    /**
     * Configures aliases for Filter classes to
     * make reading things nicer and simpler.
     */
    public array $aliases = [
        'csrf'      => CSRF::class,
        'toolbar'   => DebugToolbar::class,
        'honeypot'  => Honeypot::class,
        'invalidchars' => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'auth'      => AuthFilter::class,
        'auth:admin' => [
            AuthFilter::class => ['admin']
        ],
        'auth:bendahara' => [
            AuthFilter::class => ['bendahara']
        ],
        'auth:admin,bendahara' => [
            AuthFilter::class => ['admin', 'bendahara']
        ],
        'auth:siswa' => [
            AuthFilter::class => ['siswa']
        ]
    ];

    /**
     * List of filter aliases that are always
     * applied before and after every request.
     */
    public array $globals = [
        'before' => [
            'csrf',
        ],
        'after' => [
            'toolbar',
        ],
    ];

    /**
     * List of filter aliases that works on a
     * particular HTTP method (GET, POST, etc.).
     *
     * Example:
     * 'post' => ['foo', 'bar']
     *
     * If you use this, you should disable auto-routing because auto-routing
     * permits any HTTP method to access a controller. Accessing the controller
     * with a method you don't expect could bypass the filter.
     */
    public array $methods = [
        'post' => ['csrf']
    ];

    /**
     * List of filter aliases that should run on any
     * before or after URI patterns.
     *
     * Example:
     * 'isLoggedIn' => ['before' => ['account/*', 'profiles/*']]
     */
    public array $filters = [
        'auth' => [
            'before' => [
                'dashboard/*',
                'students/*',
                'payments/*',
                'settings/*',
                'reports/*',
                'profile/*'
            ]
        ],
        'auth:admin' => [
            'before' => [
                'settings/*',
                'users/*'
            ]
        ],
        'auth:admin,bendahara' => [
            'before' => [
                'reports/*',
                'payments/create',
                'payments/verify/*'
            ]
        ]
    ];

    /**
     * List of filter aliases that should run on any
     * before or after URI patterns.
     *
     * Example:
     * 'isLoggedIn' => ['before' => ['account/*', 'profiles/*']]
     */
    public array $filterGroups = [];
}
