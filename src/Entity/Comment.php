<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 */
class Comment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $com_DateTime;

    /**
     * @ORM\Column(type="string", length=1999)
     */
    private $com_text;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="user_Comments")
     */
    private $com_Owner;

    /**
     * @ORM\OneToMany(targetEntity=Cryptocurrency::class, mappedBy="crpt_Comments")
     */
    private $com_Subject;

    /**
    * @ORM\PrePersist()
    */
    public function prePersist()
    {
        $this->com_DateTime = new \DateTime();
    }

    public function __construct()
    {
        $this->com_Owner = new ArrayCollection();
        $this->com_Subject = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComDateTime(): ?\DateTimeInterface
    {
        return $this->com_DateTime;
    }

    public function setComDateTime(\DateTimeInterface $com_DateTime): self
    {
        $this->com_DateTime = $com_DateTime;

        return $this;
    }

    public function getComText(): ?string
    {
        return $this->com_text;
    }

    public function setComText(string $com_text): self
    {
        $this->com_text = $com_text;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getComOwner(): Collection
    {
        return $this->com_Owner;
    }

    public function addComOwner(User $comOwner): self
    {
        if (!$this->com_Owner->contains($comOwner)) {
            $this->com_Owner[] = $comOwner;
            $comOwner->setUserComments($this);
        }

        return $this;
    }

    public function removeComOwner(User $comOwner): self
    {
        if ($this->com_Owner->removeElement($comOwner)) {
            // set the owning side to null (unless already changed)
            if ($comOwner->getUserComments() === $this) {
                $comOwner->setUserComments(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Cryptocurrency>
     */
    public function getComSubject(): Collection
    {
        return $this->com_Subject;
    }

    public function addComSubject(Cryptocurrency $comSubject): self
    {
        if (!$this->com_Subject->contains($comSubject)) {
            $this->com_Subject[] = $comSubject;
            $comSubject->setCrptComments($this);
        }

        return $this;
    }

    public function removeComSubject(Cryptocurrency $comSubject): self
    {
        if ($this->com_Subject->removeElement($comSubject)) {
            // set the owning side to null (unless already changed)
            if ($comSubject->getCrptComments() === $this) {
                $comSubject->setCrptComments(null);
            }
        }

        return $this;
    }
}
