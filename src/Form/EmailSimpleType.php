<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Contract;
use App\Entity\Document;
use App\Entity\EmailSimple;
use App\Repository\ContractRepository;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class EmailSimpleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('recipients', EntityType::class, [
                'label' => 'Choisissez un ou plusieurs destinataires :',
                'class' => User::class,
                'choice_label' => 'fullName',
                'multiple' => true,
                'required' => false,
                'attr' => [
                    'class' => 'js-select'
                ]
            ])
            ->add('recipientsGroup', ChoiceType::class, [
                'required' => false,
                'multiple' => true,
                'label' => 'Destinataires groupés :',
                'choices'  => [
                    'Tous le monde' => 'all',
                    'Tous les adhérents' => 'adherents',
                    'Tous les référents' => 'referents',
                    'Tous les producteurs' => 'producteurs',
                    'Tous les trésoriers' => 'tresoriers',
                    'Tous les administrateurs' => 'admin',
                ],
                'attr' => [
                    'class' => 'js-select'
                ]
            ])
            ->add('recipientsContracts', EntityType::class, [
                'class' => Contract::class,
                'label' => 'Destinataire par contrat :',
                'required' => false,
                'multiple' => true,
                'query_builder' => function (ContractRepository $cr) {
                    return $cr->createQueryBuilder('c')
                        ->andWhere('c.status = :status')
                        ->setParameter('status', 'actif')
                        ->orderBy('c.createdAt', 'DESC');
                },
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'js-select'
                ]
            ])
            ->add('template', ChoiceType::class, [
                'required' => false,
                'multiple' => true,
                'label' => "Modèle d'email :",
                'choices'  => [
                    'Simple (par défaut)' => 'email/template/simple.html.twig',
                ],
                'attr' => [
                    'class' => 'js-select'
                ]
            ])
            ->add('message', CKEditorType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Saisissez votre message'
                ]
            ])
            ->add('documentBase', EntityType::class, [
                'label' => 'Choisissez un ou plusieurs document :',
                'class' => Document::class,
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false,
                'attr' => [
                    'class' => 'js-select'
                ]
            ])
            ->add('docName', TextType::class, [
                'label' => 'Nom du document :',
                'required' => false
            ])
            ->add('file', FileType::class, [
                'attr' => [
                    'placeholder' => 'Sélectionner un fichier'
                ],
                'label_attr' => [
                    'data-browse' => 'Parcourir'
                ],
                'label' => 'Document :',
                'multiple' => false,
                'mapped' => false,
                'required' => false
            ])
            ->add('saveDoc', CheckboxType::class, [
                'label' => 'Enregistrer le document dans la base',
                'required' => false
            ])
            ->add('copyMail', CheckboxType::class, [
                'label' => 'Recevoir une copie du mail',
                'required' => false
            ])/* 
            ->add('sender', EntityType::class, [
                'label' => 'Choisissez un expéditeur :',
                'class' => User::class,
                'choice_label' => 'fullName',
                'multiple' => false,
                'required' => false,
                'attr' => [
                    'class' => 'js-select'
                ]
            ]) */;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EmailSimple::class,
        ]);
    }
}
