<?php

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/register", name="registerPage")
     */
    // public function register( ServerRequestInterface $request)
    // {

    //     $error = '';

    //     if ($request->getMethod() === 'POST') {
    //         $dataSubmitted = $request->getParsedBody();

    //         if (
    //             (strlen(trim($dataSubmitted['email']))) === 0 ||
    //             (strlen(trim($dataSubmitted['pseudo']))) === 0 ||
    //             (strlen(trim($dataSubmitted['inputPassword']))) === 0 ||
    //             (strlen(trim($dataSubmitted['confirmPassword']))) === 0
    //         ) {
    //             $error = 'Tout les champs sont requis.';
    //         } elseif (
    //             strlen(trim($dataSubmitted['inputPassword']))
    //             !== strlen(trim($dataSubmitted['confirmPassword']))
    //         ) {
    //             $error = 'Le mot de passe et la confirmation sont différents.';
    //         } else {
    //             $passwordHash = password_hash($dataSubmitted['inputPassword'], PASSWORD_DEFAULT);
    //             $this->userRepository->registerUser($dataSubmitted['pseudo'], $dataSubmitted['email'], $passwordHash);
    //         }
    //     }

    //     return $this->render('core/auth/register.html.twig', ['error' => $error]);

    // }

    /**
     * 
     * @return Response
     * 
     * @Route("/login", name="loginPage")
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