<?php


namespace Mvdstam\Oauth2ServerLaravel\Factories;


use Carbon\Carbon;
use DateTime;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use Mockery;
use Mockery\Mock;
use Mvdstam\Oauth2ServerLaravel\Tests\TestCase;

class JWTFactoryTest extends TestCase
{

    /**
     * @var JWTFactory
     */
    protected $jwtFactory;

    /**
     * @var Carbon
     */
    protected $now;

    /**
     * @var string
     */
    protected $publicKey;

    /**
     * @var string
     */
    protected $privateKey;

    protected function setUp()
    {
        parent::setUp();

        list($this->publicKey, $this->privateKey) = $this->getKeyPair();

        $this->jwtFactory = new JWTFactory;

        Carbon::setTestNow($this->now = new Carbon);
    }

    protected function tearDown()
    {
        parent::tearDown();

        Carbon::setTestNow();
    }

    public function testGetFromPrivateKeyReturnsTokenWhichVerifiesCorrectly()
    {
        /**
         * @var Mock $tokenMock
         * @var Mock $clientMock
         */
        $tokenMock = Mockery::mock(AccessTokenEntityInterface::class);
        $clientMock = Mockery::mock(ClientEntityInterface::class);
        $scopeList = ['scope-1', 'scope-2', 'scope-3', 'scope-4'];

        $scopeMocks = [
            Mockery::mock(ScopeEntityInterface::class, ['getIdentifier' => current($scopeList)]),
            Mockery::mock(ScopeEntityInterface::class, ['getIdentifier' => next($scopeList)]),
            Mockery::mock(ScopeEntityInterface::class, ['getIdentifier' => next($scopeList)]),
            Mockery::mock(ScopeEntityInterface::class, ['getIdentifier' => next($scopeList)])
        ];

        $userId = 'super-awesome-user-id';
        $clientId = 'super-awesome-client-id';
        $tokenId = 'super-awesome-token-id';
        $expiryDateTime = new DateTime('now +1 hours');

        $tokenMock
            ->shouldReceive('getIdentifier')
            ->once()
            ->andReturn($tokenId);

        $tokenMock
            ->shouldReceive('getUserIdentifier')
            ->once()
            ->andReturn($userId);

        $tokenMock
            ->shouldReceive('getExpiryDateTime')
            ->once()
            ->andReturn($expiryDateTime);

        $tokenMock
            ->shouldReceive('getScopes')
            ->once()
            ->andReturn($scopeMocks);

        $tokenMock
            ->shouldReceive('getClient')
            ->once()
            ->andReturn($clientMock);

        $clientMock
            ->shouldReceive('getIdentifier')
            ->once()
            ->andReturn($clientId);

        /** @var AccessTokenEntityInterface $tokenMock */
        $token = $this->jwtFactory->getForAccessToken($tokenMock, new CryptKey($this->privateKey));

        /*
         * Verify we actually got a token object
         */
        $this->assertInstanceOf(Token::class, $token);

        /*
         * Verify payload
         */
        $this->assertEquals($this->now->timestamp, $token->getClaim('iat'));
        $this->assertEquals($this->now->timestamp, $token->getClaim('nbf'));
        $this->assertEquals($expiryDateTime->getTimestamp(), $token->getClaim('exp'));
        $this->assertEquals($tokenId, $token->getClaim('jti'));
        $this->assertEquals(url('/'), $token->getClaim('iss'));
        $this->assertEquals($clientId, $token->getClaim('aud'));
        $this->assertEquals($userId, $token->getClaim('sub'));
        $this->assertEquals(implode(',', $scopeList), $token->getClaim('scopes'));

        /*
         * Verify integrity of token
         */
        $this->assertTrue($token->verify(new Sha256, new Key($this->publicKey)));
    }

}
