<?php
// src/AppBundle/Entity/Metadata.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

use Gedmo\Mapping\Annotation as Gedmo,
    Gedmo\Translatable\Translatable;

use AppBundle\Entity\Utility\DoctrineMapping\IdMapper,
    AppBundle\Entity\Utility\DoctrineMapping\TranslationMapper;

/**
 * @ORM\Table(name="metadata")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\MetadataRepository")
 *
 * @Gedmo\TranslationEntity(class="AppBundle\Entity\MetadataTranslation")
 */
class Metadata implements Translatable
{
    use IdMapper, TranslationMapper;

    /**
     * @ORM\OneToMany(targetEntity="MetadataTranslation", mappedBy="object", cascade={"persist", "remove"})
     */
    protected $translations;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    protected $route;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     *
     * @Gedmo\Translatable
     */
    protected $title;

    /**
     * @ORM\Column(type="string", length=1023, nullable=true)
     *
     * @Gedmo\Translatable
     */
    protected $description;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @Gedmo\Translatable
     */
    protected $text;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $rawText;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $textFormatter;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $robots;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->translations = new ArrayCollection;
    }

    /**
     * To string
     */
    public function __toString()
    {
        return ( $this->title ) ? $this->title : "";
    }

    /**
     * Set route
     *
     * @param string $route
     * @return Metadata
     */
    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Get route
     *
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Metadata
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
     * Set description
     *
     * @param string $description
     * @return Metadata
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return Metadata
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set rawText
     *
     * @param string $rawText
     * @return Metadata
     */
    public function setRawText($rawText)
    {
        $this->rawText = $rawText;

        return $this;
    }

    /**
     * Get rawText
     *
     * @return string
     */
    public function getRawText()
    {
        return $this->rawText;
    }

    /**
     * Set textFormatter
     *
     * @param string $textFormatter
     * @return Metadata
     */
    public function setTextFormatter($textFormatter)
    {
        $this->textFormatter = $textFormatter;

        return $this;
    }

    /**
     * Get textFormatter
     *
     * @return string
     */
    public function getTextFormatter()
    {
        return $this->textFormatter;
    }

    /**
     * Set robots
     *
     * @param string $robots
     * @return Metadata
     */
    public function setRobots($robots)
    {
        $this->robots = $robots;

        return $this;
    }

    /**
     * Get robots
     *
     * @return string
     */
    public function getRobots()
    {
        return $this->robots;
    }
}
