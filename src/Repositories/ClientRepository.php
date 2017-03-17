<?php


namespace Mvdstam\Oauth2ServerLaravel\Repositories;


use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

class ClientRepository extends AbstractRepository implements ClientRepositoryInterface
{

    public function model()
    {
        return ClientEntityInterface::class;
    }

    public function getClientEntity($clientIdentifier, $grantType, $clientSecret = null, $mustValidateSecret = true)
    {
        $client = $this->findWhere([
            ['id', '=', $clientIdentifier]
        ] + ($mustValidateSecret ? [['secret', '=', $clientSecret]] : []));

        return $client ? $client->first() : null;
    }

}
