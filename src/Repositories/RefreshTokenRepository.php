<?php


namespace Mvdstam\Oauth2ServerLaravel\Repositories;


use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;

class RefreshTokenRepository extends AbstractRepository implements RefreshTokenRepositoryInterface
{
    public function model()
    {
        return RefreshTokenEntityInterface::class;
    }

    public function getNewRefreshToken()
    {
        return $this->makeModel(false);
    }

    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity)
    {
        return $this->forceCreate([
            'id' => $refreshTokenEntity->getIdentifier(),
            'expires_at' => $refreshTokenEntity->getExpiryDateTime(),
            'access_token_id' => $refreshTokenEntity->getAccessToken()->getIdentifier()
        ]);
    }

    public function revokeRefreshToken($tokenId)
    {
        $this->delete($tokenId);
    }

    public function isRefreshTokenRevoked($tokenId)
    {
        return empty($this->find($tokenId));
    }

}
