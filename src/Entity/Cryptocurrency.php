<?php

namespace App\Entity;

use App\Repository\CryptocurrencyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CryptocurrencyRepository::class)
 */
class Cryptocurrency
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
    private $crpt_Name;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $crpt_Symbol;

    /**
     * @ORM\Column(type="float")
     */
    private $crpt_Price;

    /**
     * @ORM\Column(type="float")
     */
    private $crpt_MarketCap;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $crpt_TwitterFollowers;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="user_Favourites")
     */
    private $crpt_fans;

    /**
     * @ORM\ManyToOne(targetEntity=Comment::class, inversedBy="com_Subject")
     */
    private $crpt_Comments;

    /**
     * @ORM\ManyToMany(targetEntity=Category::class, inversedBy="cat_cryptocurrencies")
     */
    private $crpt_Categories;

    public function __construct()
    {
        $this->crpt_fans = new ArrayCollection();
        $this->crpt_Categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCrptName(): ?string
    {
        return $this->crpt_Name;
    }

    public function setCrptName(string $crpt_Name): self
    {
        $this->crpt_Name = $crpt_Name;

        return $this;
    }

    public function getCrptSymbol(): ?string
    {
        return $this->crpt_Symbol;
    }

    public function setCrptSymbol(string $crpt_Symbol): self
    {
        $this->crpt_Symbol = $crpt_Symbol;

        return $this;
    }

    public function getCrptPrice(): ?float
    {
        return $this->crpt_Price;
    }

    public function setCrptPrice(float $crpt_Price): self
    {
        $this->crpt_Price = $crpt_Price;

        return $this;
    }

    public function getCrptMarketCap(): ?float
    {
        return $this->crpt_MarketCap;
    }

    public function setCrptMarketCap(float $crpt_MarketCap): self
    {
        $this->crpt_MarketCap = $crpt_MarketCap;

        return $this;
    }

    public function getCrptTwitterFollowers(): ?int
    {
        return $this->crpt_TwitterFollowers;
    }

    public function setCrptTwitterFollowers(?int $crpt_TwitterFollowers): self
    {
        $this->crpt_TwitterFollowers = $crpt_TwitterFollowers;

        return $this;
    }

    public function getCrptCategory(): ?Category
    {
        return $this->crpt_Category;
    }

    public function setCrptCategory(?Category $crpt_Category): self
    {
        $this->crpt_Category = $crpt_Category;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getCrptFans(): Collection
    {
        return $this->crpt_fans;
    }

    public function addCrptFan(User $crptFan): self
    {
        if (!$this->crpt_fans->contains($crptFan)) {
            $this->crpt_fans[] = $crptFan;
        }

        return $this;
    }

    public function removeCrptFan(User $crptFan): self
    {
        $this->crpt_fans->removeElement($crptFan);

        return $this;
    }

    public function getCrptComments(): ?Comment
    {
        return $this->crpt_Comments;
    }

    public function setCrptComments(?Comment $crpt_Comments): self
    {
        $this->crpt_Comments = $crpt_Comments;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCrptCategories(): Collection
    {
        return $this->crpt_Categories;
    }

    public function addCrptCategory(Category $crptCategory): self
    {
        if (!$this->crpt_Categories->contains($crptCategory)) {
            $this->crpt_Categories[] = $crptCategory;
        }

        return $this;
    }

    public function removeCrptCategory(Category $crptCategory): self
    {
        $this->crpt_Categories->removeElement($crptCategory);

        return $this;
    }
}
