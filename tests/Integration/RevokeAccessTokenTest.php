<?php


namespace Integration;


use Illuminate\Support\Facades\Route;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use Mvdstam\Oauth2ServerLaravel\Tests\AbstractGrantTypeTest;

class RevokeAccessTokenTest extends AbstractGrantTypeTest
{

    protected $grantTypes = [
        'client_credentials'
    ];

    protected $responseBodyText = 'You have successfully requested this resource.';

    protected function setUp()
    {
        parent::setUp();

        // Create a route with the oauth2 resource route middleware attached
        Route::get('my-resource', function() {
            return response($this->responseBodyText);
        })->middleware(['oauth2-resource']);
    }

    public function testAccessTokenCanBeRevokedSuccessFully()
    {
        /*
         * Request a access token using the known
         * client credentials
         */
        $this->post('oauth2/access_token', [
            'grant_type' => 'client_credentials'
        ], $this->getClientCredentialsHeader())
            ->seeStatusCode(200)
            ->seeJsonContains(['token_type' => 'Bearer']);

        /*
         * Verify if the access token was signed correctly using
         * a known public key
         */
        $accessToken = json_decode($this->response->getContent())->access_token;
        $this->verifyToken($accessToken);

        /*
         * Fetch a resource
         */
        $this
            ->get('my-resource', ['Authorization' => "Bearer {$accessToken}"])
            ->seeStatusCode(200)
            ->see($this->responseBodyText);

        /*
         * Revoke the access token
         */
        $this
            ->delete('oauth2/access_token', [], ['Authorization' => "Bearer {$accessToken}"])
            ->seeStatusCode(200);

        /*
         * Try and fetch a resource with a revoked token
         */
        $this
            ->get('my-resource', ['Authorization' => "Bearer {$accessToken}"])
            ->seeStatusCode(401);
    }

}
