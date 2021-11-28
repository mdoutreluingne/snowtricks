<?php

namespace App\Form;

use App\Entity\Video;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class VideoAddFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('url', TextType::class, [
                'label' => false,
                'attr' => ['class' => 'videoAddInput', 'placeholder' => 'Lien vers les vidéos'],
                'required' => false,
                'constraints' => [
                new Regex(['pattern' => "^((http(s)?:\\/\\/)?((w){3}.)?youtu(be|.be)?(\\.com)?\\/.+)|(/http:\/\/www\.dailymotion\.com\/video\/+/)|((http(s)?:\\/\\/)?((w){3}.)?dai(ly|.ly)?(\\.com)?\\/.+)|((http(s)?:\/\/)?((w){3}.)?player.vimeo.com/video\/.+)|(#TO_DELETE#)^", 'message' => 'L\'URL de la vidéo entrée n\'est pas valide ! Nous acceptons les vidéos provenant de Youtube, Dailymotion et Viméo.']),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Video::class,
        ]);
    }
}
