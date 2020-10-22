<?php

namespace App\Form\PermanenceForm;

use App\Entity\Permanence;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Form\DataTransformer\FrenchToDateTimeTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class Permanence2Type extends AbstractType
{
    private $transformer;

    public function __construct(FrenchToDateTimeTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate', TextType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => 'Date de la 1ère permanence',
                    'class' => 'dateInput'
                ]
            ])
            ->add('endDate', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Date de la dernière permanence',
                    'class' => 'dateInput'
                ]
            ])
            ->add('numberPlaces', NumberType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => 'Saisissez le nombre de place'
                ]
            ])
            ->add('frequency', ChoiceType::class, [
                'required' => true,
                'multiple' => false,
                'label' => 'Fréquence des permanences :',
                'choices'  => [
                    '1 fois par semaine' => '+7days',
                    '2 fois par mois' => '+14days',
                    '1 fois par mois' => '+1month',
                ],
            ]);
        $builder->get('startDate')->addModelTransformer($this->transformer);
        $builder->get('endDate')->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Permanence::class,
        ]);
    }
}
