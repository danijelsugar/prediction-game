<?php

namespace App\Validator;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class CurrentPasswordValidator extends ConstraintValidator
{
    public function __construct(
        private TokenStorageInterface $tokenStorage, 
        private UserPasswordHasherInterface $hasher
    ) {
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof CurrentPassword) {
            throw new UnexpectedTypeException($constraint, CurrentPassword::class);
        }

        $user = $this->tokenStorage->getToken()->getUser();

        if (!$user instanceof PasswordAuthenticatedUserInterface) {
            throw new ConstraintDefinitionException('The User object must implement the PasswordAuthenticatedUserInterface interface.');
        }

        if (!$this->hasher->isPasswordValid($user, $value)) {
            $this->context->addViolation($constraint->message);
        }
    }
}
