<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Form\ResetPassType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use phpDocumentor\Reflection\Types\Null_;
use phpDocumentor\Reflection\Types\This;
use Swift_Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @route ("/login", name="login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * @route ("/inscription", name="security.registration")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @param Swift_Mailer $mailer
     * @return Response
     */
    public function registration(Request $request, UserPasswordEncoderInterface $encoder, Swift_Mailer $mailer)
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $user->setActivationToken(md5(uniqid()));

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            //Send activation message
            $message = (new \Swift_Message('Activation de votre compte'))
                ->setFrom('adessemail@monsite.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView('emails/activation.html.twig', [
                        'token' => $user->getActivationToken()
                    ]),
                    'text/html'
                );
            $mailer->send($message);

            $this->addFlash('success', 'Inscription réussie ! Veuillez consulter votre boite mail pour finaliser votre inscription.');

            return $this->redirectToRoute('login');
        }

        return $this->render('security/registration.html.twig', [
            'form' => $form->createView()
        ]);

    }

    /**
     * @Route("/activation/{token}", name="security_activation")
     * @param $token
     * @param UserRepository $userRepository
     * @return RedirectResponse
     */
    public function activation($token, UserRepository $userRepository)
    {
        $user = $userRepository->findOneBy(['activation_token' => $token]);

        if (!$user)
        {
            throw $this->createNotFoundException('Cet utilisateur n\'exsite pas.');
        }

        //On supprime le token
        $user->setActivationToken(null);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->addFlash('success', 'Vous avez bien activé votre compte! Connecter vous avec votre email et mot de passe.');

        return $this->redirectToRoute('login');

    }

    /**
     * @Route("/oubli-pass", name="security_forgotten_password")
     * @param Request $request
     * @param UserRepository $userRepository
     * @param Swift_Mailer $mailer
     * @param TokenGeneratorInterface $tokenGenerator
     * @return RedirectResponse|Response
     */
    public function forgottenPass(Request $request, UserRepository $userRepository, Swift_Mailer $mailer, TokenGeneratorInterface $tokenGenerator)
    {
        // On crée le formulaire
        $form = $this->createForm(ResetPassType::class);

        //On traite le firmulaire
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $donnees = $form->getData();

            //On recherche si un utilisateur a cet email
            $user = $userRepository->findOneByEmail($donnees['email']);

            if($user == null)
            {
                $this->addFlash('danger', 'Cette adresse email n\'existe pas');
                return $this->redirectToRoute('security_forgotten_password');
            }

            //On genere un token
            $token = $tokenGenerator->generateToken();

            try
            {
                $user->setResetToken($token);
                $this->entityManager->persist($user);
                $this->entityManager->flush();
            }catch (\Exception $e)
            {
                $this->addFlash('danger', 'Une erreur est survenue : ' . $e->getMessage());
                return $this->redirectToRoute('login');
            }

            // On genere url de reinisisalisation du mot de passe
            $url = $this->generateUrl('security_reset_password', [
                'token' => $token
            ], UrlGeneratorInterface::ABSOLUTE_URL);

            //Envoie du message
            $message = (new \Swift_Message('Mot de passe oublié'))
                ->setFrom('adessemail@monsite.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView('emails/reset_password.html.twig', [
                        'url' => $url
                    ]),
                    'text/html'
                );
            $mailer->send($message);

            //Message flash
            $this->addFlash('success', 'Un email de réinisisalisation de mot de passe vous a été envoyer.');

            return $this->redirectToRoute('login', [
                'token' => $token
            ]);

        }

        // On envoie vers la page de demande d'email
        return $this->render('security/forgotten_password.html.twig', [
            'emailForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/reset-pass/{token}", name="security_reset_password")
     * @param $token
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return RedirectResponse|Response
     */
    public function resetPassword($token, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        //Recherche utilisateur avec le token
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['reset_token' => $token]);

        if($user == null)
        {
            $this->addFlash('danger', 'Token inconnu');
            return $this->redirectToRoute('login');
        }

        //Si le formulaire est envoyé en méthode post (form fait a la main)
        if($request->isMethod('POST'))
        {

            $user->setResetToken(null);


            $user->setPassword($passwordEncoder->encodePassword($user, $request->get('password')));
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash('success', 'Mot de passe modifier avec succé');
            return $this->redirectToRoute('login');

        }else{
            return $this->render('security/reset_password.html.twig', [
                'token' => $token
            ]);
        }

    }


}