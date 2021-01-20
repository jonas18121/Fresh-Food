<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class UserController extends BaseController
{
    private $repository;
    private $manager;
    protected $encoder;

    public function __construct(UserRepository $repository, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        $this->repository = $repository;
        $this->manager = $manager;
        $this->encoder = $encoder;
    }

    /**
     * @Route("/users/search/{q?}", name="user_search")
     */
    public function search(string $q): JSONResponse
    {
        /** @var UserRepository $repository */
        $repository = $this->manager->getRepository(User::class);
        $q = strtolower($q);
        $users = $repository
            ->createQueryBuilder('u')
            ->select('u.id', 'u.pseudo', 'u.email')
            ->where('LOWER(u.email) LIKE :email')
            ->setParameter('email', "%$q%")
            ->setMaxResults(25)
            ->getQuery()
            ->getResult();
        return new JsonResponse($users);
    }

    /**
     * @Route("/users", name="user_index", methods="GET")
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $query = $this->repository->createQueryBuilder('u')->orderBy('u.id', 'DESC');
        if ($request->get('q')) {
            $query = $query->where('u.id LIKE :search')
                ->orWhere('u.pseudo LIKE :search')
                ->orWhere('u.email LIKE :search')
                ->setParameter('search', "%" . $request->get('q') . "%");
        }

        $users = $paginator->paginate(
            $query->getQuery(),
            $request->query->getInt('page', 1),
            7
        );

        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("user/new", name="user_new", methods={"GET", "POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getPassword());

            $user->setPassword($hash);
            $this->manager->persist($user);
            $this->manager->flush();

            $this->addFlash('success', 'L\' utilisateur " ' . $user->getEmail() . ' " a bien été créé !');
            return $this->redirectToRoute('admin_user_index');
        }
        return $this->render('admin/user/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/user/{id}/edit", name="user_edit", methods={"GET", "PUT"})
     */
    public function edit(User $user, Request $request): Response
    {
        $form = $this->createForm(UserType::class, $user, [
            'method' => 'PUT',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setUpdatedAt(new \DateTime());
            $this->manager->flush();
            $this->addFlash('success', 'L\' utilisateur " ' . $user->getEmail() . ' " a bien été modifié !');
            return $this->redirectToRoute('admin_user_index');
        }
        return $this->render('admin/user/edit.html.twig', [
            'product' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/user/{id}", name="user_delete", requirements={"id": "\d+"}, methods="DELETE")
     * @ParamConverter("user", options={"id" = "id"})
     */
    public function delete(User $user, Request $request): Response
    {
        if ($this->isCsrfTokenValid(
            'delete' . $user->getId(),
            $request->get('_token')
        )) {
            $this->manager->remove($user);
            $this->manager->flush();
            $this->addFlash('success', 'L\' utilisateur " ' . $user->getEmail() . ' "  et son ou ses produit(s) ont bien été supprimés !');
        }
        return $this->redirectToRoute('admin_user_index');
    }
}
