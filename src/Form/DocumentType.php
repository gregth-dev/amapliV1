<?php

namespace App\Form;

use App\Entity\Document;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class DocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du document :'
            ])
            ->add('files', FileType::class, [
                'attr' => [
                    'placeholder' => 'Sélectionner un fichier'
                ],
                'label_attr' => [
                    'data-browse' => 'Parcourir'
                ],
                'label' => 'Document :',
                'multiple' => false,
                'mapped' => false,
                'required' => true
            ])
            ->add('information', TextareaType::class, [
                'label' => 'Information complémentaire :',
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Document::class,
        ]);
    }
}
