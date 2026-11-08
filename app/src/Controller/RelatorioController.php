<?php

namespace App\Controller;

use App\Report\LivrosPorAutorReport;
use Doctrine\DBAL\Connection;
use koolreport\widgets\koolphp\Table;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RelatorioController extends AbstractController
{
    #[Route('/relatorio', name: 'app_relatorio_index')]
    public function index(Connection $connection): Response
    {
        $report = new LivrosPorAutorReport($connection);
        $report->run();

        ob_start();

        Table::create([
            'dataSource' => $report->dataStore('livrosPorAutor'),
            'grouping' => [
                'autor_nome' => [
                    'calculate' => [],
                    'top' => '<span class="fw-semibold">{autor_nome}</span>'
                ],
            ],
            'columns' => [
                'livro_titulo' => [
                    'label' => 'Livro',
                ],
                'editora' => [
                    'label' => 'Editora',
                ],
                'edicao' => [
                    'label' => 'Edição',
                ],
                'ano_publicacao' => [
                    'label' => 'Ano',
                ],
                'valor' => [
                    'label' => 'Valor',
                    'type' => 'number',
                    'decimals' => 2,
                    'prefix' => 'R$',
                ],
                'assuntos' => [
                    'label' => 'Assuntos',
                ]
            ]
        ]);

        $tabela = ob_get_clean();

        return $this->render('relatorio/index.html.twig', [
            'tabela' => $tabela,
        ]);
    }
}
