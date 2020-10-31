<?php


namespace App\DataFixtures;


use App\Entity\Image;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ImageFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
        $image = new Image();
        $image->setUrl('http://placekitten.com/600/300');
        $image->setAlt('Mon petit chat');
        $image->setTrick(1);
        $manager->persist($image);
        $manager->flush();

    }

}