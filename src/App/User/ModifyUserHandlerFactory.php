<?php
declare(strict_types=1);

namespace App\User;

use Psr\Container\ContainerInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Mezzio\Hal\HalResponseFactory;
use Mezzio\Hal\ResourceGenerator;

class ModifyUserHandlerFactory
{
    public function __invoke(ContainerInterface $container) : ModifyUserHandler
    {
        $filters = $container->get('InputFilterManager');

        return new ModifyUserHandler(
            $container->get(UserModel::class),
            $container->get(ResourceGenerator::class),
            $container->get(HalResponseFactory::class),
            $filters->get(UserInputFilter::class)
        );
    }
}
