<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261106001332 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Cria a VIEW de relatório de livros';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE OR REPLACE VIEW vw_relatorio_livros AS
            SELECT
                a.CodAu AS autor_id,
                a.Nome AS autor_nome,
                l.Codl AS livro_id,
                l.Titulo AS livro_titulo,
                l.Editora AS editora,
                l.Edicao AS edicao,
                l.AnoPublicacao AS ano_publicacao,
                l.Valor AS valor,
                GROUP_CONCAT(
                    DISTINCT s.Descricao
                    ORDER BY s.Descricao
                    SEPARATOR ', '
                ) AS assuntos
            FROM Autor a
            INNER JOIN Livro_Autor la
                ON la.Autor_CodAu = a.CodAu
            INNER JOIN Livro l
                ON l.Codl = la.Livro_Codl
            LEFT JOIN Livro_Assunto ls
                ON ls.Livro_Codl = l.Codl
            LEFT JOIN Assunto s
                ON s.codAs = ls.Assunto_codAs
            GROUP BY
                a.CodAu,
                a.Nome,
                l.Codl,
                l.Titulo,
                l.Editora,
                l.Edicao,
                l.AnoPublicacao,
                l.Valor
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP VIEW IF EXISTS vw_relatorio_livros');
    }
}
