<?php
// src/Controller/ContactController.php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/contact')]
#[IsGranted('ROLE_USER')]
class ContactController extends AbstractController
{
    #[Route('/', name: 'app_contact_index', methods: ['GET'])]
    public function index(Request $request, ContactRepository $contactRepository): Response
    {
        $search = $request->query->get('search', '');
        $page = $request->query->getInt('page', 1);
        
        if (!empty($search)) {
            $paginator = $contactRepository->search($search, $page);
        } else {
            $paginator = $contactRepository->findAllPaginated($page);
        }
        
        $totalContacts = count($paginator);
        $contactsPerPage = ContactRepository::PAGINATOR_PER_PAGE;

        return $this->render('contact/index.html.twig', [
            'contacts' => $paginator,
            'search' => $search,
            'page' => $page,
            'totalContacts' => $totalContacts,
            'contactsPerPage' => $contactsPerPage,
            'totalPages' => ceil($totalContacts / $contactsPerPage),
        ]);
    }

    #[Route('/new', name: 'app_contact_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'upload d'image
            $imageFile = $form->get('imageFile')->getData();
            
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                // Déplacer le fichier
                try {
                    $imageFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de l\'image.');
                    return $this->redirectToRoute('app_contact_new');
                }

                $contact->setImage($newFilename);
            }

            $entityManager->persist($contact);
            $entityManager->flush();

            $this->addFlash('success', 'Contact ajouté avec succès !');
            return $this->redirectToRoute('app_contact_show', ['id' => $contact->getId()]);
        }

        return $this->render('contact/form.html.twig', [
            'contact' => $contact,
            'form' => $form->createView(),
            'title' => 'Nouveau Contact',
        ]);
    }

    #[Route('/{id}', name: 'app_contact_show', methods: ['GET'])]
    public function show(Contact $contact): Response
    {
        return $this->render('contact/show.html.twig', [
            'contact' => $contact,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_contact_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Contact $contact, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'upload d'image
            $imageFile = $form->get('imageFile')->getData();
            
            if ($imageFile) {
                // Supprimer l'ancienne image si elle existe
                if ($contact->getImage()) {
                    $oldImage = $this->getParameter('uploads_directory').'/'.$contact->getImage();
                    if (file_exists($oldImage)) {
                        unlink($oldImage);
                    }
                }

                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de l\'image.');
                    return $this->redirectToRoute('app_contact_edit', ['id' => $contact->getId()]);
                }

                $contact->setImage($newFilename);
            }

            $contact->setDateModification(new \DateTime());
            $entityManager->flush();

            $this->addFlash('success', 'Contact modifié avec succès !');
            return $this->redirectToRoute('app_contact_show', ['id' => $contact->getId()]);
        }

        return $this->render('contact/form.html.twig', [
            'contact' => $contact,
            'form' => $form->createView(),
            'title' => 'Modifier Contact',
        ]);
    }

    #[Route('/{id}', name: 'app_contact_delete', methods: ['POST'])]
    public function delete(Request $request, Contact $contact, EntityManagerInterface $entityManager): Response
    {
        $contactName = $contact->getNomComplet();
        
        if ($this->isCsrfTokenValid('delete'.$contact->getId(), $request->request->get('_token'))) {
            // Supprimer l'image si elle existe
            if ($contact->getImage()) {
                $imagePath = $this->getParameter('uploads_directory').'/'.$contact->getImage();
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $entityManager->remove($contact);
            $entityManager->flush();
            
            $this->addFlash('success', "Le contact \"{$contactName}\" a été supprimé avec succès !");
        } else {
            $this->addFlash('error', "Token CSRF invalide. Suppression annulée.");
        }

        return $this->redirectToRoute('app_contact_index');
    }
}