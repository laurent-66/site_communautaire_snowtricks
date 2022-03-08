<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Figure;
use App\Entity\FigureGroup;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class NewTrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('figureGroup', EntityType::class,['choice_label'=> 'name','class' => FigureGroup::class])
            ->add('coverImage', FileType::class, [

                'label' => 'Image de couverture (jpeg,jpg ou png)',
    
                // unmapped means that this field is not associated to any entity property
                'mapped' => false,
    
                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,
            ])
            ->add(
                'illustrations',
                CollectionType::class,
                [
                    'entry_type' => IllustrationType::class,
                    'entry_options' => ['label' => false],
                    'allow_add' => true,
                    'label'=> false,
                    // 'error_bubbling'=> false
                    
                ],
            )
            ->add(
                'videos',
                CollectionType::class,
                [
                    'entry_type' => VideoType::class,
                    'entry_options' => ['label' => false],
                    'allow_add' => true,
                    'label'=> false
                ]
            )
            ->add( 'alternativeAttribute', TextType::class , [

                'label'=> 'Entrez le nom de l\'image',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Figure::class,
        ]);
    }
}
