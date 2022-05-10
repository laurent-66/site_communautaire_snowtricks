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

        $form = $this->createForm(RegistrationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newUser = $form->getData();

            $pseudoRegister = $newUser->getPseudo();
            $emailRegister = $newUser->getEmail();

            $passwordHashed = $this->passwordHasher->hashPassword($newUser, $newUser->getPassword());
            $newUser->setPassword($passwordHashed);
            $newUser->setPseudo($pseudoRegister);
            $newUser->setEmail($emailRegister);
            $newUser->setUrlPhoto('defaultProfil.jpg');
            $newUser->setAlternativeAttribute('Avatar par defaut');
            $newUser->setFixture(0);
            $this->entityManager->persist($newUser);
            $this->entityManager->flush();

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

            $objectUploadedFile = $form->get('urlPhotoFile')->getData();
            $updateUser = $form->getData();
            $updatePseudoUser = $updateUser->getPseudo();
            $updateEmailUser = $updateUser->getEmail();
            $urlPhotoUser = $updateUser->getUrlPhoto();
            $updateAttributeUser = $updateUser->getAlternativeAttribute();

            if( $objectUploadedFile ) {

                $fileNameUpload = $objectUploadedFile->getClientOriginalName();
                $extensionFile = $objectUploadedFile->guessExtension();

                $originalFilename = pathinfo($fileNameUpload, PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$extensionFile;

                try {
  
                    if ( $urlPhotoUser !== "defaultProfil.jpg") {

                        $pathUrlPhoto = $this->getParameter('images_profil_directory');
                        $filePath = $pathUrlPhoto."\\".$urlPhotoUser; 
                        unlink($filePath);

                        $objectUploadedFile->move(
                            $this->getParameter('images_profil_directory'),
                            $newFilename
                        );

                    } else if ($urlPhotoUser === "defaultProfil.jpg") {

                        $objectUploadedFile->move(
                            $this->getParameter('images_profil_directory'),
                            $newFilename
                        );

                    }

                    $updateUser->setUrlPhoto($newFilename);
                    $updateUser->setAlternativeAttribute($updateAttributeUser);
                    $this->entityManager->persist($updateUser);

                } catch (FileException $e) {
                    dump($e);
       
                }  

            } else {

                $updateUser->setUrlPhoto('defaultProfil.jpg');
                $updateUser->setAlternativeAttribute('Avatar par defaut');
                $this->entityManager->persist($updateUser); 

            }

            $updateUser->setPseudo($updatePseudoUser);
            $updateUser->setEmail($updateEmailUser);

            $this->entityManager->persist($updateUser);
            $this->entityManager->flush();

            return $this->redirectToRoute('homePage');
        }

        return $this->renderForm('core/auth/updateProfil.html.twig', ['form' => $form, 'user'=> $user]);
    }


    /**
     * delete user
     *
     * @param Request $request
     * @return void
     * 
     * @Route("/account/deleteProfile", name="deleteProfile")
     */
    // function deleteProfile(Request $request) {
    //     $userCurrent = $this->getUser();
    //     $this->entityManager->remove($userCurrent);
    //     $this->entityManager->flush();
    //     return $this->redirectToRoute('homePage');
    // }

}







