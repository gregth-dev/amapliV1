<?php

namespace App\Form\ContractForm;

use App\Form\OrderForm\Order2Type;
use App\Entity\ContractMember;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ContractMember3Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $idProducer = $options['idProducer'];

        $builder
            ->add('orders', CollectionType::class, [
                'label' => false,
                'mapped' => false,
                'entry_type' => Order2Type::class,
                'allow_add' => true,
                'allow_delete' => true,
                'entry_options' => [
                    'label' => " ",
                    'data' => [
                        'idProducer' => $idProducer
                    ]
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ContractMember::class,
            'idProducer' => null,
        ]);
    }
}
