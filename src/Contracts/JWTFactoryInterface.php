<?php


namespace Mvdstam\Oauth2ServerLaravel\Contracts;


use Lcobucci\JWT\Token;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;

interface JWTFactoryInterface
{

    /**
     * @param AccessTokenEntityInterface $accessToken
     * @param CryptKey $privateKey
     * @return Token
     */
    public function getForAccessToken(AccessTokenEntityInterface $accessToken, CryptKey $privateKey);

}
