<?php

declare(strict_types=1);

namespace ArtoxLab\Bundle\ClarcRbacBundle\Core\Interfaces\Security;

use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class AbstractCommandVoter extends Voter
{
    public const CLI = 'cli';

    public const REQUEST = 'request';

    //@phpcs:ignore
    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === static::class;
    }

    //@phpcs:ignore
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if ($this->executionInterface() === self::REQUEST) {
            return $this->hasRequestAccess($user, $subject);
        }

        if ($this->executionInterface() === self::CLI) {
            return $this->hasCliAccess($user, $subject);
        }

        throw new LogicException('This code should not be reached!');
    }

    private function executionInterface(): string
    {
        return match (PHP_SAPI) {
            'cli' => self::CLI,
            default => self::REQUEST,
        };
    }

    abstract protected function hasRequestAccess(UserInterface $user, mixed $subject): bool;

    abstract protected function hasCliAccess(UserInterface $user, mixed $subject): bool;
}
