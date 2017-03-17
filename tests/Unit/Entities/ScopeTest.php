<?php


namespace Mvdstam\Oauth2ServerLaravel\Entities;


use Mvdstam\Oauth2ServerLaravel\Tests\TestCase;

class ScopeTest extends TestCase
{

    /**
     * @var Scope
     */
    protected $scope;

    /**
     * @var array
     */
    protected $data = [
        'id' => 'foo-faz'
    ];

    protected function setUp()
    {
        parent::setUp();

        $this->scope = new Scope;
        $this->scope->forceFill($this->data);
    }

    public function testGetIdentifierReturnsIdentifier()
    {
        $this->assertEquals($this->data['id'], $this->scope->getIdentifier());
    }

}
