<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Security\AccountNotVerifiedAuthenticationException;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\UserPassportInterface;

class CheckVerifiedUserSubscriber implements EventSubscriberInterface
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onCheckPassport(CheckPassportEvent $event)
    {
        $passport = $event->getPassport();

        if (!$passport instanceof UserPassportInterface) {
            // TODO: throw exception
            return;
        }

        $user = $passport->getUser();

        if (!$user instanceof User) {
            throw new \UnexpectedValueException(sprintf('Unexpected user type'));
        }
        
        if (!$user->getIsVerified()) {
            throw new AccountNotVerifiedAuthenticationException();
        }
    }

    public function onLoginFailure(LoginFailureEvent $event)
    {
        if (!$event->getException() instanceof AccountNotVerifiedAuthenticationException) {
            return;
        }

        $username = $event->getRequest()->request->get('username');
        
        $response = new RedirectResponse(
            $this->router->generate(
                'app_verify_resend_email',
                [
                    'username' => $username
                ],
            )
        );
        $event->setResponse($response);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CheckPassportEvent::class => ['onCheckPassport', -10],
            LoginFailureEvent::class => 'onLoginFailure'
        ];
    }
}