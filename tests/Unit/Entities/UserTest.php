<?php


namespace Mvdstam\Oauth2ServerLaravel\Entities;


use Mvdstam\Oauth2ServerLaravel\Tests\TestCase;

class UserTest extends TestCase
{

    /**
     * @var User
     */
    protected $user;

    /**
     * @var array
     */
    protected $data = [
        'id' => 'foo-bar'
    ];

    protected function setUp()
    {
        parent::setUp();

        $this->user = new User;
        $this->user->forceFill($this->data);
    }

    public function testGetIdentifierReturnsIdentifier()
    {
        $this->assertEquals($this->data['id'], $this->user->getIdentifier());
    }

}
