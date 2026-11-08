<?php

namespace App\Tests\Controller;

use App\Entity\Assunto;
use App\Repository\AssuntoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class AssuntoControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    /** @var EntityRepository<Assunto> $assuntoRepository */
    private EntityRepository $assuntoRepository;
    private string $path = '/assunto/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->assuntoRepository = $this->manager->getRepository(Assunto::class);

    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Assuntos');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first()->text());
    }

    public function testNew(): void
    {
        $quantidade = $this->assuntoRepository->count([]);

        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Salvar', [
            'assunto[descricao]' => 'Testing',
        ]);

        self::assertResponseRedirects('/assunto');

        self::assertSame($quantidade + 1, $this->assuntoRepository->count([]));

    }

    public function testShow(): void
    {
        $fixture = new Assunto();
        $fixture->setDescricao('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Detalhes do assunto');

        // Use assertions to check that the properties are properly displayed.
//        $this->markTestIncomplete('This test was generated');
    }

    public function testEdit(): void
    {
        $fixture = new Assunto();
        $fixture->setDescricao('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Atualizar', [
            'assunto[descricao]' => 'Editar Assunto',
        ]);

        self::assertResponseRedirects('/assunto');

        $assuntoAtualizado = $this->assuntoRepository->find($fixture->getId());

        self::assertNotNull($assuntoAtualizado);
        self::assertSame('Editar Assunto', $assuntoAtualizado->getDescricao());

//        $this->markTestIncomplete('This test was generated');
    }

    public function testRemove(): void
    {
        $fixture = new Assunto();
        $fixture->setDescricao('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Sim, excluir assunto');

        self::assertResponseRedirects('/assunto');

        $assuntoExcluido = $this->assuntoRepository->find($fixture->getId());

        self::assertNull($assuntoExcluido);

    }
}
