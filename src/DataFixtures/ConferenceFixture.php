<?php


namespace App\DataFixtures;


use App\Entity\Comment;
use App\Entity\Conference;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ConferenceFixture extends Fixture
{


    public function load(ObjectManager $manager)
    {

        $amsterdam = new Conference();
        $amsterdam->setCity('Amsterdam');
        $amsterdam->setYear('2020');
        $amsterdam->setIsInternational(true);
        $manager->persist($amsterdam);

        $paris = new Conference();
        $paris->setCity('Paris');
        $paris->setYear('2020');
        $paris->setIsInternational(false);
        $manager->persist($paris);


        $comment1 = new Comment();
        $comment1->setConference($amsterdam);
        $comment1->setAuthor('Edwin');
        $comment1->setEmail('edwin@optiwise.nl');
        $comment1->setText('nice');
        $comment1->setState('published');
        $manager->persist($comment1);

        $manager->flush();

    }

}