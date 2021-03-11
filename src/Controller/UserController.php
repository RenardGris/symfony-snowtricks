<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ForgotPasswordType;
use App\Form\RegisterType;
use App\Form\ResetPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{

    /**
     * @Route("/login", name="login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('user/login.html.twig');
    }

    /**
     * @Route("/logout", name="logout")*
     * @return RedirectResponse
     */
    public function logout(): RedirectResponse
    {
        return $this->redirectToRoute('figure_index');
    }

    /**
     * @Route("/register", name="register")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $encoder
     * @param \Swift_Mailer $mailer
     * @return Response
     */
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, \Swift_Mailer $mailer): Response
    {

        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $hash = $encoder->encodePassword($user, $user->getPassword());

            $user->setPassword($hash);
            $user->setAvatar('http://placeimg.com/120/120/any')
                ->setValidate(false)
                ->setToken(md5($user->getEmail()));

            $manager->persist($user);
            $manager->flush();

            $this->sendValidationMail(user: $user, mailer: $mailer);
            return $this->redirectToRoute('login');
        }

        return $this->render('user/register.html.twig', [
            'formUserRegistration' => $form->createView()
        ]);
    }

    /**
     * @Route("/validate_user/{token}", name="validate_user")
     * @param User $user
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function validate(User $user, EntityManagerInterface $manager): Response
    {

        $message = 'Votre compte a déjà été validée';
        if(!$user->getValidate()){
            $user->setValidate(true);
            $manager->persist($user);
            $manager->flush();
            $message = null;
        }

        return $this->render('user/validate.html.twig', [
            'user' => $user,
            'message' => $message,
        ]);
    }

    /**
     * @Route("/forget_password", name="forgot_password")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param \Swift_Mailer $mailer
     * @return Response
     *
     */
    public function forgotPassword(Request $request, EntityManagerInterface $manager, \Swift_Mailer $mailer): Response
    {

        $formForgetPassword = $this->createForm(ForgotPasswordType::class);

        $formForgetPassword->handleRequest($request);

        if($formForgetPassword->isSubmitted() && $formForgetPassword->isValid()){
            $email = $formForgetPassword->get('email')->getData();

            $user = $manager->getRepository(User::class)->findOneBy(['email' => $email]);

            //Changer le token de l'utilisateur pour eviter qu'l puisse changer de mot de passe a la suite avec la meme url

            if($user){
                $this->sendResetPasswordMail($user, $mailer);
            }

        }

        return $this->render('user/forgot_password.html.twig', [
            'formForgetPassword' => $formForgetPassword->createView(),
        ]);
    }

    /**
     * @Route("/reset_password/{token}", name="reset_password")
     * @param User $user
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function resetPassword(User $user, Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder): Response
    {

        $formResetPassword = $this->createForm(ResetPasswordType::class);
        $formResetPassword->handleRequest($request);

        if($formResetPassword->isSubmitted() && $formResetPassword->isValid()){
            $password = $encoder->encodePassword($user, $formResetPassword->get('password')->getData());
            $user->setPassword($password);
            $manager->persist($user);
            $manager->flush();
        }

        return $this->render('user/reset_password.html.twig', [
            'formResetPassword' => $formResetPassword->createView(),
        ]);
    }


    /**
     * @param User $user
     * @param \Swift_Mailer $mailer
     */
    protected function sendValidationMail(User $user, \Swift_Mailer $mailer) {

        $message = (new \Swift_Message('Validez votre inscription'))
            ->setFrom('test@test.com')
            ->setTo($user->getEmail())
            ->setContentType("text/html")
            ->setBody(
                $this->renderView(
                    'email/register_validation.html.twig',
                    ['user' => $user]
                )
            );

        $mailer->send($message);
    }

    /**
     * @param User $user
     * @param \Swift_Mailer $mailer
     */
    protected function sendResetPasswordMail(User $user, \Swift_Mailer $mailer) {

        $message = (new \Swift_Message('Reset Password'))
            ->setFrom('test@test.com')
            ->setTo($user->getEmail())
            ->setContentType("text/html")
            ->setBody(
                $this->renderView(
                    'email/reset_password.html.twig',
                    ['user' => $user]
                )
            );

        $mailer->send($message);
    }


}
