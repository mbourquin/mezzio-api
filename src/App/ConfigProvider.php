<?php

declare(strict_types=1);

namespace App;

use Mezzio\Authentication;
use Mezzio\Hal\Metadata\MetadataMap;
use Mezzio\Hal\Metadata\RouteBasedCollectionMetadata;
use Mezzio\Hal\Metadata\RouteBasedResourceMetadata;
use Laminas\Hydrator\ObjectPropertyHydrator;
use League\OAuth2\Server\Grant;

/**
 * The configuration provider for the App module
 *
 * @see https://docs.zendframework.com/zend-component-installer/
 */
class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     *
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'authentication' => $this->getAuthenticationConfig(),
            'templates'    => $this->getTemplates(),
            MetadataMap::class => $this->getHalConfig(),
        ];
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies(): array
    {
        return [
            'factories'  => [
                User\CreateUserHandler::class => User\CreateUserHandlerFactory::class,
                User\ModifyUserHandler::class => User\ModifyUserHandlerFactory::class,
                User\UserHandler::class => User\UserHandlerFactory::class,
                User\UserModel::class => User\UserModelFactory::class
            ],
            'aliases' => [
                Authentication\AuthenticationInterface::class => Authentication\OAuth2\OAuth2Adapter::class,
            ],
        ];
    }


    /**
     * Returns the templates configuration
     */
    public function getTemplates(): array
    {
        return [
            'paths' => [
                'app'    => ['templates/app'],
                'error'  => ['templates/error'],
                'layout' => ['templates/layout'],
            ],
        ];
    }


    public function getHalConfig(): array
    {
        return [
            [
                '__class__' => RouteBasedResourceMetadata::class,
                'resource_class' => User\UserEntity::class,
                'route' => 'api.user',
                'extractor' => ObjectPropertyHydrator::class,
            ],
            [
                '__class__' => RouteBasedCollectionMetadata::class,
                'collection_class' => User\UserCollection::class,
                'collection_relation' => 'users',
                'route' => 'api.users',
            ]
        ];
    }

    public function getAuthenticationConfig()
    {
        return [
            'private_key'    => getcwd() . '/data/oauth2/private.key',
            'public_key'     => getcwd() . '/data/oauth2/public.key',
            'encryption_key' => require getcwd() . '/data/oauth2/encryption.key',
            'access_token_expire'  => 'P1D',
            'refresh_token_expire' => 'P1M',
            'auth_code_expire'     => 'PT10M',
            'pdo' => [
                'dsn' => 'sqlite:' . getcwd() . '/data/oauth2.sqlite'
            ],

            // Set value to null to disable a grant
            'grants' => [
                Grant\ClientCredentialsGrant::class => Grant\ClientCredentialsGrant::class,
                Grant\PasswordGrant::class          => Grant\PasswordGrant::class,
                Grant\AuthCodeGrant::class          => Grant\AuthCodeGrant::class,
                Grant\ImplicitGrant::class          => Grant\ImplicitGrant::class,
                Grant\RefreshTokenGrant::class      => Grant\RefreshTokenGrant::class
            ],
        ];
    }
}
