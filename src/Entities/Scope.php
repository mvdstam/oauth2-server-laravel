<?php


namespace Mvdstam\Oauth2ServerLaravel\Entities;


use League\OAuth2\Server\Entities\ScopeEntityInterface;

/**
 * Class Scope
 * @package Mvdstam\Oauth2ServerLaravel\Entities
 * @property-read string $id
 */
class Scope extends AbstractEntity implements ScopeEntityInterface
{

    public function getIdentifier()
    {
        return $this->id;
    }

}
