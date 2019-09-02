<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Message;
use AppBundle\Entity\Track;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class SeeTrackController extends Controller
{
    /**
     * Quand un membre veut voir un track (avec les commentaires et les track2)
     * Accessible aux membres connectés
     * @Route("/seetrack/{id}", name="seetrack"), requirements={"id" = "\d+"}, defaults={"id" = null})
     * @param Track|null $track
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function seeTrackAction(Track $track = null)
    {
        // !!! A VOIR !!!
        if (!$track) {
            throw $this->createNotFoundException("Cette piste de musique n'existe pas");
        }


        // On va passer en pramètres tous les tracks pour pouvoir afficher ceux relié au track visualisé
        $trackRepository = $this->getDoctrine()->getRepository(Track::class);
        $tracks = $trackRepository->findAll();

        // Aussi tous les tracks pour pouvoir afficher ceux relié au track visualisé
        $messageRepository = $this->getDoctrine()->getRepository(Message::class);
        $messages = $messageRepository->findAll();

        $img_track_directory = $this->getParameter('img_track_directory');
        $track_directory = $this->getParameter('track_directory');

        return $this->render('/Track/seetrack.html.twig',
            array(
                'tracks' => $tracks,
                'messages' => $messages,
                'track' => $track,
                'img_track_directory' => $img_track_directory,
                'track_directory' => $track_directory
            ));
    }

    /**
     * Page qui permet d'ajouter un message (commentaire) sur un track
     * L'utilisateur doit être connecté !
     *
     * @Route("/newmessage/{track}/{user}", name="newmessage")
     *
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function newMessageAction(Track $track = null, Request $request, EntityManagerInterface $em)
    {
//         1) Construire le form Track
//        $message = new Message();
//
//        $message->setCorps('Coucou');
//        $message->setActif(1);
//        $message->setUser($this->getUser());
//        $message->setTrack('');

        // On va passer en pramètres tous les tracks pour pouvoir afficher ceux relié au track visualisé
        $trackRepository = $this->getDoctrine()->getRepository(Track::class);
        $tracks = $trackRepository->findAll();

        // Aussi tous les tracks pour pouvoir afficher ceux relié au track visualisé
        $messageRepository = $this->getDoctrine()->getRepository(Message::class);
        $messages = $messageRepository->findAll();

        $img_track_directory = $this->getParameter('img_track_directory');
        $track_directory = $this->getParameter('track_directory');


        $coucou = "oui";

        return $this->render(
            '/Track/seetrack.html.twig', [
                'coucou' => $coucou,
                 'tracks' => $tracks,
                'messages' => $messages,
                'track' => $track,
                'img_track_directory' => $img_track_directory,
                'track_directory' => $track_directory
            ]
//            array('newTrackForm' => $newTrackForm->createView(),
//                'request' => $request,
//            )
        );

    }
}
