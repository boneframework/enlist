<?php

declare(strict_types=1);

namespace Bone\Passport\Enlist;

use Barnacle\Container;
use Barnacle\RegistrationInterface;
use Bone\Contracts\Container\EntityRegistrationInterface;
use Bone\Passport\Enlist\Middleware\PassportApplicationMiddleware;
use Bone\Passport\Enlist\Middleware\PassportApplicationsMiddleware;
use Bone\Passport\Enlist\Service\RegistrationService;
use Del\Passport\PassportControl;
use Doctrine\ORM\EntityManagerInterface;

class EnlistPackage implements RegistrationInterface, EntityRegistrationInterface
{
    public function addToContainer(Container $c): void
    {
        $c[RegistrationService::class] = $c->factory(function (Container $c) {
            $entityManager =$c->get(EntityManagerInterface::class);
            $passportControl = $c->get(PassportControl::class);

            return new RegistrationService($entityManager, $passportControl);
        });

        $c[PassportApplicationMiddleware::class] = $c->factory(function (Container $c) {
            $entityManager =$c->get(EntityManagerInterface::class);

            return new PassportApplicationMiddleware($entityManager);
        });

        $c[PassportApplicationsMiddleware::class] = $c->factory(function (Container $c) {
            $registrationService =$c->get(RegistrationService::class);

            return new PassportApplicationsMiddleware($registrationService);
        });
    }

    public function getEntityPath(): string
    {
        return __DIR__ . '/Entity';
    }
}
