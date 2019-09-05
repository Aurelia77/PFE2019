<?php

namespace AppBundle\Form;

use AppBundle\Entity\User;
use Doctrine\DBAL\Types\BooleanType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
// Type
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
// Constraints
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class RegisterUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // L'ordre ne compte pas, il sera choisi dans la VUE
            ->add('email', EmailType::class)
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent être identiques.',
                'first_options'  => array('label' => 'Mot de passe'),
                'second_options' => array('label' => 'Je répète le mot de passe'),
                'constraints' => array(
                    new NotBlank(),
                    new Length(array('max' => 15,'min' => 6))
                ),                
            ))
            ->add('pseudo', TextType::class)
            ->add('firstName', TextType::class, ['required' => false])
            ->add('lastName', TextType::class, ['required' => false])
            ->add('sexe', ChoiceType::class, [
                'choices' => [
                        'Féminin' => 'stock_2',
                        'Masculin' => 'stock_1',
                        'Autre' => 'stock_0',
                ],
                // On veut des boutons radio :
                // pas dans un SELECT mais des boutons
                'expanded' => true,
                // une seule possibilité :
                'required' => false,

                // On ne veut pas de bouton "none" :
                'placeholder' => false
            ])


            ->add('photo', FileType::class, ['label' => 'Image/photo du compte (taille max : 2048 KiB)','required' => false])


//            ->add('rgpd', CheckboxType::class, [
//                'label'    => 'Show this entry publicly?',
//                'required' => true,
//            ]);

            //            'class' => "form-control"]



        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }
}