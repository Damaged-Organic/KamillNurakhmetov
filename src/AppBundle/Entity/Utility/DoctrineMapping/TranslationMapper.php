<?php
// src/AppBundle/Entity/Utility/DoctrineMapping/TranslationMapper.php
namespace AppBundle\Entity\Utility\DoctrineMapping;

use Doctrine\ORM\Mapping as ORM;

use Gedmo\Mapping\Annotation as Gedmo;

trait TranslationMapper
{
    /**
     * @Gedmo\Locale
     */
    protected $locale;

    /*
     * Gedmo locale methods
     * Trait's function uses $this->translations property which should be defined in trait-user class
     */

    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    public function getTranslatableLocale()
    {
        return $this->locale;
    }

    public function getTranslations()
    {
        return $this->translations;
    }

    public function addTranslation($t)
    {
        $this->translations->add($t);
        $t->setObject($this);
    }

    public function removeTranslation($t)
    {
        $this->translations->removeElement($t);
    }

    public function setTranslations($translations)
    {
        $this->translations = $translations;
    }

    /* END Gedmo locale methods */
}