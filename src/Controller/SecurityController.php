<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Notification\CreateAccountNotification;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @var CreateAccountNotification
     */
    private $createAccountNotify;

    public function __construct(CreateAccountNotification $createAccountNotify)
    {
        $this->createAccountNotify = $createAccountNotify;
    }

    /**
     *@Route("/registration", name="security_registration")
     */
    public function registration(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder): Response
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $hash = $encoder->encodePassword($user, $user->getPassword());

            $user->setActivationToken(md5(uniqid()));

            $user->setPassword($hash);
            $manager->persist($user);
            $manager->flush();
            
            $this->createAccountNotify->notifyCreateAccountForAdmin();
            $this->createAccountNotify->notifyCreateAccountForUser($user);

            $this->addFlash('success', 'Votre compte a bien était créé, Connectez-vous !');
            return $this->redirectToRoute('security_login');
        }
        return $this->render('security/registration.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/activation/{token}", name="activation")
     */
    public function activation($token, UserRepository $userRepo, EntityManagerInterface $manager)
    {
        //vérifie si un utilisateur a ce token
        $user = $userRepo->findOneBy(['activation_token' => $token]);

        if(!$user){// 0 user
            throw $this->createNotFoundException('cet utilisateur n\'existe pas');
        }

        $user->setActivationToken(null);//supprime le token
        $manager->persist($user);
        $manager->flush();
        
        $this->addFlash('success', 'votre compte est bien activer !');
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/login", name="security_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
             return $this->redirectToRoute('product_index');
         }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername, 
            'error' => $error
        ]);
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout()
    {
        //throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
