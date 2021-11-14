<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }
    
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setUsername("admin");
        $user->setEmail("admin@gmail.com");
        $user->setLastName("admin");
        $user->setFirstName("admin");
        $user->setRoles(["ROLE_ADMIN"]);
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            'adminadmin'
        ));
        $user->setIsVerified(1);

        $manager->persist($user);
        $manager->flush();

        $user = new User();
        $user->setUsername("max");
        $user->setEmail("maxime.doutreluingne@sfr.fr");
        $user->setLastName("Doutreluingne");
        $user->setFirstName("Maxime");
        $user->setRoles(["ROLE_USER"]);
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            'testtest'
        ));
        $user->setIsVerified(1);

        $manager->persist($user);
        $manager->flush();
    }
}
