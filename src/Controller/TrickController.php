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
class TrickController extends AbstractController
{

    private $security;


    private $em;

    public function __construct(Security $security, EntityManagerInterface $em)
    {
        $this->security = $security;
        $this->em = $em;
    }

    /**
     * @Route("/tricks", name="trick_index")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $repository = $this->getDoctrine()->getRepository(Trick::class);
        $tricks = $repository->findBy(['status' => 'valide'], ['create_at' => 'DESC']);
        return $this->render('trick/index.html.twig', [
            'current_menu' => 'home',
            'request' => $request,
            'tricks' => $tricks
        ]);
    }

    /**
     * @route ("/profile/tricks", name="profile_tricks")
     * @return Response
     */
    public function tricks(): Response
    {
        return $this->render('/profile/tricks.html.twig');
    }


    /**
     * @Route("/{slug}_{id}/", name="trick_read")
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

            return $this->redirectToRoute('trick_read', ['id' => $trick->getId(), 'slug' => $trick->getSlug()]);

        }


        return $this->render('trick/read.html.twig', [
            'trick' => $trick,
            'form' => $form->createView()
        ]);

    }

    /**
     * @route ("profile/trick/edite/{id}", name="trick_delete", methods="DELETE")
     * @param Trick $trick
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Trick $trick, Request $request): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete' . $trick->getId(), $request->get('_token')))
        {
            $this->em->remove($trick);
            $this->em->flush();
            $this->addFlash('success', 'Trick supprimé avec succès');

        }
        return $this->redirectToRoute('trick_index');

    }

    /**
     * @route ("profile/trick/create", name="trick_create")
     * @param Request $request
     * @param UserInterface $user
     * @return RedirectResponse|Response
     */
    public function create(Request $request, UserInterface $user)
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $images = $form->get('images')->getData();
            foreach ($images as $image)
            {
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();
                $image->move($this->getParameter('images_directory'), $fichier);
                $img = new Image();
                $img->setName($fichier);
                $trick->addImage($img);
            }
            $video = $form->get('videos')->getData();
            if ($video)
            {
                $vid = new Video();
                $vid->setUrl($video);
                $trick->addVideo($vid);
            }

            $slug = (new \App\URL)->slugify($trick->getTitle());
            $trick->setSlug($slug);
            $trick->setUser($user);

            $this->em->persist($trick);
            $this->em->flush();
            $this->addFlash('success', 'Trick crée avec succès');
            return $this->redirectToRoute('trick_index');
        }
        return $this->render('trick/new.html.twig', ['trick' => $trick, 'form' => $form->createView()]);
    }

    /**
     * @route ("profile/trick/edit/{id}", name="trick_edit", methods="GET|POST")
     * @param Trick $trick
     * @param Request $request
     * @param UserInterface $user
     * @return Response
     */
    public function edit(Trick $trick, Request $request, UserInterface $user): Response
    {
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $images = $form->get('images')->getData();
            foreach ($images as $image)
            {
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();
                $image->move($this->getParameter('images_directory'), $fichier);
                $img = new Image();
                $img->setName($fichier);
                $trick->addImage($img);
            }
            $video = $form->get('videos')->getData();
            if(!$video == null)
            {
                $vid = new Video();
                $vid->setUrl($video);
                $vid->setTrick($trick);
                $trick->addVideo($vid);
            }
            $slug = (new \App\URL)->slugify($trick->getTitle());
            $trick->setSlug($slug);
            $trick->setUser($user);
            $this->em->flush();
            $this->addFlash('success', 'Trick modifié avec succès');
            return $this->redirectToRoute('trick_edit', ['id' => $trick->getId()] );
        }
        return $this->render('trick/edit.html.twig', ['trick' => $trick, 'form' => $form->createView()]);
    }

    /**
     * @Route("profile/delete/image/{id}", name="trick_delete_image", methods={"DELETE"})
     * @param Image $image
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteImage(Image $image, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if($this->isCsrfTokenValid('delete' . $image->getId(), $data['_token']))
        {
            $imageName = $image->getName();
            unlink($this->getParameter('images_directory') . '/' . $imageName);

            $tricks = $image->getTrick();
            $tricks->removeImage($image);

            $em = $this->getDoctrine()->getManager();
            $em->remove($image);
            $em->flush();

            //On repond en json
            return new JsonResponse(['success' => 1]);
        }else{
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }
    }


    /**
     * @Route("profile/delete/video/{id}", name="trick_delete_video", methods={"DELETE"})
     * @param Video $video
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteVideo(Video $video, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if($this->isCsrfTokenValid('delete' . $video->getId(), $data['_token']))
        {
            $em = $this->getDoctrine()->getManager();
            $em->remove($video);
            $em->flush();

            return new JsonResponse(['success' => 1]);
        }else{
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }
    }

    /**
     * @Route("profile/edit/image/trick/{id}", name="trick_main_image")
     * @param Trick $trick
     * @param Request $request
     * @param Image $image
     * @return RedirectResponse
     */
    public function defaultImage(trick $trick, Request $request): RedirectResponse
    {
        $imageId = $request->get('id_img');
        $img = $this->em->find(Image::class, $imageId);
        $trick->setMainImage($img);

        $this->em->persist($trick);
        $this->em->flush();
        $this->addFlash('success', 'Trick modifié avec succès');
        return $this->redirectToRoute('trick_edit', ['id' => $trick->getId()] );
    }

    /**
     * @Route("/{start}", name="loadMoreTricks", requirements={"start": "\d+"})
     * @param int $start
     * @return Response
     */
    public function loadMoreTricks($start = 5): Response
    {
        $repository = $this->getDoctrine()->getRepository(Trick::class);
        $tricks = $repository->findBy(['status' => 'valide'], ['create_at' => 'DESC'], 5, $start);
        return $this->render('trick/loadMoreTrick.html.twig', [
            'tricks' => $tricks
        ]);
    }

    /**
     * @Route("/{slug}_{id}/{start}", name="loadMoreComments", requirements={"start": "\d+"})
     * @param Trick $trick
     * @param int $start
     * @return Response
     */
    public function loadMoreComments(Trick $trick, $start = 5): Response
    {
        $slug = $trick->getSlug();
        $id = $trick->getId();
        $repository = $this->getDoctrine()->getRepository(Trick::class);
        $trick = $repository->find($id);

        return $this->render('trick/loadMoreComment.html.twig', [
            'trick' => $trick,
            'start' => $start
        ]);
    }

}
