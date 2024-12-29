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
        string $approvalClass,
        ?int $approvalEntityId = null
    ): array {
        $this->checkRoleManagesResource($role, $resource);
        $this->checkForExisting($role, $user, $resource);
        $entityId = $resource->getResourceId();
        $application = new PassportApplication();
        $application->setRole($role);
        $application->setUser($user);
        $application->setEntityId($entityId);
        $application->setApprovalClass($approvalClass);
        $application->setApprovalEntityId($approvalEntityId);
        $this->entityManager->persist($application);
        $this->entityManager->flush();
        $approvalNotificationRole = $this->passportControl->findRole($approvalClass);
        $passports = $this->passportControl->findPassportRoles($approvalNotificationRole, $approvalEntityId);
        $reps = [];

        foreach ($passports as $passport) {
            $rep = $this->entityManager->getRepository(User::class)->find($passport->getUserId());
            $reps[] = ['email' => $rep->getEmail(), 'name' => $rep->getPerson()->getFullName()];
        }

        return $reps;
    }

    public function checkForExisting(Role $role, User $user, Resource $resource): void
    {
        $passport = $this->passportControl->findUserPassport($user->getId());

        if ($this->passportControl->hasPassportRole($passport, $role->getRoleName(), $resource->getResourceId())) {
            throw new EnlistException(sprintf(EnlistException::ROLE_EXISTS, $role->getRoleName(), $resource->getResourceType(), $resource->getResourceId()), 400);
        }

        $existingApplication = $this->entityManager->getRepository(PassportApplication::class)->findOneBy([
           'user' => $user,
           'role' => $role,
           'entityId' => $resource->getResourceId(),
        ]);

        if ($existingApplication) {
            throw new EnlistException(EnlistException::APPLICATION_EXISTS, 400);
        }
    }

    /** @throws EnlistException */
    public function approveApplication(PassportApplication $application, PassportInterface $passport, User $approvedBy): void
    {
        $this->checkValidApplication($application);
        $role = $application->getRole();
        $resource = $this->findResourceOrFail($role, $application);
        $this->checkRoleManagesResource($role, $resource);
        $adminPassport = $this->passportControl->findUserPassport($approvedBy->getId());
        $canApprove = $this->passportControl->isAuthorized($adminPassport, $resource, $role->getRoleName());

        if (!$canApprove) {
            throw new EnlistException(EnlistException::UNAUTHORISED);
        }

        $this->passportControl->grantEntitlement($passport, $role, $resource, $passport->getUserId());
        $application->setApprovedBy($approvedBy);
        $application->setApprovalDate(new  \DateTime('now',  new \DateTimeZone('UTC')));
        $this->entityManager->flush();
    }

    /** @throws EnlistException */
    public function declineApplication(PassportApplication $application, User $declinedBy): void
    {
        $this->checkValidApplication($application);
        $role = $application->getRole();
        $resource = $this->findResourceOrFail($role, $application);
        $this->checkRoleManagesResource($role, $resource);
        $adminPassport = $this->passportControl->findUserPassport($declinedBy->getId());
        $canDecline = $this->passportControl->isAuthorized($adminPassport, $resource, $role->getRoleName());

        if (!$canDecline) {
            throw new EnlistException(EnlistException::UNAUTHORISED);
        }

        $application->setExpiredBy($declinedBy);
        $application->setExpiryDate(new  \DateTime('now',  new \DateTimeZone('UTC')));
        $this->entityManager->flush();
    }

    private function checkValidApplication(PassportApplication $application)
    {
        if ($application->getExpiryDate() !== null) {
            throw new EnlistException(EnlistException::APPLICATION_DECLINED);
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

    public function getNewApplications(?string $checkingRole = null, ?int $approvalEntityId = null): array
    {
        $criteria = ['approvedBy' => null];

        if ($checkingRole) {
            $criteria['approvalClass'] = $checkingRole;
        }

        if ($approvalEntityId) {
            $criteria['approvalEntityId'] = $approvalEntityId;
        }

        return $this->entityManager->getRepository(PassportApplication::class)->findBy($criteria, ['createdAt' => 'ASC']);
    }
}
