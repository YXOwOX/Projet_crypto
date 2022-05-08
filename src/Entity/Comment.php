<?php

namespace App\Entity;

use App\Repository\CommentRepository;
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
     * @ORM\Column(type="string", length=999)
     */
    private $com_Text;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="user_Comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $com_Owner;

    /**
     * @ORM\ManyToOne(targetEntity=Cryptocurrency::class, inversedBy="crpt_Comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $com_Subject;

    /**
    * @ORM\PrePersist()
    */
    public function prePersist()
    {
        $this->com_DateTime = new \DateTime('NOW');
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
        return $this->com_Text;
    }

    public function setComText(string $com_Text): self
    {
        $this->com_Text = $com_Text;

        return $this;
    }

    public function getComOwner(): ?User
    {
        return $this->com_Owner;
    }

    public function setComOwner(?User $com_Owner): self
    {
        $this->com_Owner = $com_Owner;

        return $this;
    }

    public function getComSubject(): ?Cryptocurrency
    {
        return $this->com_Subject;
    }

    public function setComSubject(?Cryptocurrency $com_Subject): self
    {
        $this->com_Subject = $com_Subject;

        return $this;
    }
}
