<?php

namespace App\Entity;

use App\Repository\CommentaireRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommentaireRepository::class)
 *
 */
class Commentaire
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Groups({"commentaire:write"})
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"commentaire:write"})
     */
    private $date;

    /**
     * @ORM\Column(type="blob", nullable=true)
     * @Groups({"commentaire:write"})
     */
    private $pieceJointe;

    /**
     * @ORM\ManyToOne(targetEntity=LivrableRendu::class, inversedBy="commentaires")
     * @Groups({"commentaire:write"})
     */
    private $livrableRendu;

    /**
     * @ORM\ManyToOne(targetEntity=Formateur::class, inversedBy="commentaires")
     * @Groups({"commentaire:write"})
     */
    private $formateur;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getPieceJointe()
    {
        return $this->pieceJointe;
    }

    public function setPieceJointe($pieceJointe): self
    {
        $this->pieceJointe = $pieceJointe;

        return $this;
    }

    public function getLivrableRendu(): ?LivrableRendu
    {
        return $this->livrableRendu;
    }

    public function setLivrableRendu(?LivrableRendu $livrableRendu): self
    {
        $this->livrableRendu = $livrableRendu;

        return $this;
    }

    public function getFormateur(): ?Formateur
    {
        return $this->formateur;
    }

    public function setFormateur(?Formateur $formateur): self
    {
        $this->formateur = $formateur;

        return $this;
    }
}
