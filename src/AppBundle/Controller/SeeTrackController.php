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
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function seeTrackAction(Track $track = null)
    {
        // !!! A VOIR !!!
        if (!$track) {
            throw $this->createNotFoundException("Cet piste de musique n'existe pas");
        }

        $id = $track->getId();
        $title = $track->getTitle();
        //$user = $track->getUser();

        $nametrack = $track->getTrack();

        //$pdfPath = $this->getParameter('dir.downloads').'/piano.mp3';

        //$pdfPath = $this->getParameter('dir.downloads').'/ $nametrack;
        $pdfPath = 'audio/'.$nametrack.'.mp3';

//        return $this->file($pdfPath);

        return $this->render('/Track/seetrack.html.twig', [
            'id' => $id,
            'nametrack' => $nametrack,
            'title' => $title,
            'pdfPath' => $pdfPath,
        ]);
    }
}
