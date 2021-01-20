<?php

namespace App\Controller\Admin;

use App\Entity\Emplacement;
use App\Form\EmplacementType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\EmplacementRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @IsGranted("ROLE_ADMIN")
 */
final class EmplacementController extends BaseController
{
    private $repository;
    private $manager;

    public function __construct(EmplacementRepository $repository, EntityManagerInterface $manager)
    {
        $this->repository = $repository;
        $this->manager = $manager;
    }

    /**
     * @Route("/emplacements", name="emplacement_index", methods="GET")
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $query = $this->repository->createQueryBuilder('e')->orderBy('e.id', 'DESC');
        if ($request->get('q')) {
            $query = $query->where('e.id LIKE :search')
                ->orWhere('e.name LIKE :search')
                ->setParameter('search', "%" . $request->get('q') . "%");
        }

        $emplacements = $paginator->paginate(
            $query->getQuery(),
            $request->query->getInt('page', 1),
            7
        );
        return $this->render('admin/emplacement/index.html.twig', [
            'emplacements' => $emplacements,
        ]);
    }

    /**
     * @Route("emplacement/new", name="emplacement_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $emplacement = new Emplacement();
        $emplacement->setAuthor($this->getUser());
        $form = $this->createForm(EmplacementType::class, $emplacement);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($emplacement);
            $this->manager->flush();

            $this->addFlash('success', 'L\' emplacement " '. $emplacement->getName() .' " a bien été créé !');
            return $this->redirectToRoute('admin_emplacement_index');
        }

        return $this->render('admin/emplacement/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/emplacement/{id}/edit", requirements={"id": "\d+"}, name="emplacement_edit", methods={"GET", "PUT"})
     */
    public function edit(Emplacement $emplacement, Request $request): Response
    {
        $form = $this->createForm(EmplacementType::class, $emplacement, [
            'method' => 'PUT',
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $emplacement->setUpdatedAt(new \DateTime());
            $this->manager->flush();
            $this->addFlash('success', 'L\' emplacement " '. $emplacement->getName() .' " a bien été modifié !');
            return $this->redirectToRoute('admin_emplacement_index');
        }
        return $this->render('admin/emplacement/edit.html.twig', [
            'emplacement' => $emplacement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/emplacement/{id}", name="emplacement_delete", requirements={"id": "\d+"}, methods="DELETE")
     * @ParamConverter("category", options={"id" = "id"})
     */
    public function delete(Emplacement $emplacement, Request $request): Response
    {
        if($this->isCsrfTokenValid('delete' . $emplacement->getId(), $request->get('_token'))){
            $this->manager->remove($emplacement);
            $this->manager->flush();
            $this->addFlash('success', 'L\' emplacement " '. $emplacement->getName() .' " et son ou ses produit(s) associé(s) à cet emplacement ont bien était supprimé(s) !');
        }
        return $this->redirectToRoute('admin_emplacement_index');
    }
}