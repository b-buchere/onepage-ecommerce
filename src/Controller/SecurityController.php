<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\LoginType;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
    
    private $emailVerifier;
    private $csrfTokenManager;
    
    public function __construct(  CsrfTokenManagerInterface $csrfTokenManager){
        $this->csrfTokenManager = $csrfTokenManager;
    }
    
    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils, Request $request ): Response
    {
        
        $user=new Utilisateur();
        $formLogin = $this->createForm(LoginType::class, $user, ['attr'=>array('id'=>"member-login")]);

        $lastUsername = '';
        $loginerror = '';

        if($this->getUser()) {
            return $this->redirectToRoute('home');
        }        

        $loginerror = $authenticationUtils->getLastAuthenticationError();
        
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->renderForm('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $loginerror,
            'formLogin'=>$formLogin,
            'dark_body_bg'=>true
        ]);
    }
    
    
    /**
     * @Route("/logout", name="logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
    
    #[Route('/verify/email', name: 'verify_email')]
    public function verifyUserEmail(Request $request, EntityManagerInterface $manager): Response
    {
        $id = $request->get('id');        
        
        if (null === $id) {
            return $this->redirectToRoute('register');
        }
        
        $repository = $manager->getRepository(Users::class);
        $user = $repository->find($id);
        
        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }
        
        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());
            
            return $this->redirectToRoute('register');
        }
        
        $this->addFlash('success', 'Your email address has been verified.');
        
        return $this->redirectToRoute('register');
    }
        
}
