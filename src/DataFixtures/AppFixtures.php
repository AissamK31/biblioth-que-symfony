<?php

namespace App\DataFixtures;

use App\Entity\Auteur;
use App\Entity\Livre;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Créer des auteurs
        $auteurs = [];
        $nomsAuteurs = ['Hugo', 'Camus', 'Zola', 'Dumas', 'Balzac'];
        $prenomsAuteurs = ['Victor', 'Albert', 'Émile', 'Alexandre', 'Honoré'];
        
        for ($i = 0; $i < 5; $i++) {
            $auteur = new Auteur();
            $auteur->setNom($nomsAuteurs[$i]);
            $auteur->setPrenom($prenomsAuteurs[$i]);
            $manager->persist($auteur);
            $auteurs[] = $auteur;
        }
        
        // Créer des livres et les lier à un auteur aléatoire
        for ($i = 0; $i < 5; $i++) {
            $livre = new Livre();
            $livre->setTitre('Livre ' . $i);
            $livre->setResume('Résumé du livre ' . $i);
            // Ajout d'une couverture (URL d'image placeholder)
            $livre->setCouverture('https://via.placeholder.com/150' . $i);
            // Attribution d'un auteur aléatoire
            $auteurAleatoire = $auteurs[array_rand($auteurs)];
            $livre->setAuteur($auteurAleatoire);
            
            $manager->persist($livre);
        }

        $manager->flush();
    }
}
?>