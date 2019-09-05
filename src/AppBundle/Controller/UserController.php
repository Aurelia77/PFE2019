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

        $pagination = $paginator->paginate(
            $trackRepository->findBy(array(), array('creationdate' => 'DESC')),   // La query que l'on veut paginer
            $request->query->getInt('page', 1),    // On récupère le numéro de la page et on le défini à 1 par défaut
            3                                            // Nombre d'éléments affichés par page
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

        return $this->render('/User/user.html.twig', array(
            'user' => $user,
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
     * @Route("/{id}", name="users_profil", requirements={"id" = "\d+"}, defaults={"id" = null})
     * @param User $user
     * @param EntityManagerInterface $em
     * @param Request $request
     *
     * @return Response
     */
    public function profileAction(User $user = null, EntityManagerInterface $em, Request $request, PaginatorInterface $paginator)
    {
        if (!$user) {
            return $this->returnNotFound();
        }

        $trackRepository = $this->getDoctrine()->getRepository(Track::class);

        $pagination = $paginator->paginate(
            $trackRepository->findBy(array(), array('creationdate' => 'DESC')),   // La query que l'on veut paginer
            $request->query->getInt('page', 1),    // On récupère le numéro de la page et on le défini à 1 par défaut
            3                                             // Nombre d'éléments affichés par page
        );

        $img_track_directory = $this->getParameter('img_track_directory');
        $img_user_directory = $this->getParameter('img_user_directory');
        $track_directory = $this->getParameter('track_directory');

        return $this->render('user/userIdProfil.html.twig', array(
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
    private function returnNotFound() {  //string $from = null){

//        return $this->json(array('status' => 'notFound'), JsonResponse::HTTP_NOT_FOUND);
//        return $this->json(array('status' => 'notFound', 'from' => $from), Response::HTTP_NOT_FOUND);
        return $this->render('404.html.twig');
    }

}
