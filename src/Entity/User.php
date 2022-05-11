<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface, \Serializable
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
     * @ORM\ManyToMany(targetEntity=Cryptocurrency::class, mappedBy="crpt_fans")
     */
    private $user_Favourites;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="com_Owner")
     */
    private $user_Comments;

    /**
     * @ORM\Column(type="array")
     */
    private $user_Role = [];


    public function __toString() {
      return $this->user_Pseudo;
    }

    public function __construct()
    {
        $this->user_Favourites = new ArrayCollection();
        $this->user_Comments = new ArrayCollection();
        $this->isActive = true;
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


    public function getUserRole(): ?array
    {
        $roles = $this->user_Role;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setUserRole(array $user_Role): self
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




    public function getUsername()
    {
        return $this->user_Pseudo;
    }

    public function getPassword()
   {
       return $this->user_Password;
   }

   public function getRoles(): array
   {
       $roles = $this->user_Role;
       // guarantee every user at least has ROLE_USER
       $roles[] = 'ROLE_USER';

       return array_unique($roles);
   }

    public function eraseCredentials()
    {
    }

    public function getSalt()
   {
       return null;
   }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->user_Pseudo,
            $this->user_Password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->user_Pseudo,
            $this->user_Password,
            // see section on salt below
            // $this->salt
        ) = unserialize($serialized, array('allowed_classes' => false));
    }


}
