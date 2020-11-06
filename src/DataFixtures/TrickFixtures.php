<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\TrickGroup;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Class TrickFixtures
 * @package App\DataFixtures
 */
class TrickFixtures extends Fixture
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {

        $user = new User();
        $user->setFirstName('Boulanger');
        $user->setLastName('Michel');
        $user->setRole('admin');
        $user->setStatus('valide');
        $user->setPseudo('admin');
        $user->setEmail('admin@admin.com');
        $user->setPassword('1234');
        $manager->persist($user);

        $user2 = new User();
        $user2->setFirstName('Boulanger');
        $user2->setLastName('Michel');
        $user2->setRole('admin');
        $user2->setStatus('valide');
        $user2->setPseudo('toto');
        $user2->setEmail('toto@admin.com');
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
            $trick->setTitle("Trick N°" . $i);
            $trick->setContent("Contenue du Trick N°" . $i);
            $trick->setStatus("valide");
            $trick->setMainImage("http://placekitten.com/300/300");
            $trick->setSlug('slug-trick' . $i);
            $trick->setUser($user);
            $trick->addTrickGroup($trickGroup1);
            $manager->persist($trick);

            // Images
            $image = new Image();
            $image->setUrl('http://placekitten.com/600/300');
            $image->setAlt('Mon petit chat');
            $image->setTrick($trick);
            $manager->persist($image);

            for ($j = 1; $j <= rand(5, 15); $j++)
            {
               $comment = new Comment();
               $comment->setTrick($trick);
               $comment->setUser($user);
               $comment->setStatus('valide');
               $comment->setContent("Contenue du commentaire N°" . $j);
               $manager->persist($comment);
            }

        }


        $manager->flush();
    }
}