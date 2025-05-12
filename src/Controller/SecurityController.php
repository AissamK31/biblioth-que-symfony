<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\SecurityBundle\Security;

class SecurityController extends AbstractController
{
    public function __construct(
        private Security $security
    ) {}

    #[Route('/login', name: 'app_login')]
    public function login(Request $request, AuthenticationUtils $authenticationUtils, LoggerInterface $logger): Response
    {
        // Si l'utilisateur est déjà connecté et qu'il n'y a pas de paramètre 'switch'
        if ($this->getUser() && !$request->query->has('switch')) {
            return $this->render('security/already_logged_in.html.twig', [
                'user' => $this->getUser()
            ]);
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        if ($error) {
            $logger->error('Login error: ' . $error->getMessage());
            $this->addFlash('error', 'Une erreur est survenue lors de la connexion. Veuillez réessayer.');
        }
        
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): Response
    {
        // Cette méthode devrait en principe ne jamais être appelée, 
        // car Symfony intercepte l'URL /logout
        // Mais on peut l'utiliser comme fallback pour éviter les erreurs
        
        // On désactive la vérification CSRF pour la déconnexion
        $this->security->logout(false);
        
        $this->addFlash('info', 'Vous avez été déconnecté avec succès.');
        
        // Redirection vers la page de connexion
        return $this->redirectToRoute('app_login');
    }

    #[Route('/manual-logout', name: 'app_manual_logout')]
    public function manualLogout(): Response
    {
        // Méthode alternative de déconnexion sans protection CSRF
        $this->security->logout(false);
        
        $this->addFlash('info', 'Vous avez été déconnecté avec succès.');
        
        // Redirection vers la page de connexion
        return $this->redirectToRoute('app_login');
    }
} 