<?php

namespace App\Tests\Service;

use App\Entity\Livro;
use App\Service\LivroService;
use Doctrine\DBAL\ConnectionException;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class LivroServiceTest extends TestCase
{
    public function testSalvarLivro(): void
    {
        $livro = new Livro();

        $entityManager = $this->createMock(EntityManagerInterface::class);

        $entityManager->expects(self::once())->method('persist')->with($livro);
        $entityManager->expects(self::once())->method('flush');

        $livroService = new LivroService($entityManager);

        $livroService->salvar($livro);
    }

    public function testSalvarBancoFalha(): void
    {
        $livro = new Livro();

        $entityManager = $this->createMock(EntityManagerInterface::class);

        $entityManager->expects(self::once())->method('persist')->with($livro);
        $entityManager
            ->expects(self::once())
            ->method('flush')
            ->willThrowException(
                new ConnectionException(
                    new \Exception('Falha ao salvar livro.')
                )
            );

        $livroService = new LivroService($entityManager);

        $this->expectException(\Doctrine\DBAL\Exception::class);

        $livroService->salvar($livro);
    }
}
