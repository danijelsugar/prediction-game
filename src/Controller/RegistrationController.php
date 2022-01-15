<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Mime\Email;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Service\FootballDataService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(
        Request $request, 
        UserPasswordHasherInterface $userPasswordHasher, 
        EntityManagerInterface $entityManager, 
        VerifyEmailHelperInterface $verifyEmailHelper,
        MailerInterface $mailer,
        FootballDataService $footballDataService
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

            $this->addFlash(
                'success', 
                'In order to login you need to verify your mail address. Verification mail has been sent to your email, check your inbox.'
            );

            $email = (new Email())
                ->from('exploder.pro@gmail.com')
                ->to($user->getEmail())
                ->subject('Verify your mail address')
                ->html('<p>To verify your mail address click <a href=' . $signatureComponents->getSignedUrl() . '>here</a><p>');

            $mailer->send($email);

            /*$userAuthenticator->authenticateUser(
                $user,
                $loginFormAuthenticator,
                $request,
            );*/

            return $this->redirectToRoute('app_login');
        }

        $competitions = $footballDataService->fetchData(
            'competitions',
            [
                'plan' => 'TIER_ONE'
            ]
        );

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'competitions' => $competitions
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

        if (!$request->query->get('id')) {
            return $this->redirectToRoute('app_home');
        }
        
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
            $this->addFlash('danger', $e->getReason() . ' Click on resend email button to request verification link.');

            return $this->redirectToRoute(
                'app_verify_resend_email',
                [
                    'username' => $user->getUsername()
                ],
            );
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
        UserRepository $userRepository,
        MailerInterface $mailer,
        FootballDataService $footballDataService
    ): Response {

        if ($request->getMethod() === 'POST') {
            
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

            $email = (new Email())
                ->from('exploder.pro@gmail.com')
                ->to($user->getEmail())
                ->subject('Verify your mail address')
                ->html('<p>To verify your mail address click <a href=' . $signatureComponents->getSignedUrl() . '>here.</a><p>');

            $mailer->send($email);

            $this->addFlash('success', 'Mail has been sent, check your email inbox to verify account.');

            return $this->redirectToRoute('app_login');
        }

        $competitions = $footballDataService->fetchData(
            'competitions',
            [
                'plan' => 'TIER_ONE'
            ]
        );

        return $this->render('registration/resend_verify_email.html.twig', [
            'competitions' => $competitions
        ]);

    }
}
