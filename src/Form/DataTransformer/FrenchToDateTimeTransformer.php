<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class qui gère la transformation des champs date de l'application
 */
class FrenchToDateTimeTransformer implements DataTransformerInterface
{
    public function transform($date)
    {
        return $date ? $date->format('d/m/Y') : null;
    }

    public function reverseTransform($frenchDateFormat)
    {
        if (!$frenchDateFormat)
            return null;
        $date = \DateTime::createFromFormat('d/m/Y', $frenchDateFormat);
        if (!$date)
            return null;
        return $date;
    }
}
