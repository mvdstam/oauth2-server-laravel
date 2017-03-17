<?php

return [
    /*
     * RSA Keypair
     *
     * These keys are used to sign (and validate) JWT tokens. Clients consuming
     * your application should validate the JWT tokens using your *public* key.
     * Keep your private key secret at all times and do *not* share it with
     * anyone.
     */
    'key' => [
        'public' => 'public.pem',
        'private' => 'private.pem',
        'passphrase' => null
    ],

    /*
     * Grant type configuration
     *
     * Each grant type can be enabled/disabled and its implementation can be
     * replaced as well by simply changing the classname of the grant type, as long if
     * League\OAuth2\Server\Grant\GrantTypeInterface is implemented for that grant.
     *
     * All TTL's are given in "interval_spec" notation. See also: http://php.net/manual/en/dateinterval.construct.php.
     * By default, access tokens expire after 1 hour. Refresh tokens expire after 14 days. Please note that the implicit
     * grant does *not* support refresh tokens.
     */
    'grants' => [
        'password' => [
            'class' => \League\OAuth2\Server\Grant\PasswordGrant::class,
            'enabled' => true,
            'access_token_ttl' => 'PT1H',
            'refresh_token_ttl' => 'P14D'
        ],

        'client_credentials' => [
            'class' => \League\OAuth2\Server\Grant\ClientCredentialsGrant::class,
            'enabled' => true,
            'access_token_ttl' => 'PT1H',
            'refresh_token_ttl' => 'P14D'
        ],

        'refresh_token' => [
            'class' => \League\OAuth2\Server\Grant\RefreshTokenGrant::class,
            'enabled' => true,
            'access_token_ttl' => 'PT1H',
            'refresh_token_ttl' => 'P14D'
        ],

        'authorization_code' => [
            'class' => \League\OAuth2\Server\Grant\AuthCodeGrant::class,
            'enabled' => true,
            'access_token_ttl' => 'PT1H',
            'refresh_token_ttl' => 'P14D'
        ],

        'implicit' => [
            'class' => \League\OAuth2\Server\Grant\ImplicitGrant::class,
            'enabled' => true,
            'access_token_ttl' => 'PT1H'
        ]
    ],

    /*
     * OAuth2 only needs 2 URI's to work.
     *
     * The access_token route is used by clients to request access tokens (POST) from the server,
     * as well as revoking tokens (DELETE) should the client want to do this manually.
     * The authorize route is used by clients to initiate (GET) the "authorization_code" and "implicit" grant types and,
     * after authenticating and authorizing the user, to complete the authorization process (POST).
     */
    'paths' => [
        'access_token' => 'oauth2/access_token',
        'authorize' => 'oauth2/authorize'
    ],

    /*
     * Controller name for your OAuth server
     */
    'controller' => \Mvdstam\Oauth2ServerLaravel\Http\Controllers\OAuth2Controller::class
];
