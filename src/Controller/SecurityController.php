<?php

namespace App\Controller;
use App\Entity\User;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\UpdateProfilType;
use App\Form\PasswordUpdateType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Notifier\CustomLoginLinkNotification;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Notifier\Recipient\RecipientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Http\LoginLink\LoginLinkNotification;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;

class SecurityController extends AbstractController
{
 /**
  *
  * @param Request $request
  * @return Response
  *
  * @Route("/account/register", name="registerPage")
  */
    public function register( Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;

        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        //renseigne l'instance $user des informations entrée dans le formulaire et envoyé dans la requête
        $form->handleRequest($request);
        $registerData = $form->getData();
        $pseudoRegister = $registerData->getPseudo();
        $emailRegister = $registerData->getEmail();
        $passwordRegister = $registerData->getPassword();
        $urlPhotoRegister = $registerData->getUrlPhoto();

        if ($form->isSubmitted() && $form->isValid()) {

            if(strlen(trim($urlPhotoRegister)) === 0 ) {
                $user->setUrlPhoto('defaultProfil.jpg');
            }
 
            //Hash du mot de passe
            $passwordHashed = $this->passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($passwordHashed);
            //Persister l'utilisateur
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            //Redirection
            return $this->redirectToRoute('homePage');
        }
        
        return $this->render('core/auth/register.html.twig', ['form' => $form->createView()]);
    } 

    /**
     * Undocumented function
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordHasherInterface $passwordHasher
     * @return Response
     * 
     * @Route("/account/login", name="login")
     */
    public function login( Request $request, EntityManagerInterface $entityManager, AuthenticationUtils $authenticationUtils)
    {
        $this->entityManager = $entityManager;

        $user = new User();
        $formLogin = $this->createForm(LoginType::class, $user);
        //renseigne l'instance $user des informations entrée dans le formulaire et envoyé dans la requête
        $formLogin->handleRequest($request);
        $loginData = $formLogin->getData();
        $emailLogin = $loginData->getEmail();
        $passwordLogin = $loginData->getPassword();


        if ($formLogin->isSubmitted() && $formLogin->isValid()) {

            // get the login error if there is one
            $error = $authenticationUtils->getLastAuthenticationError();

            // last username entered by the user
            $lastUsername = $authenticationUtils->getLastUsername();

            return $this->render('core/auth/login.html.twig', [
                'last_username' => $lastUsername,
                'error'         => $error,
            ]);

        }
        
        return $this->render('core/auth/login.html.twig', ['formLogin' => $formLogin->createView()]);
    } 


    /**
     * 
     * @return Response
     * 
     * @Route("/account/login", name="login")
     */
     function loginold(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('core/auth/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    /**
     * form mot de passe oublié: demande mail + Généraion du lien url de connexion + envoi message par mail avec 
     * 
     * @Route("/account/login_link", name="loginLink")
     */
    public function requestLoginLink(

        NotifierInterface $notifier,
        // RecipientInterface $recipient[],
        LoginLinkHandlerInterface $loginLinkHandler, 
        UserRepository $userRepository, 
        Request $request
        
        )
    {
        // check if login form is submitted
        if ($request->isMethod('POST')) {
            // load the user in some way (e.g. using the form input)
            $email = $request->request->get('email');
            $user = $userRepository->findOneBy(['email' => $email]);

            if($user) {

                // create a login link for $user this returns an instance
                // of LoginLinkDetails
                $loginLinkDetails = $loginLinkHandler->createLoginLink($user);

                // create a notification based on the login link details

                $notification = new LoginLinkNotification(
                    $loginLinkDetails,
                    'Bienvenue sur le site communautaire snowtrick !' // email subject
                );

                // create a recipient for this user
                $recipient = new Recipient($user->getEmail());

                // send the notification to the user
                $notifier->send($notification, $recipient);

                // render a "Login link is sent!" page
                
                return $this->render('core/security/login_link_sent.html.twig');
                // return $this->redirectToRoute('loginLink');

            } else {

                return $this->render('core/security/login_link_sent.html.twig');
                // return $this->redirectToRoute('loginLink');
            }

        }

        // if it's not submitted, render the "login" form
        return $this->render('core/security/login_link.html.twig');
    }

    /**
    * 
    * Authentificateur de lien de connexion
    * 
    * @Route("/account/login_check", name="login_check")
    */
    public function check(Request $request)
    {
        // get the login link query parameters
        // automatically returns these elements to the AuthenticationSuccessHandler service
        $expires = $request->query->get('expires');
        $username = $request->query->get('user');
        $hash = $request->query->get('hash');

        // and render a template with the button
        return $this->render('security/process_login_link.html.twig', [
            'expires' => $expires,
            'user' => $username,
            'hash' => $hash,
        ]);

    }


    /**
     * Reset user password
     *
     * @return Response
     * 
     * @Route("/account/resetPassword", name="resetPassword")
     */
    public function resetPassword(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher) {

        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;

        if($this->getUser()){

            $passwordUpdate = new PasswordUpdate();

            $formUpdatePassword = $this->createForm(PasswordUpdateType::class, $passwordUpdate);

            $formUpdatePassword->handleRequest($request);

            if($formUpdatePassword->isSubmitted() && $formUpdatePassword->isValid()) {
                $user = $this->getUser();
                $data = $formUpdatePassword->getData();
                $newPassword = $data->getNewPassword();

                //Hash du mot de passe
                $passwordHashed = $this->passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($passwordHashed);
                //Persister l'utilisateur
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                //Redirection
                return $this->redirectToRoute('homePage');
            }

            return $this->render('core/auth/updatePassword.html.twig', [ 'formUpdatePassword' => $formUpdatePassword->createView()]);
        }
        
            return $this->redirectToRoute('homePage');
    }


    /**
     * Update user profil
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordHasherInterface $passwordHasher
     * @return Response
     * 
     * @Route("/account/updateProfil", name="updatProfilPage")
     */
     function updateProfil( 
        Request $request,
        EntityManagerInterface $entityManager, 
        UserPasswordHasherInterface $passwordHasher, 
        SluggerInterface $slugger
        )
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;

        $user = $this->getUser();

        $form = $this->createForm(UpdateProfilType::class, $user);

        //renseigne l'instance $user des informations entrée dans le formulaire et envoyé dans la requête
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $profil = $form->getData();

            $profilImage = $form->get('url_photo')->getData();

            // upload and register the image profil

            if ($profilImage) {
                $originalFilename = pathinfo($profilImage->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$profilImage->guessExtension();

                // Move the file to the directory where images are stored
                try {

                    //deletion of the stored image
                    $currentUrlPhoto = $user->getUrlPhoto();
                    $pathUrlPhoto = $this->getParameter('images_profil_directory');
                    $filePath = $pathUrlPhoto."/".$currentUrlPhoto; 
                    unlink($filePath);

                    //Adding the image to store
                    $profilImage->move(
                        $this->getParameter('images_profil_directory'),
                        $newFilename
                    );

                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $profil->setUrlPhoto($newFilename);
            }
            //Persister l'utilisateur
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            //Redirection
            return $this->redirectToRoute('homePage');
        }

        return $this->render('core/auth/updateProfil.html.twig', ['form' => $form->createView(), 'user'=> $user]);
    }
}







