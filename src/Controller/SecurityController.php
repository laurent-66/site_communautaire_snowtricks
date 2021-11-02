<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    /**
     * @Route("/register", name="registerPage", methods={"get","post"})
     */
    public function register()
    {
        exit;
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