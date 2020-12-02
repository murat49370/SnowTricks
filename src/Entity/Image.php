<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ImageRepository::class)
 */
class Image
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;



    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=Trick::class, inversedBy="images", cascade={"remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $trick;

//    /**
//     * @ORM\OneToOne(targetEntity=Trick::class, mappedBy="main_image")
//     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
//     */
//    private $mainImage;

    public function getId(): ?int
    {
        return $this->id;
    }

     public function getTrick(): ?Trick
    {
        return $this->trick;
    }

    public function setTrick(?Trick $trick): self
    {
        $this->trick = $trick;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return Image
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

//    public function getMainImage(): ?Trick
//    {
//        return $this->mainImage;
//    }
//
//    public function setMainImage(?Trick $mainImage): self
//    {
//        $this->mainImage = $mainImage;
//
//        // set (or unset) the owning side of the relation if necessary
//        $newMain_image = null === $mainImage ? null : $this;
//        if ($mainImage->getMainImage() !== $newMain_image) {
//            $mainImage->setMainImage($newMain_image);
//        }
//
//        return $this;
//    }

    public function __toString(): string
    {
        return $this->name;
    }




}
