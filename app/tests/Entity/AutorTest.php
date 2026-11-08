<?php

namespace App\Tests\Entity;

use App\Entity\Autor;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class AutorTest extends TestCase
{
    public function testNomeNaoUtrapassarCaracteres()
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $autor = new Autor();
        $autor->setNome(str_repeat("a", 41));

        $erros = $validator->validate($autor);

        $this->assertCount(1, $erros);;
        $this->assertSame('O nome do autor deve ter no máximo 40 caracteres.', $erros[0]->getMessage());

    }

}
