<?php

namespace App\Form;

use App\Entity\Figure;
use App\Form\VideoType;
use App\Entity\FigureGroup;
use App\Form\IllustrationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class DescriptionTrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,[
                'label'=>'Modifier le nom de la figure',
                'required' => false
            ])

            ->add('coverImageFile', FileType::class, [ 

                'label' => 'Image de couverture (jpeg,jpg ou png)',
                'required' => false, 
            ])

            ->add( 'alternativeAttribute', TextType::class , [

                'label'=> 'Décrivez l\'image en un mot (par défaut le nom du fichier sera choisie)',
                'required' => false,
            ])

            ->add('description', TextareaType::class, [
                    'label'=>'Modifier la description de la figure',
                    'required' => false,
                    'attr' => ['rows'=>'5'],
                ])
                
            ->add('figureGroup', EntityType::class,['choice_label'=> 'name','class' => FigureGroup::class])

            ->add(
                'illustrations',
                CollectionType::class, 
                [
                    'entry_type' => IllustrationType::class, 
                    'entry_options' => ['label' => false],
                    'allow_add' => true,
                    'allow_delete' => true, 
                    'label'=> false, 
                    'by_reference'=> false,
                     
                ],
            )

            ->add(
                'videos',
                CollectionType::class,
                [
                    'entry_type' => VideoType::class,
                    'entry_options' => ['label' => false],
                    'allow_add' => true,
                    'allow_delete' => true, 
                    'label'=> false,
                    'by_reference'=> false,
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Figure::class,
        ]);
    }
}
