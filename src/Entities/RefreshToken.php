<?php


namespace Mvdstam\Oauth2ServerLaravel\Entities;


use DateTime;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use Mvdstam\Oauth2ServerLaravel\Repositories\AccessTokenRepository;

/**
 * Class RefreshToken
 * @package Mvdstam\Oauth2ServerLaravel\Entities
 * @property-read string $id
 * @property-read DateTime $expires_at
 * @property-read AccessToken $access_token
 */
class RefreshToken extends AbstractEntity implements RefreshTokenEntityInterface
{

    protected $dates = [
        'expires_at'
    ];

    public function getIdentifier()
    {
        return $this->id;
    }

    public function setIdentifier($identifier)
    {
        $this->forceFill(['id' => $identifier]);
    }

    public function getExpiryDateTime()
    {
        return $this->expires_at;
    }

    public function setExpiryDateTime(DateTime $dateTime)
    {
        $this->forceFill(['expires_at' => $dateTime]);
    }

    public function getAccessToken()
    {
        return $this->access_token;
    }

    public function setAccessToken(AccessTokenEntityInterface $accessToken)
    {
        $accessTokens = app(AccessTokenRepository::class);

        $this->access_token = $accessTokens->findOrFail($accessToken->getIdentifier());
    }

    public function accessToken()
    {
        return $this->belongsTo(AccessToken::class);
    }

}
