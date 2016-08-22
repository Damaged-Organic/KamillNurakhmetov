<?php
// src/AppBundle/Entity/StoryCategory.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

use Gedmo\Mapping\Annotation as Gedmo,
    Gedmo\Translatable\Translatable;

use AppBundle\Entity\Utility\DoctrineMapping\IdMapper,
    AppBundle\Entity\Utility\DoctrineMapping\TranslationMapper;

/**
 * @ORM\Table(name="stories_categories")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\StoryCategoryRepository")
 *
 * @Gedmo\TranslationEntity(class="AppBundle\Entity\StoryCategoryTranslation")
 */
class StoryCategory
{
    use IdMapper, TranslationMapper;

    const FILTER_MYSTIC     = 'mystic';
    const FILTER_ADVENTURES = 'adventures';
    const FILTER_WAR        = 'war';
    const FILTER_LOVE       = 'love';
    const FILTER_LIFE       = 'life';

    /**
     * @ORM\OneToMany(targetEntity="StoryCategoryTranslation", mappedBy="object", cascade={"persist", "remove"})
     */
    protected $translations;

    /**
     * @ORM\OneToMany(targetEntity="Story", mappedBy="storyCategory", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $stories;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $alias;

    /**
     * @ORM\Column(type="string", length=511, nullable=false)
     *
     * @Gedmo\Translatable
     */
    protected $title;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->translations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->stories      = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * To string
     */
    public function __toString()
    {
        return ( $this->title ) ? $this->title : "";
    }

    /**
     * Set alias
     *
     * @param string $alias
     * @return StoryCategory
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Get alias
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return StoryCategory
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Add story
     *
     * @param \AppBundle\Entity\Story $story
     * @return StoryCategory
     */
    public function addStory(\AppBundle\Entity\Story $story)
    {
        $story->addStoryCategory($this);
        $this->stories[] = $story;

        return $this;
    }

    /**
     * Remove stories
     *
     * @param \AppBundle\Entity\Story $stories
     */
    public function removeStory(\AppBundle\Entity\Story $stories)
    {
        $this->stories->removeElement($stories);
    }

    /**
     * Get stories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStories()
    {
        return $this->stories;
    }

    static public function getFilterParameters()
    {
        return [self::FILTER_MYSTIC, self::FILTER_ADVENTURES, self::FILTER_WAR, self::FILTER_LOVE, self::FILTER_LIFE];
    }
}
