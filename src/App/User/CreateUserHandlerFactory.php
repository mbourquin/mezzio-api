<?php
declare(strict_types=1);

namespace App\User;

use Psr\Container\ContainerInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Mezzio\Hal\HalResponseFactory;
use Mezzio\Hal\ResourceGenerator;
use Mezzio\Helper\UrlHelper;

class CreateUserHandlerFactory
{
    public function __invoke(ContainerInterface $container) : CreateUserHandler
    {
        $filters = $container->get('InputFilterManager');

        return new CreateUserHandler(
            $container->get(UserModel::class),
            $container->get(ResourceGenerator::class),
            $container->get(HalResponseFactory::class),
            $container->get(UrlHelper::class),
            $filters->get(UserInputFilter::class)
        );
    }
}
