<?php


use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use League\OAuth2\Server\Grant\ImplicitGrant;
use Mvdstam\Oauth2ServerLaravel\Entities\User;
use Mvdstam\Oauth2ServerLaravel\Tests\AbstractGrantTypeTest;

class ImplicitGrantTypeTest extends AbstractGrantTypeTest
{

    /**
     * @var string[]
     */
    protected $grantTypes = [
        'implicit'
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
                'response_type' => 'token',
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
        parse_str($url['fragment'], $fragment);

        // Verify state integrity
        $this->assertArrayHasKey('state', $fragment);
        $this->assertEquals($state, $fragment['state']);

        /*
         * 3) Authorization is complete: the redirect_uri contains
         *    an access_token in its fragment component.
         */
        $this->assertArrayHasKey('access_token', $fragment);

        /*
         * Verify if the access token was signed correctly using
         * a known public key
         */
        $this->verifyToken($fragment['access_token']);
    }

}
