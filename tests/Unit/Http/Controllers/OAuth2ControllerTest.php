<?php


namespace Mvdstam\Oauth2ServerLaravel\Http\Controllers;


use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use Mockery;
use Mockery\Mock;
use Mvdstam\Oauth2ServerLaravel\Tests\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Uuid;

class OAuth2ControllerTest extends TestCase
{

    /**
     * @var OAuth2Controller
     */
    protected $controller;

    /**
     * @var AuthorizationServer
     */
    protected $authorizationServer;

    /**
     * @var AccessTokenRepositoryInterface
     */
    protected $accessTokens;

    protected function setUp()
    {
        parent::setUp();

        $this->authorizationServer = Mockery::mock(AuthorizationServer::class);
        $this->accessTokens = Mockery::mock(AccessTokenRepositoryInterface::class);

        $this->controller = Mockery::mock(
            OAuth2Controller::class,
            [$this->authorizationServer, $this->accessTokens]
        )->makePartial();
    }

    public function testAccessTokenRespondsToAccessTokenRequestWithAuthorizationServer()
    {
        /** @var Mock $authorizationServerMock */
        $authorizationServerMock = $this->authorizationServer;
        $serverRequest = Mockery::mock(ServerRequestInterface::class);
        $response = Mockery::mock(ResponseInterface::class);
        $serverResponse = Mockery::mock(ResponseInterface::class);

        $authorizationServerMock
            ->shouldReceive('respondToAccessTokenRequest')
            ->once()
            ->with($serverRequest, $response)
            ->andReturn($serverResponse);

        /**
         * @var ServerRequestInterface $serverRequest
         * @var ResponseInterface $response
         */
        $result = $this->controller->accessToken($serverRequest, $response);
        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertSame($serverResponse, $result);
    }

    public function testAccessTokenReturnsGeneratedHttpResponseWhenExceptionWasThrown()
    {
        /** @var Mock $authorizationServerMock */
        $authorizationServerMock = $this->authorizationServer;
        $serverRequest = Mockery::mock(ServerRequestInterface::class);
        $response = app(ResponseInterface::class);

        $authorizationServerMock
            ->shouldReceive('respondToAccessTokenRequest')
            ->once()
            ->with($serverRequest, $response)
            ->andThrow(OAuthServerException::class);

        /**
         * @var ServerRequestInterface $serverRequest
         * @var ResponseInterface $response
         */
        $result = $this->controller->accessToken($serverRequest, $response);
        $this->assertInstanceOf(ResponseInterface::class, $result);

        $this->assertEquals(400, $result->getStatusCode());
    }

    public function testRevokeAccessTokenRevokesAccessToken()
    {
        /**
         * @var Mock $accessTokensMock
         * @var Mock $serverRequest
         */
        $accessTokensMock = $this->accessTokens;
        $serverRequest = Mockery::mock(ServerRequestInterface::class);
        $response = Mockery::mock(ResponseInterface::class);

        $accessTokenId = Uuid::uuid1();

        $serverRequest
            ->shouldReceive('getAttribute')
            ->once()
            ->with('oauth_access_token_id')
            ->andReturn($accessTokenId);

        $accessTokensMock
            ->shouldReceive('revokeAccessToken')
            ->once()
            ->with($accessTokenId);

        /**
         * @var ServerRequestInterface $serverRequest
         * @var ResponseInterface $response
         */
        $this->controller->revokeAccessToken($serverRequest, $response);
    }

    public function testRevokeAccessTokenReturnsGeneratedHttpResponseWhenExceptionWasThrown()
    {
        /**
         * @var Mock $accessTokensMock
         * @var Mock $serverRequest
         */
        $accessTokensMock = $this->accessTokens;
        $serverRequest = Mockery::mock(ServerRequestInterface::class);
        $response = app(ResponseInterface::class);

        $accessTokenId = Uuid::uuid1();

        $serverRequest
            ->shouldReceive('getAttribute')
            ->once()
            ->with('oauth_access_token_id')
            ->andReturn($accessTokenId);

        $accessTokensMock
            ->shouldReceive('revokeAccessToken')
            ->once()
            ->with($accessTokenId)
            ->andThrow(OAuthServerException::class);

        /**
         * @var ServerRequestInterface $serverRequest
         * @var ResponseInterface $response
         */
        $result = $this->controller->revokeAccessToken($serverRequest, $response);
        $this->assertInstanceOf(ResponseInterface::class, $result);

        $this->assertEquals(400, $result->getStatusCode());
    }

}
