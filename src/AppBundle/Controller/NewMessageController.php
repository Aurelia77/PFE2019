<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Message;
use AppBundle\Entity\Track;
use AppBundle\Form\CreateBookType;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormTypeInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;                // Car on utilise le paramètre : EntityManagerInterface $em
use AppBundle\Repository\UserRepository;



class NewMessageController extends Controller
{
//    /**
//     * Page qui permet d'ajouter un message (commentaire) sur un track
//     * L'utilisateur doit être connecté !
//     *
//     * @Route("/newmessage/{track}/{user}", name="newmessage")
//     *
//     *
//     * @param Request $request
//     * @param EntityManagerInterface $em
//     * @return Response
//     */
//    public function newMessageAction(Request $request, EntityManagerInterface $em)
//    {
////         1) Construire le form Track
////        $message = new Message();
////
////        $message->setCorps('Coucou');
////        $message->setActif(1);
////        $message->setUser($this->getUser());
////        $message->setTrack('');
//
//        $coucou = "oui";
//
//        return $this->render(
//            '/Track/seetrack.html.twig', [
//                    'coucou' => $coucou
//                ]
////            array('newTrackForm' => $newTrackForm->createView(),
////                'request' => $request,
////            )
//        );
//
//    }



}