<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Mezzio\Application;
use Mezzio\Authentication;
use Mezzio\Helper\BodyParams\BodyParamsMiddleware;
use Mezzio\MiddlewareFactory;

return function (Application $app, MiddlewareFactory $factory, ContainerInterface $container) : void {
    
    // OAuth2 token route
    $app->post('/oauth', Authentication\OAuth2\TokenEndpointHandler::class, 'oauth-token');

    // OAuth2 authorize (testing, not working)
    $app->get('/authorize', [
        Authentication\OAuth2\AuthorizationMiddleware::class,
        Authentication\OAuth2\AuthorizationHandler::class]);

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
