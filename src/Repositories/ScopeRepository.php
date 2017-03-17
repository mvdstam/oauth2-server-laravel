<?php


namespace Mvdstam\Oauth2ServerLaravel\Repositories;


use Illuminate\Database\Eloquent\Collection;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use Mvdstam\Oauth2ServerLaravel\Entities\Client;

class ScopeRepository extends AbstractRepository implements ScopeRepositoryInterface
{

    public function model()
    {
        return ScopeEntityInterface::class;
    }

    public function getScopeEntityByIdentifier($identifier)
    {
        return $this->find($identifier);
    }

    /**
     * @inheritdoc
     */
    public function finalizeScopes(
        array $scopes,
        $grantType,
        ClientEntityInterface $clientEntity,
        $userIdentifier = null
    )
    {
        /**
         * @var Client $client
         * @var Collection $clientScopes
         */
        $client = Client::find($clientEntity->getIdentifier());
        $clientScopes = $client->scopes;

        $requestedScopeIds = [];
        foreach($scopes as $scope) {
            $requestedScopeIds[] = $scope->getIdentifier();
        }

        if (array_diff($requestedScopeIds, $clientScopes->modelKeys())) {
            throw OAuthServerException::invalidScope(implode(' ', $requestedScopeIds));
        }

        return $scopes;
    }

}
