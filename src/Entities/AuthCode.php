<?php


namespace Mvdstam\Oauth2ServerLaravel\Entities;


use League\OAuth2\Server\Entities\AuthCodeEntityInterface;

/**
 * @inheritdoc
 * Class AuthCode
 * @property-read string $redirect_uri
 */
class AuthCode extends AbstractToken implements AuthCodeEntityInterface
{

    public function getRedirectUri()
    {
        return $this->redirect_uri;
    }

    public function setRedirectUri($uri)
    {
        $this->forceFill(['redirect_uri' => $uri]);
    }

}
