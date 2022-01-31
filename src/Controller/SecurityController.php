<?php

namespace App\Controller;
use App\Entity\User;
use App\Form\RegistrationType;
use App\Form\UpdateProfilType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
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
  * @Route("/register", name="registerPage")
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

        if(
            strlen(trim($pseudoRegister)) === 0 ||
            strlen(trim($emailRegister)) === 0 ||
            strlen(trim($passwordRegister)) === 0
        ) {

            $error = 'Tout les champs sont requis.';
        
        }  else if ($form->isSubmitted() && $form->isValid()) {


            if(strlen(trim($urlPhotoRegister)) === 0 ) {
                $user->setUrlPhoto('/images/mute-grab.JPG');
            }
 
                    //Hash du mot de passe
                    $passwordHashed = $this->passwordHasher->hashPassword($user, $user->getPassword());
                    $user->setPassword($passwordHashed);
                    //Persister l'utilisateur
                    $this->entityManager->persist($user);
                    $this->entityManager->flush();
                    //Redirection
                    return $this->redirectToRoute('homePage');

            } else {
                $error = "Tout les champs sont requis";
            } 
            return $this->render('core/auth/register.html.twig', ['form' => $form->createView(), 'error'=> $error]);
        }

    /**
     * 
     * @return Response
     * 
     * @Route("/login", name="login")
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
    * @Route("/login_check", name="login_check")
    */
    public function check()
    {
        throw new \LogicException('This code should never be reached');
    }


    /**
     * @Route("/login_link", name="loginLink")
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



            //intégrer ici le générateur de token

            // create a login link for $user this returns an instance
            // of LoginLinkDetails
            $loginLinkDetails = $loginLinkHandler->createLoginLink($user);
            $loginLink = $loginLinkDetails->getUrl();


            dump($loginLink);
            exit;

            // create a notification based on the login link details


            //ici utilisé l'objet personnalisé  CustomLoginNotification


            $notification = new LoginLinkNotification(
                $loginLinkDetails,
                'Welcome to MY WEBSITE!' // email subject
            );
            // create a recipient for this user
            $this->recipient = new Recipient($user->getEmail());

            // send the notification to the user
            $notifier->send($notification, $this->recipient);

            // render a "Login link is sent!" page
            return $this->render('core/security/login_link_sent.html.twig');

        }

        // if it's not submitted, render the "login" form
        return $this->render('core/security/login_link.html.twig');
    }



    /**
     * 
     * @return Response
     * 
     * @Route("/generateToken", name="generateToken")
     */
    function generateToken( Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager )
    {
        $this->entityManager = $entityManager;
        $parametersBag = $request->request;
        $email = $parametersBag->get("email");

        $userEmailregistered = $userRepository->findOneByEmail($email);

        if($userEmailregistered) {

            $token = md5(uniqId());
            $userEmailregistered->setLastPasswordToken($token);
            $this->entityManager->persist($userEmailregistered);
            $this->entityManager->flush();

            $urlUpdatePassword = "localhost:8000/compte/update-password/".$token;

            dump($urlUpdatePassword);
            exit;

        } else {
            echo "faux";
            echo "flash: Votre demande à bien été prise en compte veuillez consulter votre boite mail";
        }

    }



    /**
     * Modification du mot de passe de l'utilisateur
     *
     * @return Response
     * 
     * @Route("/compte/update-password/{token}", name="updatePassword")
     */
    public function updatePassword(
        $token,
        Request $request,
        UserRepository $userRepository,
        AuthenticationUtils $authenticationUtils

    ) {
        dump($request);
        $user = $this->getUser();
        dump($user);
        exit;
        $userEmailregistered = $userRepository->findOneByLastPasswordToken($token);

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        dump($userEmailregistered);
        exit;


        return $this->render('updatePassword.html.twig');

    }












    /**
     * Undocumented function
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordHasherInterface $passwordHasher
     * @return Response
     * 
     * @Route("/updateProfil", name="updatProfilPage")
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
        }else{
            $error = "Veuillez renseigner tout les champs";
        }

        return $this->render('core/auth/updateProfil.html.twig', ['form' => $form->createView(), 'user'=> $user, 'error'=> $error]);
    }
}