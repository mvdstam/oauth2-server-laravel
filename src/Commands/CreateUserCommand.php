<?php


namespace Mvdstam\Oauth2ServerLaravel\Commands;


use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Mvdstam\Oauth2ServerLaravel\Repositories\UserRepository;
use Ramsey\Uuid\Uuid;

class CreateUserCommand extends Command
{

    /**
     * @var string
     */
    protected $signature = 'oauth2-server:create-user {--username=} {--password=}';

    /**
     * @var string
     */
    protected $description = 'Create new users for your OAuth2 server';

    /**
     * @var UserRepository
     */
    protected $users;

    /**
     * CreateUserCommand constructor.
     * @param UserRepository $users
     */
    public function __construct(UserRepository $users)
    {
        parent::__construct();
        
        $this->users = $users;
    }

    public function handle()
    {
        try {
            $this->users->forceCreate([
                'id' => (string) Uuid::uuid1(),
                'username' => $this->option('username') ?: $this->ask('Please enter desired username'),
                'password' => Hash::make($this->option('password') ?: $this->ask('Please enter desired password'))
            ]);
        } catch (Exception $e) {
            $this->error("An error occurred: {$e->getMessage()}");
            return 1;
        }

        $this->info('User created successfully');
    }

}
