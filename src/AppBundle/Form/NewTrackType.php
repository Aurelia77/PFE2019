<?php

namespace AppBundle\Form;



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
            ->add('image', FileType::class, ['label' => 'Choisissez une image']);
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Track'             // J'avais fait ça avec l'import en haut Message :    'data_class' => Message::class,
        ));
    }
}