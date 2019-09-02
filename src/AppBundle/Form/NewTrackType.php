<?php

namespace AppBundle\Form;


use AppBundle\Entity\MotClef;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
// Type
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

// Constraints

class NewTrackType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ['label' => 'Titre de ma création'])
            // Type : FilType pour
            ->add('track', FileType::class, ['label' => 'Votre création (taille max : 2048 KiB)'])
            ->add('image', FileType::class, ['label' => 'Choisissez une image'])
            // Des checkboxes avec les mots clefs (choix multiples)
            ->add('motsclefs', EntityType::class, [
                // On veut des checkboxes :
                // plusieurs possibilités
                'multiple' => true,
                // pas dans un SELECT mais des boutons
                'expanded' => true,
                // looks for choices from this entity
                'class' => MotClef::class,

                // uses the User.username property as the visible option string
                'choice_label' => 'mot',

                'required' => false,
                'by_reference' => false, // utiliser les fonction addMotClef() et removeMotClef()
//               'by_reference' => true, // ne pas utiliser les fonctions addMotClef() et removeMotClef()
            ]);
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Track'             // J'avais fait ça avec l'import en haut Message :    'data_class' => Message::class,
        ));
    }
}