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

//        $id = $track->getId();
//        $title = $track->getTitle();
//        $nametrack = $track->getTrack();
//        $path = 'audio/'.$nametrack.'.mp3';
//        $image = $track->getImage();
//        $pathImg = 'images/'.$image.'.mp3';

        //$pdfPath = $this->getParameter('dir.downloads').'/piano.mp3';
        //$pdfPath = $this->getParameter('dir.downloads').'/ $nametrack;


//        return $this->file($pdfPath);


//        // On va stocker les fileNames des images de profil existantes avant que $user soit hydraté par le formulaire
//        $profileImagesFileNames = [];
//        foreach ($user->getProfileImages() as $profileImage){
//            $profileImagesFileNames[$profileImage->getId()] = $profileImage->getFileName();
//        }

        // On va chercher toutes les musiques num 2 qui sont liées à cette musique
        $sons2 = [];
//        for ($track as tracks) {
//
//        }
//        foreach ($track->getId1() as $son2){
//            $profileImagesFileNames[$profileImage->getId()] = $profileImage->getFileName();
//        }


        $trackRepository = $this->getDoctrine()->getRepository(Track::class);
        $tracks = $trackRepository->findAll();



        return $this->render('/Track/seetrack.html.twig',
            array(  'tracks' => $tracks,
                    'track' => $track
//            'id' => $id,
//            'nametrack' => $nametrack,
//            'title' => $title,
//            'path' => $path,
//            'pathImg' => $pathImg,
        ));
    }
}
