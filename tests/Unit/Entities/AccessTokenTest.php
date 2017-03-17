<?php


namespace Mvdstam\Oauth2ServerLaravel\Entities;


use League\OAuth2\Server\CryptKey;
use Mockery;
use Mockery\Mock;
use Mvdstam\Oauth2ServerLaravel\Contracts\JWTFactoryInterface;
use Mvdstam\Oauth2ServerLaravel\Tests\TestCase;

class AccessTokenTest extends TestCase
{

    /**
     * @var AccessToken
     */
    protected $accessToken;

    protected function setUp()
    {
        parent::setUp();

        $this->accessToken = new AccessToken;
    }

    public function testConvertToJWTReturnsTokenString()
    {
        /**
         * @var Mock $jwtFactoryMock
         * @var Mock $cryptKeyMock
         */
        $jwtFactoryMock = Mockery::mock(JWTFactoryInterface::class);
        $cryptKeyMock = Mockery::mock(CryptKey::class);

        $tokenString = 'super-high-tech-jwt-token';

        $jwtFactoryMock
            ->shouldReceive('getForAccessToken')
            ->once()
            ->with($this->accessToken, $cryptKeyMock)
            ->andReturn($tokenString);

        $this->app->instance(JWTFactoryInterface::class, $jwtFactoryMock);

        /** @var CryptKey $cryptKeyMock **/
        $this->assertEquals($tokenString, $this->accessToken->convertToJWT($cryptKeyMock));

    }

}
