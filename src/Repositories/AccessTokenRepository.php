<?php


namespace Mvdstam\Oauth2ServerLaravel\Repositories;


use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use Mvdstam\Oauth2ServerLaravel\Entities\AccessToken;


class AccessTokenRepository extends AbstractRepository implements AccessTokenRepositoryInterface
{
    public function model()
    {
        return AccessTokenEntityInterface::class;
    }

    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null)
    {
        /** @var AccessTokenEntityInterface $accessToken */
        $accessToken = $this->makeModel(false);

        $accessToken->setClient($clientEntity);

        array_map([$accessToken, 'addScope'], $scopes);

        if (!is_null($userIdentifier)) {
            $accessToken->setUserIdentifier($userIdentifier);
        }

        return $accessToken;
    }

    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity)
    {
        /** @var AccessToken $accessToken */
        $accessToken = $this->makeModel(false);

        $accessToken->forceFill([
            'id' => $accessTokenEntity->getIdentifier(),
            'expires_at' => $accessTokenEntity->getExpiryDateTime(),
            'user_id' => $accessTokenEntity->getUserIdentifier(),
            'client_id' => $accessTokenEntity->getClient()->getIdentifier()
        ]);

        array_map([$accessToken, 'addScope'], $accessTokenEntity->getScopes());

        $accessToken->save();

        return $accessToken;
    }

    public function revokeAccessToken($tokenId)
    {
        $this->delete($tokenId);
    }

    public function isAccessTokenRevoked($tokenId)
    {
        return empty($this->find($tokenId));
    }

}
