<?php


namespace Mvdstam\Oauth2ServerLaravel\Entities;


use DateTime;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use Mvdstam\Oauth2ServerLaravel\Repositories\ScopeRepository;

/**
 * Class AbstractToken
 * @package Mvdstam\Oauth2ServerLaravel\Entities
 * @property-read string $id
 * @property-read DateTime $expires_at
 * @property-read string|int|null $user_id
 * @property-read Scope[]|\Illuminate\Database\Eloquent\Collection $scopes
 * @property-read User|null $user
 * @property-read Client $client
 */
abstract class AbstractToken extends AbstractEntity
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

    public function setUserIdentifier($identifier)
    {
        $this->forceFill(['user_id' => $identifier]);
    }

    public function getUserIdentifier()
    {
        return $this->user_id;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function setClient(ClientEntityInterface $client)
    {
        $this->client()->associate($client->getIdentifier());
    }

    public function addScope(ScopeEntityInterface $scope)
    {
        /** @var ScopeRepository $scopes */
        $scopes = app(ScopeRepository::class);

        if (!in_array($scope->getIdentifier(), $this->scopes->modelKeys())) {
            $this->scopes->add($scopes->findOrFail($scope->getIdentifier()));
        }
    }

    public function getScopes()
    {
        return $this->scopes->all();
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function scopes()
    {
        return $this->belongsToMany(Scope::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
