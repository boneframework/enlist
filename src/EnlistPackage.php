<?php

declare(strict_types=1);

namespace Bone\Passport\Enlist;

use Barnacle\Container;
use Barnacle\RegistrationInterface;
use Bone\Contracts\Container\EntityRegistrationInterface;
use Bone\Passport\Enlist\Service\RegistrationService;
use Doctrine\ORM\EntityManagerInterface;

class EnlistPackage implements RegistrationInterface, EntityRegistrationInterface
{
    public function addToContainer(Container $c)
    {
        $c[RegistrationService::class] = $c->factory(function (Container $c) {
            $entityManager =$c->get(EntityManagerInterface::class);
            $passportControl = $c->get(EntityManagerInterface::class);

            return new RegistrationService($entityManager, $passportControl);
        });
    }

    public function getEntityPath(): string
    {
        return __DIR__ . '/Entity';
    }
}
