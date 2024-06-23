<?php

namespace App\Controller;

use App\Entity\Uilisateur;
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
use App\Entity\Utilisateur;

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
        $formLogin = $this->createForm(LoginType::class, $user, ['attr'=>array('id'=>"member-login"), 'action'=>'/loginRegister']);

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
     * @Route("/register", name="register")
     * @Route("/{_locale}/register", defaults={"_locale"="fr_FR"}, requirements={"_locale"="en_GB|fr_FR"})
     */
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface): Response
    {
        
        $user=new Users();
        $formRegister = $this->createForm(RegisterType::class, $user, ['attr'=>array('id'=>"member-registration"), 'action'=>'/register']);
        $formRegister->handleRequest($request);

        if ($formRegister->isSubmitted() && $formRegister->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasherInterface->hashPassword(
                    $user,
                    $formRegister->get('password')->getData()
                )
            );
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            
            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('verify_email', $user,
                (new TemplatedEmail())
                ->from(new Address('support@thestreamshop.net', 'Thestreamshop'))
                ->to($user->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            // do anything else you need here, like send an email
            $this->addFlash('success', 'register.emailverification');
            return $this->redirectToRoute('loginRegister');
        }
        return $this->render('security/register.html.twig', [
            'formRegister' => $formRegister->createView(),
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
