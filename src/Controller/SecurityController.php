<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/register", name="registerPage")
     */
    public function register( Request $request)
    {
        dump($request);
        exit;
        $error = '';

        // if ($request->getMethod() === 'POST') {
        //     $dataSubmitted = $request->getParsedBody();

        //     if (
        //         (strlen(trim($dataSubmitted['email']))) === 0 ||
        //         (strlen(trim($dataSubmitted['pseudo']))) === 0 ||
        //         (strlen(trim($dataSubmitted['inputPassword']))) === 0 ||
        //         (strlen(trim($dataSubmitted['confirmPassword']))) === 0
        //     ) {
        //         $error = 'Tout les champs sont requis.';
        //     } elseif (
        //         strlen(trim($dataSubmitted['inputPassword']))
        //         !== strlen(trim($dataSubmitted['confirmPassword']))
        //     ) {
        //         $error = 'Le mot de passe et la confirmation sont différents.';
        //     } else {
        //         $passwordHash = password_hash($dataSubmitted['inputPassword'], PASSWORD_DEFAULT);
        //         $this->userRepository->registerUser($dataSubmitted['pseudo'], $dataSubmitted['email'], $passwordHash);
        //     }
        // }





        // ... e.g. get the user data from a registration form

        // $user = new User();
        // $plaintextPassword = "";
        
        // hash the password (based on the security.yaml config for the $user class)

        // $hashedPassword = $passwordHasher->hashPassword(
        //     $user,
        //     $plaintextPassword
        // );
        //     $user->setPassword($hashedPassword);
    


        return $this->render('core/auth/register.html.twig', ['error' => $error]);

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
     * @Route("/disconnect", name="disconnectPage", methods={"get"})
    */
    public function disconnect()
    {
        exit;
    }
}