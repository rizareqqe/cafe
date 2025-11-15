<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity]
#[ORM\Table(name: 'orders')]
#[Vich\Uploadable]
class OrderEntity
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Customer::class)]
    private ?Customer $customer = null;

    #[ORM\ManyToMany(targetEntity: Dish::class)]
    private Collection $dishes;

    #[Vich\UploadableField(mapping: 'order_document', fileNameProperty: 'documentName')]
    private ?File $documentFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $documentName = null;

    public function __construct()
    {
        $this->dishes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }
    public function setCustomer(?Customer $c): self
    {
        $this->customer = $c;
        return $this;
    }
    public function getDishes(): Collection
    {
        return $this->dishes;
    }
    public function addDish(Dish $d): self
    {
        if (!$this->dishes->contains($d)) $this->dishes->add($d);
        return $this;
    }
    public function removeDish(Dish $d): self
    {
        $this->dishes->removeElement($d);
        return $this;
    }

    public function setDocumentFile(?File $f = null): void
    {
        $this->documentFile = $f;
    }
    public function getDocumentFile(): ?File
    {
        return $this->documentFile;
    }
    public function getDocumentName(): ?string
    {
        return $this->documentName;
    }
    public function setDocumentName(?string $n): void
    {
        $this->documentName = $n;
    }

    public function getTotalPrice(): int
    {
        return array_reduce($this->dishes->toArray(), fn($s, $d) => $s + $d->getPrice(), 0);
    }
}
