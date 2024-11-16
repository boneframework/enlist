<?php

namespace Bone\Passport\Enlist\Tests;

use Bone\Passport\Enlist\Entity\PassportApplication;
use Codeception\Test\Unit;
use DateTimeInterface;
use Del\Entity\User;
use Del\Passport\Entity\Role;

class PassportApplicationTest extends Unit
{
    public function testGettersAndSetters()
    {
        $date = new \DateTime();
        $role =new Role();
        $user = new User();
        $application =  new PassportApplication();
        $application->setRole($role);
        $application->setUser($user);
        $application->setExpiryDate($date);
        $application->setApprovalDate($date);
        $application->setApprovedBy($user);
        $application->setExpiredBy($user);
        $this->assertInstanceOf(DateTimeInterface::class, $application->getApprovalDate());
        $this->assertInstanceOf(DateTimeInterface::class, $application->getExpiryDate());
        $this->assertInstanceOf(User::class, $application->getUser());
        $this->assertInstanceOf(User::class, $application->getApprovedBy());
        $this->assertInstanceOf(User::class, $application->getExpiredBy());
    }
}
