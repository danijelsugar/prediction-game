<?php

namespace App\Controller;

use App\Service\FootballDataService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils, FootballDataService $footballDataService): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $competitions = $footballDataService->fetchData(
            'competitions',
            [
                'plan' => 'TIER_ONE'
            ]
        );

        return $this->render('security/login.html.twig', [
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'competitions' => $competitions
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \Exception('logout() should never be reached');
    }
}
