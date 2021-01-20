<?php

namespace App\Controller\Admin;

use App\Entity\Unity;
use App\Form\UnityType;
use App\Entity\UnitySearch;
use App\Form\UnitySearchType;
use App\Repository\UnityRepository;
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
final class UnityController extends BaseController 
{
    private $repository;
    private $manager;

    public function __construct(UnityRepository $repository, EntityManagerInterface $manager)
    {
        $this->repository = $repository;
        $this->manager = $manager;
    }

    /**
     * @Route("/units", name="unity_index", methods="GET")
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $query = $this->repository->createQueryBuilder('u')->orderBy('u.id', 'DESC');
        if ($request->get('q')) {
            $query = $query->where('u.id LIKE :search')
                ->orWhere('u.name LIKE :search')
                ->setParameter('search', "%" . $request->get('q') . "%");
        }
        $units = $paginator->paginate(
            $query->getQuery(),
            $request->query->getInt('page', 1),
            7
        );
        return $this->render('admin/unity/index.html.twig', [
            'units' => $units,
        ]);
    }

    /**
     * @Route("unity/new", name="unity_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $unity = new Unity();
        $unity->setAuthor($this->getUser());
        $form = $this->createForm(UnityType::class, $unity);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($unity);
            $this->manager->flush();

            $this->addFlash('success', 'L\' unité " '. $unity->getName() .' " a bien été créée !');
            return $this->redirectToRoute('admin_unity_index');
        }

        return $this->render('admin/unity/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/unity/{id}/edit", requirements={"id": "\d+"}, name="unity_edit", methods={"GET", "PUT"})
     */
    public function edit(Unity $unity, Request $request): Response
    {
        $form = $this->createForm(UnityType::class, $unity, [
            'method' => 'PUT',
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $unity->setUpdatedAt(new \DateTime());
            $this->manager->flush();
            $this->addFlash('success', 'L\' unité " '. $unity->getName() .' " a bien été modifiée !');
            return $this->redirectToRoute('admin_unity_index');
        }
        return $this->render('admin/unity/edit.html.twig', [
            'unity' => $unity,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/unity/{id}", name="unity_delete", requirements={"id": "\d+"}, methods="DELETE")
     * @ParamConverter("unity", options={"id" = "id"})
     */
    public function delete(Unity $unity, Request $request): Response
    {
        if($this->isCsrfTokenValid('delete' . $unity->getId(), $request->get('_token'))){
            $this->manager->remove($unity);
            $this->manager->flush();
            $this->addFlash('success', 'L\' unité " '. $unity->getName() .' " et son ou ses produit(s) associé(s) à cette unité ont bien était supprimé(s) !');
        }
        return $this->redirectToRoute('admin_unity_index');
    }
}
