<?php


namespace App\Controller\Admin;


use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\Video;
use App\Form\TrickType;
use App\Repository\TrickRepository;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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

//        $tricks = $paginator->paginate($this->repository->getAllTricksQuery(),
//            $request->query->getInt('page', 1),
//            10
//        );
//        dd($tricks);
        $tricks = $this->repository->findBy(
            array(), array('create_at' => 'DESC')
        );
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
            // on récupere les images
            $images = $form->get('images')->getData();

            // On boucle les images
            foreach ($images as $image)
            {
                //On genere nouveau non de fichier
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();

                $image->move(
                    $this->getParameter('images_directory'), $fichier
                );
                // On stoch l'image dans la BDD (nom)
                $img = new Image();
                $img->setName($fichier);
                $trick->addImage($img);
            }

            // on récupere les video
            $video = $form->get('videos')->getData();
            if(!$video == null)
            {
                $vid = new Video();
                $vid->setUrl($video);
                $vid->setTrick($trick);
                $trick->addVideo($vid);
            }
            $trick->setUpdateAt(new \DateTime());


            $this->em->flush();
            $this->addFlash('success', 'Trick modifié avec succès');
            return $this->redirectToRoute('admin_trick_index');
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
            // on récupere les images
            $images = $form->get('images')->getData();

            // On boucle les images
            foreach ($images as $image)
            {
                //On genere nouveau non de fichier
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();

                $image->move(
                    $this->getParameter('images_directory'), $fichier
                );
                // On stoch l'image dans la BDD (nom)
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

            $this->em->persist($trick);
            $this->em->flush();
            $this->addFlash('success', 'Trick crée avec succès');
            return $this->redirectToRoute('admin_trick_index');
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
        return $this->redirectToRoute('admin_trick_index');

    }

//    /**
//     * @Route("admin/delete/image/{id}", name="trick_delete_image", methods={"DELETE"})
//     * @param Image $image
//     * @param Request $request
//     * @return JsonResponse
//     */
//    public function deleteImage(Image $image, Request $request)
//    {
//        $data = json_decode($request->getContent(), true);
//
//        if($this->isCsrfTokenValid('delete' . $image->getId(), $data['_token']))
//        {
//            $imageName = $image->getName();
//            unlink($this->getParameter('images_directory') . '/' . $imageName);
//
//            $em = $this->getDoctrine()->getManager();
//            $em->remove($image);
//            $em->flush();
//
//            //On repond en json
//            return new JsonResponse(['success' => 1]);
//        }else{
//            return new JsonResponse(['error' => 'Token Invalide'], 400);
//        }
//
//
//    }
//
//    /**
//     * @Route("admin/delete/video/{id}", name="trick_delete_video", methods={"DELETE"})
//     * @param Video $video
//     * @param Request $request
//     * @return JsonResponse
//     */
//    public function deleteVideo(Video $video, Request $request)
//    {
//        $data = json_decode($request->getContent(), true);
//
//        if($this->isCsrfTokenValid('delete' . $video->getId(), $data['_token']))
//        {
//            $em = $this->getDoctrine()->getManager();
//            $em->remove($video);
//            $em->flush();
//
//            return new JsonResponse(['success' => 1]);
//        }else{
//            return new JsonResponse(['error' => 'Token Invalide'], 400);
//        }


//    }

}