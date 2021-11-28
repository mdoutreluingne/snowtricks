<?php

namespace App\Form;

use App\Entity\Trick;
use App\Entity\Video;
use App\Repository\TrickRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

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
            ->add('url', TextType::class, [
                'label' => false,
                'attr' => ['class' => 'videoAddInput', 'placeholder' => 'Lien vers la vidéo'],
                'required' => false,
                'constraints' => [
                    new Regex(['pattern' => "^((http(s)?:\\/\\/)?((w){3}.)?youtu(be|.be)?(\\.com)?\\/.+)|(/http:\/\/www\.dailymotion\.com\/video\/+/)|((http(s)?:\/\/)?((w){3}.)?player.vimeo.com/video\/.+)|(#TO_DELETE#)^", 'message' => 'L\'URL de la vidéo entrée n\'est pas valide ! Nous acceptons les vidéos provenant de Youtube, Dailymotion et Viméo.']),
                ],
            ]);  
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
