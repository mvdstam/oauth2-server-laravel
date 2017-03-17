<?php


namespace Mvdstam\Oauth2ServerLaravel\Entities;


use DateTime;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use Mockery;
use Mockery\Mock;
use Mvdstam\Oauth2ServerLaravel\Repositories\ScopeRepository;
use Mvdstam\Oauth2ServerLaravel\Tests\TestCase;
use Ramsey\Uuid\Uuid;

class AbstractTokenTest extends TestCase
{

    /**
     * @var AbstractToken
     */
    protected $abstractToken;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Scope[]|Collection
     */
    protected $scopes = [];

    protected $data = [
        'id' => '3571ed5d-08fe-4dc2-bfc3-75decde48824',
        'expires_at' => '2016-01-01 00:00:00',
        'user_id' => 100,
        'client_id' => 200
    ];

    protected function setUp()
    {
        parent::setUp();

        $this->data['client'] = $this->client = Mockery::mock(Client::class);
        $this->data['scopes'] = $this->scopes = new Collection([
            $this->getScopeMock('scope-1'),
            $this->getScopeMock('scope-2'),
            $this->getScopeMock('scope-3')
        ]);

        AbstractToken::unguard();
        $this->abstractToken = Mockery::mock(AbstractToken::class)->makePartial();
        $this->abstractToken->__construct($this->data);
        AbstractToken::reguard();

        /** @var Mock $abstractTokenMock */
        $abstractTokenMock = $this->abstractToken;

        $abstractTokenMock
            ->shouldReceive('getKeyName')
            ->andReturn('id');
    }

    public function testGetIdentifierReturnsKeyNameAttributeValue()
    {
        $this->assertEquals(
            $this->data['id'],
            $this->abstractToken->getIdentifier()
        );
    }

    public function testSetIdentifierStoresIdentifier()
    {
        $newId = '3571ed5d-08fe-4dc2-bfc3-75decde48822';

        $this->assertNotEquals($newId, $this->data['id']);
        $this->abstractToken->setIdentifier($newId);
        $this->assertEquals($newId, $this->abstractToken->getIdentifier());
    }

    public function testGetExpiryDateTimeReturnsDateTimeInstance()
    {
        $this->assertEquals(new DateTime($this->data['expires_at']), $this->abstractToken->getExpiryDateTime());
    }

    public function testSetExpiryDateTimeStoresDateTime()
    {
        $newDateTime = new DateTime('2017-01-01 00:00:00');

        $this->assertNotEquals($newDateTime, new DateTime($this->data['expires_at']));
        $this->abstractToken->setExpiryDateTime($newDateTime);
        $this->assertEquals($newDateTime, $this->abstractToken->getExpiryDateTime());
    }

    public function testSetUserIdentifierStoresUserIdentifier()
    {
        $newUserId = 101;

        $this->assertNotEquals($newUserId, $this->abstractToken->getUserIdentifier());
        $this->abstractToken->setUserIdentifier($newUserId);
        $this->assertEquals($newUserId, $this->abstractToken->getUserIdentifier());
    }

    public function testGetUserIdentifierReturnsUserIdentifier()
    {
        $this->assertEquals(
            $this->data['user_id'],
            $this->abstractToken->getUserIdentifier()
        );
    }

    public function testClientReturnsBelongsToRelation()
    {
        $relation = $this->abstractToken->client();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertInstanceOf(Client::class, $relation->getRelated());
    }

    public function testScopesReturnsBelongsToManyRelation()
    {
        $relation = $this->abstractToken->scopes();

        $this->assertInstanceOf(BelongsToMany::class, $relation);
        $this->assertInstanceOf(Scope::class, $relation->getRelated());
    }

    public function testUserReturnsBelongsToRelation()
    {
        $relation = $this->abstractToken->user();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertInstanceOf(User::class, $relation->getRelated());
    }

    public function testGetClientReturnsClient()
    {
        $this->assertSame($this->client, $this->abstractToken->getClient());
    }

    public function testSetClientAssociatesWithRelation()
    {
        /**
         * @var Mock $clientMock
         */
        $clientMock = Mockery::mock(ClientEntityInterface::class);

        $clientId = (string) Uuid::uuid1();

        $clientMock
            ->shouldReceive('getIdentifier')
            ->once()
            ->andReturn($clientId);

        /** @noinspection PhpParamsInspection */
        $this->abstractToken->setClient($clientMock);

        $clientMock
            ->shouldHaveReceived('getIdentifier')
            ->once();
    }

    public function testAddScopeAddsScopeModelInstance()
    {
        /**
         * @var Mock $scopeMock
         * @var Mock $scopeRepositoryMock
         * @var Mock $scopeModelMock
         */
        $scopeMock = Mockery::mock(ScopeEntityInterface::class);
        $scopeRepositoryMock = Mockery::mock(ScopeRepository::class);
        $scopeId = 'foo-scope';
        $scopeModelMock = $this->getScopeMock($scopeId);

        $scopeMock
            ->shouldReceive('getIdentifier')
            ->andReturn($scopeId);

        $scopeRepositoryMock
            ->shouldReceive('findOrFail')
            ->with($scopeId)
            ->andReturn($scopeModelMock);

        $this->app->instance(ScopeRepository::class, $scopeRepositoryMock);

        $this->assertNotContains($scopeId, $this->scopes->modelKeys());

        /** @var Scope $scopeMock */
        $this->abstractToken->addScope($scopeMock);

        $this->assertContains($scopeId, $this->scopes->modelKeys());
    }

    public function testGetScopesReturnsAllRelatedScopes()
    {
        $this->assertEquals($this->scopes->all(), $this->abstractToken->getScopes());
    }

    /**
     * @param $scopeId
     * @return Mock
     */
    protected function getScopeMock($scopeId)
    {
        /** @var Mock $scopeMock */
        $scopeMock = Mockery::mock(Scope::class);

        $scopeMock
            ->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn($scopeId);

        $scopeMock
            ->shouldReceive('getKey')
            ->andReturn($scopeId);

        return $scopeMock;
    }

}
