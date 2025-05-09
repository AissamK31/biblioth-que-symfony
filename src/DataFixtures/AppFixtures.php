<?php // Début du fichier PHP

namespace App\DataFixtures; // Espace de noms pour les fixtures

use App\Entity\Livre; // Import de l'entité Livre
use Doctrine\Bundle\FixturesBundle\Fixture; // Import de la classe Fixture de base
use Doctrine\Persistence\ObjectManager; // Import du gestionnaire d'objets Doctrine

class AppFixtures extends Fixture // Définition de la classe qui étend Fixture
{
    public function load(ObjectManager $manager): void // Méthode exécutée lors du chargement des fixtures
    {
        // $product = new Product(); // Commentaire d'exemple (code généré automatiquement)
        // $manager->persist($product); // Commentaire d'exemple (code généré automatiquement)
        for ($i = 0; $i < 10; $i++) { // Boucle pour créer 10 livres
            $livre = new Livre(); // Création d'une nouvelle instance de Livre
            $livre->setTitre("livre " . $i); // Définit le titre avec une valeur incluant l'index
            $livre->setResume("Le livre de la vie est un livre qui parle de la vie " . $i); // Définit le résumé
            $livre->setCouverture("https://via.placeholder.com/150 " . $i); // Définit l'URL de la couverture (placeholder)
            $manager->persist($livre); // Indique à Doctrine de persister cet objet
        }




        $manager->flush(); // Exécute les requêtes SQL pour insérer tous les objets persistés

    }
}
