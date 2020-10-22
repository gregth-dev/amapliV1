<?php

namespace App\Form\PaymentForm;

use App\Entity\Organism;
use App\Entity\SubscriptionPayment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Form\DataTransformer\FrenchToDateTimeTransformer;
use App\Form\DataTransformer\SubscriptionPaymentTransformer;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class SubscriptionPaymentType extends AbstractType
{
    private $transformer;
    private $spt;

    public function __construct(FrenchToDateTimeTransformer $transformer, SubscriptionPaymentTransformer $spt)
    {
        $this->transformer = $transformer;
        $this->spt = $spt;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('checkNumber', NumberType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Numéro du chèque'
                ]
            ])
            ->add('checkOrder', EntityType::class, [
                'class' => Organism::class,
                'choice_label' => 'fullName',
                'multiple' => false,
                'required' => true,
            ])
            ->add('depositDate', TextType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Date de dépôt',
                    'class' => 'dateInput'
                ]
            ])
            ->add('amount', NumberType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Montant du chèque'
                ]
            ]);
        $builder->get('depositDate')->addModelTransformer($this->transformer);
        $builder->get('checkOrder')->addModelTransformer($this->spt);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SubscriptionPayment::class,
        ]);
    }
}
