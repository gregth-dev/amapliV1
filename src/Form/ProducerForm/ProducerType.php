<?php

namespace App\Form\ProducerForm;

use App\Entity\User;
use App\Entity\Producer;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ProducerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du producteur :'
            ])
            ->add('checkOrder', TextType::class, [
                'label' => 'Ordre à mettre sur les chèques :'
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'label' => 'Sélectionner un Adhérent qui représente le producteur :',
                'query_builder' => function (UserRepository $ur) {
                    return $ur->createQueryBuilder('u')
                        ->andWhere('u.memberType = :membertype')
                        ->setParameter('membertype', 'Adhérent')
                        ->orderBy('u.lastName', 'ASC');
                },
                'choice_label' => 'fullName',
                'required' => true,
                'attr' => [
                    'class' => 'js-select'
                ]
            ])
            ->add('referent', EntityType::class, [
                'class' => User::class,
                'label' => 'Sélectionner un référent pour le producteur :',
                'query_builder' => function (UserRepository $ur) {
                    return $ur->createQueryBuilder('u')
                        ->andWhere('u.memberType != :membertype')
                        ->andWhere('u.memberType != :membertype2')
                        ->setParameter('membertype', 'Adhérent')
                        ->setParameter('membertype2', 'Producteur')
                        ->orderBy('u.lastName', 'ASC');
                },
                'choice_label' => 'fullName',
                'required' => true,
                'attr' => [
                    'class' => 'js-select'
                ]
            ])
            ->add('substitute', EntityType::class, [
                'class' => User::class,
                'label' => 'Sélectionner un suppléant pour le producteur :',
                'query_builder' => function (UserRepository $ur) {
                    return $ur->createQueryBuilder('u')
                        ->andWhere('u.memberType != :membertype')
                        ->andWhere('u.memberType != :membertype2')
                        ->setParameter('membertype', 'Adhérent')
                        ->setParameter('membertype2', 'Producteur')
                        ->orderBy('u.lastName', 'ASC');
                },
                'choice_label' => 'fullName',
                'required' => false,
                'attr' => [
                    'class' => 'js-select'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Producer::class,
        ]);
    }
}
