<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
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


}
