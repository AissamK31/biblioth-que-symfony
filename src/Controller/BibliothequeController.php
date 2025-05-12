<?php

namespace App\Controller;

use App\Entity\Emprunt;
use App\Entity\Livre;
use App\Repository\EmpruntRepository;
use App\Repository\LivreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/bibliotheque')]
class BibliothequeController extends AbstractController
{
    #[Route('/', name: 'app_bibliotheque')]
    public function index(LivreRepository $livreRepository, EmpruntRepository $empruntRepository): Response
    {
        // Récupérer tous les livres
        $livres = $livreRepository->findAll();
        
        // Créer un tableau associatif pour marquer les livres déjà empruntés
        $livresDisponibilite = [];
        foreach ($livres as $livre) {
            $livresDisponibilite[$livre->getId()] = !$empruntRepository->isLivreEmprunte($livre->getId());
        }
        
        return $this->render('bibliotheque/index.html.twig', [
            'livres' => $livres,
            'livresDisponibilite' => $livresDisponibilite
        ]);
    }
    
    #[Route('/livre/{id}', name: 'app_bibliotheque_livre_show')]
    public function show(Livre $livre, EmpruntRepository $empruntRepository): Response
    {
        // Vérifier si le livre est disponible
        $disponible = !$empruntRepository->isLivreEmprunte($livre->getId());
        
        return $this->render('bibliotheque/show.html.twig', [
            'livre' => $livre,
            'disponible' => $disponible
        ]);
    }
    
    #[Route('/emprunter/{id}', name: 'app_bibliotheque_emprunter')]
    public function emprunter(Livre $livre, Request $request, EntityManagerInterface $entityManager, EmpruntRepository $empruntRepository): Response
    {
        // Vérifier que l'utilisateur est connecté
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();
        
        // Vérifier que le livre est disponible
        if ($empruntRepository->isLivreEmprunte($livre->getId())) {
            $this->addFlash('error', 'Ce livre est déjà emprunté.');
            return $this->redirectToRoute('app_bibliotheque_livre_show', ['id' => $livre->getId()]);
        }
        
        // Créer un nouvel emprunt
        $emprunt = new Emprunt();
        $emprunt->setLivre($livre);
        $emprunt->setUtilisateur($user);
        
        // Persister l'emprunt
        $entityManager->persist($emprunt);
        $entityManager->flush();
        
        $this->addFlash('success', 'Le livre a été emprunté avec succès. Date de retour prévue : ' . $emprunt->getDateRetourPrevue()->format('d/m/Y'));
        
        return $this->redirectToRoute('app_mes_emprunts');
    }
    
    #[Route('/mes-emprunts', name: 'app_mes_emprunts')]
    public function mesEmprunts(EmpruntRepository $empruntRepository): Response
    {
        // Vérifier que l'utilisateur est connecté
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();
        
        // Récupérer les emprunts actifs
        $empruntsActifs = $empruntRepository->findEmpruntsActifs($user);
        
        // Récupérer les emprunts en retard
        $empruntsEnRetard = $empruntRepository->findEmpruntsEnRetard($user);
        
        return $this->render('bibliotheque/mes_emprunts.html.twig', [
            'empruntsActifs' => $empruntsActifs,
            'empruntsEnRetard' => $empruntsEnRetard
        ]);
    }
    
    #[Route('/historique', name: 'app_historique_emprunts')]
    public function historiqueEmprunts(EmpruntRepository $empruntRepository): Response
    {
        // Vérifier que l'utilisateur est connecté
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();
        
        // Récupérer l'historique des emprunts
        $historiqueEmprunts = $empruntRepository->findHistoriqueEmprunts($user);
        
        return $this->render('bibliotheque/historique.html.twig', [
            'historiqueEmprunts' => $historiqueEmprunts
        ]);
    }
    
    #[Route('/retourner/{id}', name: 'app_retourner_livre')]
    public function retournerLivre(Emprunt $emprunt, EntityManagerInterface $entityManager): Response
    {
        // Vérifier que l'utilisateur est connecté et propriétaire de l'emprunt
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();
        
        if ($emprunt->getUtilisateur() !== $user) {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à retourner ce livre.');
            return $this->redirectToRoute('app_mes_emprunts');
        }
        
        // Retourner le livre
        $emprunt->retourner();
        
        // Persister les changements
        $entityManager->flush();
        
        $this->addFlash('success', 'Le livre a été retourné avec succès.');
        
        return $this->redirectToRoute('app_mes_emprunts');
    }
} 