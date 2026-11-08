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
    l.Valor;
