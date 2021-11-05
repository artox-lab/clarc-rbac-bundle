<?php

declare(strict_types=1);

namespace ArtoxLab\Bundle\ClarcRbacBundle\Core\Interfaces\Security\Annotation;

interface AnnotationsLoaderInterface
{
    /**
     * @template T
     *
     * @param T $subject
     *
     * @return iterable<RulesVoter>
     */
    public function getAnnotations(object $subject): iterable;
}