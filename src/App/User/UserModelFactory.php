<?php
declare(strict_types=1);

namespace App\User;

use Psr\Container\ContainerInterface;
use Laminas\Db\Adapter\AdapterInterface;

class UserModelFactory
{
    public function __invoke(ContainerInterface $container) : UserModel
    {
        return new UserModel(
            $container->get(AdapterInterface::class)
        );
    }
}
