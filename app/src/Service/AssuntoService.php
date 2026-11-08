<?php

namespace App\Service;

use App\Entity\Assunto;
use Doctrine\ORM\EntityManagerInterface;

class AssuntoService
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function salvar(Assunto $assunto): void
    {
        $this->entityManager->persist($assunto);
        $this->entityManager->flush();
    }

    public function editar(): void
    {
        $this->entityManager->flush();
    }

    public function excluir(Assunto $assunto): void
    {
        $this->entityManager->remove($assunto);
        $this->entityManager->flush();
    }
}
