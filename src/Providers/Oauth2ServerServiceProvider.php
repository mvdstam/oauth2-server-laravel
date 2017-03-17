<?php


namespace Mvdstam\Oauth2ServerLaravel\Providers;


use DateInterval;
use Illuminate\Support\ServiceProvider;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\GrantTypeInterface;
use League\OAuth2\Server\Grant\ImplicitGrant;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use League\OAuth2\Server\ResourceServer;
use Mvdstam\Oauth2ServerLaravel\Commands\CreateClientCommand;
use Mvdstam\Oauth2ServerLaravel\Commands\CreateScopeCommand;
use Mvdstam\Oauth2ServerLaravel\Commands\CreateUserCommand;
use Mvdstam\Oauth2ServerLaravel\Contracts\JWTFactoryInterface;
use Mvdstam\Oauth2ServerLaravel\Entities\AccessToken;
use Mvdstam\Oauth2ServerLaravel\Entities\AuthCode;
use Mvdstam\Oauth2ServerLaravel\Entities\Client;
use Mvdstam\Oauth2ServerLaravel\Entities\RefreshToken;
use Mvdstam\Oauth2ServerLaravel\Entities\Scope;
use Mvdstam\Oauth2ServerLaravel\Entities\User;
use Mvdstam\Oauth2ServerLaravel\Factories\JWTFactory;
use Mvdstam\Oauth2ServerLaravel\Repositories\AccessTokenRepository;
use Mvdstam\Oauth2ServerLaravel\Repositories\AuthCodeRepository;
use Mvdstam\Oauth2ServerLaravel\Repositories\ClientRepository;
use Mvdstam\Oauth2ServerLaravel\Repositories\RefreshTokenRepository;
use Mvdstam\Oauth2ServerLaravel\Repositories\ScopeRepository;
use Mvdstam\Oauth2ServerLaravel\Repositories\UserRepository;

class Oauth2ServerServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this
            ->publishConfig()
            ->loadRoutes()
            ->loadMigrations()
            ->registerCommands();
    }

    public function register()
    {
        // Merge config
        $this->mergeConfigFrom(
            dirname(__DIR__).'/config/oauth2-server.php', 'oauth2-server'
        );

        /*
         * Bind entities
         */
        $this->app->bind(AccessTokenEntityInterface::class, AccessToken::class);
        $this->app->bind(AuthCodeEntityInterface::class, AuthCode::class);
        $this->app->bind(ClientEntityInterface::class, Client::class);
        $this->app->bind(RefreshTokenEntityInterface::class, RefreshToken::class);
        $this->app->bind(ScopeEntityInterface::class, Scope::class);
        $this->app->bind(UserEntityInterface::class, User::class);

        /*
         * Bind repositories
         */
        $this->app->bind(AccessTokenRepositoryInterface::class, AccessTokenRepository::class);
        $this->app->bind(AuthCodeRepositoryInterface::class, AuthCodeRepository::class);
        $this->app->bind(ClientRepositoryInterface::class, ClientRepository::class);
        $this->app->bind(RefreshTokenRepositoryInterface::class, RefreshTokenRepository::class);
        $this->app->bind(ScopeRepositoryInterface::class, ScopeRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);

        /*
         * Bind miscellaneous classes
         */
        $this->app->bind(JWTFactoryInterface::class, JWTFactory::class);

        /*
         * OAuth2 Resource server
         */
        $this->app->singleton(ResourceServer::class, function() {
            return new ResourceServer(
                app(AccessTokenRepositoryInterface::class),
                app('oauth2-server.key.public')
            );
        });

        /*
         * OAuth2 Authorization server
         */
        $this->app->singleton(AuthorizationServer::class, function() {
            return new AuthorizationServer(
                app(ClientRepositoryInterface::class),
                app(AccessTokenRepositoryInterface::class),
                app(ScopeRepositoryInterface::class),
                app('oauth2-server.key.private'),
                app('oauth2-server.key.public')
            );
        });

        /*
         * Add active grants to authorization server
         */
        $this->app->resolving(AuthorizationServer::class, function(AuthorizationServer $authorizationServer) {
            foreach(config('oauth2-server.grants') as $grantConfig) {
                if (!(boolean) $grantConfig['enabled']) continue;

                /** @var GrantTypeInterface $grant */
                $grant = app($grantConfig['class']);

                // Set refresh token TTL
                if ($grant->getIdentifier() !== 'implicit') {
                    $grant->setRefreshTokenTTL(new DateInterval($grantConfig['refresh_token_ttl']));
                }

                // Enable grant type
                $authorizationServer->enableGrantType(
                    $grant,
                    new DateInterval($grantConfig['access_token_ttl'])
                );
            }
        });

        /*
         * Authorization code grant type
         */
        $this->app->singleton(AuthCodeGrant::class, function() {
            return new AuthCodeGrant(
                app(AuthCodeRepositoryInterface::class),
                app(RefreshTokenRepositoryInterface::class),
                new DateInterval(config('oauth2-server.grants.authorization_code.access_token_ttl'))
            );
        });

        /*
         * Implicit grant type
         */
        $this->app->singleton(ImplicitGrant::class, function() {
            return new ImplicitGrant(
                new DateInterval(config('oauth2-server.grants.implicit.access_token_ttl'))
            );
        });

        /*
         * RSA keypair for JWT signing
         */
        $this->app->singleton('oauth2-server.key.public', function() {
            return new CryptKey(config('oauth2-server.key.public'));
        });

        $this->app->singleton('oauth2-server.key.private', function() {
            return new CryptKey(config('oauth2-server.key.private'), config('oauth2-server.key.passphrase'));
        });
    }

    protected function loadRoutes()
    {
        if (!$this->app->routesAreCached()) {
            require dirname(__DIR__) . '/Http/routes.php';
        }

        return $this;
    }

    protected function loadMigrations()
    {
        $this->publishes([
            dirname(__DIR__) . '/migrations' => database_path('migrations')
        ], 'migrations');

        return $this;
    }

    protected function publishConfig()
    {
        $this->publishes([
            dirname(__DIR__) . '/config/oauth2-server.php' => config_path('oauth2-server.php'),
        ]);

        return $this;
    }

    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateScopeCommand::class,
                CreateClientCommand::class,
                CreateUserCommand::class
            ]);
        }

        return $this;
    }

}
