<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\SearchProductData;
use App\Form\ProductType;
use App\Form\SearchProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/product")
 */
class ProductController extends AbstractController
{
    /** @var ProductRepository $repository */
    private $repository;
    
    /** @var EntityManagerInterface $manager */
    private $manager;

    public function __construct(ProductRepository $repository, EntityManagerInterface $manager)
    {
        $this->repository = $repository;
        $this->manager = $manager;
    }

    /**
    * 
    * @Route("", name="product_index", methods="GET")
    */
    public function index(Request $request): Response
    {
        $data = new SearchProductData();
        $data->page = $request->get('page', 1);
        $form = $this->createForm(SearchProductType::class, $data);
        $form->handleRequest($request);

        $userCurrent = $this->getUser();
        $products = $this->repository->searchProduct($userCurrent, $data);
        
        // Convertion d'une requête JSON en HTML pour filtrer les produits en renvoyant des pages HTML
        // $request->get('ajax') = si il a 'ajax' dans l'url, lorsque l'on souhaite revenir en arrière, évite un retour d'une page en JSON
        if($request->get('ajax')) {
            return new JsonResponse([
                'products' => $this->renderView('product/_products.html.twig', ['products' => $products]),
                'pagination' => $this->renderView('product/_pagination.html.twig', ['products' => $products]),
                'pages' => ceil($products->getTotalItemCount() / $products->getItemNumberPerPage()) // Récupération du nombre(s) de page(s) selon le(s) filtre(s) sélectionné(s)
                ]);
        }

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'form' => $form->createView(),
            //'formSearch' => $formSearch->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="product_show", requirements={"id": "\d+"})
     */
    public function show(Product $product, ValidatorInterface $validator): Response
    {
        $this->denyAccessUnlessGranted('show', $product);

        // pas utile ici, juste pour un exemple de validation hors formulaire
        $errors = $validator->validate($product);
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }
    
    /**
     * @Route("/new", name="product_new")
     */
    public function new(Request $request): Response
    {
        $user = $this->getUser();
        if($user->getActivationToken()){
            $this->addFlash('message', 'Vous devez activer votre compte pour ajouter un produit');
            return $this->redirectToRoute('home');
        }

        $product = new Product();
        $product->setAuthor($this->getUser());
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($product);
            $this->manager->flush();
            $this->addFlash('success', 'Votre produit ' . $product->getName() . ' a bien était crée !');
            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="product_edit", requirements={"id": "\d+"}, methods={"GET", "PUT"})
     */
    public function edit(Product $product, Request $request): Response
    {
        $this->denyAccessUnlessGranted('edit', $product);

        $user = $this->getUser();
        if($user->getActivationToken()){
            $this->addFlash('message', 'Vous devez activer votre compte pour modifier un produit');
            return $this->redirectToRoute('home');
        }

        $form = $this->createForm(ProductType::class, $product, [
            'method' => 'PUT',
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->manager->flush();
            $this->addFlash('success', 'Votre produit ' . $product->getName() . ' a bien était modifié !');
            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/delete", name="product_delete", requirements={"id": "\d+"}, methods="DELETE")
     */
    public function delete(Product $product, Request $request): Response
    {
        $this->denyAccessUnlessGranted('delete', $product);

        if($this->isCsrfTokenValid('delete' . $product->getId(), $request->get('_token'))){
            $this->manager->remove($product);
            $this->manager->flush();
            $this->addFlash('success', 'Votre produit ' . $product->getName() . ' a bien était supprimé !');
        }
        return $this->redirectToRoute('product_index');
    }
}
