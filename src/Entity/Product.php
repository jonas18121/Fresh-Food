<?php

namespace App\Entity;

use App\Entity\User;
use App\Entity\Category;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 */
class Product
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min=4, 
     *      minMessage="Le nom du produit doit avoir au minimum 4 caractères",
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank()
     * @Assert\GreaterThan(0, message="Définissez une quantité ")
     */
    private $quantity;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\LessThanOrEqual(
     *  "today", 
     *  message="Choisissez une date inférieur ou égale à la date d'aujourd'hui !"
     * )
     */
    private $purchase_date;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\GreaterThanOrEqual(
     *  propertyPath="purchase_date", 
     *  message="La Date Limite de Consomation doit être supérieur ou égale à la date d'achat du produit"
     * )
     */
    private $expiration_date;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\GreaterThanOrEqual(
     *  propertyPath="purchase_date", 
     *  message="La Date à Durée Minimale supérieur ou égale à la date d'achat du produit "
     * )
     */
    private $best_before_date;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $classifiedIn;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Emplacement", inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $placeIn;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Unity", inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $units;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\ImageProduct", inversedBy="product", cascade={"persist", "remove"})
     */
    private $imageProduct;

    public function __construct() {
        $this->createdAt = new \DateTimeImmutable();
        //$this->updateAt = new \DateTimeImmutable();
    }

    public function getImageProduct(): ?ImageProduct
    {
        return $this->imageProduct;
    }

    public function setImageProduct(?ImageProduct $imageProduct): self
    {
        $this->imageProduct = $imageProduct;

        if($imageProduct){
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    public function setQuantity(float $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getPurchaseDate(): ?\DateTimeInterface
    {
        return $this->purchase_date;
    }

    public function setPurchaseDate(\DateTimeInterface $purchase_date): self
    {
        $this->purchase_date = $purchase_date;

        return $this;
    }

    public function getExpirationDate(): ?\DateTimeInterface
    {
        return $this->expiration_date;
    }

    public function setExpirationDate(?\DateTimeInterface $expiration_date): self
    {
        $this->expiration_date = $expiration_date;

        return $this;
    }

    public function getBestBeforeDate(): ?\DateTimeInterface
    {
        return $this->best_before_date;
    }

    public function setBestBeforeDate(?\DateTimeInterface $best_before_date): self
    {
        $this->best_before_date = $best_before_date;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Category
     */
    public function getClassifiedIn(): ?Category
    {
        return $this->classifiedIn;
    }

    public function setClassifiedIn(?Category $classifiedIn)
    {
        $this->classifiedIn = $classifiedIn;

        return $this;
    }
    
    /**
     * @return Emplacement
     */
    public function getPlaceIn(): ?Emplacement
    {
        return $this->placeIn;
    }

    public function setPlaceIn(?Emplacement $placeIn)
    {
        $this->placeIn = $placeIn;

        return $this;
    }

    /**
     * @return Unity
     */
    public function getUnits(): ?Unity
    {
        return $this->units;
    }

    public function setUnits(?Unity $units)
    {
        $this->units = $units;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }
}
