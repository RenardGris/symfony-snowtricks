<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{

    /**
     * @Route("/login", name="login", methods={"GET"})
     * @return Response
     *
     */
    public function login(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("/logout", name="logout", methods={"GET"})
     * @return Response
     *
     */
    public function logout(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("/register", name="register", methods={"GET"})
     * @return Response
     *
     */
    public function register(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("/validate_user/{token}", name="validate_user", methods={"GET"})
     * @return Response
     *
     */
    public function validate(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("/forget_password", name="forgot_password", methods={"GET"})
     * @return Response
     *
     */
    public function forgotPassword(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("/reset_password", name="reset_password", methods={"GET"})
     * @return Response
     *
     */
    public function resetPassword(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }


}
