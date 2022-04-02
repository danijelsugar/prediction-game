<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

class CurrentPassword extends Constraint
{
    public $message = 'The current password is not valid.';
}
