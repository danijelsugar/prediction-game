<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

class CurrentPassword extends Constraint
{
    public string $message = 'The current password is not valid.';
}
