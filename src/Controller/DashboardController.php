<?php
// src/Controller/DashboardController.php

namespace App\Controller;

use App\Repository\ContactRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(ContactRepository $contactRepository): Response
    {
        $stats = $contactRepository->getStats();
        $recentContacts = $contactRepository->getRecentContacts(5);

        return $this->render('dashboard/index.html.twig', [
            'stats' => $stats,
            'recentContacts' => $recentContacts,
        ]);
    }

    #[Route('/about', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('dashboard/about.html.twig');
    }
}