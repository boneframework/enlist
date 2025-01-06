<?php

declare(strict_types=1);

namespace Bone\Passport\Enlist\Traits;

use Bone\Passport\Enlist\Entity\PassportApplication;
use Doctrine\ORM\Mapping as ORM;

trait HasPassportApplication
{
    #[ORM\OneToOne]
    private PassportApplication $application;

    public function getApplication(): PassportApplication
    {
        return $this->application;
    }

    public function setApplication(PassportApplication $application): void
    {
        $this->application = $application;
    }
}
