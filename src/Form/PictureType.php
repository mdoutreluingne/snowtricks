<?php

namespace App\Form;

use App\Entity\Trick;
use App\Entity\Picture;
use App\Repository\TrickRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class PictureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', FileType::class, [
                'multiple' => false,
                'required' => true,
                'mapped' => false,
                'label' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5Mi',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png'
                        ],
                    ])
                ],
                'attr' => ['placeholder' => "Choisir l'image à la une"]
            ])
            ->add('updated_at')
            ->add('size')
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Picture::class,
            'trick' => null
        ]);
    }
}
