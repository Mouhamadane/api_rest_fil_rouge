<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping\InheritanceType;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * 
 * @ApiResource(
 *      normalizationContext={"groups"={"user:read"}},
 *      collectionOperations={
 *          "get_users"={
 *              "method"="GET",
 *              "path"="admin/users",
 *              "security"="is_granted('ROLE_ADMIN')",
 *              "security_message"= "Vous n'avez pas acces à cette ressource",
 *          },
 *          "add_users"={
 *              "method"="POST",
 *              "path"="admin/users",
 *              "security"="is_granted('ROLE_ADMIN')",
 *              "security_message"= "Vous n'avez pas acces à cette ressource",
 *              "route_name"="add_user"
 *          }    
 *      },
 *      itemOperations={
 *          "get_user"={
 *               "normalization_context"={"groups"={"user:read","user:read:all"}},
 *               "method"="GET",
 *               "path"="admin/users/{id}",
 *               "security"="is_granted('ROLE_ADMIN')",
 *               "security_message"= "Vous n'avez pas acces à cette ressource",
 *          },
 *           "update_user"={
 *                 "method"="PUT",
 *                 "path"="admin/users/{id}",
 *                 "security"="is_granted('ROLE_ADMIN')",
 *                 "security_message"= "Vous n'avez pas acces à cette ressource",
 *         }
 * }
 * )
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="dtype", type="string")
 * @DiscriminatorMap({"formateur" = "Formateur", "apprenant" = "Apprenant","user"="User"})
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * 
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({
     *     "promo:read",
     *     "promo:groupe:principal:read",
     *     "apprenant:competence:read",
     *     "promo:formateur:read",
     *     "promo:apprenant:read",
     *     "profilsortie:read"
     * })
     */

    protected $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({
     *      "profil:read:all",
     *      "promo:read",
     *      "apprenant:competence:read",
     *      "promo:write",
     *      "promo:formateur:read",
     *      "promo:apprenant:read",
     *      "profilsortie:read",
     *      "profilSortieSSS:read",
     *      "profilSortieapp:read",
     *      "briefpromo:read",
     *      "briefvalide:read"
     * })
     */
    protected $email;

    protected $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    protected $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({
     *      "profil:read:all",
     *      "user:read",
     *      "promo:read",
     *      "groupecompetence:read",
     *      "promo:groupe:principal:read",
     *      "promo:formateur:read",
     *      "promo:apprenant:read",
     *      "profilsortie:read",
     *      "profilsortieEtudiant:read",
     *      "profilSortieSSS:read",
     *      "profilSortieapp:read",
     *      "brief:read"
     * })
     */
    protected $nom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({
     *      "profil:read:all",
     *      "user:read",
     *      "promo:read",
     *      "apprenant:competence:read",
     *      "groupecompetence:read",
     *      "promo:groupe:principal:read",
     *      "promo:formateur:read",
     *      "promo:apprenant:read",
     *      "profilsortie:read",
     *      "profilSortieSSS:read",
     *      "profilSortieapp:read",
     *      "brief:read"
     * })
     */
    protected $prenom;


    /**
     * @ORM\Column(type="boolean")
     * @Groups({"user:read:all","apprenant:competence:read"})
     * 
     */
    protected $statut=false;


    /**
     * @ORM\Column(type="blob", nullable=true)
     * @Groups({"user:read:all","apprenant:competence:read"})
     */
    protected $avatar;

    /**
     * @ORM\ManyToOne(targetEntity=Profil::class, inversedBy="user")
     * @Groups({"user:read:all","apprenant:competence:read"})
     */
    protected $profil;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $isDeleted = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_'.strtoupper($this->getProfil()->getLibelle());

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getStatut(): ?bool
    {
        return $this->statut;
    }

    public function setStatut(bool $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getAvatar()
    {
        return $this->avatar;
    }

    public function setAvatar($avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getProfil(): ?Profil
    {
        return $this->profil;
    }

    public function setProfil(?Profil $profil): self
    {
        $this->profil = $profil;

        return $this;
    }

    public function getIsDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }
}
