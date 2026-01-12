<?php
// src/Controller/DashboardController.php

namespace App\Controller;

use App\Repository\ContactRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(ContactRepository $contactRepository): Response
    {
        $user = $this->getUser();
        
        // Rediriger vers le login si non connecté
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Calculer les statistiques
        $totalContacts = $contactRepository->count([]);
        $recentContacts = $contactRepository->findBy([], ['id' => 'DESC'], 5);
        
        // Compter les contacts avec/sans image (adaptez selon votre entité Contact)
        $withImage = 0;
        $withoutImage = 0;
        $allContacts = $contactRepository->findAll();
        
        foreach ($allContacts as $contact) {
            if (method_exists($contact, 'getImage') && $contact->getImage()) {
                $withImage++;
            } else {
                $withoutImage++;
            }
        }

        // Compter les contacts récents (des 7 derniers jours)
        $recentCount = 0;
        $oneWeekAgo = new \DateTime('-7 days');
        foreach ($allContacts as $contact) {
            if (method_exists($contact, 'getDateCreation') && $contact->getDateCreation() >= $oneWeekAgo) {
                $recentCount++;
            }
        }

        $stats = [
            'total' => $totalContacts,
            'recent' => $recentCount,
            'with_image' => $withImage,
            'without_image' => $withoutImage,
        ];

        return $this->render('dashboard/index.html.twig', [
            'user' => $user,
            'stats' => $stats,
            'recentContacts' => $recentContacts,
            'contacts' => $allContacts,
        ]);
    }
}