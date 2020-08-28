<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CommentaireGeneralRepository;

/**
 * @ORM\Entity(repositoryClass=CommentaireGeneralRepository::class)
 * @ApiResource(
 *   collectionOperations={
 *   "getCommentaire"={
 *              "method"="GET", 
 *              "path"="users/promo/{id}/apprenant/{ida}/chats/date",
 * 
 * 
 * 
 *          },
 *   "AddCommentaire"={
 *              "method"="POST", 
 *              "path"="users/promo/{id}/apprenant/{ida}/chats",
 *              "defaults"={"id"=null}
 * 
 * },
 * }
 * )
 */
class CommentaireGeneral
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="blob", nullable=true)
     */
    private $pieceJointe;

    /**
     * @ORM\ManyToOne(targetEntity=FilDeDiscussion::class, inversedBy="commentaireGenerals")
     */
    private $filDeDiscussion;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $user;

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
        return base64_encode((stream_get_contents($this->pieceJointe))) ;
    }

    public function setPieceJointe($pieceJointe): self
    {
        $this->pieceJointe = $pieceJointe;

        return $this;
    }

    public function getFilDeDiscussion(): ?FilDeDiscussion
    {
        return $this->filDeDiscussion;
    }

    public function setFilDeDiscussion(?FilDeDiscussion $filDeDiscussion): self
    {
        $this->filDeDiscussion = $filDeDiscussion;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
