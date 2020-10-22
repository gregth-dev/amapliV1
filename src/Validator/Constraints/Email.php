<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 */
class Email extends Constraint
{
    public $message = "Cet email existe déjà.";
}
