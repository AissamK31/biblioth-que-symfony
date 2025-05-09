<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\LivreRepository;
use App\Entity\Livre;


final class LivrecontrollerController extends AbstractController
{
    #[Route('/livrecontroller', name: 'app_livrecontroller')]
    public function index(LivreRepository $livreRepository): Response
    {
        $livres = $livreRepository->findAll();
        return $this->render('livrecontroller/index.html.twig', [
            'livres' => $livres,
        ]);
    }

    #[Route('/livre/{id}', name: 'app_livre_show')]
    public function show(Livre $livre): Response
    {
        return $this->render('livrecontroller/show.html.twig', [
            'livre' => $livre,
        ]);
    }
}
