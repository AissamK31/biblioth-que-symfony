<?php

namespace App\DataFixtures;

use App\Entity\Livre;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        for ($i = 0; $i < 10; $i++) {
            $livre = new Livre();
            $livre->setTitre("livre " . $i);
            $livre->setResume("Le livre de la vie est un livre qui parle de la vie " . $i);
            $livre->setCouverture("https://via.placeholder.com/150 " . $i);
            $manager->persist($livre);
        }




        $manager->flush();

    }
}
