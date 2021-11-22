<?php

namespace App\EventSubscriber;

use ReflectionClass;
use Doctrine\ORM\Events;
use App\Entity\Documents;
use App\Entity\Trick;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Security;

class TrickSubscriber implements EventSubscriber
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->persistTrick($args);
        $this->saveTrick($args);
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $this->saveTrick($args);
    }

    private function persistTrick(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        // if this subscriber only applies to certain entity types,
        // add some code to check the entity type as early as possible
        if (!$entity instanceof Trick) {
            return;
        }

        $args->getObject()->setCreatedAt(new \DateTimeImmutable());
    }

    private function saveTrick(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        // if this subscriber only applies to certain entity types,
        // add some code to check the entity type as early as possible
        if (!$entity instanceof Trick) {
            return;
        }

        $entity->setUpdatedAt(new \DateTimeImmutable());
        $entity->setSlug($this->generateSlug($entity->getName()));
        $entity->setUser($this->security->getUser());
    }

    /**
     * Generate Slug
     *
     * @param string $name
     * @return string
     */
    private function generateSlug(string $name): string
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
    }
}
