<?php

namespace App\Form\UserForm;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserIdentityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastName', TextType::class, [
                'label' => 'Saisissez votre nom :',
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Saisissez votre prénom :',
            ])
            ->add('address', TextType::class, [
                'label' => 'Saisissez votre adresse :',
            ])
            ->add('city', TextType::class, [
                'label' => 'Saisissez votre ville :',
            ])
            ->add('postcode', TextType::class, [
                'label' => 'Saisissez votre code postal :',
            ])
            ->add('phone1', TextType::class, [
                'label' => 'Saisissez votre téléphone n°1 :',
            ])
            ->add('phone2', TextType::class, [
                'label' => 'Saisissez votre téléphone n°2 :',
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
