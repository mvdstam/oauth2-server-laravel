<?php


namespace Mvdstam\Oauth2ServerLaravel\Entities;


use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Mvdstam\Oauth2ServerLaravel\Tests\TestCase;

class ClientTest extends TestCase
{

    /**
     * @var Client
     */
    protected $client;

    protected $data = [
        'id' => '3571ed5d-08fe-4dc2-bfc3-75decde48824',
        'name' => 'foo',
        'redirect_uri' => 'some-redirect-uri'
    ];

    protected function setUp()
    {
        parent::setUp();

        Client::unguard();
        $this->client = new Client($this->data);
        Client::reguard();
    }

    public function testGetIdentifierReturnsIdentifier()
    {
        $this->assertEquals($this->data['id'], $this->client->getIdentifier());
    }

    public function testGetNameReturnsName()
    {
        $this->assertEquals($this->data['name'], $this->client->getName());
    }

    public function testGetRedirectUriReturnsRedirectUri()
    {
        $this->assertEquals($this->data['redirect_uri'], $this->client->getRedirectUri());
    }

    public function testAccessTokensReturnsHasManyRelation()
    {
        $relation = $this->client->accessTokens();

        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertInstanceOf(AccessToken::class, $relation->getRelated());
    }

    public function testScopesReturnsBelongsToManyRelation()
    {
        $relation = $this->client->scopes();

        $this->assertInstanceOf(BelongsToMany::class, $relation);
        $this->assertInstanceOf(Scope::class, $relation->getRelated());
    }

}
