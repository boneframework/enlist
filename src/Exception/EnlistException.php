<?php

declare(strict_types=1);

namespace Bone\Passport\Enlist\Exception;

use Exception;

class EnlistException extends Exception
{
    const APPLICATION_NOT_FOUND = 'Passport Application not found';
    const APPLICATION_APPROVED = 'Passport Application has already been approved.';
    const APPLICATION_EXISTS = 'Passport Application already exists.';
    const APPLICATION_DECLINED = 'Passport Application has already been declined.';
    const INVALID_ENTITY_FOR_ROLE = 'Role %s does not manage entity %s';
    const RESOURCE_NOT_FOUND = '%s with ID %s not found';
    const ROLE_EXISTS = 'The user already has the %s role for entity %s with ID %s';
    const UNAUTHORISED = 'You are not authorised to perform this action.';
}
