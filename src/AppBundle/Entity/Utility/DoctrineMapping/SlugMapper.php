<?php
// src/AppBundle/Entity/Utility/DoctrineMapping/SlugMapper.php
namespace AppBundle\Entity\Utility\DoctrineMapping;

use Doctrine\ORM\Mapping as ORM;

use Gedmo\Mapping\Annotation as Gedmo;

trait SlugMapper
{
    /**
     * @ORM\Column(length=128)
     *
     * @Gedmo\Slug(
     *      fields={"title"},
     *      separator="_",
     *      style="lower"
     * )
     * @Gedmo\Translatable
     */
    protected $slug;

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }
}
