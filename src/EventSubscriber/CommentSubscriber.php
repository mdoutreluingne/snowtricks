<?php

namespace App\EventSubscriber;

use App\Entity\Comment;
use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class CommentSubscriber implements EventSubscriber
{
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->persistComment($args);
        $this->saveComment($args);
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $this->saveComment($args);
    }

    private function persistComment(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        // if this subscriber only applies to certain entity types,
        // add some code to check the entity type as early as possible
        if (!$entity instanceof Comment) {
            return;
        }

        $args->getObject()->setCreatedAt(new \DateTimeImmutable());
    }

    private function saveComment(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        // if this subscriber only applies to certain entity types,
        // add some code to check the entity type as early as possible
        if (!$entity instanceof Comment) {
            return;
        }

        $entity->setUpdatedAt(new \DateTimeImmutable());
    }
}
