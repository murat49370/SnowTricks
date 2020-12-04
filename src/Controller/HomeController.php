<?php

namespace App\Controller;


use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\Video;
use App\Form\CommentType;
use App\Form\TrickType;
use App\URL;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * Class HomeController
 * @package App\Controller
 */
class HomeController extends AbstractController
{

    private $security;


    private $em;

    public function __construct(Security $security, EntityManagerInterface $em)
    {
        $this->security = $security;
        $this->em = $em;
    }

    /**
     * @Route("/", name="home")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $repository = $this->getDoctrine()->getRepository(Trick::class);
        $tricks = $repository->findBy(['status' => 'valide'], ['create_at' => 'DESC']);
        return $this->render('home/index.html.twig', [
            'current_menu' => 'home',
            'request' => $request,
            'tricks' => $tricks
        ]);
    }



}
