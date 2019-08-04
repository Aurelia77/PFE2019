<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Track;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DownloadController extends Controller
{
    /**
     * Quand un membre veut télécharger un track
     * Accessible aux membres connectés
     * @Route("/download/{id}", name="download"), requirements={"id" = "\d+"}, defaults={"id" = null})
     * @param Track|null $track
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadAction(Track $track = null)
    {
        // !!! A VOIR !!!
        if (!$track) {
            throw $this->createNotFoundException("Cet piste de musique n'existe pas");
        }



       //$id = $track->getId();
       $nametrack = $track->getTrack();

        //$pdfPath = $this->getParameter('dir.downloads').'/piano.mp3';

        //$pdfPath = $this->getParameter('dir.downloads').'/ $nametrack;
        $pdfPath = 'audio/'.$nametrack.'.mp3';

        return $this->file($pdfPath);

//        return $this->render('/User/ZZZdownload.html.twig', [
//            'id' => $id,
//            'nametrack' => $nametrack,
//            'pdfPath' => $pdfPath,
//        ]);
    }
}
