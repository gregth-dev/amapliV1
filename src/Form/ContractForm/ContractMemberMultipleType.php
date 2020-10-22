<?php

namespace App\Form\ContractForm;

use App\Entity\User;
use App\Entity\Contract;
use App\Entity\ContractMember;
use App\Entity\Product;
use App\Repository\ContractRepository;
use App\Repository\UserRepository;
use DateTime;
use DateTimeZone;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContractMemberMultipleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('contract', EntityType::class, [
                'class' => Contract::class,
                'label' => 'Sélectionnez un contrat producteur :',
                'required' => true,
                'choice_label' => 'fullName',
                'multiple' => false,
                'query_builder' => function (ContractRepository $cr) {
                    $date = (int) date('Y') . '-' . (int) date('m') . '-' . (int) date('d');
                    return $cr->createQueryBuilder('c')
                        ->andWhere('c.status != :status')
                        ->andWhere('c.endDate > :today')
                        ->andWhere('c.multidistribution = :multidistribution')
                        ->setParameter('status', 'archivé')
                        ->setParameter('today', new \DateTimeImmutable("$date-01T00:00:00"))
                        ->setParameter('multidistribution', '0')
                        ->orderBy('c.createdAt', 'ASC');
                },
                'attr' => [
                    'class' => 'js-select'
                ]
            ])
            ->add('subscribers', EntityType::class, [
                'class' => User::class,
                'label' => 'Sélectionnez des Adhérents :',
                'required' => true,
                'multiple' => true,
                'choice_label' => 'fullName',
                'attr' => [
                    'class' => 'js-select'
                ]
            ])
            ->add('product', EntityType::class, [
                'class' => Product::class,
                'label' => 'Sélectionnez un produit :',
                'required' => true,
                'multiple' => false,
                'choice_label' => 'fullName',
                'attr' => [
                    'class' => 'js-select'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ContractMember::class,
        ]);
    }
}
