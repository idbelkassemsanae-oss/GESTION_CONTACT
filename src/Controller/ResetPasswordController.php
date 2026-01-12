<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class ResetPasswordController extends AbstractController
{
    #[Route('/reset-password', name: 'app_forgot_password_request')]
    public function request(Request $request): Response
    {
        return $this->render('security/reset_password_request.html.twig', [
            // Vous pouvez ajouter un formulaire ici plus tard
        ]);
    }

    #[Route('/reset-password/check-email', name: 'app_check_email')]
    public function checkEmail(): Response
    {
        return $this->render('security/check_email.html.twig');
    }

    #[Route('/reset-password/reset/{token}', name: 'app_reset_password')]
    public function reset(string $token = null): Response
    {
        if ($token === null) {
            return $this->redirectToRoute('app_forgot_password_request');
        }

        return $this->render('security/reset_password.html.twig', [
            'token' => $token,
        ]);
    }
}