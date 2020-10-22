<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Donation;
use App\Form\PaymentForm\DonationPaymentType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class DonationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('donor', EntityType::class, [
                'label' => 'Sélectionner le donateur :',
                'class' => User::class,
                'choice_label' => 'fullName',
                'multiple' => false,
                'attr' => [
                    'class' => 'js-select'
                ]
            ])
            ->add('amount', NumberType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => "Saisir le montant du don"
                ]
            ])
            ->add('payment', CollectionType::class, [
                'label' => false,
                'entry_type' => DonationPaymentType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'entry_options' => ['label' => " "]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Donation::class,
        ]);
    }
}
