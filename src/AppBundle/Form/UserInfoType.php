<?php

namespace AppBundle\Form;

use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
// Type
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UserInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pseudo', TextType::class)
            ->add('firstName', TextType::class, ['required' => false,
                'label' => 'Prénom'])
            ->add('lastName', TextType::class, ['required' => false,
                'label' => 'Nom'])
//            ->add('photo', TextType::class)
//            ->add('photo', FileType::class, ['label' => 'Image/photo du compte (taille max : 2048 KiB)'])


            // !!! PROBLEME !!! The options "choice_label", "class", "multiple" do not exist
//            ->add('favoriteBook', TextType::class, array(
//                // On veut des entités de type Message
//                'class' => 'AppBundle:Message',
//                // On va utiliser l'attribut title des livre pour les afficher dans le listing
//                'choice_label' => 'name',
//                // Ne pas permettre une selection multiple
//                'multiple' => false,
//                // Dissocier les éléments (check boxes)
//                //'expanded' => true,
//                'required' => false,
//                'by_reference' => false, // utiliser les fonction addGroup() et removeGroup()
////                'by_reference' => true, // ne pas utiliser les fonctions addGroup() et removeGroup()
//            ));
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }
}