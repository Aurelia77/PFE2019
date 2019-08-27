<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Track;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


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

        $trackRepository = $this->getDoctrine()->getRepository(Track::class);

        // On veut aussi passer en pramètres tous les tracks pour pouvoir afficher ceux relié au track visualisé
        $tracks = $trackRepository->findAll();

        return $this->render('/Track/seetrack.html.twig',
            array(  'tracks' => $tracks,
                    'track' => $track
        ));
    }
}
