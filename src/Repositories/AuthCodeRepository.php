<?php


namespace Mvdstam\Oauth2ServerLaravel\Repositories;


use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use Mvdstam\Oauth2ServerLaravel\Entities\AuthCode;

class AuthCodeRepository extends AbstractRepository implements AuthCodeRepositoryInterface
{

    public function model()
    {
        return AuthCodeEntityInterface::class;
    }

    public function getNewAuthCode()
    {
        return $this->makeModel(false);
    }

    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity)
    {
        /** @var AuthCode $authCode */
        $authCode = $this->makeModel(false);

        $authCode->forceFill([
            'id' => $authCodeEntity->getIdentifier(),
            'expires_at' => $authCodeEntity->getExpiryDateTime(),
            'client_id' => $authCodeEntity->getClient()->getIdentifier(),
            'user_id' => $authCodeEntity->getUserIdentifier(),
            'redirect_uri' => $authCodeEntity->getRedirectUri()
        ]);

        array_map([$authCode, 'addScope'], $authCodeEntity->getScopes());

        $authCode->save();

        return $authCode;
    }

    public function revokeAuthCode($codeId)
    {
        $this->delete($codeId);
    }

    public function isAuthCodeRevoked($codeId)
    {
        return empty($this->find($codeId));
    }

}
