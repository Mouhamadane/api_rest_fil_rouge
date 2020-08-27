<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\RessourceRepository;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=RessourceRepository::class)
 */
class Ressource
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"brief:read", "briefpromo:read","promo_brief:read","briefassigne:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"brief:read", "briefpromo:read"})
     */
    private $titre;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"brief:read","promo_brief:read", "briefpromo:read","briefassigne:read"})
     */
    private $url;

   

    /**
     * @ORM\ManyToOne(targetEntity=Brief::class, inversedBy="ressources")
     */
    private $brief;

    /**
     * @ORM\Column(type="blob")
     * @Groups({"brief:read"})
     */
    private $PieceJointe;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

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

    

    public function getBrief(): ?Brief
    {
        return $this->brief;
    }

    public function setBrief(?Brief $brief): self
    {
        $this->brief = $brief;

        return $this;
    }

    public function getPieceJointe()
    {
        return base64_encode((stream_get_contents($this->PieceJointe))) ;
    }

    public function setPieceJointe($PieceJointe): self
    {
        $this->PieceJointe = $PieceJointe;

        return $this;
    }
}
