<?php


namespace Mvdstam\Oauth2ServerLaravel\Repositories;


use Illuminate\Support\Facades\Hash;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;

class UserRepository extends AbstractRepository implements UserRepositoryInterface
{
    public function model()
    {
        return UserEntityInterface::class;
    }

    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $clientEntity
    )
    {
        $user = $this->findWhere([['username', '=', $username]]);

        if (!$user || !($user = $user->first())) {
            return null;
        }

        if (!Hash::check($password, $user->password)) {
            return null;
        }

        return $user;
    }

}
