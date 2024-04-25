<?php

namespace App\Controller;

use App\Entity\Produits;
use App\Entity\Categorie;
use App\Form\ProduitType;
use App\Repository\ProduitsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/user')]
class ProduitController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(Request $request, EntityManagerInterface $entityManager, ProduitsRepository $produitsRepository): Response
    {
        $taille = $request->query->get('taille');
        $categoryId = $request->query->getInt('category', 0);

        // Handle case where no specific category is selected
        $categoryId = $categoryId !== 0 ? $categoryId : null;

        $categories = $entityManager->getRepository(Categorie::class)->findAll();
        $quantitiesByCategory = [];
        foreach ($categories as $category) {
            $quantitiesByCategory[$category->getId()] = count($category->getProduits());
        }

        $page = $request->query->getInt('page', 1);
        $limit = 12;
        $produits = $produitsRepository->findByFilters($taille, $categoryId, $page, $limit);
        $maxPage = ceil($produits->getTotalItemCount() / $limit);

        return $this->render('home/index.html.twig', [
            'produits' => $produits,
            'categories' => $categories,
            'quantitiesByCategory' => $quantitiesByCategory,
            'page' => $page,
            'maxPage' => $maxPage,
            'selectedTaille' => $taille,
            'selectedCategory' => $categoryId
        ]);
    }


    #[Route('/produit/nouveau', name: 'add_produit', methods: ["GET", "POST"])]
// src/Controller/ProduitController.php
    public function nouveau(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $produit = new Produits();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $brochureFile */
            $brochureFile = $form->get('brochure')->getData();

            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();

                try {
                    $brochureFile->move(
                        $this->getParameter('images_directory'), // Use the correct parameter name
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Existing code...
                }

                $produit->setBrochureFilename($newFilename);
                $this->addFlash('success','Ajoutée avec succées' );
            }

            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('app_home'); // Redirect as needed
        }

        return $this->render('home/nouveau.html.twig', [
            'produitForm' => $form->createView(),
        ]);
    }


    #[Route('/produit/edit/{id}', name: 'edit_produit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Produits $produit, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $brochureFile */
            $brochureFile = $form->get('brochure')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('images_directory'), // Use the correct parameter name
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Existing code...
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $produit->setBrochureFilename($newFilename);
            }

            $entityManager->flush();
            $this->addFlash('success', 'Produit modifié avec succès!');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('home/edit.html.twig', [
            'produitForm' => $form->createView(),
        ]);
    }

    #[Route('/produit/delete/{id}', name: 'delete_produit')]
    public function delete(Produits $produits = null, ManagerRegistry $doctrine, EntityManagerInterface $entityManager): RedirectResponse
    {
        if ($produits) {
            $entityManager = $doctrine->getManager();
            $entityManager->remove($produits);
            $entityManager->flush();
            $this->addFlash('success', 'Product deleted successfully.');
        } else {
            $this->addFlash('error', 'Product does not exist.');
        }

        return $this->redirectToRoute('app_home');
    }


}
