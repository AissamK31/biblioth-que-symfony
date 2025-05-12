<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        // Cette route est accessible seulement aux utilisateurs avec ROLE_USER
        $this->denyAccessUnlessGranted('ROLE_USER');
        
        return $this->render('dashboard/index.html.twig');
    }
} 