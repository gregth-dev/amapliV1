<?php

namespace App\Form;

use App\Entity\Delivery;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Form\DataTransformer\FrenchToDateTimeTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class DeliveryType extends AbstractType
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
                'attr' => [
                    'placeholder' => 'Date de distribution',
                    'class' => 'dateInput'
                ]
            ])
            ->add('status', ChoiceType::class, [
                'required' => false,
                'multiple' => false,
                'label' => 'Etat de la livraison :',
                'choices'  => [
                    'Validée' => 'Validée',
                    'Annulée' => 'Annulée',
                    'Reportée' => 'Reportée',
                    'A Confirmer' => 'A Confirmer',
                ],
            ]);
        $builder->get('date')->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Delivery::class,
        ]);
    }
}
