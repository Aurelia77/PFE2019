<?php
/**
 * Created by PhpStorm.
 * User: ACER
 * Date: 06/04/2019
 * Time: 01:34
 */

namespace AppBundle\Form;

//use AppBundle\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateBookType extends AbstractType          // ne pas oublier d'étendre  extends AbstractType !!!'
{
//    public function buildForm(FormBuilderInterface $builder, array $options)
//    {
//        $builder
//            ->add('title', TextType::class)
//            ->add('author', TextType::class)
//            //->add('submit', SubmitType::class)
//        ;
//    }
//
//    public function configureOptions(OptionsResolver $resolver)
//    {
//        $resolver->setDefaults(array(
//            'data_class' => 'AppBundle\Entity\Message'             // J'avais fait ça avec l'import en haut Message :    'data_class' => Message::class,
//        ));
//    }
//
//
////    LUCAS
//    /**
//     * {@inheritdoc}
//     */
//    public function getBlockPrefix()
//    {
//        return 'appbundle_book';
//    }




// ESSAI AVEC UPLOAD D'IMAGE (author)
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
            ->add('author', FileType::class)
            //->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Message'             // J'avais fait ça avec l'import en haut Message :    'data_class' => Message::class,
        ));
    }


//    LUCAS
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_book';
    }
}