<?php

namespace App\Form\PaymentForm;

use App\Entity\PaymentPDF;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Form\DataTransformer\FrenchToDateTimeTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class PaymentPDFType extends AbstractType
{
    private $transformer;

    public function __construct(FrenchToDateTimeTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('status', ChoiceType::class, [
                'required' => true,
                'multiple' => false,
                'label' => false,
                'choices'  => [
                    'Tous les chèques' => 'all',
                    'Chèques remis' => 'remis',
                    'Chèques à remettre' => 'à remettre',
                    'Chèques non remis' => 'non remis',
                ],
            ])
            ->add('startDate', TextType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => 'Date de début de période',
                    'class' => 'dateInput'
                ]
            ])
            ->add('endDate', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Date de fin de période',
                    'class' => 'dateInput'
                ]
            ]);
        $builder->get('startDate')->addModelTransformer($this->transformer);
        $builder->get('endDate')->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PaymentPDF::class,
        ]);
    }
}
