<?php

namespace App\Report;

use Doctrine\DBAL\Connection;
use RuntimeException;
use koolreport\KoolReport;
use koolreport\processes\Sort;
use PDO;

class LivrosPorAutorReport extends KoolReport
{
    public function __construct(private readonly Connection $connection)
    {
        parent::__construct();
    }

    protected function settings()
    {
        $pdo = $this->connection->getNativeConnection();

        if (!$pdo instanceof PDO) {
            throw new RuntimeException('O relatório necessita de uma conexão PDO');
        }

        return [
            'assets' => [
                'path' => '/var/www/html/public/koolreport_assets',
                'url' => '/koolreport_assets',
            ],
            'dataSources' => [
                'livros' => [
                    'connection' => $pdo,
                    'charset' => 'utf8mb4',
                ],
            ]
        ];
    }

    protected function setup()
    {
        $this->src('livros')
            ->query("
                SELECT *
                FROM vw_relatorio_livros
                ORDER BY autor_nome, livro_titulo
            ")
            ->pipe(new Sort([
                'autor_nome' => 'asc',
                'livro_titulo' => 'asc',
            ]))
            ->pipe($this->dataStore('livrosPorAutor'));
    }
}
