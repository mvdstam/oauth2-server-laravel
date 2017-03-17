<?php


namespace Mvdstam\Oauth2ServerLaravel\Entities;


use League\OAuth2\Server\Entities\ClientEntityInterface;

/**
 * Class Client
 * @package Mvdstam\Oauth2ServerLaravel\Entities
 * @property-read string $id
 * @property-read string $secret
 * @property-read string $name
 * @property-read string|null $redirect_uri
 * @property-read AccessToken[]|\Illuminate\Database\Eloquent\Collection $accessTokens
 * @property-read Scope[]\\Illuminate\Database\Eloquent\Collection $scopes
 */
class Client extends AbstractEntity implements ClientEntityInterface
{

    public function getIdentifier()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getRedirectUri()
    {
        return $this->redirect_uri;
    }

    public function accessTokens()
    {
        return $this->hasMany(AccessToken::class);
    }

    public function scopes()
    {
        return $this->belongsToMany(Scope::class);
    }

}
