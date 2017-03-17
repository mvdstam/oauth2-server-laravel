<?php


use Illuminate\Support\Facades\Route;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use Mvdstam\Oauth2ServerLaravel\Tests\AbstractGrantTypeTest;

class ResourceServerTest extends AbstractGrantTypeTest
{

    /**
     * @var string
     */
    protected $responseBodyText = 'You have successfully requested this resource.';

    /**
     * @var string[]
     */
    protected $grantTypes = [
        'client_credentials'
    ];

    protected function setUp()
    {
        parent::setUp();

        // Create a route with the oauth2 resource route middleware attached
        Route::get('my-resource', function() {
            return response($this->responseBodyText);
        })->middleware(['oauth2-resource']);
    }

    /**
     * @inheritdoc
     */
    public function testResourceIsInaccessibleWithoutAccessToken()
    {
        $this
            ->get('my-resource')
            ->seeStatusCode(401)
            ->seeJson(['error' => 'access_denied']);
    }

    /**
     * @inheritdoc
     */
    public function testResourceIsInaccessibleWithInvalidAccessToken()
    {
        $this
            ->get('my-resource', ['Authorization' => "Bearer not-a-valid-token"])
            ->seeStatusCode(401)
            ->seeJson(['error' => 'access_denied']);
    }

    /**
     * @inheritdoc
     */
    public function testResourceIsAccessibleWithAccessToken()
    {
        /*
         * Request a access token using the known
         * client credentials
         */
        $this->post('oauth2/access_token', [
            'grant_type' => 'client_credentials',
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

}
