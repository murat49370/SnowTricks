<?php


namespace App\Controller\Admin;


use App\Entity\Comment;
use App\Entity\Trick;
use App\Form\AdminCommentType;
use App\Form\CommentType;
use App\Form\TrickType;
use App\Repository\CommentRepository;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class AdminCommentController extends AbstractController
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
     * AdminCommentController constructor.
     * @param CommentRepository $repository
     * @param EntityManagerInterface $em
     */
    public function __construct(CommentRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @route ("/admin/comment", name="admin_comment_index")
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $comments = $paginator->paginate($this->repository->getAllCommentsQuery(),
            $request->query->getInt('page', 1),
            10
        );
//        $comments = $this->repository->findBy(
//            array(), array('create_at' => 'DESC')
//        );
        return $this->render('/admin/comment/index.html.twig', [
            'comments' => $comments,
        ]);
    }

    /**
     * @route ("/admin/comment/edit/{id}", name="admin_comment_edit", methods="GET|POST")
     * @param Comment $comment
     * @param Request $request
     * @return Response
     */
    public function edit(Comment $comment, Request $request)
    {
        $form = $this->createForm(AdminCommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->em->flush();
            $this->addFlash('success', 'Commentaire modifié avec succès');
            return $this->redirectToRoute('admin_comment_index');
        }

        return $this->render('admin/comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView()
        ]);
    }



    /**
     * @route ("/admin/comment/edite/{id}", name="admin_comment_delete", methods="DELETE")
     * @param Comment $comment
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Comment $comment, Request $request)
    {
        if ($this->isCsrfTokenValid('delete' . $comment->getId(), $request->get('_token')))
        {
            $this->em->remove($comment);
            $this->em->flush();
            $this->addFlash('success', 'Comment supprimé avec succès');

        }
        return $this->redirectToRoute('admin_comment_index');

    }

    /**
     * @route ("/admin/comment/update/{id}", name="admin_comment_update")
     * @param Comment $comment
     * @return RedirectResponse
     */
    public function updateStatus(Comment $comment)
    {
        $comment->setStatus('valide');
        $this->em->persist($comment);
        $this->em->flush();
        $this->addFlash('success', 'Commentaire valider avec succès');
        return $this->redirectToRoute('admin_comment_index');

    }


}