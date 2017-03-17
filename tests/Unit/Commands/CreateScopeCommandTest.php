<?php


namespace Mvdstam\Oauth2ServerLaravel\Commands;


use Exception;
use Mockery;
use Mockery\Mock;
use Mvdstam\Oauth2ServerLaravel\Repositories\ScopeRepository;
use Mvdstam\Oauth2ServerLaravel\Tests\TestCase;

class CreateScopeCommandTest extends TestCase
{

    /**
     * @var CreateScopeCommand
     */
    protected $command;

    /**
     * @var ScopeRepository
     */
    protected $scopes;

    protected function setUp()
    {
        parent::setUp();

        $this->scopes = Mockery::mock(ScopeRepository::class);
        $this->command = Mockery::mock(CreateScopeCommand::class)->makePartial();
        $this->command->__construct($this->scopes);
    }

    public function testHandleUsesArgumentToCreateNewScope()
    {
        /**
         * @var Mock $commandMock
         * @var Mock $scopesMock
         */
        $commandMock = $this->command;
        $scopesMock = $this->scopes;
        $scopeId = 'my-new-scope';

        $commandMock
            ->shouldReceive('argument')
            ->once()
            ->with('id')
            ->andReturn($scopeId);

        $commandMock
            ->shouldReceive('info')
            ->once();

        $scopesMock
            ->shouldReceive('forceCreate')
            ->once()
            ->with(['id' => $scopeId]);

        $this->command->handle();
    }

    public function testHandleAsksForScopeIdWhenArgumentIsOmitted()
    {
        /**
         * @var Mock $commandMock
         * @var Mock $scopesMock
         */
        $commandMock = $this->command;
        $scopesMock = $this->scopes;
        $scopeId = 'my-new-scope';

        $commandMock
            ->shouldReceive('argument')
            ->once()
            ->with('id')
            ->andReturnNull();

        $commandMock
            ->shouldReceive('ask')
            ->once()
            ->andReturn($scopeId);

        $commandMock
            ->shouldReceive('info')
            ->once();

        $scopesMock
            ->shouldReceive('forceCreate')
            ->once()
            ->with(['id' => $scopeId]);

        $this->command->handle();
    }

    public function testHandleOutputsErrorWhenExceptionIsThrown()
    {
        /**
         * @var Mock $commandMock
         * @var Mock $scopesMock
         */
        $commandMock = $this->command;
        $scopesMock = $this->scopes;
        $scopeId = 'my-new-scope';

        $commandMock
            ->shouldReceive('argument')
            ->once()
            ->with('id')
            ->andReturn($scopeId);

        $commandMock
            ->shouldReceive('error')
            ->once();

        $scopesMock
            ->shouldReceive('forceCreate')
            ->once()
            ->with(['id' => $scopeId])
            ->andThrow(Exception::class);

        $this->command->handle();
    }
}
