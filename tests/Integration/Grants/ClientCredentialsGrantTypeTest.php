<?php


use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use Mvdstam\Oauth2ServerLaravel\Tests\AbstractGrantTypeTest;

class ClientCredentialsGrantTypeTest extends AbstractGrantTypeTest
{

    /**
     * @var string[]
     */
    protected $grantTypes = [
        'client_credentials'
    ];

    public function testAccessTokenCanBeRequestedAndVerified()
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
        $this->verifyToken(json_decode($this->response->getContent())->access_token);
    }

}
