<?php

declare(strict_types=1);

namespace Bone\Passport\Enlist\Entity;

use Bone\BoneDoctrine\Traits\HasCreatedAtDate;
use Bone\BoneDoctrine\Traits\HasExpiryDate;
use Bone\BoneDoctrine\Traits\HasId;
use DateTimeInterface;
use Del\Entity\User;
use Del\Passport\Traits\HasRole;
use Del\Traits\HasUser;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class PassportApplication
{
    use HasId;
    use HasUser;
    use HasRole;

    #[ORM\Column(type: 'integer')]
    private int $entityId;

    #[ORM\Column(type: 'string')]
    private string $approvalClass;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $approvalEntityId;

    #[ORM\ManyToOne]
    private ?User $approvedBy = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTimeInterface $approvalDate = null;

    #[ORM\ManyToOne]
    private ?User $expiredBy = null;

    #[ORM\Column(type: 'json')]
    protected array $additional = [];

    use HasCreatedAtDate;
    use HasExpiryDate;

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

    public function getApprovalEntityId(): ?int
    {
        return $this->approvalEntityId;
    }

    public function setApprovalEntityId(?int $approvalEntityId): void
    {
        $this->approvalEntityId = $approvalEntityId;
    }

    public function getApprovedBy(): ?User
    {
        return $this->approvedBy;
    }

    public function setApprovedBy(?User $approvedBy): void
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

    public function getExpiredBy(): ?User
    {
        return $this->expiredBy;
    }

    public function setExpiredBy(?User $expiredBy): void
    {
        $this->expiredBy = $expiredBy;
    }

    public function getAdditionalRegistrationInfo(): array
    {
        return $this->getSettings();
    }

    public function setAdditionalRegistrationInfo(array $info): void
    {
        $this->setSettings($info);
    }
}
