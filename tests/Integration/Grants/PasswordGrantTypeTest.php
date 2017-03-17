<?php


use Illuminate\Support\Facades\Hash;
use League\OAuth2\Server\Grant\PasswordGrant;
use Mvdstam\Oauth2ServerLaravel\Entities\User;
use Mvdstam\Oauth2ServerLaravel\Tests\AbstractGrantTypeTest;

class PasswordGrantTypeTest extends AbstractGrantTypeTest
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
        'password'
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
        $this->verifyToken(json_decode($this->response->getContent())->access_token);
    }

}
