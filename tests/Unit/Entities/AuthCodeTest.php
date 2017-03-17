<?php


namespace Mvdstam\Oauth2ServerLaravel\Entities;


use Mvdstam\Oauth2ServerLaravel\Tests\TestCase;

class AuthCodeTest extends TestCase
{

    /**
     * @var AuthCode
     */
    protected $authCode;

    /**
     * @var array
     */
    protected $data = [
        'redirect_uri' => 'my-redirect-uri'
    ];

    protected function setUp()
    {
        parent::setUp();

        $this->authCode = new AuthCode;
        $this->authCode->forceFill($this->data);
    }

    public function testGetRedirectUriReturnsRedirectUri()
    {
        $this->assertEquals($this->data['redirect_uri'], $this->authCode->getRedirectUri());
    }

    public function testSetRedirectUriSetsNewRedirectUri()
    {
        $newRedirectUri = 'my-other-redirect-uri';

        $this->assertNotEquals($newRedirectUri, $this->data['redirect_uri']);
        $this->authCode->setRedirectUri($newRedirectUri);
        $this->assertEquals($newRedirectUri, $this->authCode->getRedirectUri());
    }
}
