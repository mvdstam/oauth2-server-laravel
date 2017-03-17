<?php


use Illuminate\Support\Facades\Hash;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use Mvdstam\Oauth2ServerLaravel\Entities\User;
use Mvdstam\Oauth2ServerLaravel\Tests\AbstractGrantTypeTest;

class RefreshTokenGrantTypeTest extends AbstractGrantTypeTest
{

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string[]
     */
    protected $grantTypes = [
        'password',
        'refresh_token'
    ];

    protected function setUp()
    {
        parent::setUp();

        factory(User::class)->create([
            'username' => $this->username = 'phpunit',
            'password' => Hash::make($this->password = 'testpassword')
        ]);
    }

    public function testAccessTokenCanBeRequestedAndVerified()
    {
        /*
         * Request a access token using the known
         * client credentials
         */
        $this->post('oauth2/access_token', [
            'username' => $this->username,
            'password' => $this->password,
            'grant_type' => 'password'
        ], $this->getClientCredentialsHeader())
            ->seeStatusCode(200)
            ->seeJsonContains(['token_type' => 'Bearer']);

        /*
         * Verify if the access token was signed correctly using
         * a known public key
         */
        $result = json_decode($this->response->getContent());
        $this->verifyToken($result->access_token);

        /*
         * Request a new access token using the refresh token
         * we got from the last request
         */
        $this->post('oauth2/access_token', [
            'refresh_token' => $result->refresh_token,
            'grant_type' => 'refresh_token'
        ], $this->getClientCredentialsHeader())
            ->seeStatusCode(200)
            ->seeJsonContains(['token_type' => 'Bearer']);

        $newResult = json_decode($this->response->getContent());
        $this->verifyToken($newResult->access_token);

        // Verify we actually got a new token
        $this->assertNotEquals($result->access_token, $newResult->access_token);
    }

}
