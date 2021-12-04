<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Trick;
use App\Repository\UserRepository;
use App\Repository\CategoryRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TrickFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    public function __construct(UserRepository $userRepository, CategoryRepository $categoryRepository)
    {
        $this->userRepository = $userRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $tricks = ['Melancholie', 'Mute', '540 rotation', 'Indy', 'Stalefish', 'Japan Air', 'Nose grab', '180 rotation', 'Tail grab', '900 rotation'];
        $tricksDescription = [
            "Saisie de la carre backside de la planche, entre les deux pieds, avec la main avant.",
            "Saisie de la carre frontside de la planche entre les deux pieds avec la main avant.",
            "Cinq quatre pour un tour et demi.",
            "Saisie de la carre frontside de la planche, entre les deux pieds, avec la main arrière.",
            "Saisie de la carre backside de la planche entre les deux pieds avec la main arrière.",
            "Saisie de l'avant de la planche, avec la main avant, du côté de la carre frontside.",
            "Saisie de la partie avant de la planche, avec la main avant.",
            "Désigne un demi-tour, soit 180 degrés d'angle.",
            "Saisie de la partie arrière de la planche, avec la main arrière.",
            "Deux tours et demi.",
        ];

        $user = $this->userRepository->findOneBy(['username' => 'admin']);
        $category = $this->categoryRepository->findOneBy(['name' => 'Grabs']);

        $i = 0;
        foreach ($tricks as $trick) {
            $newTrick = new Trick();
            $newTrick->setName($trick);
            $newTrick->setCreatedAt(new \DateTime());
            $newTrick->setUpdatedAt(new \DateTime());
            $newTrick->setUser($user);
            $newTrick->setSlug(strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $trick), '-')));
            $newTrick->setCategory($category);
            $newTrick->setDescription($tricksDescription[$i]);

            $i++;
            $manager->persist($newTrick);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            AppFixtures::class, CategoryFixtures::class
        ];
    }
}
