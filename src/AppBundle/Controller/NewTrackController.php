<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Track;
use AppBundle\Form\NewTrackType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;                // Car on utilise le paramètre : EntityManagerInterface $em
use AppBundle\Repository\UserRepository;

class NewTrackController extends Controller
{
    /**
     * Page qui permet d'importer un nouveau tracks via un formulaire
     * L'utilisateur doit être connecté !
     * @Route("/newtrack", name="newtrack")
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

        // 2) Hydrater l'objet Track (avec ce qui est rentré dans le formulaire)
        $newTrackForm->handleRequest($request);
        // Et on ajoute le User qui est authentifié
        $track->setUser($this->getUser());

        // 3) Validation du Form
        if ($newTrackForm->isSubmitted() && $newTrackForm->isValid()) {

            $trackDatas = $newTrackForm->getData();

            // FICHIER MUSIQUE : $fileSong contient la musique uploadée (stockée de manière temporaire)
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $fileSong */
            $fileSong = $trackDatas->getTrack();

            // Tester si $file est une instance de UploadedFile permet de savoir s'il s'agit d'un fichier qui vient d'être uploadé, ou si il s'agit d'un fichier déjà stocké auparavant, qu'il ne faut donc pas modifier (si modif de track)
            if ($fileSong && $fileSong instanceof UploadedFile) {
                // Generer un nom unique pour le fichier
                //$fileName = md5(uniqid()) . '.' . $file->guessExtension();
//                $fileName = md5(uniqid()) . '.mp3';
                $fileName = md5(uniqid());

                // Déplacer le fichier temporaire dans le dossier prévu au stockage des images de profile
                $fileSong->move(
//                    'audio/'.$fileName
                    $this->getParameter('track_directory'), $fileName.'.mp3'
                );
                // Mettre à jour l'attribut fileName de l'entité Track avec le nouveau nom du fichier
                $trackDatas->setTrack($fileName);
            }


            // FICHIER IMAGE : fileImg contient l'image uploadée (stockée de manière temporaire)
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $fileImg */
            $fileImg = $trackDatas->getImage();

            // Tester si $file est une instance de UploadedFile permet de savoir s'il s'agit d'un fichier qui vient d'être uploadé, ou si il s'agit d'un fichier déjà stocké auparavant, qu'il ne faut donc pas modifier (si modif de track)
            if ($fileImg && $fileImg instanceof UploadedFile) {
                // Generer un nom unique pour le fichier
                //$fileName = md5(uniqid()) . '.' . $file->guessExtension();
//                $fileName = md5(uniqid()) . '.mp3';
                $fileName = md5(uniqid());

                // Déplacer le fichier temporaire dans le dossier prévu au stockage des images de profile
                $fileImg->move(
//                    'audio/'.$fileName
                    $this->getParameter('img_directory'), $fileName.'.jpg'
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
            $this->addFlash('success', 'Votre piste de musique a bien été ajoutée !');


            return $this->redirectToRoute('home');
        }



        return $this->render(
            '/Track/newtrack.html.twig', array('newTrackForm' => $newTrackForm->createView(),
                'request' => $request)
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

}