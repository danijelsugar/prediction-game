<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(
        Request $request, 
        UserPasswordHasherInterface $userPasswordHasher, 
        EntityManagerInterface $entityManager, 
        UserAuthenticatorInterface $userAuthenticator, 
        LoginFormAuthenticator $loginFormAuthenticator,
        VerifyEmailHelperInterface $verifyEmailHelper
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email
            $signatureComponents = $verifyEmailHelper->generateSignature(
                'app_verify_email',
                $user->getId(),
                $user->getEmail(),
                ['id' => $user->getId()]
            );

            $this->addFlash('success', 'Confirm your email at: '.$signatureComponents->getSignedUrl());

            /*$userAuthenticator->authenticateUser(
                $user,
                $loginFormAuthenticator,
                $request,
            );*/

            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/verify", name="app_verify_email")
     */
    public function verifyUserEmail(
        Request $request, 
        VerifyEmailHelperInterface $verifyEmailHelper,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $userRepository->find($request->query->get('id'));
        if (!$user) {
            throw $this->createNotFoundException();
        }

        try {
            $verifyEmailHelper->validateEmailConfirmation(
                $request->getUri(),
                $user->getId(),
                $user->getEmail(),
            );
        } catch (VerifyEmailExceptionInterface $e) {
            $this->addFlash('error', $e->getReason());

            return $this->redirectToRoute('app_register');
        }

        $user->setIsVerified(true);
        $entityManager->flush();

        $this->addFlash('success', 'Account Verified! You can now log in.');
        
        return $this->redirectToRoute('app_login');

    }

    /**
     * @Route("verify/resend", name="app_verify_resend_email")
     */
    public function resendVerifyEmail(
        Request $request, 
        VerifyEmailHelperInterface $verifyEmailHelper, 
        UserRepository $userRepository
    ): Response {

        if ($request->getMethod() === 'POST') {
            
            //dd($request);
            $user = $userRepository->findOneBy(['username' => $request->query->get('username')]);

            if (!$user) {
                throw $this->createNotFoundException();
            }

            $signatureComponents = $verifyEmailHelper->generateSignature(
                'app_verify_email',
                $user->getId(),
                $user->getEmail(),
                ['id' => $user->getId()]
            );

            $this->addFlash('success', 'Confirm your email at: '.$signatureComponents->getSignedUrl());
        }

        return $this->render('registration/resend_verify_email.html.twig');
    }
}
