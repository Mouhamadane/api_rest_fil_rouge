<?php

namespace App\Entity;

use App\Repository\FilDeDiscussionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FilDeDiscussionRepository::class)
 */
class FilDeDiscussion
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titre;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\OneToOne(targetEntity=Promos::class, cascade={"persist", "remove"})
     */
    private $promo;

    /**
     * @ORM\OneToMany(targetEntity=CommentaireGeneral::class, mappedBy="filDeDiscussion")
     */
    private $commentaireGenerals;

    public function __construct()
    {
        $this->commentaireGenerals = new ArrayCollection();
    }

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getPromo(): ?Promos
    {
        return $this->promo;
    }

    public function setPromo(?Promos $promo): self
    {
        $this->promo = $promo;

        return $this;
    }

    /**
     * @return Collection|CommentaireGeneral[]
     */
    public function getCommentaireGenerals(): Collection
    {
        return $this->commentaireGenerals;
    }

    public function addCommentaireGeneral(CommentaireGeneral $commentaireGeneral): self
    {
        if (!$this->commentaireGenerals->contains($commentaireGeneral)) {
            $this->commentaireGenerals[] = $commentaireGeneral;
            $commentaireGeneral->setFilDeDiscussion($this);
        }

        return $this;
    }

    public function removeCommentaireGeneral(CommentaireGeneral $commentaireGeneral): self
    {
        if ($this->commentaireGenerals->contains($commentaireGeneral)) {
            $this->commentaireGenerals->removeElement($commentaireGeneral);
            // set the owning side to null (unless already changed)
            if ($commentaireGeneral->getFilDeDiscussion() === $this) {
                $commentaireGeneral->setFilDeDiscussion(null);
            }
        }

        return $this;
    }
}
