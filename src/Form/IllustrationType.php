<?php

namespace App\Form;

use App\Entity\Illustration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class IllustrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fileIllustration', FileType::class, [

                'label' => 'Inserer un image dans la collection (jpeg,jpg ou png)',
                'required' => false,
            ])
            ->add('alternativeAttribute', TextType::class, [

                'label' => 'Décrivez l\'image en un mot (par défaut le nom du fichier sera choisie)',
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Illustration::class,
            'validation_groups' => ['base']
        ]);
    }
}
