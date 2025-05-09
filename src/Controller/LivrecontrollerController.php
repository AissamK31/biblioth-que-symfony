<?php // Début du fichier PHP, obligatoire

namespace App\Controller; // Définit l'espace de noms du contrôleur

// Importation des classes nécessaires pour le contrôleur
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; // Classe de base pour tous les contrôleurs Symfony
use Symfony\Component\HttpFoundation\Response; // Classe pour générer des réponses HTTP
use Symfony\Component\Routing\Attribute\Route; // Attribut pour définir les routes
use App\Repository\LivreRepository; // Repository pour accéder aux données des livres
use App\Entity\Livre; // Entité Livre


final class LivrecontrollerController extends AbstractController // Définition de la classe contrôleur qui hérite d'AbstractController
{
    #[Route('/livrecontroller', name: 'app_livrecontroller')] // Définit la route URL et son nom
    public function index(LivreRepository $livreRepository): Response // Méthode pour afficher tous les livres, avec injection du repository
    {
        $livres = $livreRepository->findAll(); // Récupère tous les livres depuis la base de données
        return $this->render('livrecontroller/index.html.twig', [ // Rend le template avec les données
            'livres' => $livres, // Passe la variable $livres au template
        ]);
    }

    #[Route('/livre/{id}', name: 'app_livre_show')] // Route avec paramètre id pour afficher un livre spécifique
    public function show(Livre $livre): Response // Méthode avec ParamConverter qui injecte automatiquement l'objet Livre
    {
        return $this->render('livrecontroller/show.html.twig', [ // Rend le template de détail
            'livre' => $livre, // Passe la variable $livre au template
        ]);
    }
}
