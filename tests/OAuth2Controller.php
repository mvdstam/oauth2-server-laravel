<?php


namespace Mvdstam\Oauth2ServerLaravel\Tests;


use Illuminate\Http\Request;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class OAuth2Controller extends \Mvdstam\Oauth2ServerLaravel\Http\Controllers\OAuth2Controller
{

    public function authorize(ServerRequestInterface $request, ResponseInterface $response)
    {
        try {
            session([
                'oauth2.authorize' => serialize($this->authorizationServer->validateAuthorizationRequest($request))
            ]);
        } catch (OAuthServerException $e) {
            return $e->generateHttpResponse($response);
        }
    }

    public function completeAuthorization(UserRepositoryInterface $users, Request $request, ResponseInterface $response)
    {
        try {
            /** @var AuthorizationRequest $authorizeRequest */
            $authorizeRequest = unserialize(session()->pull('oauth2.authorize'));

            $authorizeRequest->setUser($users->getUserEntityByUserCredentials(
                $request->input('login.username'),
                $request->input('login.password'),
                $authorizeRequest->getGrantTypeId(),
                $authorizeRequest->getClient()
            ));

            $authorizeRequest->setAuthorizationApproved(true);

            return $this->authorizationServer->completeAuthorizationRequest(
                $authorizeRequest,
                $response
            );
        } catch (OAuthServerException $e) {
            return $e->generateHttpResponse($response);
        }
    }

}
