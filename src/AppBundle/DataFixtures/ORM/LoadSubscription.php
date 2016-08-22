<?php
// src/AppBundle/DataFixtures/ORM/LoadSubscription.php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface,
    Doctrine\Common\Persistence\ObjectManager;

use AppBundle\Entity\Subscription;

class LoadSubscription extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $subscription_1 = (new Subscription)
            ->setName("1 день")
            ->setDurationNumber(1)
            ->setDurationMeasure("day")
            ->setPriceUah(1.00)
            ->setPriceUsd(1.00)
            ->setPriceRub(1.00);
        $manager->persist($subscription_1);

        // ---

        $subscription_2 = (new Subscription)
            ->setName("2 недели")
            ->setDurationNumber(2)
            ->setDurationMeasure("week")
            ->setPriceUah(2.00)
            ->setPriceUsd(2.00)
            ->setPriceRub(2.00);
        $manager->persist($subscription_2);

        // ---

        $subscription_3 = (new Subscription)
            ->setName("1 месяц")
            ->setDurationNumber(1)
            ->setDurationMeasure("month")
            ->setPriceUah(3.00)
            ->setPriceUsd(3.00)
            ->setPriceRub(3.00);
        $manager->persist($subscription_3);

        // ---

        $manager->flush();
    }

    public function getOrder()
    {
        return 3;
    }
}