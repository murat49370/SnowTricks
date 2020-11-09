<?php

namespace App\Controller;


use App\Entity\Comment;
use App\Entity\Trick;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;


/**
 * Class HomeController
 * @package App\Controller
 */
class TrickController extends AbstractController
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @Route("/", name="home")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $tricks = $this->getDoctrine()->getRepository(Trick::class)->getAllTricks();
        return $this->render('home/index.html.twig', [
            'current_menu' => 'home',
            'request' => $request,
            'tricks' => $tricks
        ]);
    }

    /**
     * @Route("/trick-{id}", name="trick_read")
     * @param Trick $trick
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function read(Trick $trick, Request $request, EntityManagerInterface $entityManager):Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);
        if ($form-> isSubmitted() && $form->isValid())
        {
            $user = $this->security->getUser();
            $comment->setUser($user);

            $comment->setTrick($trick);
            $entityManager->persist($comment);
            $entityManager->flush();
            $this->addFlash('success', 'Commentaire a bien été enregistré, il sera publier aprés sa validation');

            return $this->redirectToRoute('trick_read', ['id' => $trick->getId()]);

        }


        return $this->render('trick/read.html.twig', [
            'trick' => $trick,
            'form' => $form->createView()
        ]);

    }
}
