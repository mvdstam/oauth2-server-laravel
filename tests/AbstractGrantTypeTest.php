<?php


namespace Mvdstam\Oauth2ServerLaravel\Tests;


use League\OAuth2\Server\AuthorizationServer;
use Mvdstam\Oauth2ServerLaravel\Entities\Client;
use Ramsey\Uuid\Uuid;

/**
 * Class AbstractGrantTypeTest
 * @package Integration
 *
 * This class acts as a template for tests regarding the various
 * grant types that OAuth2 has to offer.
 */
abstract class AbstractGrantTypeTest extends IntegrationTestCase
{

    /**
     * @var Uuid
     */
    protected $clientId;

    /**
     * @var Uuid
     */
    protected $clientSecret;

    /**
     * @var string
     */
    protected $redirectUri;

    /**
     * @var string[]
     */
    protected $grantTypes = [];

    protected function setUp()
    {
        parent::setUp();

        factory(Client::class)->create([
            'id' => $this->clientId = Uuid::uuid1(),
            'secret' => $this->clientSecret = Uuid::uuid1(),
            'name' => 'PHPUnit test',
            'redirect_uri' => $this->redirectUri = 'http://localhost'
        ]);
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        foreach($this->grantTypes as $grantType) {
            $app['config']->set("oauth2-server.grants.{$grantType}.enabled", true);
        }
    }


    /**
     * Creates a standard HTTP-Basic Auth header based on the current client credentials.
     *
     * @return array
     */
    protected function getClientCredentialsHeader()
    {
        return [
            'Authorization' => 'Basic ' . base64_encode(implode(':', [$this->clientId, $this->clientSecret]))
        ];
    }

}
