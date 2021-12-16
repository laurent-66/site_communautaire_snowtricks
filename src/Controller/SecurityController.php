<?php

namespace App\Controller;
use App\Entity\User;
use App\Form\RegistrationType;
use App\Form\UpdateProfilType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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

        if($form->isSubmitted() && $form->isValid() && $form->getConfig()->getMethod() === 'POST') {

            //Hash du mot de passe
            $passwordHashed = $this->passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($passwordHashed);
            //Persister l'utilisateur
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            //Redirection
            return $this->redirectToRoute('homePage');
        }else{
            $error = "Veuillez renseigner tout les champs";
        }

        return $this->render('core/auth/register.html.twig', ['form' => $form->createView(), 'error'=> $error]);
    }

    /**
     * 
     * @return Response
     * 
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
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
     * Undocumented function
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordHasherInterface $passwordHasher
     * @return Response
     * 
     * @Route("/updateProfil", name="updatProfilPage")
     */
    public function updateProfil( Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;

        $user = new User();
        $form = $this->createForm(UpdateProfilType::class, $user);
        //renseigne l'instance $user des informations entrée dans le formulaire et envoyé dans la requête
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            //Hash du mot de passe
            $passwordHashed = $this->passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($passwordHashed);
            //Persister l'utilisateur
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            //Redirection
            return $this->redirectToRoute('homePage');
        }else{
            $error = "Veuillez renseigner tout les champs";
        }

        return $this->render('core/auth/updateProfil.html.twig', ['form' => $form->createView(), 'error'=> $error]);
    }










}