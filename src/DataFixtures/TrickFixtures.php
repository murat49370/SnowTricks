<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\TrickGroup;
use App\Entity\User;
use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class TrickFixtures
 * @package App\DataFixtures
 */
class TrickFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        $user = new User();
        $user->setFirstName('Boulanger');
        $user->setLastName('Michel');
        $user->setRoles(["ROLE_ADMIN"]) ;
        $user->setStatus('valide');
        $user->setPseudo('admin');
        $user->setEmail('admin@admin.com');
        $user->setPassword($this->passwordEncoder->encodePassword($user, '1234'));
        $manager->persist($user);

        $user2 = new User();
        $user2->setFirstName('Toto');
        $user2->setLastName('Lacourt');
        //$user2->setRole('admin');
        $user2->setStatus('valide');
        $user2->setPseudo('toto');
        $user2->setEmail('jitewaboh@lagify.com');
        $user2->setPassword('1234');
        $manager->persist($user2);

        $trickGroup1 = new TrickGroup();
        $trickGroup1->setTitle("TrickGroup 1");
        $trickGroup1->setSlug('slug-trick-group-1');
        $manager->persist($trickGroup1);

        $trickGroup2 = new TrickGroup();
        $trickGroup2->setTitle("TrickGroup 2");
        $trickGroup2->setSlug('slug-trick-group-2');
        $manager->persist($trickGroup2);



        for ($i = 1; $i <= 25; $i++)
        {
            $trick = new Trick();
            $trick->setTitle($faker->words(5, true));
            $trick->setContent($faker->sentence(rand(75, 500), true));
            $trick->setStatus("valide");
            //$trick->setMainImage("http://placekitten.com/300/150");
            $trick->setSlug('slug-trick' . $i);
            $trick->setUser($user);
            $trick->addTrickGroup($trickGroup1);
            $manager->persist($trick);

            // Images
            $image = new Image();
            $image->setName('1.jpg');
            $image->setTrick($trick);
            $manager->persist($image);
            $image2 = new Image();
            $image2->setName('2.jpg');
            $image2->setTrick($trick);
            $manager->persist($image2);
            $image3 = new Image();
            $image3->setName('3.jpg');
            $image3->setTrick($trick);
            $manager->persist($image3);

            //video
            $video = new Video();
            $video->setTrick($trick);
            $video->setUrl('https://www.youtube.com/embed/8CtWgw9xYRE');
            $manager->persist($video);
            $video2 = new Video();
            $video2->setTrick($trick);
            $video2->setUrl('https://www.youtube.com/embed/o7OB24ACnVM');
            $manager->persist($video2);
            $video3 = new Video();
            $video3->setTrick($trick);
            $video3->setUrl('https://www.youtube.com/embed/46EpVLMbI-A');
            $manager->persist($video3);

            for ($j = 1; $j <= rand(3, 5); $j++)
            {
               $comment = new Comment();
               $comment->setTrick($trick);
               $comment->setUser($user);
               $comment->setStatus('valide');
               $comment->setContent($faker->sentence(15, true));
               $manager->persist($comment);
            }
            for ($j = 1; $j <= rand(3, 5); $j++)
            {
                $comment = new Comment();
                $comment->setTrick($trick);
                $comment->setUser($user2);
                $comment->setStatus('valide');
                $comment->setContent($faker->sentence(15, true));
                $manager->persist($comment);
            }

        }


        $manager->flush();
    }
}