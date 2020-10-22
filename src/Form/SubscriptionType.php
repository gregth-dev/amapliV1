<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Organism;
use App\Entity\Subscription;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Form\PaymentForm\SubscriptionPaymentType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class SubscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('subscriber', EntityType::class, [
                'label' => 'Sélectionner un adhérent :',
                'class' => User::class,
                'choice_label' => 'fullName',
                'multiple' => false,
                'attr' => [
                    'class' => 'js-select'
                ]
            ])
            ->add('organism', EntityType::class, [
                'label' => 'Sélectionner une ou plusieurs associations :',
                'class' => Organism::class,
                'choice_label' => 'fullName',
                'multiple' => true,
                'required' => true,
                'attr' => [
                    'class' => 'js-select'
                ]
            ])
            ->add('payment', CollectionType::class, [
                'label' => false,
                'entry_type' => SubscriptionPaymentType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'entry_options' => ['label' => " "]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Subscription::class,
        ]);
    }
}
