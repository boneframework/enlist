<?php

declare(strict_types=1);

namespace Bone\Passport\Enlist\Middleware;

use Bone\Passport\Enlist\Entity\PassportApplication;
use Bone\Passport\Enlist\Exception\EnlistException;
use Bone\Passport\Enlist\Service\RegistrationService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PassportApplicationsMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly RegistrationService $registrationService
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $applications = $this->registrationService->getNewApplications();
        $request = $request->withAttribute('applications', $applications);

        return $handler->handle($request);
    }
}
