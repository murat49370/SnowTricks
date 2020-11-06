<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ImageRepository::class)
 */
class Image
{
//    /**
//     * @ORM\OneToOne(targetEntity=Trick::class)
//     * @ORM\Column(type="integer")
//     */
//    protected $trick;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $alt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @ORM\ManyToOne(targetEntity=Trick::class, inversedBy="images")
     * @ORM\JoinColumn(nullable=false)
     */
    private $trick;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(string $alt): self
    {
        $this->alt = $alt;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

//    /**
//     * @return int
//     */
//    public function getTrick(): ?int
//    {
//        return $this->trick;
//    }
//
//    /**
//     * @param int $trick
//     * @return Image
//     */
//    public function setTrick($trick)
//    {
//        $this->trick = $trick;
//        return $this;
//    }

public function getTrick(): ?Trick
{
    return $this->trick;
}

public function setTrick(?Trick $trick): self
{
    $this->trick = $trick;

    return $this;
}


}
