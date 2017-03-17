<?php


namespace Mvdstam\Oauth2ServerLaravel\Entities;


use League\OAuth2\Server\Entities\UserEntityInterface;

/**
 * Class User
 * @package Mvdstam\Oauth2ServerLaravel\Entities
 * @property-read string $id
 */
class User extends AbstractEntity implements UserEntityInterface
{

    public function getIdentifier()
    {
        return $this->id;
    }

}
