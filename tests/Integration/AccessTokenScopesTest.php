<?php


use Illuminate\Support\Facades\Route;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use Mvdstam\Oauth2ServerLaravel\Entities\Client;
use Mvdstam\Oauth2ServerLaravel\Entities\Scope;
use Mvdstam\Oauth2ServerLaravel\Tests\AbstractGrantTypeTest;

class AccessTokenScopesTest extends AbstractGrantTypeTest
{

    /**
     * @var string[]
     */
    protected $grantTypes = [
        'client_credentials'
    ];

    /**
     * @var string
     */
    protected $responseBodyText = 'You have successfully requested this resource.';

    /**
     * @var string[]
     */
    protected $scopes = [
        'scope-1',
        'scope-2',
        'scope-3'
    ];

    protected function setUp()
    {
        parent::setUp();

        foreach($this->scopes as $scope) {
            Scope::forceCreate(['id' => $scope]);
        }

        // Add scopes to client
        Client::find($this->clientId)->scopes()->saveMany(Scope::all());

        // Create a route with the oauth2 resource route middleware attached
        Route::get('my-resource', function() {
            return response($this->responseBodyText);
        })->middleware(['oauth2-resource:scope-1+scope-2']);
    }

    public function testAccessTokenCanBeRequestedWithScopes()
    {
        $this->post('oauth2/access_token', [
            'grant_type' => 'client_credentials',
            'scope' => 'scope-1 scope-2'
        ], $this->getClientCredentialsHeader())
            ->seeStatusCode(200)
            ->seeJsonContains(['token_type' => 'Bearer']);

        $this->verifyToken(json_decode($this->response->getContent())->access_token);
    }

    public function testResourceIsAccessibleWithAccessTokenHavingCorrectScopes()
    {
        /*
         * Request a access token using the known
         * client credentials
         */
        $this->post('oauth2/access_token', [
            'grant_type' => 'client_credentials',
            'scope' => 'scope-1 scope-2 scope-3'
        ], $this->getClientCredentialsHeader())
            ->seeStatusCode(200)
            ->seeJsonContains(['token_type' => 'Bearer']);

        /*
         * Verify if the access token was signed correctly using
         * a known public key
         */
        $accessToken = json_decode($this->response->getContent())->access_token;
        $this->verifyToken($accessToken);

        $this
            ->get('my-resource', ['Authorization' => "Bearer {$accessToken}"])
            ->seeStatusCode(200)
            ->see($this->responseBodyText);
    }

    public function testResourceIsInaccessibleWithAccessTokenMissingCorrectScopes()
    {
        /*
         * Request a access token using the known
         * client credentials
         */
        $this->post('oauth2/access_token', [
            'grant_type' => 'client_credentials',
            'scope' => 'scope-1'
        ], $this->getClientCredentialsHeader())
            ->seeStatusCode(200)
            ->seeJsonContains(['token_type' => 'Bearer']);

        /*
         * Verify if the access token was signed correctly using
         * a known public key
         */
        $accessToken = json_decode($this->response->getContent())->access_token;
        $this->verifyToken($accessToken);

        $this
            ->get('my-resource', ['Authorization' => "Bearer {$accessToken}"])
            ->seeStatusCode(400)
            ->seeJsonContains(['error' => 'invalid_scope']);
    }

    public function testAccessTokenCannotBeObtainedForScopesNotAssignedToClient()
    {
        $scopeId = 'scope-4';
        Scope::forceCreate(['id' => $scopeId]);

        /*
         * Request a access token using the known
         * client credentials
         */
        $this->post('oauth2/access_token', [
            'grant_type' => 'client_credentials',
            'scope' => $scopeId
        ], $this->getClientCredentialsHeader())
            ->seeStatusCode(400)
            ->seeJsonContains(['error' => 'invalid_scope']);
    }
}
