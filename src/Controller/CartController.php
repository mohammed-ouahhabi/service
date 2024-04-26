<?php

namespace App\Controller;

use App\Entity\Produits;
use App\Repository\ProduitsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cart', name: 'cart_')]
class CartController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(SessionInterface $session, ProduitsRepository $produitsRepository)
    {
        $panier = $session->get('panier', []);
        $data = [];
        $total = 0;

        foreach ($panier as $id => $quantity) {
            $produit = $produitsRepository->find($id);
            if ($produit) {  // Check if the product was found
                $data[] = [
                    'produit' => $produit,
                    'quantity' => $quantity
                ];
                $total += $produit->getPrix() * $quantity; // Calculate total for each product
            }
        }

        return $this->render('cart/index.html.twig', [
            'items' => $data,
            'total' => $total
        ]);
    }


    #[Route('/add/{id}', name: 'add_cart')]
    public function add(int $id, SessionInterface $session, ProduitsRepository $produitsRepository)
    {
        $panier = $session->get('panier', []);
        if (isset($panier[$id])) {
            $panier[$id]++;
        } else {
            $panier[$id] = 1;
        }
        $session->set('panier', $panier);

        $this->addFlash('success', 'Product added to cart successfully!');
        return $this->redirectToRoute('cart_index');
    }


    #[Route('/remove/{id}', name: 'remove')]
    public function remove(int $id, SessionInterface $session, ProduitsRepository $produitsRepository)
    {
        $panier = $session->get('panier', []);
        if (!empty($panier[$id])) {
            if($panier[$id] > 1){
                $panier[$id]--;
            }

        } else {
            unset($panier[$id]);
        }
        $session->set('panier', $panier);

        $this->addFlash('success', 'Product added to cart successfully!');
        return $this->redirectToRoute('cart_index');
    }



    #[Route('/delete/{id}', name: 'delete')]
    public function delete(int $id, SessionInterface $session, ProduitsRepository $produitsRepository)
    {
        $panier = $session->get('panier', []);
        if (!empty($panier[$id])) {



            unset($panier[$id]);
        }
        $session->set('panier', $panier);

        $this->addFlash('success', 'Product added to cart successfully!');
        return $this->redirectToRoute('cart_index');
    }
    #[Route('/empty', name: 'empty')]
    public function empty(SessionInterface $session)
    {

        $session->remove('panier');
        return $this->redirectToRoute('cart_index');
    }


}
