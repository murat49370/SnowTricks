<?php


namespace App\Controller\Profile;


use App\Entity\User;
use App\Form\ProfileType;
use App\Repository\TrickRepository;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ProfileHomeController extends AbstractController
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
     * @route ("/profile", name="profile_index")
     * @return Response
     */
    public function index()
    {
        return $this->render('/profile/index.html.twig');
    }

    /**
     * @route ("/profile/tricks", name="profile_tricks")
     * @return Response
     */
    public function tricks()
    {
        return $this->render('/profile/tricks.html.twig');
    }

    /**
     * @route ("/profile/edit", name="profile_edit", methods="GET|POST")
     * @param Request $request
     * @return Response
     */
    public function edit(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->em->persist($user);
            $this->em->flush();
            $this->addFlash('success', 'Profile modifié avec succès');
            return $this->redirectToRoute('profile_index');
        }

        return $this->render('profile/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }



}