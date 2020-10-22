<?php

namespace App\Form\PermanenceForm;

use App\Entity\Permanence;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Form\DataTransformer\FrenchToDateTimeTransformer;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class Permanence3Type extends AbstractType
{
    private $transformer;

    public function __construct(FrenchToDateTimeTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', TextType::class, [
                'label' => "Date de la permanence :",
                'attr' => [
                    'placeholder' => 'Date de permanence',
                    'class' => 'dateInput'
                ]
            ])
            ->add('numberPlaces', NumberType::class, [
                'label' => "Nombre de places",
                'required' => true,
                'attr' => [
                    'placeholder' => 'Saisissez le nombre de place'
                ]
            ])
            ->add('informations', TextareaType::class, [
                'label' => "Informations complémentaires",
                'required' => false
            ]);
        $builder->get('date')->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Permanence::class,
        ]);
    }
}
