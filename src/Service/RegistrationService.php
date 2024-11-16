<?php

declare(strict_types=1);

namespace Bone\Passport\Enlist\Service;

use Bone\Passport\Enlist\Exception\EnlistException;
use Bone\Passport\Enlist\Entity\PassportApplication;
use Del\Entity\User;
use Del\Passport\Entity\Role;
use Del\Passport\Passport;
use Del\Passport\PassportControl;
use Del\Passport\PassportInterface;
use Del\Passport\Resource;
use Doctrine\ORM\EntityManagerInterface;

readonly class RegistrationService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PassportControl        $passportControl,
    ) {
    }

    /** @throws EnlistException */
    public function applyForRole(
        Role $role,
        User $user,
        Resource $resource,
        int $approvalEntityId
    ): array {
        $this->checkRoleManagesResource($role, $resource);
        $entityId = $resource->getResourceId();
        $approvalClass = $resource->getResourceType();
        $application = new PassportApplication();
        $application->setRole($role);
        $application->setUser($user);
        $application->setEntityId($entityId);
        $application->setApprovalClass($approvalClass);
        $application->setApprovalEntityId($approvalEntityId);
        $this->entityManager->persist($application);
        $this->entityManager->flush();

        return $this->passportControl->findPassportRoles($role, $entityId);
    }

    /** @throws EnlistException */
    public function approveApplication(PassportApplication $application, PassportInterface $passport, PassportInterface $approvedBy): void
    {
        $this->checkValidApplication($application);
        $role = $application->getRole();
        $resource = $this->findResourceOrFail($role, $application);
        $this->checkRoleManagesResource($role, $resource);
        $canApprove = $this->passportControl->isAuthorized($approvedBy, $resource, $role->getRoleName());

        if (!$canApprove) {
            throw new EnlistException(EnlistException::UNAUTHORISED);
        }

        $this->passportControl->grantEntitlement($passport, $role, $resource, $passport->getUserId());
        $application->setApprovedBy($approvedBy->getUserId());
        $application->setApprovalDate(new  \DateTime('now',  new \DateTimeZone('UTC')));
        $this->entityManager->flush();
    }

    /** @throws EnlistException */
    public function declineApplication(PassportApplication $application, PassportInterface $declinedBy): void
    {
        $this->checkValidApplication($application);
        $role = $application->getRole();
        $resource = $this->findResourceOrFail($role, $application);
        $this->checkRoleManagesResource($role, $resource);
        $canDecline = $this->passportControl->isAuthorized($declinedBy, $resource, $role->getRoleName());

        if (!$canDecline) {
            throw new EnlistException(EnlistException::UNAUTHORISED);
        }

        $application->setExpiredBy($declinedBy->getUserId());
        $application->setExpiryDate(new  \DateTime('now',  new \DateTimeZone('UTC')));
        $this->entityManager->flush();
    }

    private function checkValidApplication(PassportApplication $application)
    {
        if ($application->getExpiryDate() !== null) {
            throw new EnlistException(EnlistException::APPLICATION_DECLIUNED);
        }

        if ($application->getApprovalDate() !== null) {
            throw new EnlistException(EnlistException::APPLICATION_APPROVED);
        }
    }

    /** @throws EnlistException */
    private function findResourceOrFail(Role $role, PassportApplication $application): Resource
    {
        $resource = $this->entityManager->getRepository($role->getClass())->find($application->getEntityId());

        if (!$resource) {
            throw new EnlistException(
                sprintf(EnlistException::RESOURCE_NOT_FOUND, $role->getClass(), $application->getEntityId())
            );
        }

        return new Resource($resource);
    }

    /** @throws EnlistException */
    private function checkRoleManagesResource(Role $role, Resource $resource): void
    {
        if ($role->getClass() !== $resource->getResourceType()) {
            throw new EnlistException(
                sprintf(EnlistException::INVALID_ENTITY_FOR_ROLE, $role->getRoleName(), $resource->getResourceType())
            );
        }
    }
}