<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/test-redirect-admin', name: 'test_redirect_admin')]
    public function testRedirectAdmin(): Response
    {
        return $this->redirect('/admin');
    }

    #[Route('/test-redirect-home', name: 'test_redirect_home')]
    public function testRedirectHome(): Response
    {
        return $this->redirectToRoute('app_home');
    }
} 