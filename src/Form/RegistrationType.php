<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationType extends AbstractType
{
    /**
     * Undocumented function
     *
     * @param FormBuilderInterface $builder
     * @param string $label
     * @param string $placeholder
     * @param array $options
     * @return array
     */
    private function getConfiguration($label, $placeholder, $options = []) {
        return array_merge([
            'label' => $label,
            'attr' => [
                'placeholder' => $placeholder,
                
            ],
            'required' => false
        ], $options);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::class, $this->getConfiguration("pseudo", "votre pseudo"))
            ->add('email', TextType::class, $this->getConfiguration("Email", "Votre adresse email"))
            ->add('password', PasswordType::class, $this->getConfiguration("Mot de passe", "choisissez un mot de passe"))
            ->add('urlPhoto', FileType::class, [

                'label' => 'Image de couverture (jpeg,jpg ou png)',
    
                // unmapped means that this field is not associated to any entity property
                'mapped' => false,
    
                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,
            ])






            // ->add('url_photo', UrlType::class,[
            //     'label'=>'Photo de profil',
            //     'attr'=> ['placholder' =>'Url de votre avatar', 'type' =>'text'],
            //     'required' => false 
            //     ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
} 