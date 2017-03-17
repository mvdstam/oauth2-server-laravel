<?php


namespace Integration\Commands;


use Mvdstam\Oauth2ServerLaravel\Entities\Scope;
use Mvdstam\Oauth2ServerLaravel\Tests\IntegrationTestCase;

class CreateScopeCommandTest extends IntegrationTestCase
{

    public function testCreateScopeWithArgumentCreatesScope()
    {
        $scope = new Scope;
        $scopeId = 'my-new-scope';

        $this->dontSeeInDatabase($scope->getTable(), ['id' => $scopeId]);
        $this->artisan("oauth2-server:create-scope", ['id' => $scopeId]);
        $this->seeInDatabase($scope->getTable(), ['id' => $scopeId]);

        $this->assertEquals(0, $this->code);
    }

    public function testCreateScopeWithDuplicateScopeReturnsError()
    {
        $scope = new Scope;
        $scopeId = 'my-new-scope';

        $this->dontSeeInDatabase($scope->getTable(), ['id' => $scopeId]);
        $this->artisan("oauth2-server:create-scope", ['id' => $scopeId]);
        $this->seeInDatabase($scope->getTable(), ['id' => $scopeId]);
        $this->assertEquals(0, $this->code);

        $this->artisan("oauth2-server:create-scope", ['id' => $scopeId]);
        $this->assertEquals(1, $this->code);
    }

}
