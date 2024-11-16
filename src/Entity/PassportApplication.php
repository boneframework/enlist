<?php

declare(strict_types=1);

namespace Bone\Passport\Enlist\Entity;

use Bone\BoneDoctrine\Traits\HasCreatedAtDate;
use Bone\BoneDoctrine\Traits\HasExpiryDate;
use Bone\BoneDoctrine\Traits\HasId;
use DateTimeInterface;
use Del\Passport\Entity\Role;
use Del\Traits\HasUser;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class PassportApplication
{
    use HasId;
    use HasUser;

    #[ORM\ManyToOne]
    private Role $role;

    #[ORM\Column(type: 'integer')]
    private int $entityId;

    #[ORM\Column(type: 'string')]
    private string $approvalClass;

    #[ORM\Column(type: 'integer')]
    private int $approvalEntityId;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $approvedBy = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTimeInterface $approvalDate = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $expiredBy = null;

    use HasCreatedAtDate;
    use HasExpiryDate;

    public function getRole(): Role
    {
        return $this->role;
    }

    public function setRole(Role $role): void
    {
        $this->role = $role;
    }

    public function getEntityId(): int
    {
        return $this->entityId;
    }

    public function setEntityId(int $entityId): void
    {
        $this->entityId = $entityId;
    }

    public function getApprovalClass(): string
    {
        return $this->approvalClass;
    }

    public function setApprovalClass(string $approvalClass): void
    {
        $this->approvalClass = $approvalClass;
    }

    public function getApprovalEntityId(): int
    {
        return $this->approvalEntityId;
    }

    public function setApprovalEntityId(int $approvalEntityId): void
    {
        $this->approvalEntityId = $approvalEntityId;
    }

    public function getApprovedBy(): ?int
    {
        return $this->approvedBy;
    }

    public function setApprovedBy(?int $approvedBy): void
    {
        $this->approvedBy = $approvedBy;
    }

    public function getApprovalDate(): ?DateTimeInterface
    {
        return $this->approvalDate;
    }

    public function setApprovalDate(?DateTimeInterface $approvalDate): void
    {
        $this->approvalDate = $approvalDate;
    }

    public function getExpiredBy(): ?int
    {
        return $this->expiredBy;
    }

    public function setExpiredBy(?int $expiredBy): void
    {
        $this->expiredBy = $expiredBy;
    }
}
