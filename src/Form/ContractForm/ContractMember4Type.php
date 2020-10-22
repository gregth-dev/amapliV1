<?php

namespace App\Form\ContractForm;


use App\Form\PaymentForm\PaymentType;
use App\Entity\ContractMember;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ContractMember4Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('payments', CollectionType::class, [
                'label' => false,
                'entry_type' => PaymentType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'entry_options' => ['label' => " "]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ContractMember::class,
        ]);
    }
}
