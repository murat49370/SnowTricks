<?php

namespace App\DataFixtures;

use App\Entity\TrickGroup;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TrickGroupFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $trickGroup1 = new TrickGroup();
        $trickGroup1->setTitle("Grabs");
        $trickGroup1->setSlug('grabs');
        $manager->persist($trickGroup1);

        $trickGroup2 = new TrickGroup();
        $trickGroup2->setTitle("Rotations");
        $trickGroup2->setSlug('rotations');
        $manager->persist($trickGroup2);

        $trickGroup3 = new TrickGroup();
        $trickGroup3->setTitle("Flips");
        $trickGroup3->setSlug('flips');
        $manager->persist($trickGroup3);

        $trickGroup4 = new TrickGroup();
        $trickGroup4->setTitle("Slides");
        $trickGroup4->setSlug('slides');
        $manager->persist($trickGroup4);

        $trickGroup5 = new TrickGroup();
        $trickGroup5->setTitle("Old school");
        $trickGroup5->setSlug('old-school');
        $manager->persist($trickGroup5);

        $manager->flush();
    }
}
