<?php

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SecurityController extends AbstractController
{
    /**
     * @Route("/register", name="registerPage", methods={"get","post"})
     */
    public function register( ServerRequestInterface $request)
    {
        exit;
        var_dump($request);

        $error = '';

        if ($request->getMethod() === 'POST') {
            $dataSubmitted = $request->getParsedBody();

            if (
                (strlen(trim($dataSubmitted['email']))) === 0 ||
                (strlen(trim($dataSubmitted['pseudo']))) === 0 ||
                (strlen(trim($dataSubmitted['inputPassword']))) === 0 ||
                (strlen(trim($dataSubmitted['confirmPassword']))) === 0
            ) {
                $error = 'Tout les champs sont requis.';
            } elseif (
                strlen(trim($dataSubmitted['inputPassword']))
                !== strlen(trim($dataSubmitted['confirmPassword']))
            ) {
                $error = 'Le mot de passe et la confirmation sont différents.';
            } else {
                $passwordHash = password_hash($dataSubmitted['inputPassword'], PASSWORD_DEFAULT);
                $this->userRepository->registerUser($dataSubmitted['pseudo'], $dataSubmitted['email'], $passwordHash);
            }
        }

        return $this->render('core/register.html.twig', ['error' => $error]);

    }

    /**
     * @Route("/login", name="loginPage", methods={"get","post"})
     */
    public function login()
    {
        exit;
    }

    /**
     * @Route("/disconnect", name="disconnectPage", methods={"get"})
    */
    public function disconnect()
    {
        exit;
    }
}