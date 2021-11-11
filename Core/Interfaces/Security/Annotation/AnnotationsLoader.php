<?php

declare(strict_types=1);

namespace ArtoxLab\Bundle\ClarcRbacBundle\Core\Interfaces\Security\Annotation;

use Doctrine\Common\Annotations\Reader;
use ReflectionClass;
use ReflectionException;
use RuntimeException;

final class AnnotationsLoader implements AnnotationsLoaderInterface
{
    private Reader $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @template T
     *
     * @param T $subject
     *
     * @return iterable<RulesVoter>
     */
    public function getAnnotations(object $subject): iterable
    {
        try {
            $reflection = new ReflectionClass($subject);
        } catch (ReflectionException) {
            throw new RuntimeException('Failed to read annotation!');
        }

        yield from $this->lookup($reflection);
    }

    /**
     * @return iterable<RulesVoter>
     */
    private function lookup(ReflectionClass $reflection): iterable
    {
        if (PHP_VERSION_ID >= 80000) {
            foreach ($reflection->getAttributes(RulesVoter::class) as $attribute) {
                yield $attribute->newInstance();
            }
        }

        foreach ($this->reader->getClassAnnotations($reflection) as $annotation) {
            if ($annotation instanceof RulesVoter) {
                yield $annotation;
            }
        }
    }
}
