<?php


namespace App\Controller\Admin;


use App\Entity\TrickGroup;
use App\Entity\User;
use App\Form\TrickGroupType;
use App\Form\TrickType;
use App\Form\UserType;
use App\Repository\TrickGroupRepository;
use App\Repository\TrickRepository;


use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class AdminCategoryController extends AbstractController
{

    /**
     * @var TrickGroupRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */

    private $em;

    /**
     * AdminCategoryController constructor.
     * @param TrickGroupRepository $repository
     * @param EntityManagerInterface $em
     */
    public function __construct(TrickGroupRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @route ("/admin/category", name="admin_category_index")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {

        $categories = $this->repository->findBy(
            array(), array('id' => 'DESC')
        );
        return $this->render('/admin/category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @route ("/admin/category/edit/{id}", name="admin_category_edit", methods="GET|POST")
     * @param TrickGroup $trickGroup
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function edit(TrickGroup $trickGroup, Request $request)
    {
        $form = $this->createForm(TrickGroupType::class, $trickGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->em->flush();
            $this->addFlash('success', 'Categorie modifié avec succès');
            return $this->redirectToRoute('admin_category_index');
        }

        return $this->render('admin/category/edit.html.twig', [
            'trickGroup' => $trickGroup,
            'form' => $form->createView()
        ]);
    }

    /**
     * @route ("/admin/category/create", name="admin_category_create")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function create(Request $request)
    {
        $category = new TrickGroup();
        $form = $this->createForm(TrickGroupType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->em->persist($category);
            $this->em->flush();
            $this->addFlash('success', 'Category crée avec succès');
            return $this->redirectToRoute('admin_category_index');
        }

        return $this->render('admin/category/new.html.twig', [
            'category' => $category,
            'form' => $form->createView()
        ]);

    }

    /**
     * @route ("/admin/category/edite/{id}", name="admin_category_delete", methods="DELETE")
     * @param TrickGroup $category
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(TrickGroup $category, Request $request)
    {
        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->get('_token')))
        {
            $this->em->remove($category);
            $this->em->flush();
            $this->addFlash('success', 'Catégorie supprimé avec succès');

        }
        return $this->redirectToRoute('admin_category_index');

    }


}