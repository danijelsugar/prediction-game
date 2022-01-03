<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    /**
     * @Route("/admin", name="app_admin")
     */
    public function admin(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController-admin',
        ]);
    }

    /**
     * @Route("/admin/login")
     */
    public function adminLogin()
    {
        return new Response('Pretend admin login page, that should be public');
    }

    /**
     * @Route("/question", name="app_question")
     */
    public function question(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController-Question',
        ]);
    }

    /**
     * @Route("/admin/comments")
     */
    public function adminComments()
    {
        $this->denyAccessUnlessGranted('ROLE_COMMENT_ADMIN');

        return new Response('Pretend comments admin page');
    }
}
