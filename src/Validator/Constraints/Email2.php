<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 */
class Email2 extends Constraint
{
    public $message = "Cet email existe déjà.";
}
