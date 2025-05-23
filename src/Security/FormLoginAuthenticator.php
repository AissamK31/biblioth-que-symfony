<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Psr\Log\LoggerInterface;

class FormLoginAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';
    // Dans Symfony 6.2+, cette constante a été déplacée
    private const LAST_USERNAME = '_security.last_username';

    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private LoggerInterface $logger
    ) {
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('_username', '');
        $request->getSession()->set(self::LAST_USERNAME, $email);
        
        $this->logger->info('Authentication attempt for user: ' . $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('_password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $user = $token->getUser();
        $roles = $user->getRoles();
        
        $this->logger->info('Authentication success for user: ' . $user->getUserIdentifier());
        $this->logger->info('User roles: ' . implode(', ', $roles));
        
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            $this->logger->info('Redirecting to target path: ' . $targetPath);
            return new RedirectResponse($targetPath);
        }
        
        // Vérifier explicitement si l'utilisateur est un administrateur
        if (in_array('ROLE_ADMIN', $roles)) {
            $this->logger->info('Admin user detected, redirecting to /admin');
            // Redirection directe vers /admin (pas de génération d'URL)
            return new RedirectResponse('/admin');
        }

        // Pour les utilisateurs normaux, redirection vers le dashboard
        $this->logger->info('Regular user, redirecting to dashboard');
        return new RedirectResponse($this->urlGenerator->generate('app_dashboard'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
} 