<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Trick;
use App\Entity\Video;
use App\Entity\Picture;
use App\Entity\Category;
use App\Form\VideoAddFormType;
use App\Form\PictureAddFormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class TrickType extends AbstractType
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        //Get last url
        $url[] = array_values(array_filter(explode('/', $this->requestStack->getCurrentRequest()->getPathInfo())));

        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'label' => "Nom"
            ])
            ->add('description', TextareaType::class, [
                'required' => false
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'required' => true,
                'label' => "Catégories"
            ])
            ->add('picture_collection', FileType::class, [
                'multiple' => true,
                'required' => false,
                'mapped' => false,
                'label' => false,
                'constraints' => [
                new All([
                    new File([
                        'maxSize' => '5Mi',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                    ])
                ])
                ],
                'attr' => ['placeholder' => "Choisir les images"]
            ])
            
        ;
        /* Add input video for new form */
        if ($url[0][1] === "new") {
            $builder->add('videos', CollectionType::class, [
                'entry_type' => VideoAddFormType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'label' => false,
                'prototype_data' => new Video(),
            ]);
        }
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
