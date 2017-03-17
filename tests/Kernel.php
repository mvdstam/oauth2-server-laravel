<?php

namespace Mvdstam\Oauth2ServerLaravel\Tests;

use Mvdstam\Oauth2ServerLaravel\Http\Middleware\OAuth2Middleware;

class Kernel extends \Orchestra\Testbench\Http\Kernel
{

    protected $routeMiddleware = [
        'oauth2-resource' => OAuth2Middleware::class
    ];

}
