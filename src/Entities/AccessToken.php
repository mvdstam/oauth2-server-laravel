<?php


namespace Mvdstam\Oauth2ServerLaravel\Entities;


use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use Mvdstam\Oauth2ServerLaravel\Contracts\JWTFactoryInterface;

/**
 * @inheritdoc
 * Class AccessToken
 */
class AccessToken extends AbstractToken implements AccessTokenEntityInterface
{

    /**
     * @inheritdoc
     */
    public function convertToJWT(CryptKey $privateKey)
    {
        /** @var JWTFactoryInterface $jwtService */
        $jwtFactory = app(JWTFactoryInterface::class);

        return (string) $jwtFactory->getForAccessToken($this, $privateKey);
    }

}
