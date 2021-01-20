<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\AdminProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @IsGranted("ROLE_ADMIN")
 */
final class ProductController extends BaseController
{
    private $repository;
    private $manager;
    
    public function __construct(ProductRepository $repository, EntityManagerInterface $manager)
    {
        $this->repository = $repository;
        $this->manager = $manager;
    }
    /**
    * @Route("/products", name="product_index", methods="GET")
    */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $query = $this->repository->createQueryBuilder('p')->orderBy('p.id', 'DESC');
        if ($request->get('q')) {
            $query = $query->where('p.id LIKE :search')
            ->orWhere('p.name LIKE :search')
            ->setParameter('search', "%". $request->get('q') ."%");
        }

        $products = $paginator->paginate(
            $query->getQuery(),
            $request->query->getInt('page', 1),
           12 // Le plus petit dénominateur commun 2,3,4,5 et 6 : 60
        );
        
        return $this->render('admin/product/index.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * @Route("product/new", name="product_new", methods={"GET", "POST"})
     */
    public function new(Request $request): Response
    {
        $product = new Product();
        $product->setAuthor($this->getUser());

        $form = $this->createForm(AdminProductType::class, $product);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($product);
            $this->manager->flush();

            $this->addFlash('success', 'Le produit " '. $product->getName() .' " a bien été créé !');
            return $this->redirectToRoute('admin_product_index', [
                'id' => $product->getId(),
            ]);
        }

        return $this->render('admin/product/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/product/{id}/edit", name="product_edit", methods={"GET", "PUT"})
     */
    public function edit(Product $product, Request $request): Response
    {
        $form = $this->createForm(AdminProductType::class, $product, [
            'method' => 'PUT',
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $product->setUpdatedAt(new \DateTime());
            $this->manager->flush();
            $this->addFlash('success', 'Le produit " '. $product->getName() .' " a bien été modifié !');
            return $this->redirectToRoute('admin_product_index');
        }
        return $this->render('admin/product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/product/{id}", name="product_delete", requirements={"id": "\d+"}, methods="DELETE")
     * @ParamConverter("product", options={"id" = "id"})
     */
    public function delete(Product $product, Request $request): Response
    {
        if($this->isCsrfTokenValid('delete' . $product->getId(), $request->get('_token'))){
            $this->manager->remove($product);
            $this->manager->flush();
            $this->addFlash('success', 'Le produit " '. $product->getName() .' " a bien été supprimé !');
        }
        return $this->redirectToRoute('admin_product_index');
    }
}