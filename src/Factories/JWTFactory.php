<?php


namespace Mvdstam\Oauth2ServerLaravel\Factories;


use Carbon\Carbon;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use Mvdstam\Oauth2ServerLaravel\Contracts\JWTFactoryInterface;

class JWTFactory implements JWTFactoryInterface
{

    /**
     * @inheritdoc
     */
    public function getForAccessToken(AccessTokenEntityInterface $accessToken, CryptKey $privateKey)
    {
        $now = Carbon::now();

        return (new Builder)
            ->setIssuer(url('/'))
            ->setIssuedAt($now->timestamp)
            ->setNotBefore($now->timestamp)
            ->setId($accessToken->getIdentifier())
            ->setExpiration($accessToken->getExpiryDateTime()->getTimestamp())
            ->setAudience($accessToken->getClient()->getIdentifier())
            ->setSubject($accessToken->getUserIdentifier())
            ->set('scopes', implode(',', $this->getScopesAsList($accessToken)))
            ->sign(new Sha256, new Key($privateKey->getKeyPath(), $privateKey->getPassPhrase()))
            ->getToken();
    }


    /**
     * @param AccessTokenEntityInterface $accessToken
     * @return string[]
     */
    public function getScopesAsList(AccessTokenEntityInterface $accessToken)
    {
        $scopes = [];
        foreach ($accessToken->getScopes() as $scope) {
            $scopes[] = $scope->getIdentifier();
        }

        return $scopes;
    }

}
