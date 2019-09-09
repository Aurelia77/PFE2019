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
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function userAction(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator)
    {
        $trackRepository = $this->getDoctrine()->getRepository(Track::class);

        $user = $this->getUser();

        $pagination = $paginator->paginate(
// On rechercher les track qui ont été mis en ligne par l'utilisateur connecté, qui sont actifs et par ordre antéchronologique
            $trackRepository->findBy(array('actif' => 1, 'user' => $user), array('creationdate' => 'DESC')),   // La query que l'on veut paginer
            $request->query->getInt('page', 1),    // On récupère le numéro de la page et on le défini à 1 par défaut
            3                                             // Nombre d'éléments affichés par page
        );

        $user = $this->getUser();
        $userInfoForm = $this->createForm(UserInfoType::class, $user, array());
        $userInfoForm->handleRequest($request);

        if ($userInfoForm->isSubmitted() && $userInfoForm->isValid()) {
            $em->flush();
            $this->addFlash(
                "success", "Modifications enregistrées avec succès"
            );
        }

        $img_track_directory = $this->getParameter('img_track_directory');
        $img_user_directory = $this->getParameter('img_user_directory');
        $track_directory = $this->getParameter('track_directory');

        $tracks = $trackRepository->findAll();

        return $this->render('/User/user.html.twig', array(
            'user' => $user,
            'tracks' => $tracks,
            'userInfoForm' => $userInfoForm->createView(),
            'pagination' => $pagination,
            'img_track_directory' => $img_track_directory,
            'img_user_directory' => $img_user_directory,
            'track_directory' => $track_directory
        ));
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
//
            $userExists = $this->getDoctrine()->getRepository(User::class)->findOneBy(array('email' => $changeEmailForm->getData()->getEmailTemp()));

//            var_dump($request->request->get('emailTemp'));
            if($userExists == null){

//
//            $userRepository->findBy(array('email' => 1, 'user' => $user), array('creationdate' => 'DESC')),   // La query que l'on veut paginer
//

//            if ($userRepository->findOneByEmail($userMail);


            // On génère un token unique
            $emailToken = md5(uniqid());
            $user->setEmailToken($emailToken);
            // On sauvegarde l'utilisateur
            $em->flush();
            // On lui envoie un email de validation
            $userEmailService->sendValidationEmail($user);
            $this->addFlash(
                "success", "Un email de validation vous a été envoyé"
            );

            }else{
                $this->addFlash(
                    "warning", "Cet email est déjà utilisé."
                );
            }
        }

        return $this->render('/User/useremail.html.twig', array('changeEmailForm' => $changeEmailForm->createView()));
    }

    /**
     * Supprime les données personnelles d'un membre (mail, pseudo, nom, prénom, password) et ses commentaires laissés sur le site
     * Pour les admins et aussi pour le membre connecté qui veut désactiver son compte
     *
     * @Route("/suppDatasUser", name="suppDatasUser")
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function suppDatasUser(EntityManagerInterface $em)
    {
        $user = $this->getUser();

        $user->setEmail('#')
            ->setActif(0)
            ->setFirstName('#')
            ->setLastName('#')
            ->setPseudo('#')
            ->setPassword('#')
//            ->setPhoto('#')
            ->setRoles('#')
//            ->setEmailToken('#')
//            ->setLostPasswordToken('#')
        ;

        // On va chercher tous les messages pour pouvoir désactiver ceux de l'utilisateur connecté
//        $messageRepository = $this->getDoctrine()->getRepository(Message::class);
//        $messages = $messageRepository->findBy(array('user' => $user));
//
//        for ($message in $messages) {
//
//        }


        $em->persist($user);
        $this->addFlash('success', 'Plus aucune donnée personnelle sur l\'utilisateur !');

        $em->flush();

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/userprofil/{id}", name="users_profil", requirements={"id" = "\d+"}, defaults={"id" = null})
     * @param User $user
     * @param Request $request
     *
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function profileAction(User $user = null, Request $request, PaginatorInterface $paginator)
    {
        // User $user = null : pour que si l'id du User n'existe pas, ->returnNotFound(); qui est gérer ci-desous (page 404)
        if (!$user) {
            return $this->returnNotFound();
        }

        $trackRepository = $this->getDoctrine()->getRepository(Track::class);

        $pagination = $paginator->paginate(
// On rechercher les track qui ont été mis en ligne par l'utilisateur visualisé, qui sont actifs et par ordre antéchronologique
            $trackRepository->findBy(array('actif' => 1, 'user' => $user), array('creationdate' => 'DESC')),   // La query que l'on veut paginer
            $request->query->getInt('page', 1),    // On récupère le numéro de la page et on le défini à 1 par défaut
            3                                             // Nombre d'éléments affichés par page
        );

        $img_track_directory = $this->getParameter('img_track_directory');
        $img_user_directory = $this->getParameter('img_user_directory');
        $track_directory = $this->getParameter('track_directory');

        return $this->render('/User/userIdProfil.html.twig', array(
            'user' => $user,
            'pagination' => $pagination,
            'img_track_directory' => $img_track_directory,
            'img_user_directory' => $img_user_directory,
            'track_directory' => $track_directory
        ));
    }

    /**
     * Retourne une page 404
     *
     * @return Response
     */
    private function returnNotFound()
    {  //string $from = null){

//        return $this->json(array('status' => 'notFound'), JsonResponse::HTTP_NOT_FOUND);
//        return $this->json(array('status' => 'notFound', 'from' => $from), Response::HTTP_NOT_FOUND);
        return $this->render('404.html.twig');
    }

}
