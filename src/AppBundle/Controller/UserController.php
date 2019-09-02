<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Track;
use AppBundle\Repository\UserRepository;
use AppBundle\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\EntityManagerInterface;
// Forms
use AppBundle\Form\ChangePasswordType;
use AppBundle\Form\ChangeEmailType;
use AppBundle\Form\UserInfoType;
// Services
use AppBundle\Service\Security\UserEmailService;

// Note : il serait tout à fait possible de fusionner les 3 Actions (userAction, userPasswordAction, userEmailAction) en une seule !!!???

/**
 * @Route("/private/user")
 */
class UserController extends Controller
{

    /**
     * Affiche les infos du membre
     * @Route("/", name="user")
     * 
     * @param Request $request
     * @param EntityManagerInterface $em
     * 
     * @return Response
     */
    public function userAction(Request $request, EntityManagerInterface $em)
    {
        $user = $this->getUser();
        $userInfoForm = $this->createForm(UserInfoType::class, $user, array());
        $userInfoForm->handleRequest($request);

        if ($userInfoForm->isSubmitted() && $userInfoForm->isValid()) {
            $em->flush();
            $this->addFlash(
                "success", "Modifications enregistrées avec succès"
            );
        }        

        $img_user_directory = $this->getParameter('img_user_directory');

        return $this->render('/User/user.html.twig', array(
            'userInfoForm' => $userInfoForm->createView(),
            'img_user_directory' => $img_user_directory
        ));
    }

    /**
     * Affiche les tracks que le membre a mis sur le site
     * @Route("/tarcks", name="usertracks")
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     *
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function userTracksAction(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator)
    {

//        if (!$user) {
//            throw $this->createNotFoundException("Cet utilisateur n'existe pas");
//        }
//
//        // On va stocker les fileNames des images de profil existantes avant que $user soit hydraté par le formulaire
//        $profileImagesFileNames = [];
//        foreach ($user->getProfileImages() as $profileImage){
//            $profileImagesFileNames[$profileImage->getId()] = $profileImage->getFileName();
//        }
//        // Création du form
//        $formUser = $this->createForm(\AppBundle\Form\UserType::class, $user);
//        // Hydratation de $user depuis la requete
//        $formUser->handleRequest($request);
//
//        // Validation du form
//        if ($formUser->isSubmitted() && $formUser->isValid()) {
//            // AJOUT & UPDATE DES IMAGES (idéalement, déplacer cette fonctionnalité dans un service)
//            // NOTE : Grâce à orphanRemoval=true sur la relation user->profileImages, la suppression va se faire automatiquement
//            foreach ($formUser->get('profileImages') as $profileImageForm) {
//                $profileImage = $profileImageForm->getData();
//                // $file contient le fichier uploadé (stocké de manière temporaire)
//                /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
//                $file = $profileImage->getFileName();
//                // Tester si $file est une instance de UploadedFile permet de savoir s'il s'agit d'un fichier qui vient d'être uploadé, ou si il s'agit d'un fichier déjà stocké auparavant, qu'il ne faut donc pas modifier
//                if ($file && $file instanceof UploadedFile) {
//                    // Generer un nom unique pour le fichier
//                    $fileName = md5(uniqid()) . '.' . $file->guessExtension();
//                    // Déplacer le fichier temporaire dans le dossier prévu au stockage des images de profile
//                    $file->move(
//                        $this->getParameter('profile_images_directory'), $fileName
//                    );
//                    // Mettre à jour l'attribut fileName de l'entité ProfileImage avec le nouveau nom du fichier
//                    $profileImage->setFileName($fileName);
//                }
//                // Si l'entité est nouvelle et que $file est vide, on supprime l'entité de la collection et on ajoute un message d'erreur
//                elseif ($profileImage->getId() === null) {
//                    $user->removeProfileImage($profileImage);
//                    $profileImageForm->get('fileName')->addError(new \Symfony\Component\Form\FormError('Aucune image sélectionnée'));
//                }
//                // L'image est déjà existante en base, mais $file est null car aucun fichier n'a été soumis. On ne veut surtout pas se retrouver avec un null comme nom de fichier dans la base, donc on réinitialise $profileImage à sa valeur initiale
//                elseif(isset($profileImagesFileNames[$profileImage->getId()])){
//                    $profileImage->setFileName($profileImagesFileNames[$profileImage->getId()]);
//                }
//            }
//
//            // Modification de l'utilisateur
//            $em->flush();
//            $this->addFlash('success', 'Utilisateur modifié avec succès');
//        }
//
//        return $this->render('user/pages/profile.html.twig', array(
//            'user' => $user,
//            'groupes' => $em->getRepository(Groupe::class)->findAll(),
//            'baseUrlToSendMail' => $this->generateUrl('users_mail'),
//            'formUser' => $formUser->createView(),
//        ));


        // On va chercher tous les track de la BDD

        //$userRepository = $em->getRepository(User::class);        OK (en ajoutant $em ds les param) mais on peut faire sans !!! :
        $trackRepository = $this->getDoctrine()->getRepository(Track::class);

        $pagination = $paginator->paginate(
            $trackRepository->findAll(),                        // La query que l'on veut paginer
            $request->query->getInt('page', 1),    // On récupère le numéro de la page et on le défini à 1 par défaut
            6                                             // Nombre d'éléments affichés par page
        );

        $img_track_directory = $this->getParameter('img_track_directory');
        $track_directory = $this->getParameter('track_directory');


        // On va chercher les infos sur le membre connecté

//        $user = $this->getUser();
//        $userInfoForm = $this->createForm(UserInfoType::class, $user, array());
//        $userInfoForm->handleRequest($request);

//        if ($userInfoForm->isSubmitted() && $userInfoForm->isValid()) {
//            $em->flush();
//            $this->addFlash(
//                "success", "Modifications enregistrées avec succès"
//            );
//        }

//        $img_user_directory = $this->getParameter('img_user_directory');

        return $this->render('/User/usertracks.html.twig', [
            'pagination' => $pagination,
            'img_track_directory' => $img_track_directory,
            'track_directory' => $track_directory
        ]);
    }

    /**
     * Changer le mdp du membre
     * @Route("/password", name="userpassword")
     * 
     * @param Request $request
     * @param EntityManagerInterface $em     
     * @param UserPasswordEncoderInterface $passwordEncoder
     * 
     * @return Response
     */
    public function userPasswordAction(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = $this->getUser();

        $changePasswordForm = $this->createForm(ChangePasswordType::class, $user, array());
        $changePasswordForm->handleRequest($request);

        if ($changePasswordForm->isSubmitted() && $changePasswordForm->isValid()) {

            // Encode le password (you could also do this via Doctrine listener)
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());

            $user->setPassword($password);

            // On sauvegarde l'utilisateur
            $em->flush();
            $this->addFlash(
                "success", "Mot de passe changé avec succès"
            );
        }

        return $this->render('/User/userpassword.html.twig', array('changePasswordForm' => $changePasswordForm->createView()));
    }

    /**
     * Changer l'email de l'utilisateur
     * @Route("/email", name="useremail")
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param UserEmailService $userEmailService
     *
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function userEmailAction(Request $request, EntityManagerInterface $em, UserEmailService $userEmailService)
    {
        $user = $this->getUser();
        $changeEmailForm = $this->createForm(ChangeEmailType::class, $user, array());
        $changeEmailForm->handleRequest($request);
        if ($changeEmailForm->isSubmitted() && $changeEmailForm->isValid()) {
            // On génère un token unique
            $emailToken = md5(uniqid());
            $user->setEmailToken($emailToken);
            // On sauvegarde l'utilisateur
            $em->flush();
            // On lui envoie un email de validation
            $userEmailService->sendValidationEmail($user);
            $this->addFlash(
                "warning", "Un email de validation vous a été envoyé"
            );
        }
        
        return $this->render('/User/useremail.html.twig', array('changeEmailForm' => $changeEmailForm->createView(), 'nompage' => 'User:userEmail'));
    }

    /**
     * @Route("/{id}", name="users_profile", requirements={"id" = "\d+"}, defaults={"id" = null})
     * @param User $user
     * @param EntityManagerInterface $em
     * @param Request $request
     *
     * @return Response
     */
    public function profileAction(User $user = null, EntityManagerInterface $em, Request $request)
    {
        if (!$user) {
            throw $this->createNotFoundException("Cet utilisateur n'existe pas");
        }

//        // On va stocker les fileNames des images de profil existantes avant que $user soit hydraté par le formulaire
//        $profileImagesFileNames = [];
//        foreach ($user->getProfileImages() as $profileImage){
//            $profileImagesFileNames[$profileImage->getId()] = $profileImage->getFileName();
//        }

        // Création du form
        $formUser = $this->createForm(\AppBundle\Form\UserType::class, $user);
        // Hydratation de $user depuis la requete
        $formUser->handleRequest($request);

        // Validation du form
//        if ($formUser->isSubmitted() && $formUser->isValid()) {
//            // AJOUT & UPDATE DES IMAGES (idéalement, déplacer cette fonctionnalité dans un service)
//            // NOTE : Grâce à orphanRemoval=true sur la relation user->profileImages, la suppression va se faire automatiquement
//            foreach ($formUser->get('profileImages') as $profileImageForm) {
//                $profileImage = $profileImageForm->getData();
//                // $file contient le fichier uploadé (stocké de manière temporaire)
//                /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
//                $file = $profileImage->getFileName();
//                // Tester si $file est une instance de UploadedFile permet de savoir s'il s'agit d'un fichier qui vient d'être uploadé, ou si il s'agit d'un fichier déjà stocké auparavant, qu'il ne faut donc pas modifier
//                if ($file && $file instanceof UploadedFile) {
//                    // Generer un nom unique pour le fichier
//                    $fileName = md5(uniqid()) . '.' . $file->guessExtension();
//                    // Déplacer le fichier temporaire dans le dossier prévu au stockage des images de profile
//                    $file->move(
//                        $this->getParameter('profile_images_directory'), $fileName
//                    );
//                    // Mettre à jour l'attribut fileName de l'entité ProfileImage avec le nouveau nom du fichier
//                    $profileImage->setFileName($fileName);
//                }
//                // Si l'entité est nouvelle et que $file est vide, on supprime l'entité de la collection et on ajoute un message d'erreur
//                elseif ($profileImage->getId() === null) {
//                    $user->removeProfileImage($profileImage);
//                    $profileImageForm->get('fileName')->addError(new \Symfony\Component\Form\FormError('Aucune image sélectionnée'));
//                }
//                // L'image est déjà existante en base, mais $file est null car aucun fichier n'a été soumis. On ne veut surtout pas se retrouver avec un null comme nom de fichier dans la base, donc on réinitialise $profileImage à sa valeur initiale
//                elseif(isset($profileImagesFileNames[$profileImage->getId()])){
//                    $profileImage->setFileName($profileImagesFileNames[$profileImage->getId()]);
//                }
//            }
//
//            // Modification de l'utilisateur
//            $em->flush();
//            $this->addFlash('success', 'Utilisateur modifié avec succès');
//        }

        return $this->render('user/pages/profile.html.twig', array(
            'user' => $user,
            'groupes' => $em->getRepository(Groupe::class)->findAll(),
            'baseUrlToSendMail' => $this->generateUrl('users_mail'),
            'formUser' => $formUser->createView(),
        ));
    }

}
