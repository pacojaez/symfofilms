<?php

namespace App\Entity;

use App\Repository\MovieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MovieRepository::class)
 */
class Movie
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $titulo;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $duracion;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $director;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $genero;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sinopsis;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $estreno;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $valoracion;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $caratula;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="movies")
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity=Comment::class, mappedBy="movie")
     */
    private $comments;

    /**
     * @ORM\ManyToMany(targetEntity=Actor::class, inversedBy="movies")
     */
    private $actors;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->actors = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): self
    {
        $this->titulo = $titulo;

        return $this;
    }

    public function getDuracion(): ?int
    {
        return $this->duracion;
    }

    public function setDuracion(?int $duracion): self
    {
        $this->duracion = $duracion;

        return $this;
    }

    public function getDirector(): ?string
    {
        return $this->director;
    }

    public function setDirector(?string $director): self
    {
        $this->director = $director;

        return $this;
    }

    public function getGenero(): ?string
    {
        return $this->genero;
    }

    public function setGenero(?string $genero): self
    {
        $this->genero = $genero;

        return $this;
    }

    public function getSinopsis(): ?string
    {
        return $this->sinopsis;
    }

    public function setSinopsis(?string $sinopsis): self
    {
        $this->sinopsis = $sinopsis;

        return $this;
    }

    public function getEstreno(): ?int
    {
        return $this->estreno;
    }

    public function setEstreno(?int $estreno): self
    {
        $this->estreno = $estreno;

        return $this;
    }

    public function getValoracion(): ?int
    {
        return $this->valoracion;
    }

    public function setValoracion(?int $valoracion): self
    {
        $this->valoracion = $valoracion;

        return $this;
    }

    public function getCaratula(): ?string
    {
        return $this->caratula;
    }

    public function setCaratula(?string $caratula): self
    {
        $this->caratula = $caratula;

        return $this;
    }

    public function  __toString(): string {
        return "La pelicula $this->titulo con id $this->id, dirigida por $this->director 
                tiene una duración de $this->duracion minutos, una valoración de $this->valoracion / 5
                 y es de género $this->genero ";
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

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->addMovie($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            $comment->removeMovie($this);
        }

        return $this;
    }

    /**
     * @return Collection|Actor[]
     */
    public function getActors(): Collection
    {
        return $this->actors;
    }

    public function addActor(Actor $actor): self
    {
        if (!$this->actors->contains($actor)) {
            $this->actors[] = $actor;
        }

        return $this;
    }

    public function removeActor(Actor $actor): self
    {
        $this->actors->removeElement($actor);

        return $this;
    }
}
