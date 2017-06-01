<?php


namespace Mvdstam\Oauth2ServerLaravel\Http\Middleware;


use Closure;
use Illuminate\Http\Request;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Middleware\ResourceServerMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\HttpFoundation\Response;

class OAuth2Middleware extends ResourceServerMiddleware
{

    /**
     * @param $request
     * @param Closure $next
     * @param string $scopes
     * @return Response
     */
    public function handle(/** @noinspection PhpUnusedParameterInspection */
        $request, Closure $next, $scopes = '')
    {
        return $this->convertToSymfonyResponse(
            $this(app(ServerRequestInterface::class), app(ResponseInterface::class), function(ServerRequestInterface $request, ResponseInterface $response) use ($next, $scopes) {
                $tokenScopes = explode(',', $request->getAttribute('oauth_scopes'));
                $routeScopes = explode('+', $scopes);

                /*
                 * Check if all scopes necessary for this route are present in
                 * the current token.
                 */
                if (array_diff($routeScopes, $tokenScopes)) {
                    return OAuthServerException::invalidScope(implode(' ', $tokenScopes))
                            ->generateHttpResponse($response);
                }

                // Store request in container
                app()->instance(ServerRequestInterface::class, $request);

                /*
                 * Normalize request and continue normal operation
                 */
                return $next(
                    Request::createFromBase((new HttpFoundationFactory)->createRequest($request))
                );
            })
        );
    }

    /**
     * Normalizes response objects to regular Symfony Response classes
     *
     * @param mixed $response
     * @return Response
     */
    protected function convertToSymfonyResponse($response)
    {
        if ( ! $response instanceof Response) {
            $response = (new HttpFoundationFactory)->createResponse($response);
        }

        return $response;
    }

}
