<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Mezzio\Application;
use Mezzio\Authentication;
use Mezzio\Helper\BodyParams\BodyParamsMiddleware;
use Mezzio\MiddlewareFactory;
use Mezzio\Authentication\OAuth2;
use Mezzio\Session\SessionMiddleware;

return function (Application $app, MiddlewareFactory $factory, ContainerInterface $container): void {

    // OAuth2 token route
    $app->post('/oauth2/token', Authentication\OAuth2\TokenEndpointHandler::class);

    // OAuth2 authorize
    $app->route('/oauth2/authorize', [
        SessionMiddleware::class,

        OAuth2\AuthorizationMiddleware::class,

        // The following middleware is provided by your application (see below):
        App\Middleware\OAuthAuthorizationMiddleware::class,

        OAuth2\AuthorizationHandler::class
    ], ['GET', 'POST']);


    // OAuth2 login
    $app->get('/oauth2/login', [
        SessionMiddleware::class,
        App\Handler\LoginHandler::class
    ], 'login');


    // API
    $app->get('/api/users[/{id}]', [
        Authentication\AuthenticationMiddleware::class,
        BodyParamsMiddleware::class,
        App\User\UserHandler::class
    ], 'api.users');

    $app->post('/api/users', [
        Authentication\AuthenticationMiddleware::class,
        BodyParamsMiddleware::class,
        App\User\CreateUserHandler::class
    ]);
    $app->route('/api/users/{id}', [
        Authentication\AuthenticationMiddleware::class,
        BodyParamsMiddleware::class,
        App\User\ModifyUserHandler::class
    ], ['PATCH', 'DELETE'], 'api.user');

    // API docs
    $app->get('/api/doc/invalid-parameter', App\Doc\InvalidParameterHandler::class);
    $app->get('/api/doc/method-not-allowed-error', App\Doc\MethodNotAllowedHandler::class);
    $app->get('/api/doc/resource-not-found', App\Doc\ResourceNotFoundHandler::class);
    $app->get('/api/doc/parameter-out-of-range', App\Doc\OutOfBoundsHandler::class);
    $app->get('/api/doc/runtime-error', App\Doc\RuntimeErrorHandler::class);
};
