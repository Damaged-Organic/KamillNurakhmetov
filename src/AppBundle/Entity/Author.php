<?php
// src/AppBundle/Entity/Author.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

use Gedmo\Mapping\Annotation as Gedmo,
    Gedmo\Translatable\Translatable;

use AppBundle\Entity\Utility\DoctrineMapping\IdMapper,
    AppBundle\Entity\Utility\DoctrineMapping\TranslationMapper;

/**
 * @ORM\Table(name="author")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\AuthorRepository")
 *
 * @Gedmo\TranslationEntity(class="AppBundle\Entity\AuthorTranslation")
 */
class Author implements Translatable
{
    use IdMapper, TranslationMapper;

    /**
     * @ORM\OneToMany(targetEntity="AuthorTranslation", mappedBy="object", cascade={"persist", "remove"})
     */
    protected $translations;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     *
     * @Gedmo\Translatable
     */
    protected $title;

    /**
     * @ORM\Column(type="text", nullable=false)
     *
     * @Gedmo\Translatable
     */
    protected $text;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    protected $rawText;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $textFormatter;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $worksNumber;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $readersNumber;

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
     * Set title
     *
     * @param string $title
     * @return Author
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
     * Set text
     *
     * @param string $text
     * @return Author
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
     * Set worksNumber
     *
     * @param integer $worksNumber
     * @return Author
     */
    public function setWorksNumber($worksNumber)
    {
        $this->worksNumber = $worksNumber;

        return $this;
    }

    /**
     * Get worksNumber
     *
     * @return integer 
     */
    public function getWorksNumber()
    {
        return $this->worksNumber;
    }

    /**
     * Set readersNumber
     *
     * @param integer $readersNumber
     * @return Author
     */
    public function setReadersNumber($readersNumber)
    {
        $this->readersNumber = $readersNumber;

        return $this;
    }

    /**
     * Get readersNumber
     *
     * @return integer 
     */
    public function getReadersNumber()
    {
        return $this->readersNumber;
    }

    /**
     * Set rawText
     *
     * @param string $rawText
     * @return Author
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
     * @return Author
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
}