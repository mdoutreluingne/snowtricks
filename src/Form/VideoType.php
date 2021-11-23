<?php

namespace App\Form;

use App\Entity\Trick;
use App\Entity\Video;
use App\Repository\TrickRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VideoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('trick', EntityType::class, [
                'attr' => [
                    'readonly' => true,
                ],
                'class' => Trick::class,
                'query_builder' => function (TrickRepository $er) use ($options) {
                    return $er->createQueryBuilder('t')
                        ->andWhere('t.id = :id')
                        ->setParameter('id', $options['trick']);
                },
                'choice_label' => 'name',
                'multiple' => false,
                'required' => true
            ])
            ->add('url')  
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Video::class,
            'trick' => null
        ]);
    }
}
