<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Trick;
use App\Entity\Picture;
use App\Entity\Category;
use App\Form\PictureAddFormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class TrickType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
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
            ->add('main_picture', FileType::class, [
                'multiple' => false,
                'required' => false,
                'mapped' => false,
                'label' => "Image à la une",
                'constraints' => [
                    new File([
                        'maxSize' => '5Mi',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                    ])
                ],
                'attr' => ['placeholder' => "Choisir l'image à la une"]
            ]);
        ;
        /* if ($this->security->isGranted('ROLE_ADMIN')) {
            $builder->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
                'required' => true,
                'label' => "Utilisateurs"
            ]);
        } */
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
