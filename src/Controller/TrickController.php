<?php

namespace App\Controller;


use App\Entity\Trick;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class HomeController
 * @package App\Controller
 */
class TrickController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $tricks = $this->getDoctrine()->getRepository(Trick::class)->findAll();
        return $this->render('home/index.html.twig', [
            'current_menu' => 'home',
            'controller_name' => 'HomeController',
            'request' => $request,
            'tricks' => $tricks
        ]);
    }

    /**
     * @Route("/trick-{id}", name="trick_read")
     * @param Trick $trick
     * @return Response
     */
    public function read(Trick $trick):Response
    {
        return $this->render('trick/read.html.twig', [
            'trick' => $trick
        ]);

    }
}
