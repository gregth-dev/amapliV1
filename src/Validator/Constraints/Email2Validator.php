<?php

namespace App\Validator\Constraints;

use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class Email2Validator extends ConstraintValidator
{
    private $ur;

    public function __construct(UserRepository $ur)
    {
        $this->ur = $ur;
    }

    public function validate($value, Constraint $constraint)
    {
        try {
            $result = $this->ur->findBy(['email' => $value]);
            if ($result) {
                return $this->context
                    ->buildViolation($constraint->message)
                    ->addViolation();
            }
        } catch (\Exception $e) {
            return $this->context
                ->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
