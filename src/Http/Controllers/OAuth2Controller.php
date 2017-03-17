<?php


namespace Mvdstam\Oauth2ServerLaravel\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

class OAuth2Controller extends Controller
{

    /**
     * @var AuthorizationServer
     */
    protected $authorizationServer;

    /**
     * @var AccessTokenRepositoryInterface
     */
    protected $accessTokens;

    /**
     * OAuth2Controller constructor.
     * @param AuthorizationServer $authorizationServer
     * @param AccessTokenRepositoryInterface $accessTokens
     */
    public function __construct(AuthorizationServer $authorizationServer, AccessTokenRepositoryInterface $accessTokens)
    {
        $this->authorizationServer = $authorizationServer;
        $this->accessTokens = $accessTokens;
    }

    public function accessToken(ServerRequestInterface $request, ResponseInterface $response)
    {
        try {
            return $this->authorizationServer->respondToAccessTokenRequest(
                $request,
                $response
            );
        } catch (OAuthServerException $e) {
            return $e->generateHttpResponse($response);
        }
    }

    public function revokeAccessToken(ServerRequestInterface $request, ResponseInterface $response)
    {
        try {
            $this->accessTokens->revokeAccessToken($request->getAttribute('oauth_access_token_id'));
        } catch (OAuthServerException $e) {
            return $e->generateHttpResponse($response);
        }
    }

    /**
     * Validates the authorization request and stores it in the session during authentication
     * of the user.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function authorize(ServerRequestInterface $request, ResponseInterface $response)
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * Finalizes the authorization process and returns an access code.
     *
     * @param UserRepositoryInterface $users
     * @param Request $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function completeAuthorization(UserRepositoryInterface $users, Request $request, ResponseInterface $response)
    {
        throw new RuntimeException('Not implemented');
    }
}
