<?php

namespace App\Tests\Controller;

use App\Entity\Assunto;
use App\Entity\Autor;
use App\Entity\Livro;
use App\Service\LivroService;
use Doctrine\DBAL\ConnectionException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class LivroControllerTest extends WebTestCase
{
    private array $livrosCriados = [];
    private array $autoresCriados = [];
    private array $assuntosCriados = [];

    public function testIndex(): void
    {
        $client = static::createClient();

        $client->request('GET', '/livro');

        self::assertResponseIsSuccessful();
    }

    public function testNew(): void
    {
        $client = static::createClient();

        $client->request('GET', '/livro/new');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('form');
    }

    public function testCadastroLivro(): void
    {
        $client = static::createClient();

        $entityManager = $client->getContainer()
            ->get(EntityManagerInterface::class);

        $autor = new Autor();
        $autor->setNome('Autor Teste');

        $assunto = new Assunto();
        $assunto->setDescricao('Ficção');

        $entityManager->persist($autor);
        $entityManager->persist($assunto);
        $entityManager->flush();

        $this->autoresCriados[] = $autor->getId();
        $this->assuntosCriados[] = $assunto->getId();

        $entityManager->clear();

        self::assertNotNull($autor->getId());
        self::assertNotNull($assunto->getId());

        $crawler = $client->request('GET', '/livro/new');

        $form = $crawler->selectButton('Salvar')->form();

        $dados = [
            'livro' => [
                'titulo' => 'Livro de Teste',
                'editora' => 'Editora Teste',
                'edicao' => '1',
                'anoPublicacao' => '2026',
                'valor' => '39,90',
                'autores' => [(string) $autor->getId()],
                'assuntos' => [(string) $assunto->getId()],
                '_token' => $form->getPhpValues()['livro']['_token'],
            ],
        ];

        $client->request('POST', '/livro/new', $dados);

        self::assertResponseRedirects('/livro');

        $livro = $entityManager
            ->getRepository(Livro::class)
            ->findOneBy(['titulo' => 'Livro de Teste']);

        self::assertNotNull($livro);

        $this->livrosCriados[] = $livro->getId();

        self::assertSame('Editora Teste', $livro->getEditora());
        self::assertSame('39.90', $livro->getValor());
        self::assertCount(1, $livro->getAutores());
        self::assertCount(1, $livro->getAssuntos());
    }

    public function testEdit(): void
    {
        $client = static::createClient();

        $entityManager = $client->getContainer()
            ->get(EntityManagerInterface::class);

        $livro = $entityManager
            ->getRepository(Livro::class)
            ->findOneBy(['titulo' => 'Livro de Teste']);

        self::assertNotNull($livro);

        $client->request('GET', '/livro/' . $livro->getId() . '/edit');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('form');
    }

    public function testAtualizarLivro(): void
    {
        $client = static::createClient();

        $entityManager = $client->getContainer()
            ->get(EntityManagerInterface::class);

        $autor = new Autor();
        $autor->setNome('Autor Edição');

        $assunto = new Assunto();
        $assunto->setDescricao('Romance');

        $livro = new Livro();
        $livro->setTitulo('Livro para Editar');
        $livro->setEditora('Editora Teste');
        $livro->setEdicao(1);
        $livro->setAnoPublicacao('2026');
        $livro->setValor('39.90');

        $livro->addAutor($autor);
        $livro->addAssunto($assunto);

        $entityManager->persist($autor);
        $entityManager->persist($assunto);
        $entityManager->persist($livro);
        $entityManager->flush();

        $this->livrosCriados[] = $livro->getId();
        $this->autoresCriados[] = $autor->getId();
        $this->assuntosCriados[] = $assunto->getId();

        $id = $livro->getId();

        $crawler = $client->request('GET', '/livro/' . $id . '/edit');

        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('Atualizar')->form();

        $dados = $form->getPhpValues();

        $dados['livro']['titulo'] = 'Livro Atualizado';

        $dados['livro']['autores'] = array_map(
            static fn ($autor) => (string) $autor->getId(),
            $livro->getAutores()->toArray()
        );

        $dados['livro']['assuntos'] = array_map(
            static fn ($assunto) => (string) $assunto->getId(),
            $livro->getAssuntos()->toArray()
        );

        $client->request('POST', '/livro/' . $id . '/edit', $dados);

        self::assertResponseRedirects('/livro');

        $entityManager->clear();

        $livroAtualizado = $entityManager
            ->getRepository(Livro::class)
            ->find($id);

        self::assertNotNull($livroAtualizado);
        self::assertSame('Livro Atualizado', $livroAtualizado->getTitulo());
    }

    public function testExcluirLivro(): void
    {
        $client = static::createClient();

        $entityManager = $client->getContainer()
            ->get(EntityManagerInterface::class);

        // Cria um livro específico para o teste de exclusão.
        $autor = new Autor();
        $autor->setNome('Autor Exclusão');

        $assunto = new Assunto();
        $assunto->setDescricao('Aventura');

        $livro = new Livro();
        $livro->setTitulo('Livro para Excluir');
        $livro->setEditora('Editora Teste');
        $livro->setEdicao(1);
        $livro->setAnoPublicacao('2026');
        $livro->setValor('29.90');

        $livro->addAutor($autor);
        $livro->addAssunto($assunto);

        $entityManager->persist($autor);
        $entityManager->persist($assunto);
        $entityManager->persist($livro);
        $entityManager->flush();

        $this->livrosCriados[] = $livro->getId();
        $this->autoresCriados[] = $autor->getId();
        $this->assuntosCriados[] = $assunto->getId();

        $id = $livro->getId();

        $crawler = $client->request('GET', '/livro/' . $id . '/edit');

        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('Sim, excluir livro')->form();

        $dados = $form->getPhpValues();

        $client->request('POST', '/livro/' . $id, $dados);

        self::assertResponseRedirects('/livro');

        $entityManager->clear();

        $livroExcluido = $entityManager
            ->getRepository(Livro::class)
            ->find($id);

        self::assertNull($livroExcluido);
    }

    public function testCadastroFalhaServico()
    {
        $client = static::createClient();
        $client->disableReboot();

        $container = static::getContainer();

        $entityManager = $container->get(EntityManagerInterface::class);

        $autor = $entityManager->getRepository(Autor::class)->find(1);
        $assunto = $entityManager->getRepository(Assunto::class)->find(1);

        self::assertNotNull($autor);
        self::assertNotNull($assunto);

        $livroService = $this->createMock(LivroService::class);

        $livroService->expects(self::once())
            ->method('salvar')
            ->willThrowException(
                new ConnectionException(
                    new \Exception('Falha ao salvar Livro')
                )
            );

        $container->set(LivroService::class, $livroService);

        $crawler = $client->request('GET', '/livro/new');
        $form = $crawler->selectButton('Salvar')->form();

        $dados = $form->getPhpValues();

        $dados['livro']['titulo'] = 'Livro com falha simulada';
        $dados['livro']['editora'] = 'Editora Teste';
        $dados['livro']['edicao'] = '1';
        $dados['livro']['anoPublicacao'] = '2026';
        $dados['livro']['valor'] = '39,90';
        $dados['livro']['autores'] = [(string) $autor->getId()];
        $dados['livro']['assuntos'] = [(string) $assunto->getId()];

        $client->request('POST', '/livro/new', $dados);

        self::assertResponseRedirects('/livro');

        $session = $client->getRequest()->getSession();
        $flashes = $session->getFlashBag()->peek('danger');

        self::assertContains('Não foi possível cadastrar o livro. Tente novamente mais tarde.', $flashes);
    }
}
