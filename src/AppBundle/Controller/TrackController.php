<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Track;
use AppBundle\Entity\Message;
use AppBundle\Entity\MotClef;

use AppBundle\Form\NewTrackType;
use AppBundle\Repository\MotClefRepository;
use AppBundle\Repository\TrackRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;                // Car on utilise le paramètre : EntityManagerInterface $em
use AppBundle\Repository\UserRepository;


class TrackController extends Controller
{
    /**
     * Voir un track (avec les commentaires et les track2 (track +1))
     * Possibilité d'ajouter un message (commentaire) sur le track
     * Accessible aux membres connectés
     *
     * @Route("/seetrack/{id}", name="seetrack"), requirements={"id" = "\d+"}, defaults={"id" = null})
     * @param Track|null $track
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function seeTrackAction(Track $track, Request $request, EntityManagerInterface $em)
    {
        // Il sait quel track on veut voir ????!!!! va chercher tout seul avec l'id ???
        if (!$track) {
            throw $this->createNotFoundException("Cette piste de musique n'existe pas");
        }

        // On va passer en paramètres tous les tracks pour pouvoir afficher ceux relié au track visualisé
        $trackRepository = $this->getDoctrine()->getRepository(Track::class);
        $tracks = $trackRepository->findBy(array(), array('creationdate' => 'DESC'));

        // Aussi tous les messages pour pouvoir afficher ceux reliés au track visualisé
        $messageRepository = $this->getDoctrine()->getRepository(Message::class);
        $messages = $messageRepository->findBy(array(), array('creationdate' => 'DESC'));

        // On va chercher les varibles stockées dans parameters.yml pour les chemins de fichiers
        $img_track_directory = $this->getParameter('img_track_directory');
        $img_user_directory = $this->getParameter('img_user_directory');
        $track_directory = $this->getParameter('track_directory');

        // Si ajout d'un message (commentaire) :
        $userMessage = $request->get('message');

        if ($userMessage) {
            $message = new Message();

            // 1-Le message écrit dans le formualaire
            $message->setCorps($userMessage);
            // 2-L'utilisateur connecté
            $message->setUser($this->getUser());
            // 3-Le track visualisé
            $trackid = $request->get('id');
            /* @var $trackRepository TrackRepository */   // ??? Pas besoin ???
            $trackRepository = $this->getDoctrine()->getRepository(Track::class);
            $track = $trackRepository->find($trackid);
            $message->setTrack($track);
            // 4-Sauvegarder le message ds la BDD
            $em->persist($message);
            $em->flush();
            $this->addFlash('success', 'Votre commentaire a bien été ajouté !');

            // Pour que ça rafraichisse la page et affiche le message ajouté
            return $this->redirect($request->getUri());
        }

//        $iduser = $this->getUser()->getId();

        return $this->render('/Track/seetrack.html.twig',
            array(
                'tracks' => $tracks,
                'messages' => $messages,
                'track1' => $track,
                'img_track_directory' => $img_track_directory,
                'img_user_directory' => $img_user_directory,
                'track_directory' => $track_directory,
                'motsclefs' => $em->getRepository(MotClef::class)->findAll(),
            ));
    }


    /**
     * Page qui permet d'importer un nouveau tracks via un formulaire
     * L'utilisateur doit être connecté !
     * @Route("/newtrack/{num}/{id1}", name="newtrack")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function newTrackAction(Request $request, EntityManagerInterface $em)
    {
        // 1) Construire le form Track
        $track = new Track();
        // $newTrackForm = $this->get('form.factory')->create(NewTrackType::class, $track);      // Ok mais mieux avec le helper ci-dessous :
        $newTrackForm = $this->createForm(NewTrackType::class, $track);

//        if ($newTrackForm->get('image')->getData() == '') {
//            // On met une image par défaut si aucune image sélectionné (sinon va l'écraser ci-dessous)
//            $track->setImage('logoDieseBemol.png');
//        }


        // 2) Hydrater l'objet Track (avec ce qui est rentré dans le formulaire, le reste ci-dessous)
        $newTrackForm->handleRequest($request);

//        'motclefchoisi' => $motclefchoisi

//        $motclefchoisi = $newTrackForm->get('trackMotclef')->getData();
//
//        /* @var $motclefRepository MotClefRepository */   // ??? Pas besoin ???
//        $motclefRepository = $this->getDoctrine()->getRepository(MotClef::class);
//        $motclef = $motclefRepository->findOneBy( array ( 'mot' => 'cordes' ));
//        $track->setTrackMotclef($motclef);

//        $article->setCategorie($form->get('categorie")->getData();


//        $track->setActif(1); Déjà dans le constructeur !!
        // On ajoute le User qui est authentifié
        $track->setUser($this->getUser());

        // Puis les champ $num et $id1

        // On récupère le paramètre num dans l'URL
        $num = $request->get('num');
        $track->setNum($num);            // Nombre de sons en plus (0 = juste 1 son, 1 = son +1 ...)

        // On récupère le paramètre id1 dans l'URL (l'id du track sans le son en plus)
        $id1 = $request->get('id1');

        // On recherche le track qui a cet id
        $trackRepository = $this->getDoctrine()->getRepository('AppBundle:Track');
        $trackWithId1 = $trackRepository->find($id1);

        if ($num > 0) {
            $track->setId1($trackWithId1);           // Sinon NULL donc non relié à un track (compo de base)
        }

        // 3) Validation du Form
        if ($newTrackForm->isSubmitted() && $newTrackForm->isValid()) {

            $trackDatas = $newTrackForm->getData();

            // Les mots clef
//            $track->addMotsclef();


            // FICHIERS : When the form is submitted, the attachment field will be an instance of UploadedFile.
            // It can be used to move the attachment file to a permanent location.

            // FICHIER MUSIQUE : $fileSong contient la musique uploadée (stockée de manière temporaire)
            // @var Symfony\Component\HttpFoundation\File\UploadedFile $fileSong
            $fileSong = $trackDatas->getTrack();

            // Tester si $file est une instance de UploadedFile permet de savoir s'il s'agit d'un fichier qui vient d'être uploadé,
            // ou si il s'agit d'un fichier déjà stocké auparavant, qu'il ne faut donc pas modifier (si modif de track)
            if ($fileSong && $fileSong instanceof UploadedFile) {
                // Generer un nom unique pour le fichier avec son extension
                $fileName = md5(uniqid()) . '.' . $fileSong->guessExtension();
//                $fileName = md5(uniqid());

                // Déplacer le fichier temporaire dans le dossier prévu au stockage des images de track
                $fileSong->move(
                    $this->getParameter('track_directory'), $fileName
                );
                // Mettre à jour l'attribut fileName de l'entité Track avec le nouveau nom du fichier
                $trackDatas->setTrack($fileName);
            }


            // FICHIER IMAGE : fileImg contient l'image uploadée (stockée de manière temporaire)
            // @var Symfony\Component\HttpFoundation\File\UploadedFile $fileImg
            $fileImg = $trackDatas->getImage();

            // Tester si $file est une instance de UploadedFile permet de savoir s'il s'agit d'un fichier qui vient d'être uploadé, ou si il s'agit d'un fichier déjà stocké auparavant, qu'il ne faut donc pas modifier (si modif de track)
            if ($fileImg && $fileImg instanceof UploadedFile) {
                // Generer un nom unique pour le fichier avec son extension
                $fileName = md5(uniqid()) . '.' . $fileImg->guessExtension();

                // Déplacer le fichier temporaire dans le dossier prévu au stockage des images de profile
                $fileImg->move(
//                    'audio/'.$fileName
                    $this->getParameter('img_track_directory'), $fileName
                );
                // Mettre à jour l'attribut fileName de l'entité Track avec le nouveau nom du fichier
                $trackDatas->setImage($fileName);
            }


            // Si l'entité est nouvelle et que $file est vide, on supprime l'entité de la collection et on ajoute un message d'erreur
//            elseif ($profileImage->getId() === null) {
//                $user->removeProfileImage($profileImage);
//                $profileImageForm->get('fileName')->addError(new \Symfony\Component\Form\FormError('Aucune image sélectionnée'));
//            }
            // Le Track est déjà existant en base, mais $file est null car aucun fichier n'a été soumis. On ne veut surtout pas se retrouver avec un null comme nom de fichier dans la base, donc on réinitialise $profileImage à sa valeur initiale
//            elseif(isset($profileImagesFileNames[$profileImage->getId()])){
//                $profileImage->setFileName($profileImagesFileNames[$profileImage->getId()]);
//            }

            // Sauvegarder le track ds la BDD
            $em->persist($track);
            $em->flush();
            $this->addFlash('success', 'Votre piste de musique a bien été ajoutée ! Vous pouvez laisser un commentaire en dessous et suivre les commentaires des autres.');

//            if (app.request.get('_route') == 'seetrack'){
//
//            }
//            return $this->redirectToRoute('seetrack/...');


//            return $this->render(
//                '/Track/seetrack.html.twig', array(
//                    'track1' => $track,
//                )
//
//            );
//
//            return $this->redirectToRoute('seetrack', array(
//                'id' => $track.getId(),
//            ));

            return $this->redirectToRoute('home');
        }


        return $this->render(
            '/Track/newtrack.html.twig', array('newTrackForm' => $newTrackForm->createView(),
                'request' => $request,
//                'motclefchoisi' => $motclefchoisi
            )
        );

    }


//
//$em = $this->getDoctrine()->getEntityManager();
//$doc = $em->find('MonBundle:Document',$id);
//$fichier = $doc->getPath();
//
//$response = new Response();
//$response->setStatusCode(200);
//$response->headers->set('Content-Type', "application/$format");
//$response->headers->set('Content-Disposition', sprintf('attachment;filename="%s"', $fichier, $format));
//$response->setCharset('UTF-8');
//
//    // prints the HTTP headers followed by the content
//$response->send();
//return $response;

    /**
     * Quand un membre veut télécharger un track
     * Accessible aux membres connectés
     * @Route("/download/{id}", name="download"), requirements={"id" = "\d+"}, defaults={"id" = null})
     * @param Track|null $track
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadAction(Track $track = null)
    {
        if (!$track) {
            throw $this->createNotFoundException("Cet piste de musique n'existe pas");
        }


        //$id = $track->getId();
        $nametrack = $track->getTrack();

        //$pdfPath = $this->getParameter('dir.downloads').'/piano.mp3';

        //$pdfPath = $this->getParameter('dir.downloads').'/ $nametrack;
        $pdfPath = 'audio/' . $nametrack;
//        $pdfPath = 'audio/'.$nametrack.'.mp3';

        return $this->file($pdfPath);

//        return $this->render('/User/ZZZdownload.html.twig', [
//            'id' => $id,
//            'nametrack' => $nametrack,
//            'pdfPath' => $pdfPath,
//        ]);
    }


    /**
     * Quand un membre veut supprimer un track
     * Seulement si aucun membre n'a uploadé de track+1 sur celui-ci
     *
     * @Route("/inactiverTrack/{id}", name="inactiverTrack"), requirements={"id" = "\d+"}, defaults={"id" = null})
     * @param Track|null $track
     * @param EntityManagerInterface $em
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function inactiverTrack(Track $track = null, EntityManagerInterface $em)
    {
        if (!$track) {
            throw $this->createNotFoundException("Cet piste de musique n'existe pas");
        }

        $track->setActif(0);

        // Sauvegarder le track mis à jour ds la BDD
        $em->persist($track);
        $em->flush();
        $this->addFlash('success', 'Le track a été supprimé (cela a été possible car aucun membre n\'a uploader de track +1 sur celui-ci.');

        return $this->redirectToRoute('user');
    }


}