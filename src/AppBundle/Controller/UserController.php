<?php
namespace AppBundle\Controller;

use AppBundle\Repository\UserRepository;
use AppBundle\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
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

// Note : il serait tout à fait possible de fusionner les 3 Actions (userAction, userPasswordAction, userEmailAction) en une seule
class UserController extends Controller
{

    /**
     * @Route("/private/user", name="user")
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
        
        return $this->render('/User/user.html.twig', array('userInfoForm' => $userInfoForm->createView(), 'nompage' => 'User:user'));
    }

    /**
     * Changer le mdp de l'utilisateur
     * 
     * @Route("/private/user/password", name="userpassword")
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
            // Encode the password (you could also do this via Doctrine listener)
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);            
            // On sauvegarde l'utilisateur
            $em->flush();
            $this->addFlash(
                "success", "Mot de passe changé avec succès"
            );
        }

        return $this->render('/User/userpassword.html.twig', array('changePasswordForm' => $changePasswordForm->createView(), 'nompage' => 'User:userPassword'));
    }

    /**
     * Changer l'email de l'utilisateur
     * 
     * @Route("/private/user/email", name="useremail")
     * 
     * @param Request $request
     * @param EntityManagerInterface $em     
     * @param UserEmailService $userEmailService
     * 
     * @return Response
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
}
