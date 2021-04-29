<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Tests\TestApp\src\Entity;

use Doctrine\ORM\Mapping as ORM;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Annotation as QAG;

/**
 * @ORM\Entity()
 * @QAG\Crud(fetchMode="manual")
 */
class Category
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @QAG\ShowInList()
     */
    private $name;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }


    public function __toString()
    {
        return $this->name;
    }
}
