<?php

declare(strict_types=1);

namespace Bone\Passport\Enlist\Middleware;

use Bone\Passport\Enlist\Entity\PassportApplication;
use Bone\Passport\Enlist\Exception\EnlistException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PassportApplicationMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }


    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $applicationId = $request->getAttribute('applicationid');
        $application = $this->entityManager->getRepository(PassportApplication::class)->find($applicationId);

        if (!$application) {
            throw new EnlistException(EnlistException::APPLICATION_NOT_FOUND);
        }

        $request = $request->withAttribute('application', $application);

        return $handler->handle($request);
    }
}
