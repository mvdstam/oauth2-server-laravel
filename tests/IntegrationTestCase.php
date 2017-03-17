<?php


namespace Mvdstam\Oauth2ServerLaravel\Tests;

use Illuminate\Foundation\Testing\DatabaseMigrations;

abstract class IntegrationTestCase extends TestCase
{

    use DatabaseMigrations;

    protected function setUp()
    {
        parent::setUp();

        $this->loadMigrationsFrom(dirname(__DIR__).'/src/migrations');
        $this->withFactories(realpath(__DIR__) . '/factories');
    }

    protected function resolveApplicationHttpKernel($app)
    {
        $app->singleton('Illuminate\Contracts\Http\Kernel', \Mvdstam\Oauth2ServerLaravel\Tests\Kernel::class);
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        /*
         * Add middleware
         */
        $app['Illuminate\Contracts\Http\Kernel']->pushMiddleware('Illuminate\Session\Middleware\StartSession');

        /*
         * Controller classname
         */
        $app['config']->set('oauth2-server.controller', OAuth2Controller::class);

        /*
         * Configuration
         */
        $app['config']->set('database.default', 'testing');

        $app['config']->set('database.connections.testing', [
            'driver' => 'mysql',
            'host' => env('DB_HOST'),
            'database' => env('DB_DATABASE'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true
        ]);

        $app['config']->set('session', [
            'driver' => 'cookie',
            'expire_on_close' => true,
            'lottery' => [2, 100],
            'cookie' => 'phpunit_session',
            'path' => '/',
            'domain' => null,
            'secure' => false
        ]);

        /*
         * Disable all grants since we're explicitly enabling them in tests
         */
        $app['config']->set('oauth2-server.grants.password.enabled', false);
        $app['config']->set('oauth2-server.grants.refresh_token.enabled', false);
        $app['config']->set('oauth2-server.grants.client_credentials.enabled', false);
        $app['config']->set('oauth2-server.grants.implicit.enabled', false);
        $app['config']->set('oauth2-server.grants.authorization_code.enabled', false);
    }

}
