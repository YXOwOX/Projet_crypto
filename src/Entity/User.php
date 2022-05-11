<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $user_Pseudo;

    /**
     * @ORM\Column(type="string", length=999)
     */
    private $user_Password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $user_Mail;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $user_Role;

    /**
     * @ORM\ManyToMany(targetEntity=Cryptocurrency::class, mappedBy="crpt_fans")
     */
    private $user_Favourites;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="com_Owner")
     */
    private $user_Comments;



    public function __construct()
    {
        $this->user_Favourites = new ArrayCollection();
        $this->user_Comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserPseudo(): ?string
    {
        return $this->user_Pseudo;
    }

    public function setUserPseudo(string $user_Pseudo): self
    {
        $this->user_Pseudo = $user_Pseudo;

        return $this;
    }

    public function getUserPassword(): ?string
    {
        return $this->user_Password;
    }

    public function setUserPassword(string $user_Password): self
    {
        $this->user_Password = $user_Password;

        return $this;
    }

    public function getUserMail(): ?string
    {
        return $this->user_Mail;
    }

    public function setUserMail(string $user_Mail): self
    {
        $this->user_Mail = $user_Mail;

        return $this;
    }

    public function getUserRole(): ?string
    {
        return $this->user_Role;
    }

    public function setUserRole(string $user_Role): self
    {
        $this->user_Role = $user_Role;

        return $this;
    }

    /**
     * @return Collection<int, Cryptocurrency>
     */
    public function getUserFavourites(): Collection
    {
        return $this->user_Favourites;
    }

    public function addUserFavourite(Cryptocurrency $userFavourite): self
    {
        if (!$this->user_Favourites->contains($userFavourite)) {
            $this->user_Favourites[] = $userFavourite;
            $userFavourite->addCrptFan($this);
        }

        return $this;
    }

    public function removeUserFavourite(Cryptocurrency $userFavourite): self
    {
        if ($this->user_Favourites->removeElement($userFavourite)) {
            $userFavourite->removeCrptFan($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getUserComments(): Collection
    {
        return $this->user_Comments;
    }

    public function addUserComment(Comment $userComment): self
    {
        if (!$this->user_Comments->contains($userComment)) {
            $this->user_Comments[] = $userComment;
            $userComment->setComOwner($this);
        }

        return $this;
    }

    public function removeUserComment(Comment $userComment): self
    {
        if ($this->user_Comments->removeElement($userComment)) {
            // set the owning side to null (unless already changed)
            if ($userComment->getComOwner() === $this) {
                $userComment->setComOwner(null);
            }
        }

        return $this;
    }

}
