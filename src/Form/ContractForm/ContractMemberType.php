<?php

namespace App\Form\ContractForm;

use DateTime;
use DateTimeZone;
use App\Entity\User;
use App\Entity\Contract;
use App\Entity\ContractMember;
use App\Repository\UserRepository;
use App\Repository\ContractRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Form\DataTransformer\FrenchToDateTimeTransformer;

class ContractMemberType extends AbstractType
{
    private $transformer;

    public function __construct(FrenchToDateTimeTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

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
                        ->setParameter('status', 'archivé')
                        ->setParameter('today', new \DateTimeImmutable("$date-01T00:00:00"))
                        ->orderBy('c.createdAt', 'ASC');
                },
                'attr' => [
                    'class' => 'js-select'
                ]
            ])
            ->add('subscriber', EntityType::class, [
                'class' => User::class,
                'label' => 'Sélectionnez un Adhérent :',
                'required' => true,
                'choice_label' => 'fullName',
                'attr' => [
                    'class' => 'js-select'
                ]
            ])
            ->add('startDate', TextType::class, [
                'label' => 'Date de la 1ère distribution :',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Date de la 1ère distribution (facultatif)',
                    'class' => 'dateInput'
                ]
            ]);
        $builder->get('startDate')->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ContractMember::class,
        ]);
    }
}
