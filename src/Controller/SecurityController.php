<?php

namespace App\Controller;
use App\Entity\User;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\UpdateProfilType;
use App\Form\PasswordUpdateType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Http\LoginLink\LoginLinkNotification;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;

class SecurityController extends AbstractController
{

    private $entityManager;
    private $passwordhasher;

    public function __construct(

        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    )
    {
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
    }


 /**
  *
  * @param Request $request
  * @return Response
  *
  * @Route("/account/register", name="registerPage")
  */
    public function register(Request $request)
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
        $registerData = $form->getData();
        $pseudoRegister = $registerData->getPseudo();
        $emailRegister = $registerData->getEmail();
        $passwordRegister = $registerData->getPassword();
        $urlPhotoRegister = $registerData->getUrlPhoto();

        if ($form->isSubmitted() && $form->isValid()) {

            // if(strlen(trim($urlPhotoRegister)) === 0 ) {
            //     $user->setUrlPhoto('defaultProfil.jpg');
            // }  
 
            $passwordHashed = $this->passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($passwordHashed);
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->redirectToRoute('homePage');
        }
        
        return $this->render('core/auth/register.html.twig', ['form' => $form->createView()]);
    } 

    /**
     * 
     * @return Response
     * 
     * @Route("/account/login", name="login")
     */
     function login(AuthenticationUtils $authenticationUtils): Response
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
     * form password forget: request mail + Generation of the link url of connection + sending message by mail
     * 
     * @Route("/account/login_link", name="loginLink")
     */
    public function requestLoginLink(

        NotifierInterface $notifier,
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

                $loginLinkDetails = $loginLinkHandler->createLoginLink($user);

                $notification = new LoginLinkNotification(
                    $loginLinkDetails,
                    'Bienvenue sur le site communautaire snowtrick !' // email subject
                );

                $recipient = new Recipient($user->getEmail());

                $notifier->send($notification, $recipient);
                
                return $this->render('core/security/login_link_sent.html.twig');


            } else {

                return $this->render('core/security/login_link_sent.html.twig');
            }
        }

        return $this->render('core/security/login_link.html.twig');
    }



    /**
    * 
    * Connection link authenticator
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
     * @param Request $request
     * @return Response
     * 
     * @Route("/account/resetPassword", name="resetPassword")
     */
    public function resetPassword(Request $request) {

        if($this->getUser()){

            $passwordUpdate = new PasswordUpdate();

            $formUpdatePassword = $this->createForm(PasswordUpdateType::class, $passwordUpdate);

            $formUpdatePassword->handleRequest($request);

            if($formUpdatePassword->isSubmitted() && $formUpdatePassword->isValid()) {
                $user = $this->getUser();
                $data = $formUpdatePassword->getData();
                $newPassword = $data->getNewPassword();
                $passwordHashed = $this->passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($passwordHashed);
                $this->entityManager->persist($user);
                $this->entityManager->flush();

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
     * @return Response
     * 
     * @Route("/account/updateProfil", name="updatProfilPage")
     */
     function updateProfil( Request $request, SluggerInterface $slugger)
    {

        $user = $this->getUser();

        $form = $this->createForm(UpdateProfilType::class, $user );

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $profil = $form->getData();

            $profilImage = $profil->getUrlPhoto();


                $originalFilename = pathinfo($profilImage->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$profilImage->guessExtension();

                //delete and add physical storage image
                try {

                    $currentUrlPhoto = $user->getUrlPhoto();
                    $pathUrlPhoto = $this->getParameter('images_profil_directory');
                    $filePath = $pathUrlPhoto."/".$currentUrlPhoto; 
                    unlink($filePath);

                    $profilImage->move(
                        $this->getParameter('images_profil_directory'),
                        $newFilename
                    );


                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

            $user->setUrlPhoto($newFilename);
            dump($user);
            exit;
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->redirectToRoute('homePage');
        }

        return $this->renderForm('core/auth/updateProfil.html.twig', ['form' => $form, 'user'=> $user]);
    }
}







