<?php

namespace Mvdstam\Oauth2ServerLaravel\Commands;

use Exception;
use Illuminate\Console\Command;

class GenerateKeyPairCommand extends Command
{

    /**
     * @var string
     */
    protected $signature = 'oauth2-server:generate-key-pair {passphrase?}';

    /**
     * @var string
     */
    protected $description = 'Generate a RSA keypair for use in your OAuth2 server';

    public function handle()
    {
        if (!($passphrase = $this->argument('passphrase'))) {
            $passphrase = $this->secret('Enter passphrase or leave empty (not recommended)');
        }

        $this->info('Creating storage directory...');
        $storagePath = storage_path('app/oauth2-server');
        if (!is_dir($storagePath) && !mkdir($storagePath, 0777, true)) {
            throw new Exception('Unable to create storage directory for oauth2 server');
        }

        $this->info('Generating keypair...');
        list($publicKey, $privateKey) = $this->getKeyPair($passphrase);

        $publicKeyFile = $storagePath . DIRECTORY_SEPARATOR . 'public.pem';
        $privateKeyFile = $storagePath . DIRECTORY_SEPARATOR . 'private.pem';

        if (is_file($publicKeyFile) || is_file($privateKeyFile)) {
            throw new Exception('Unable to store keys files because they already exist on disk.');
        }

        $this->info('Storing keys...');
        if (!file_put_contents($publicKeyFile, $publicKey) || !file_put_contents($privateKeyFile, $privateKey)) {
            throw new Exception('Unable to write keys to file. Is the directory writable?');
        }

        $this->info('Keys generated succesfully!');
    }

    /**
     * @param string|null $passphrase
     * @return string[]
     */
    protected function getKeyPair($passphrase = null)
    {
        $config = [
            'digest_alg' => 'sha256',
            'private_key_bits' => 4096,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ];

        $keyPair = openssl_pkey_new($config);
        openssl_pkey_export($keyPair, $privateKey, $passphrase);

        return [
            openssl_pkey_get_details($keyPair)['key'],
            $privateKey
        ];
    }
}
