<?php

namespace Mvdstam\Oauth2ServerLaravel\Http\Middleware;

use Illuminate\Http\Request;
use League\OAuth2\Server\ResourceServer;
use Mockery;
use Mockery\Mock;
use Mvdstam\Oauth2ServerLaravel\Tests\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class OAuth2MiddlewareTest extends TestCase
{

    /**
     * @var OAuth2Middleware
     */
    protected $middleware;

    /**
     * @var ResourceServer
     */
    protected $resourceServer;

    /**
     * @var Request
     */
    protected $request;

    protected function setUp()
    {
        parent::setUp();

        $this->resourceServer = Mockery::mock(ResourceServer::class);
        $this->request = Mockery::mock(Request::class);
        $this->middleware = new OAuth2Middleware($this->resourceServer);

        $this->app->instance(ResourceServer::class, $this->resourceServer);
    }

    public function testHandleValidatesRequest()
    {
        /**
         * @var Mock $resourceServerMock
         */
        $resourceServerMock = $this->resourceServer;
        $validatedRequest = app(ServerRequestInterface::class);

        $resourceServerMock
            ->shouldReceive('validateAuthenticatedRequest')
            ->once()
            ->andReturn($validatedRequest);

        $next = function($request) {
            $this->assertInstanceOf(Request::class, $request);

            return app(ResponseInterface::class);
        };

        $this->assertInstanceOf(ResponseInterface::class, $this->middleware->handle($this->request, $next));
    }

}
