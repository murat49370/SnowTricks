<?php


namespace App\Controller\Admin;


use App\Entity\Trick;
use App\Form\TrickType;
use App\Repository\TrickRepository;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class AdminTrickController extends AbstractController
{

    /**
     * @var TrickRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */

    private $em;

    /**
     * AdminTrickController constructor.
     * @param TrickRepository $repository
     * @param EntityManagerInterface $em
     */
    public function __construct(TrickRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @route ("/admin/trick", name="admin_trick_index")
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {

        $tricks = $paginator->paginate($this->repository->getAllTricksQuery(),
            $request->query->getInt('page', 1),
            10
        );
//        $tricks = $this->repository->findBy(
//            array(), array('create_at' => 'DESC')
//        );
        return $this->render('/admin/trick/index.html.twig', [
            'tricks' => $tricks,
        ]);
    }

    /**
     * @route ("/admin/trick/edit/{id}", name="admin_trick_edit", methods="GET|POST")
     * @param Trick $trick
     * @param Request $request
     * @return Response
     */
    public function edit(Trick $trick, Request $request)
    {
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->em->flush();
            $this->addFlash('success', 'Trick modifié avec succès');
            return $this->redirectToRoute('admin.trick.index');
        }

        return $this->render('admin/trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form->createView()
        ]);
    }

    /**
     * @route ("/admin/trick/create", name="admin_trick_create")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function create(Request $request)
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->em->persist($trick);
            $this->em->flush();
            $this->addFlash('success', 'Trick crée avec succès');
            return $this->redirectToRoute('admin.trick.index');
        }

        return $this->render('admin/trick/new.html.twig', [
            'trick' => $trick,
            'form' => $form->createView()
        ]);

    }

    /**
     * @route ("/admin/trick/edite/{id}", name="admin_trick_delete", methods="DELETE")
     * @param Trick $trick
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Trick $trick, Request $request)
    {
        if ($this->isCsrfTokenValid('delete' . $trick->getId(), $request->get('_token')))
        {
            $this->em->remove($trick);
            $this->em->flush();
            $this->addFlash('success', 'Trick supprimé avec succès');

        }
        return $this->redirectToRoute('admin.trick.index');

    }


}