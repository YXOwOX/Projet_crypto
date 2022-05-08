<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 */
class Category
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $cat_Name;

    /**
     * @ORM\Column(type="string", length=500)
     */
    private $cat_Desc;

    /**
     * @ORM\ManyToMany(targetEntity=Cryptocurrency::class, mappedBy="crpt_Categories")
     */
    private $cat_cryptocurrencies;

    public function __construct()
    {
        $this->cat_cryptocurrencies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCatName(): ?string
    {
        return $this->cat_Name;
    }

    public function setCatName(string $cat_Name): self
    {
        $this->cat_Name = $cat_Name;

        return $this;
    }

    public function getCatDesc(): ?string
    {
        return $this->cat_Desc;
    }

    public function setCatDesc(string $cat_Desc): self
    {
        $this->cat_Desc = $cat_Desc;

        return $this;
    }

    /**
     * @return Collection<int, Cryptocurrency>
     */
    public function getCatCryptocurrencies(): Collection
    {
        return $this->cat_cryptocurrencies;
    }

    public function addCatCryptocurrency(Cryptocurrency $catCryptocurrency): self
    {
        if (!$this->cat_cryptocurrencies->contains($catCryptocurrency)) {
            $this->cat_cryptocurrencies[] = $catCryptocurrency;
            $catCryptocurrency->setCrptCategory($this);
        }

        return $this;
    }

    public function removeCatCryptocurrency(Cryptocurrency $catCryptocurrency): self
    {
        if ($this->cat_cryptocurrencies->removeElement($catCryptocurrency)) {
            // set the owning side to null (unless already changed)
            if ($catCryptocurrency->getCrptCategory() === $this) {
                $catCryptocurrency->setCrptCategory(null);
            }
        }

        return $this;
    }
}
