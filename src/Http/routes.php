<?php

use Illuminate\Routing\Router;

/** @var Router $router */
$router = app(Router::class);

$router->post(config('oauth2-server.paths.access_token'), [
    'as' => 'oauth2-server::access_token',
    'uses' => config('oauth2-server.controller').'@accessToken'
]);

$router->delete(config('oauth2-server.paths.access_token'), [
    'as' => 'oauth2-server::access_token',
    'uses' => config('oauth2-server.controller').'@revokeAccessToken',
    'middleware' => ['oauth2-resource']
]);

$router->get(config('oauth2-server.paths.authorize'), [
    'as' => 'oauth2-server::authorize',
    'uses' => config('oauth2-server.controller').'@authorize'
]);

$router->post(config('oauth2-server.paths.authorize'), [
    'as' => 'oauth2-server::authorize',
    'uses' => config('oauth2-server.controller').'@completeAuthorization'
]);
