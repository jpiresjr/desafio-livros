<?php

namespace App\Tests\Entity;

use App\Entity\Livro;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class LivroTest extends TestCase
{
    public function testTituloNaoUtrapassarCaracteres(): void
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $livro = new Livro();
        $livro->setTitulo(str_repeat('A', 41));

        $erros = $validator->validateProperty($livro, 'titulo');

        $this->assertCount(1, $erros);
        $this->assertSame('O título do livro deve ter no máximo 40 caracteres.', $erros[0]->getMessage());
    }

    public function testEdicaoMaiorZero(): void
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $livro = new Livro();
        $livro->setEdicao(0);

        $erros = $validator->validateProperty($livro, 'edicao');

        $this->assertCount(1, $erros);
        $this->assertSame('A edição deve ser um número maior que zero.', $erros[0]->getMessage());
    }

    public function testAnoPublicacaoConterQuatroDigitos(): void
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $livro = new Livro();
        $livro->setAnoPublicacao(20266);

        $erros = $validator->validateProperty($livro, 'anoPublicacao');

        $this->assertCount(1, $erros);
        $this->assertSame('O ano de publicação deve conter exatamente 4 números.', $erros[0]->getMessage());
    }

    public function testValorDeveSerMaiorQueZero(): void
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $livro = new Livro();
        $livro->setValor('0.00');

        $erros = $validator->validateProperty($livro, 'valor');

        $this->assertCount(1, $erros);
        $this->assertSame('O valor do livro deve ser maior que zero.', $erros[0]->getMessage());
    }

    public function testLivroDeveTerAoMenosUmAutor(): void
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $livro = new Livro();

        $erros = $validator->validateProperty($livro, 'autores');

        $this->assertCount(1, $erros);
        $this->assertSame('Selecione pelo menos 1 autor para o livro.', $erros[0]->getMessage());

    }

    public function testLivroTerAoMenosUmAssunto(): void
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $livro = new Livro();

        $erros = $validator->validateProperty($livro, 'assuntos');

        $this->assertCount(1, $erros);
        $this->assertSame('Selecione pelo menos 1 assunto para o livro.', $erros[0]->getMessage());
    }
}
