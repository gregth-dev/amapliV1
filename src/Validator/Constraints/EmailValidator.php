<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class EmailValidator extends ConstraintValidator
{
    private $ur;

    public function __construct(UserRepository $ur)
    {
        $this->ur = $ur;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint)
    {
        //si un champ email2 dans la base correspond à $value on invalide le formulaire
        try {
            $result = $this->ur->findBy(['email2' => $value]);
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
