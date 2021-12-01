<?php

namespace App\Controller;

use App\Form\ResetPasswordType;
use App\Form\ForgotPasswordType;
use App\Repository\UserRepository;
use App\Service\MailerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class SecurityController extends BaseController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/forgot-password", name="app_forgot_password")
     */
    public function forgotPassword(Request $request, UserRepository $user, TokenGeneratorInterface $tokenGenerator, MailerService $mailerService): Response 
    {
        //Init reset password form
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Get data
            $data = $form->getData();
            $user = $user->findOneBy(['username' => $data['username']]);

            //If user does not exist
            if ($user === null) {
                $this->addFlash('danger', "Ce nom d'utilisateur est inconnue");
                return $this->redirectToRoute('app_login');
            }

            //Generated token
            $token = $tokenGenerator->generateToken();

            //We try to write the token in the database
            $user->setResetToken($token);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            //Send email
            $mailerService->send([
                "from" => 'snowtricks@gmail.com',
                "to" => $user->getEmail(),
                "subject" => 'Mot de passe oublié - SnowTricks',
                "template" => 'security/email.html.twig'
            ], [
                'token' => $token
            ]);

            $this->addFlash('success', 'E-mail de réinitialisation du mot de passe envoyé !');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/forgot_password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/reset-password/{token}", name="app_reset_password")
     */
    public function resetPassword(Request $request, string $token, UserRepository $userRepository)
    {
        //Find user with the token
        $user = $userRepository->findOneBy(['reset_token' => $token]);

        //If user does not exist
        if ($user === null) {
            $this->addFlash('danger', 'Token Inconnu');
            return $this->redirectToRoute('app_login');
        }

        //Init reset password form
        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Reset token in database
            $user->setResetToken(null);

            //Encode password
            $user->setPassword($this->passwordEncoder->encodePassword($user, $form->get('password')->getData()));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Mot de passe mis à jour');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password.html.twig', [
            'form' => $form->createView(),
            'token' => $token
        ]);
    }
}
