<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Trick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = ['Grabs', 'Rotations', 'Flips', 'Slides']; 

        foreach ($categories as $category) {
            $newCategory = new Category();
            $newCategory->setName($category);
            $manager->persist($newCategory);
        }
        
        $manager->flush();
    }
}
