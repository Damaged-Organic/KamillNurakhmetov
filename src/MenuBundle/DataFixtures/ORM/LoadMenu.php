<?php
// src/MenuBundle/DataFixtures/ORM/LoadMenu.php
namespace MenuBundle\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface,
    Doctrine\Common\Persistence\ObjectManager;

use MenuBundle\Entity\Menu;

class LoadMenu extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $menuItem = (new Menu)
            ->setTitle("Главная")
            ->setRoute("index")
        ;
        $manager->persist($menuItem);
        $manager->flush();

        $menuItem->setTitle("Homepage")
            ->setTranslatableLocale('en');
        $manager->persist($menuItem);
        $manager->flush();

        // ---

        $menuItem = (new Menu)
            ->setTitle("Автор")
            ->setRoute("author")
        ;
        $manager->persist($menuItem);
        $manager->flush();

        $menuItem->setTitle("Author")
            ->setTranslatableLocale('en');
        $manager->persist($menuItem);
        $manager->flush();

        // ---

        $menuItem = (new Menu)
            ->setTitle("Рассказы")
            ->setRoute("stories")
        ;
        $manager->persist($menuItem);
        $manager->flush();

        $menuItem->setTitle("Stories")
            ->setTranslatableLocale('en');
        $manager->persist($menuItem);
        $manager->flush();

        // ---

        $menuItem = (new Menu)
            ->setTitle("Книги")
            ->setRoute("books")
        ;
        $manager->persist($menuItem);
        $manager->flush();

        $menuItem->setTitle("Books")
            ->setTranslatableLocale('en');
        $manager->persist($menuItem);
        $manager->flush();

        // ---
    }

    public function getOrder()
    {
        return 1;
    }
}