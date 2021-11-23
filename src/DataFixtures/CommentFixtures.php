<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Comment;
use App\Repository\UserRepository;
use App\Repository\TrickRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    private $trickRepository;

    public function __construct(TrickRepository $trickRepository)
    {
        $this->trickRepository = $trickRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $comments = [
            "Super trick !",
            "Trop cool !",
            "Perso! Je me suis vautré à chaque fois que j'ai essayé",
            "J'adore !",
            "Waouh incroyable"
        ];

        foreach ($this->trickRepository->findAll() as $trick) {
            for ($i=0; $i < 2; $i++) {
                $newComment = new Comment();
                $newComment->setCreatedAt(new \DateTime());
                $newComment->setUpdatedAt(new \DateTime());
                $newComment->setUser($trick->getUser());
                $newComment->setTrick($trick);
                $newComment->setContent($comments[array_rand($comments)]);

                $manager->persist($newComment);
            }
        }
        
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            AppFixtures::class, TrickFixtures::class
        ];
    }
}
