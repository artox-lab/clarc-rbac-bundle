<?php

declare(strict_types=1);

namespace ArtoxLab\Bundle\ClarcRbacBundle\Core\Interfaces\Bus\Middleware;

use ArtoxLab\Bundle\ClarcRbacBundle\Core\Interfaces\Security\Annotation\AnnotationsLoaderInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Security\Core\Security;
use ArtoxLab\Bundle\ClarcRbacBundle\Core\Interfaces\Security\Exceptions\AccessDenyHttpException;

final class RulesVoterMiddleware implements MiddlewareInterface
{
    private Security $security;

    private AnnotationsLoaderInterface $annotationLoader;

    public function __construct(Security $security, AnnotationsLoaderInterface $annotationLoader)
    {
        $this->security = $security;
        $this->annotationLoader = $annotationLoader;
    }

    /**
     * @throws \Throwable
     */
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $message = $envelope->getMessage();
        $this->handleClassAnnotations($message);

        try {
            $returnedEnvelope = $stack->next()->handle($envelope, $stack);
        } catch (HandlerFailedException $e) {
            throw $e->getPrevious() ?: $e;
        }

        return $returnedEnvelope;
    }

    private function handleClassAnnotations(object $message): void
    {
        foreach ($this->annotationLoader->getAnnotations($message) as $rulesVoter) {
            if ($this->security->isGranted($rulesVoter->voter, $message) === false) {
                throw new AccessDenyHttpException();
            }
        }
    }
}
