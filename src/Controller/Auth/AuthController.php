<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class AuthController extends AbstractController
{
    #[Route('/login', 'page_login')]
    public function login(): Response
    {
        return $this->render('auth/login.html.twig');
    }

    #[Route('/register', 'page_register')]
    public function register(): Response
    {
        return $this->render('auth/register.html.twig');
    }

    #[Route('/forgot', 'page_forgot')]
    public function forgot(
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer,
        UserPasswordHasherInterface $userPasswordHasher
    ): Response
    {
        if($request->isMethod('POST')) {
            $email = $request->get('email');
            $password = $request->get('password');
            $passwordConfirmation = $request->get('passwordConfirmation');

            $user = $userRepository->findOneBy(['email' => $email]);
            if($user) {
                if($password === $passwordConfirmation) {
                    $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
                } else {
                    $user->setPassword($userPasswordHasher->hashPassword($user, $password));;
                    $user->setResetToken(null);
                    $entityManager->flush();
                    $this->addFlash('success', 'Votre mot de passe a été réinitialisé avec succès.');

                    $resetUrl = $this->generateUrl('page_reset', ['token' => $user->getResetToken()], 0);
                    $emailContent = (new TemplatedEmail())
                        ->from('no-reply@exemple.fr')
                        ->to($user->getEmail())
                        ->subject('Réinitialisation du mot de passe')
                        ->htmlTemplate('email/reset_password.html.twig')
                        ->context([
                            'resetToken' => $user->getResetToken(),
                            'email' => $user->getEmail(),
                            'resetUrl' => $resetUrl,
                        ]);
                    $mailer->send($emailContent);
                    return $this->redirectToRoute('page_login');
                }
            }
        }
        return $this->render('auth/forgot.html.twig');
    }


    #[Route('/reset/{token}', 'page_reset')]
    public function reset(
        string $token,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher
    ): Response
    {
        $user = $userRepository->findOneBy(['resetToken' => $token]);
        if(!$user) {
            $this->addFlash('error', 'Token invalide ou expiré');
            return $this->redirectToRoute('page_forgot');
        }

        if($request->isMethod('POST')) {
            $password = $request->get('password');
            $passwordConfirmation = $request->get('name="password_confirmation"');
            if($password === $passwordConfirmation) {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
            } else {
                $user->setPassword($userPasswordHasher->hashPassword($user, $passwordConfirmation));;
                $user->setResetToken(null);
                $entityManager->flush();
                $this->addFlash('success', 'Le mot de passe a bien été réinitialisé');
                return $this->redirectToRoute('page_login');
            }
        }

        return $this->render( 'auth/reset.html.twig', [
            'token' => $token,
        ]);
    }

    #[Route('/confirm', 'page_confirm')]
    public function confirm(): Response
    {
        return $this->render('auth/confirm.html.twig');
    }
}