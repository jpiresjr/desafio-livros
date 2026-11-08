<?php

namespace App\Entity;

use App\Repository\LivroRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LivroRepository::class)]
#[ORM\Table(name: 'Livro')]
class Livro
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'Codl', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'Titulo', length: 40)]
    #[Assert\NotBlank(message: 'O título do livro é obrigatório.')]
    #[Assert\Length(max: 40, maxMessage: 'O título do livro deve ter no máximo {{ limit }} caracteres.')]
    private ?string $titulo = null;

    #[ORM\Column(name: 'Editora', length: 40)]
    #[Assert\NotBlank(message: 'A editora do livro é obrigatória.')]
    #[Assert\Length(max: 40, maxMessage: 'A editora do livro deve ter no máximo {{ limit }} caracteres.')]
    private ?string $editora = null;

    #[ORM\Column(name: 'Edicao', type: 'integer')]
    #[Assert\NotBlank(message: 'A editição do livro é obrigatória.')]
    #[Assert\Positive(message: 'A edição deve ser um número maior que zero.')]
    private ?int $edicao = null;

    #[ORM\Column(name: 'AnoPublicacao', length: 4)]
    #[Assert\NotBlank(message: 'O ano da publicação do livro é obrigatória.')]
    #[Assert\Regex(pattern: '/^[0-9]{4}$/', message: 'O ano de publicação deve conter exatamente 4 números.')]
    private ?string $anoPublicacao = null;

    #[ORM\Column(name: 'Valor', type: 'decimal', precision: 10, scale: 2)]
    #[Assert\NotBlank(message: 'O valor do livro é obrigatório.')]
    #[Assert\Positive(message: 'O valor do livro deve ser maior que zero.')]
    #[Assert\LessThanOrEqual(value: '99999999.99', message: 'O valor do livro não pode ultrapassar R$ 99.999.999,99')]
    private ?string $valor = null;

    #[ORM\ManyToMany(targetEntity: Autor::class)]
    #[ORM\JoinTable(name: 'Livro_Autor')]
    #[ORM\JoinColumn(name: 'Livro_Codl', referencedColumnName: 'Codl')]
    #[ORM\InverseJoinColumn(name: 'Autor_CodAu', referencedColumnName: 'CodAu')]
    #[Assert\Count(min: 1, minMessage: 'Selecione pelo menos 1 autor para o livro.')]
    private Collection $autores;

    #[ORM\ManyToMany(targetEntity: Assunto::class)]
    #[ORM\JoinTable(name: 'Livro_Assunto')]
    #[ORM\JoinColumn(name: 'Livro_Codl', referencedColumnName: 'Codl')]
    #[ORM\InverseJoinColumn(name: 'Assunto_codAs', referencedColumnName: 'codAs')]
    #[Assert\Count(min: 1, minMessage: 'Selecione pelo menos 1 assunto para o livro.')]
    private Collection $assuntos;

    public function __construct()
    {
        $this->autores = new ArrayCollection();
        $this->assuntos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(?string $titulo): void
    {
        $this->titulo = $titulo;
    }

    public function getEditora(): ?string
    {
        return $this->editora;
    }

    public function setEditora(?string $editora): void
    {
        $this->editora = $editora;
    }

    public function getEdicao(): ?int
    {
        return $this->edicao;
    }

    public function setEdicao(?int $edicao): void
    {
        $this->edicao = $edicao;
    }

    public function getAnoPublicacao(): ?string
    {
        return $this->anoPublicacao;
    }

    public function setAnoPublicacao(?string $anoPublicacao): void
    {
        $this->anoPublicacao = $anoPublicacao;
    }

    public function getValor(): ?string
    {
        return $this->valor;
    }

    public function setValor(?string $valor): void
    {
        $this->valor = $valor;
    }

    public function getAutores(): Collection
    {
        return $this->autores;
    }

    public function addAutor(Autor $autor): static
    {
        if (!$this->autores->contains($autor)) {
            $this->autores->add($autor);
        }
        return $this;
    }

    public function removeAutor(Autor $autor): static
    {
        $this->autores->removeElement($autor);

        return $this;
    }

    public function getAssuntos(): Collection
    {
        return $this->assuntos;
    }

    public function addAssunto(Assunto $assunto): static
    {
        if (!$this->assuntos->contains($assunto)) {
            $this->assuntos->add($assunto);
        }
        return $this;
    }

    public function removeAssunto(Assunto $assunto): static
    {
        $this->assuntos->removeElement($assunto);

        return $this;
    }
}
