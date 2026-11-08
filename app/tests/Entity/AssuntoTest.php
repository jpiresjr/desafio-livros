<?php

namespace App\Tests\Entity;

use App\Entity\Assunto;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class AssuntoTest extends TestCase
{
    public function testDescricaoNaoUtrapassarCaracteres(): void
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $assunto = new Assunto();
        $assunto->setDescricao(str_repeat('A', 21));

        $erros = $validator->validate($assunto);

        $this->assertCount(1, $erros);
        $this->assertSame('A descrição do assunto deve ter no máximo 20 caracteres.', $erros[0]->getMessage());

    }

}
