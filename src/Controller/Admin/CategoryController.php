<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
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
final class CategoryController extends BaseController
{
    private $repository;
    private $manager;

    public function __construct(CategoryRepository $repository, EntityManagerInterface $manager)
    {
        $this->repository = $repository;
        $this->manager = $manager;
    }

    /**
     * @Route("/categories", name="category_index", methods="GET")
     */
    public function index(PaginatorInterface $paginator, Request  $request): Response
    {
        $query = $this->repository->createQueryBuilder('c')->orderBy('c.id', 'DESC');
        if ($request->get('q')) {
            $query = $query->where('c.id LIKE :search')
                ->orWhere('c.name LIKE :search')
                ->setParameter('search', "%" . $request->get('q') . "%");
        }
        $categories = $paginator->paginate(
            $query->getQuery(),
            $request->query->getInt('page', 1),
            7
        );
        return $this->render('admin/category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("category/new", name="category_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $category = new Category();
        $category->setAuthor($this->getUser());
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($category);
            $this->manager->flush();

            $this->addFlash('success', 'La catégorie " '. $category->getName() .' " a bien été créée !');
            return $this->redirectToRoute('admin_category_index');
        }
        return $this->render('admin/category/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/category/{id}/edit", requirements={"id": "\d+"}, name="category_edit", methods={"GET", "PUT"})
     */
    public function edit(Category $category, Request $request): Response
    {
        $form = $this->createForm(CategoryType::class, $category, [
            'method' => 'PUT',
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $category->setUpdatedAt(new \DateTime());
            $this->manager->flush();
            $this->addFlash('success', 'La catégorie " ' . $category->getName() . ' " a bien était modifiée !');
            return $this->redirectToRoute('admin_category_index');
        }
        return $this->render('admin/category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/category/{id}", name="category_delete", requirements={"id": "\d+"}, methods="DELETE")
     * @ParamConverter("category", options={"id" = "id"})
     */
    public function delete(Category $category, Request $request): Response
    {
        if($this->isCsrfTokenValid('delete' . $category->getId(), $request->get('_token'))){
            $this->manager->remove($category);
            $this->manager->flush();
            $this->addFlash('success', 'La catégorie " ' . $category->getName() . ' "et son ou ses produit(s) associé(s) à cette catégorie ont bien était supprimé(s) !');
        }
        return $this->redirectToRoute('admin_category_index');
    }
}