<?php

namespace App\Form\PermanenceForm;

use App\Entity\User;
use App\Entity\Permanence;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PermanenceUnsubscribeType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('participants', EntityType::class, [
                'class' => User::class,
                'label' => 'Décochez les participants que vous souhaitez désinscrire :',
                'choice_label' => 'fullName',
                'choices' => $options['participants'],
                'multiple' => true,
                'required' => true,
                'expanded' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Permanence::class,
            'participants' => null
        ]);
    }
}
