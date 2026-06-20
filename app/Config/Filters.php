<?php

namespace Config;

use App\Filters\Auth;
use CodeIgniter\Config\Filters as BaseFilters;
use CodeIgniter\Filters\Cors;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\ForceHTTPS;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\PageCache;
use CodeIgniter\Filters\PerformanceMetrics;
use CodeIgniter\Filters\SecureHeaders;

class Filters extends BaseFilters
{
    

    public array $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'cors'          => Cors::class,
        'forcehttps'    => ForceHTTPS::class,
        'pagecache'     => PageCache::class,
        'performance'   => PerformanceMetrics::class,
        'auth'          => Auth::class,
        'api_auth'      => \App\Filters\ApiAuthFilter::class,
    ];

    

    public array $required = [
        'before' => [
            'forcehttps', 
            'pagecache',  
            'cors',
        ],
        'after' => [
            'pagecache',   
            'performance', 
            'toolbar',     
            'cors',
        ],
    ];

    

    public array $globals = [
        'before' => [
        ],
        'after' => [
        ],
    ];

    

    public array $methods = [];

    

    public array $filters = [];
}
