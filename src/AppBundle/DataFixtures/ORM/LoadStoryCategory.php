<?php
// src/AppBundle/DataFixtures/ORM/LoadStoryCategory.php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface,
    Doctrine\Common\Persistence\ObjectManager;

use AppBundle\Entity\StoryCategory;

class LoadStoryCategory extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $manager->persist(
            $mystic = (new StoryCategory)
                ->setAlias(StoryCategory::FILTER_MYSTIC)
                ->setTitle("Мистика")
        );

        $manager->persist(
            $adventures = (new StoryCategory)
                ->setAlias(StoryCategory::FILTER_ADVENTURES)
                ->setTitle("Приключения")
        );

        $manager->persist(
            $war = (new StoryCategory)
                ->setAlias(StoryCategory::FILTER_WAR)
                ->setTitle("Война")
        );

        $manager->persist(
            $love = (new StoryCategory)
                ->setAlias(StoryCategory::FILTER_LOVE)
                ->setTitle("Любовь")
        );

        $manager->persist(
            $life = (new StoryCategory)
                ->setAlias(StoryCategory::FILTER_LIFE)
                ->setTitle("Жизнь")
        );

        $manager->flush();

        $this->addReference(StoryCategory::FILTER_MYSTIC, $mystic);
        $this->addReference(StoryCategory::FILTER_ADVENTURES, $adventures);
        $this->addReference(StoryCategory::FILTER_WAR, $war);
        $this->addReference(StoryCategory::FILTER_LOVE, $love);
        $this->addReference(StoryCategory::FILTER_LIFE, $life);
    }

    public function getOrder()
    {
        return 2;
    }
}
