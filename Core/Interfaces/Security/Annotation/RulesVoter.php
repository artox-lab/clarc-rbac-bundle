<?php

declare(strict_types=1);

namespace ArtoxLab\Bundle\ClarcRbacBundle\Core\Interfaces\Security\Annotation;

use Attribute;
use LogicException;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 *
 * @template T of VoterInterface
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final class RulesVoter
{
    /**
     * @var class-string<T> $voter
     */
    public string $voter;

    /**
     * @param class-string<T>|array{voter:class-string<T>} $voter
     */
    public function __construct(string|array $voter)
    {
        if (is_array($voter)) {
            if (!isset($voter['voter'])) {
                throw new LogicException('Voter name must be defined.');
            }

            $voter = $voter['voter'];
        }

        $this->voter = $voter;
    }
}
