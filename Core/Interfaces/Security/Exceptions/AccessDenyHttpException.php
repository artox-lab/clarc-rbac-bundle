<?php

declare(strict_types=1);

namespace ArtoxLab\Bundle\ClarcRbacBundle\Core\Interfaces\Security\Exceptions;

use ArtoxLab\Bundle\ClarcBundle\Core\Entity\Exceptions\DomainHttpException;
use Symfony\Component\HttpFoundation\Response;

final class AccessDenyHttpException extends DomainHttpException
{
    public function __construct()
    {
        parent::__construct('Access Denied.', Response::HTTP_FORBIDDEN);
    }
}
