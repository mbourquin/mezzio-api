<?php
declare(strict_types=1);

namespace App\User;

use Psr\Container\ContainerInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Mezzio\Hal\HalResponseFactory;
use Mezzio\Hal\ResourceGenerator;

class UserHandlerFactory
{
    public function __invoke(ContainerInterface $container) : UserHandler
    {
        return new UserHandler(
            $container->get(UserModel::class),
            $container->get(ResourceGenerator::class),
            $container->get(HalResponseFactory::class)
        );
    }
}
