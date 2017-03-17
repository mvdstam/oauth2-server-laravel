<?php


namespace Integration;


use Mvdstam\Oauth2ServerLaravel\Tests\AbstractGrantTypeTest;

class NoGrantTest extends AbstractGrantTypeTest
{

    public function testAccessTokenCannotBeRequestedWithoutGrantTypesEnabled()
    {
        /*
         * Request a access token using the known
         * client credentials
         */
        $this->post('oauth2/access_token', [
            'grant_type' => 'client_credentials'
        ], $this->getClientCredentialsHeader())
            ->seeStatusCode(400)
            ->seeJsonContains(['error' => 'unsupported_grant_type']);
    }
}
