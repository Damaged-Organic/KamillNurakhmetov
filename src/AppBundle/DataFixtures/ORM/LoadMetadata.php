<?php
// src/AppBundle/DataFixtures/ORM/LoadMetadata.php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface,
    Doctrine\Common\Persistence\ObjectManager;

use AppBundle\Entity\Metadata;

class LoadMetadata extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $metadata = (new Metadata)
            ->setRoute("index")
            ->setRobots("index, follow")
            ->setTitle("Главная")
            ->setDescription("Главная - Автор современных книг и рассказов - Камиль Нурахметов - Официальный сайт писателя");
        $manager->persist($metadata);
        $manager->flush();

        $metadata->setTitle("Homepage")
            ->setDescription("Главная - Автор современных книг и рассказов - Камиль Нурахметов - Официальный сайт писателя")
            ->setTranslatableLocale('en');
        $manager->persist($metadata);
        $manager->flush();

        // ---

        $metadata = (new Metadata)
            ->setRoute("author")
            ->setRobots("index, follow")
            ->setTitle("Автор")
            ->setDescription("Автор - Автор современных книг и рассказов - Камиль Нурахметов - Официальный сайт писателя");
        $manager->persist($metadata);
        $manager->flush();

        $metadata->setTitle("Author")
            ->setDescription("Автор - Автор современных книг и рассказов - Камиль Нурахметов - Официальный сайт писателя")
            ->setTranslatableLocale('en');
        $manager->persist($metadata);
        $manager->flush();

        // ---

        $metadata = (new Metadata)
            ->setRoute("stories")
            ->setRobots("index, follow")
            ->setTitle("Рассказы")
            ->setDescription("Рассказы - Автор современных книг и рассказов - Камиль Нурахметов - Официальный сайт писателя");
        $manager->persist($metadata);
        $manager->flush();

        $metadata->setTitle("Stories")
            ->setDescription("Рассказы - Автор современных книг и рассказов - Камиль Нурахметов - Официальный сайт писателя")
            ->setTranslatableLocale('en');
        $manager->persist($metadata);
        $manager->flush();

        // ---

        $metadata = (new Metadata)
            ->setRoute("books")
            ->setRobots("index, follow")
            ->setTitle("Книги")
            ->setDescription("Книги - Автор современных книг и рассказов - Камиль Нурахметов - Официальный сайт писателя");
        $manager->persist($metadata);
        $manager->flush();

        $metadata->setTitle("Books")
            ->setDescription("Книги - Автор современных книг и рассказов - Камиль Нурахметов - Официальный сайт писателя")
            ->setTranslatableLocale('en');
        $manager->persist($metadata);
        $manager->flush();

        // ---

        $metadata = (new Metadata)
            ->setRoute("private_office")
            ->setRobots("noindex, nofollow, noarchive")
            ->setTitle("Личный Кабинет")
            ->setDescription("Личный Кабинет - Автор современных книг и рассказов - Камиль Нурахметов - Официальный сайт писателя");
        $manager->persist($metadata);
        $manager->flush();

        $metadata->setTitle("Private Office")
            ->setDescription("Личный Кабинет - Автор современных книг и рассказов - Камиль Нурахметов - Официальный сайт писателя")
            ->setTranslatableLocale('en');
        $manager->persist($metadata);
        $manager->flush();

        // ---

        $metadata = (new Metadata)
            ->setRoute("order")
            ->setRobots("noindex, nofollow, noarchive")
            ->setTitle("Оформление заказа")
            ->setDescription("Оформление заказа - Автор современных книг и рассказов - Камиль Нурахметов - Официальный сайт писателя");
        $manager->persist($metadata);
        $manager->flush();

        $metadata->setTitle("Order")
            ->setDescription("Оформление заказа - Автор современных книг и рассказов - Камиль Нурахметов - Официальный сайт писателя")
            ->setTranslatableLocale('en');
        $manager->persist($metadata);
        $manager->flush();

        // ---

        $metadata = (new Metadata)
            ->setRoute("feedback")
            ->setRobots("noindex, nofollow, noarchive")
            ->setTitle("Обратная связь")
            ->setDescription("Обратная связь - Автор современных книг и рассказов - Камиль Нурахметов - Официальный сайт писателя");
        $manager->persist($metadata);
        $manager->flush();

        $metadata->setTitle("Feedback")
            ->setDescription("Обратная связь - Автор современных книг и рассказов - Камиль Нурахметов - Официальный сайт писателя")
            ->setTranslatableLocale('en');
        $manager->persist($metadata);
        $manager->flush();

        // ---
    }

    public function getOrder()
    {
        return 1;
    }
}
