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
        $error = $authenticationUtils->getLastAuthenticationError();
        if($error){
            $this->addFlash('error_login', $error);
        }

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
            $user->setAvatar(array_rand(['avatar-1.jpg','avatar-2.jpg','avatar-3.jpg','avatar-4.jpg'], 1))
                ->setValidate(false)
                ->setNewToken();

            $manager->persist($user);
            $manager->flush();

            $emailView = 'email/register_validation.html.twig';
            $object = 'Validez votre inscription';
            $this->sendEmail(user: $user, mailer: $mailer, emailView: $emailView, object: $object);
            return $this->redirectToRoute('figure_index');
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
            $user->setValidate(true)
                ->setNewToken();
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

            if($user){
                $emailView = 'email/reset_password.html.twig';
                $object = 'Reset Password';
                $this->sendEmail(user: $user, mailer: $mailer, emailView: $emailView, object: $object);
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
            $user->setPassword($password)
                ->setNewToken();
            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('figure_index');
        }

        return $this->render('user/reset_password.html.twig', [
            'formResetPassword' => $formResetPassword->createView(),
        ]);
    }


    /**
     * @param User $user
     * @param \Swift_Mailer $mailer
     * @param string $emailView
     * @param string $object
     */
    protected function sendEmail(User $user, \Swift_Mailer $mailer,string $emailView, string $object) {

        $message = (new \Swift_Message($object))
            ->setFrom(getenv('EMAIL'))
            ->setTo($user->getEmail())
            ->setContentType("text/html")
            ->setBody(
                $this->renderView(
                    $emailView,
                    ['user' => $user]
                )
            );

        $mailer->send($message);
    }

}
