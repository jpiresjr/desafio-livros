<?php

namespace App\Service;

use App\Entity\Autor;
use Doctrine\ORM\EntityManagerInterface;

class AutorService
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function salvar(Autor $autor): void
    {
        $this->entityManager->persist($autor);
        $this->entityManager->flush();
    }

    public function editar(): void
    {
        $this->entityManager->flush();
    }

    public function excluir(Autor $autor): void
    {
        $this->entityManager->remove($autor);
        $this->entityManager->flush();
    }
}
