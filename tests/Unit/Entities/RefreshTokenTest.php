<?php


namespace Mvdstam\Oauth2ServerLaravel\Entities;


use DateTime;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use Mockery;
use Mockery\Mock;
use Mvdstam\Oauth2ServerLaravel\Repositories\AccessTokenRepository;
use Mvdstam\Oauth2ServerLaravel\Tests\TestCase;

class RefreshTokenTest extends TestCase
{

    /**
     * @var RefreshToken
     */
    protected $refreshToken;

    /**
     * @var AccessToken
     */
    protected $accessToken;

    /**
     * @var array
     */
    protected $data = [
        'id' => 'faz-bar',
        'expires_at' => '2017-01-01 00:00:00'
    ];

    protected function setUp()
    {
        parent::setUp();

        $this->refreshToken = new RefreshToken;

        /** @var Mock $accessTokenMock */
        $accessTokenMock = $this->accessToken = Mockery::mock(AccessToken::class);

        $accessTokenMock
            ->shouldReceive('getIdentifier')
            ->andReturn('access-token-id');

        $this->refreshToken->forceFill($this->data + [
            'access_token' => $this->accessToken
        ]);
    }

    public function testGetIdentifierReturnsIdentifier()
    {
        $this->assertEquals($this->data['id'], $this->refreshToken->getIdentifier());
    }

    public function testSetIdentifierSetsNewIdentifier()
    {
        $newId = 'foo-baz';

        $this->assertNotEquals($newId, $this->refreshToken->getIdentifier());
        $this->refreshToken->setIdentifier($newId);
        $this->assertEquals($newId, $this->refreshToken->getIdentifier());
    }

    public function testGetExpiryDateTimeReturnsDateTime()
    {
        $expiryDateTime = $this->refreshToken->getExpiryDateTime();

        $this->assertInstanceOf(DateTime::class, $expiryDateTime);
        $this->assertEquals(new DateTime($this->data['expires_at']), $expiryDateTime);
    }

    public function testSetExpiryDateTimeSetsNewExpiryDateTime()
    {
        $newExpiryDateTime = new DateTime('2018-01-01 00:00:00');

        $this->assertNotEquals(new DateTime($this->data['expires_at']), $newExpiryDateTime);
        $this->refreshToken->setExpiryDateTime($newExpiryDateTime);
        $this->assertEquals($newExpiryDateTime, $this->refreshToken->getExpiryDateTime());
    }

    public function testGetAccessTokenReturnsAccessToken()
    {
        $accessToken = $this->refreshToken->getAccessToken();

        $this->assertInstanceOf(AccessTokenEntityInterface::class, $accessToken);
        $this->assertSame($this->accessToken, $accessToken);
    }

    public function testSetAccessTokenSetsNewAccessToken()
    {
        /**
         * @var Mock $accessTokenRepositoryMock
         * @var Mock $newAccessToken
         * @var Mock $newAccessTokenModelMock
         */
        $accessTokenRepositoryMock = Mockery::mock(AccessTokenRepository::class);
        $newAccessToken = Mockery::mock(AccessTokenEntityInterface::class);
        $newAccessTokenModelMock = Mockery::mock(AccessToken::class);

        $newAccessTokenId = 'faz-id';

        $accessTokenRepositoryMock
            ->shouldReceive('findOrFail')
            ->with($newAccessTokenId)
            ->andReturn($newAccessTokenModelMock);

        $newAccessToken
            ->shouldReceive('getIdentifier')
            ->andReturn($newAccessTokenId);

        $newAccessTokenModelMock
            ->shouldReceive('getIdentifier')
            ->andReturn($newAccessTokenId);

        $this->app->instance(AccessTokenRepository::class, $accessTokenRepositoryMock);

        $this->assertNotSame($newAccessTokenId, $this->refreshToken->getAccessToken()->getIdentifier());

        /** @var AccessTokenEntityInterface $newAccessToken */
        $this->refreshToken->setAccessToken($newAccessToken);

        $this->assertEquals($newAccessTokenId, $this->refreshToken->getAccessToken()->getIdentifier());
    }


    public function testAccessTokenReturnsBelongsToRelation()
    {
        $relation = $this->refreshToken->accessToken();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertInstanceOf(AccessToken::class, $relation->getRelated());
    }

}
