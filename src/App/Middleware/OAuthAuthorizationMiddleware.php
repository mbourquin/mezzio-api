<?php

namespace App\Middleware;

use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use Mezzio\Authentication\UserInterface;

class OAuthAuthorizationMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        // Assume a middleware handled the authentication check and
        // populates the user object, which also implements the
        // OAuth2 UserEntityInterface
        $user = $request->getAttribute(UserInterface::class);

        // Assume the SessionMiddleware handles and populates a session
        // container
        $session = $request->getAttribute('session');

        // This is populated by the previous middleware:
        /** @var AuthorizationRequest $authRequest */
        $authRequest = $request->getAttribute(AuthorizationRequest::class);

        // The user is authenticated:
        if ($user) {
            $authRequest->setUser($user);

            // This assumes all clients are trusted, but you could
            // handle consent here, or within the next middleware
            // as needed.
            $authRequest->setAuthorizationApproved(true);

            return $handler->handle($request);
        }

        // The user is not authenticated, show login form ...

        // Store the auth request state
        // NOTE: Do not attempt to serialize or store the authorization
        // request object. Store the query parameters instead and redirect
        // with these to this endpoint again to replay the request.
        $session->set('oauth2_request_params', $request->getQueryParams());

        return new RedirectResponse('/oauth2/login');
    }
}