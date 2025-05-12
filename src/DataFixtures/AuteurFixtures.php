<?php

namespace App\DataFixtures;

use App\Entity\Auteur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AuteurFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $auteurs = [
            ['nom' => 'Hugo', 'prenom' => 'Victor'],
            ['nom' => 'Camus', 'prenom' => 'Albert'],
            ['nom' => 'Rowling', 'prenom' => 'J.K.'],
            ['nom' => 'Tolkien', 'prenom' => 'J.R.R.'],
        ];
        
        foreach ($auteurs as $key => $data) {
            $auteur = new Auteur();
            $auteur->setNom($data['nom']);
            $auteur->setPrenom($data['prenom']);
            $manager->persist($auteur);
            
            // Référence pour utilisation dans LivreFixtures
            $this->addReference('auteur_' . $key, $auteur);
        }

        $manager->flush();
    }
}
