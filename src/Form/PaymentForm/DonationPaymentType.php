<?php

namespace App\Form\PaymentForm;

use App\Entity\DonationPayment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Form\DataTransformer\FrenchToDateTimeTransformer;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class DonationPaymentType extends AbstractType
{
    private $transformer;

    public function __construct(FrenchToDateTimeTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('checkNumber', NumberType::class, [
                'attr' => [
                    'placeholder' => 'Numéro du chèque'
                ]
            ])
            ->add('checkOrder', TextType::class, [
                'attr' => [
                    'placeholder' => 'Ordre du chèque'
                ]
            ])
            ->add('depositDate', TextType::class, [
                'attr' => [
                    'placeholder' => 'Date de dépôt',
                    'class' => 'dateInput'
                ]
            ])
            ->add('amount', NumberType::class, [
                'attr' => [
                    'placeholder' => 'Montant du chèque'
                ]
            ]);
        $builder->get('depositDate')->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DonationPayment::class,
        ]);
    }
}
