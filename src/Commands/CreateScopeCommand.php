<?php


namespace Mvdstam\Oauth2ServerLaravel\Commands;


use Exception;
use Illuminate\Console\Command;
use Mvdstam\Oauth2ServerLaravel\Repositories\ScopeRepository;

class CreateScopeCommand extends Command
{

    /**
     * @var string
     */
    protected $signature = 'oauth2-server:create-scope {id?}';

    /**
     * @var string
     */
    protected $description = 'Create new scopes for your OAuth2 server';

    /**
     * @var ScopeRepository
     */
    protected $scopes;

    /**
     * CreateScopeCommand constructor.
     * @param ScopeRepository $scopes
     */
    public function __construct(ScopeRepository $scopes)
    {
        parent::__construct();

        $this->scopes = $scopes;
    }


    public function handle()
    {
        try {
            if (!($scopeId = $this->argument('id'))) {
                $scopeId = $this->ask('Enter scope ID');
            }

            $this->scopes->forceCreate(['id' => $scopeId]);
            $this->info('Scope created succesfully!');
        } catch (Exception $e) {
            $this->error("An error occurred: {$e->getMessage()}");
            return 1;
        }
    }
}
