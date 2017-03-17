<?php

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use Mvdstam\Oauth2ServerLaravel\Entities\User;
use Mvdstam\Oauth2ServerLaravel\Tests\AbstractGrantTypeTest;

class AuthorizationCodeGrantTypeTest extends AbstractGrantTypeTest
{

    /**
     * @var string[]
     */
    protected $grantTypes = [
        'authorization_code'
    ];

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

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
        $state = Str::random(40);

        /*
         * 1) Start authorization process
         */
        $this->get('oauth2/authorize?' . http_build_query([
                'response_type' => 'code',
                'client_id' => (string) $this->clientId,
                'redirect_uri' => $this->redirectUri,
                'state' => $state
            ]))
            ->seeStatusCode(200);

        // Gather cookies since this is a stateful process
        $cookieHeaders = [];
        foreach($this->response->headers->getCookies() as $cookie) {
            $cookieHeaders[] = (string) $cookie;
        }

        /*
         * 2) Complete authorization process by "logging in" and
         *    "authorizing" the resource server.
         */
        $this
            ->post('oauth2/authorize', [
                'login' => [
                    'username' => $this->username,
                    'password' => $this->password
                ]
            ], $cookieHeaders)
            ->seeStatusCode(302);

        $url = parse_url($this->response->headers->get('location'));
        parse_str($url['query'], $query);

        // Verify state integrity
        $this->assertArrayHasKey('state', $query);
        $this->assertEquals($state, $query['state']);

        $this->assertArrayHasKey('code', $query);

        /*
         * 3) Exchange auth code for an access token
         */
        $this->post('oauth2/access_token', [
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->redirectUri,
            'code' => $query['code']
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
