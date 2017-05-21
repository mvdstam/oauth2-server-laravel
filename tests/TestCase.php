<?php

namespace Mvdstam\Oauth2ServerLaravel\Tests;

use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Mvdstam\Oauth2ServerLaravel\Providers\Oauth2ServerServiceProvider;
use Orchestra\Database\ConsoleServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{

    /**
     * @var string
     */
    protected $publicKey;

    /**
     * @var string
     */
    protected $privateKey;

    protected function getPackageProviders($app)
    {
        return [
            Oauth2ServerServiceProvider::class,
            ConsoleServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        /*
         * Generate a RSA keypair and store it in the configuration
         */
        list($this->publicKey, $this->privateKey) = $this->getKeyPair();

        $app['config']->set('oauth2-server.key.public', $this->publicKey);
        $app['config']->set('oauth2-server.key.private', $this->privateKey);
    }


    /**
     * @return string[]
     */
    protected function getKeyPair()
    {
        $config = [
            'digest_alg' => 'sha256',
            'private_key_bits' => 4096,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ];

        $keyPair = openssl_pkey_new($config);
        openssl_pkey_export($keyPair, $privateKey);

        return [
            openssl_pkey_get_details($keyPair)['key'],
            $privateKey
        ];
    }

    protected function verifyToken($accessToken)
    {
        $this->assertTrue(
            (new Parser)->parse($accessToken)->verify(new Sha256, new Key($this->publicKey))
        );
    }

}
