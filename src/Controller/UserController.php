<?php


namespace App\Controller;


use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\User;
use App\Form\RegistrationType;
use App\Form\TrickType;
use App\Form\UserType;
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


class UserController extends AbstractController
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
     * @param UserRepository $repository
     * @param EntityManagerInterface $em
     */
    public function __construct(UserRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

//    /**
//     * @route ("/profile", name="user_index")
//     * @param PaginatorInterface $paginator
//     * @param Request $request
//     * @return Response
//     */
//    public function index(PaginatorInterface $paginator, Request $request): Response
//    {
//
//        $users = $this->repository->findBy(
//            array(), array('registred_at' => 'DESC')
//        );
//        return $this->render('/admin/user/index.html.twig', [
//            'users' => $users,
//        ]);
//    }



    /**
     * @route ("/profile/{id}", name="user_edit", methods="GET|POST")
     * @param User $user
     * @param Request $request
     * @return Response
     */
    public function edit(User $user, Request $request)
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            // on récupere l'avatar
            $avatar = $form->get('avatar')->getData();
            if ($avatar)
            {
                //On genere nouveau non de fichier
                $fichier = md5(uniqid()) . '.' . $avatar->guessExtension();

                $avatar->move($this->getParameter('images_directory'), $fichier);
                // On stoch l'image dans la BDD (nom)
                $img = new Image();
                $img->setName($fichier);
                $user->addAvatar($img);
            }


            $this->em->flush();
            $this->addFlash('success', 'Utilisateur modifié avec succès');
            return $this->redirectToRoute('user_edit', ['id' => $user->getId()]);
        }

        return $this->render('profile/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * @route ("/profile/create", name="user_create")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function create(Request $request)
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->em->persist($user);
            $this->em->flush();
            $this->addFlash('success', 'Utilisateur crée avec succès');
            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);

    }

    /**
     * @route ("/profile/edite/{id}", name="user_delete", methods="DELETE")
     * @param User $user
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(User $user, Request $request)
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->get('_token')))
        {
            $this->em->remove($user);
            $this->em->flush();
            $this->addFlash('success', 'Utilisateur supprimé avec succès');

        }
        return $this->redirectToRoute('user_index');

    }


}