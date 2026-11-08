<?php

namespace App\Controller;

use App\Entity\Assunto;
use App\Form\AssuntoType;
use App\Repository\AssuntoRepository;
use App\Repository\LivroRepository;
use App\Service\AssuntoService;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/assunto')]
final class AssuntoController extends AbstractController
{
    #[Route(name: 'app_assunto_index', methods: ['GET'])]
    public function index(AssuntoRepository $assuntoRepository): Response
    {
        return $this->render('assunto/index.html.twig', [
            'assuntos' => $assuntoRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_assunto_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        AssuntoService $assuntoService,
        LoggerInterface $logger
    ): Response {
        $assunto = new Assunto();
        $form = $this->createForm(AssuntoType::class, $assunto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $assuntoService->salvar($assunto);
            } catch (Exception $exception) {
                $logger->error('Erro ao cadastrar assunto.', [
                    'exception' => $exception,
                    'op' => 'cadastro_assunto'
                ]);

                $this->addFlash(
                    'danger',
                    'Não foi possível cadastrar o assunto. Tente novamente mais tarde.'
                );

                return $this->redirectToRoute('app_assunto_new', [], Response::HTTP_SEE_OTHER);
            }

            $this->addFlash('success', 'Assunto cadastrado com sucesso!');

            return $this->redirectToRoute('app_assunto_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('assunto/new.html.twig', [
            'assunto' => $assunto,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_assunto_show', methods: ['GET'])]
    public function show(Assunto $assunto): Response
    {
        return $this->render('assunto/show.html.twig', [
            'assunto' => $assunto,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_assunto_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Assunto $assunto,
        AssuntoService $assuntoService,
        LoggerInterface $logger
    ): Response {
        $form = $this->createForm(AssuntoType::class, $assunto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $assuntoService->editar();
            } catch (Exception $exception) {
                $logger->error('Erro ao atualizar assunto.' , [
                    'exception' => $exception,
                    'op' => 'edicao_assunto',
                    'assunto_id' => $assunto->getId()
                ] );

                $this->addFlash('danger', 'Não foi possível atualizar o assunto. Tente novamente mais tarde.');

                return $this->redirectToRoute('app_assunto_index', [], Response::HTTP_SEE_OTHER);
            }

            $this->addFlash('success', 'Assunto atualizado com sucesso!');

            return $this->redirectToRoute('app_assunto_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('assunto/edit.html.twig', [
            'assunto' => $assunto,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_assunto_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Assunto $assunto,
        LivroRepository $livroRepository,
        AssuntoService $assuntoService,
        LoggerInterface $logger
    ): Response {

        if (
            !$this->isCsrfTokenValid(
            'delete'.$assunto->getId(),
            $request->getPayload()->getString('_token'))
        ) {

            $this->addFlash('danger', 'Token de segurança inválido.');
            return $this->redirectToRoute('app_assunto_index', [], Response::HTTP_SEE_OTHER);
        }

        $livroAssociado = $livroRepository->createQueryBuilder('l')
            ->innerJoin('l.assuntos', 'a')
            ->where('a.id = :assuntoId')
            ->setParameter('assuntoId', $assunto->getId())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if ($livroAssociado !== null) {

            $this->addFlash(
                'danger',
                'Não é possível excluir este assunto porque está associado a um ou mais livros. '
            );

            return $this->redirectToRoute('app_assunto_index', [], Response::HTTP_SEE_OTHER);
        }

        try {
            $assuntoService->excluir($assunto);
        } catch (ForeignKeyConstraintViolationException $exception) {
            $logger->warning('Tentativa de excluir assuntos existentes.', [
                'exception' => $exception,
                'op' => 'exclusao_assunto',
                'assunto_id' => $assunto->getId()
            ]);

            $this->addFlash(
                'danger',
                'Não foi possível excluir este assunto porque ele possui vínculo com outros registros.');

            return $this->redirectToRoute('app_assunto_index', [], Response::HTTP_SEE_OTHER);
        } catch (Exception $exception) {
            $logger->error('Erro ao excluir assunto.' , [
                'exception' => $exception,
                'op' => 'exclusao_assunto',
                'assunto_id' => $assunto->getId()
            ]);

            $this->addFlash(
                'danger',
                'Não foi possível excluir o assunto. Tente novamente mais tarde.'
            );

            return $this->redirectToRoute('app_assunto_index', [], Response::HTTP_SEE_OTHER);
        }

        $this->addFlash('success', 'Assunto removido com sucesso!');

        return $this->redirectToRoute('app_assunto_index', [], Response::HTTP_SEE_OTHER);
    }
}
