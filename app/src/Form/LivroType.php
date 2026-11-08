<?php

namespace App\Form;

use App\Entity\Assunto;
use App\Entity\Autor;
use App\Entity\Livro;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LivroType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titulo', null, [
                'attr' => [
                    'maxlength' => 255,
                ]
            ])
            ->add('editora', null, [
                'attr' => [
                    'maxlength' => 255,
                ]
            ])
            ->add('edicao', null, [
                'attr' => [
                    'min' => 0,
                ],
            ])
            ->add('anoPublicacao', null, [
                'attr' => [
                    'maxlength' => 10,
                    'pattern' => '.*',
                ]
            ])
            ->add('valor', MoneyType::class, [
                'currency' => 'BRL',
                'divisor' => 1,
                'scale' => 2,
                'html5' => false,
                'attr' => [
                    'placeholder' => 'Ex.: 39,90',
                ]
            ])
            ->add('autores', EntityType::class, [
                'class' => Autor::class,
                'choice_label' => 'nome',
                'multiple' => true,
                'expanded' => true,
                'label' => 'Autores',
            ])
            ->add('assuntos', EntityType::class, [
                'class' => Assunto::class,
                'choice_label' => 'descricao',
                'multiple' => true,
                'expanded' => true,
                'label' => 'Assuntos',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Livro::class,
        ]);
    }
}
