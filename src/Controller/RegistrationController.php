<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

class RegistrationController extends AbstractController
{
    public function __construct(
        private LoggerInterface $logger
    ) {}

    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request, 
        UserPasswordHasherInterface $userPasswordHasher, 
        EntityManagerInterface $entityManager
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->logger->info('Form submitted successfully');
                
                // Encoder le mot de passe
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );

                // Récupérer le type d'utilisateur
                $userType = $form->get('userType')->getData();
                $user->setRoles([$userType]);
                
                $this->logger->info('User role set to: ' . $userType);

                // Persister l'utilisateur
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Compte créé avec succès !');
                
                // Redirection simple et directe, sans authentification
                if ($userType === 'ROLE_ADMIN') {
                    // Redirection absolue vers le dashboard admin
                    $this->logger->info('Admin user created. Redirecting to /admin');
                    $this->addFlash('info', 'Vous pouvez maintenant vous connecter en tant qu\'administrateur.');
                    return $this->redirectToRoute('app_login');
                }
                
                $this->logger->info('Regular user created. Redirecting to home page');
                $this->addFlash('info', 'Vous pouvez maintenant vous connecter.');
                return $this->redirectToRoute('app_login');
                
            } catch (UniqueConstraintViolationException $e) {
                $this->logger->error('Email already exists: ' . $e->getMessage());
                $this->addFlash('error', 'Cet email est déjà utilisé.');
            } catch (\Exception $e) {
                $this->logger->error('Error during registration: ' . $e->getMessage());
                $this->addFlash('error', 'Une erreur est survenue lors de l\'inscription.');
            }
        } elseif ($form->isSubmitted()) {
            $this->logger->warning('Form submitted but invalid');
            $errors = [];
            foreach ($form->getErrors(true) as $error) {
                $errors[] = $error->getMessage();
            }
            $this->logger->warning('Form errors: ' . implode(', ', $errors));
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
} 