<?php


namespace Mvdstam\Oauth2ServerLaravel\Commands;


use Exception;
use Illuminate\Console\Command;
use Mvdstam\Oauth2ServerLaravel\Repositories\ClientRepository;
use Mvdstam\Oauth2ServerLaravel\Repositories\ScopeRepository;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class CreateClientCommand extends Command
{

    protected $signature = 'oauth2-server:create-client {--id=} {--secret=} {--name=} {--redirect_uri=} {--scope=*}';

    protected $description = 'Create new clients for your OAuth2 server';

    /**
     * @var ClientRepository
     */
    protected $clients;

    /**
     * @var ScopeRepository
     */
    protected $scopes;

    /**
     * CreateClientCommand constructor.
     * @param ClientRepository $clients
     * @param ScopeRepository $scopes
     */
    public function __construct(ClientRepository $clients, ScopeRepository $scopes)
    {
        parent::__construct();

        $this->clients = $clients;
        $this->scopes = $scopes;
    }

    public function handle()
    {
        try {
            $this->clients->forceCreate([
                'id' => (string) $this->getClientId(),
                'secret' => (string) $this->getClientSecret(),
                'name' => $this->option('name') ?: $this->ask('Please enter a name'),
                'redirect_uri' => $this->option('redirect_uri')
            ])->scopes()->sync($this->argument('scope'));
        } catch (Exception $e) {
            $this->error("An error occurred: {$e->getMessage()}");
            return 1;
        }

        $this->info('Client created succesfully');
    }

    /**
     * @return UuidInterface
     */
    protected function getClientId()
    {
        if (!($clientId = $this->option('id'))) {
            $this->info('Generating a random UUID for client id...');
            $clientId = Uuid::uuid1();
        } else {
            $clientId = Uuid::fromString($clientId);
        }

        return $clientId;
    }

    /**
     * @return UuidInterface
     */
    protected function getClientSecret()
    {
        if (!($clientSecret = $this->option('secret'))) {
            $this->info('Generating a random UUID for client secret...');
            $clientSecret = Uuid::uuid1();
        } else {
            $clientSecret = Uuid::fromString($clientSecret);
        }

        return $clientSecret;
    }

}
