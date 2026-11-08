<?php

namespace App\Controller;

use App\Entity\Autor;
use App\Form\AutorType;
use App\Repository\AutorRepository;
use App\Repository\LivroRepository;
use App\Service\AutorService;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/autor')]
final class AutorController extends AbstractController
{
    #[Route(name: 'app_autor_index', methods: ['GET'])]
    public function index(AutorRepository $autorRepository): Response
    {
        return $this->render('autor/index.html.twig', [
            'autors' => $autorRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_autor_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        AutorService $autorService,
        LoggerInterface $logger
    ): Response {
        $autor = new Autor();
        $form = $this->createForm(AutorType::class, $autor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $autorService->salvar($autor);
            } catch (Exception $exception) {
                $logger->error('Erro ao cadastrar autor: ', [
                    'exception' => $exception,
                    'op' => 'cadastro_autor'
                ]);

                $this->addFlash('danger', 'Erro ao cadastrar autor. Tente novamente mais tarde.');

                return $this->redirectToRoute('app_autor_index', [], Response::HTTP_SEE_OTHER);
            }

            $this->addFlash('success', 'Autor cadastrado com sucesso!');

            return $this->redirectToRoute('app_autor_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('autor/new.html.twig', [
            'autor' => $autor,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_autor_show', methods: ['GET'])]
    public function show(Autor $autor): Response
    {
        return $this->render('autor/show.html.twig', [
            'autor' => $autor,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_autor_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Autor $autor,
        AutorService $autorService,
        LoggerInterface $logger
    ): Response {
        $form = $this->createForm(AutorType::class, $autor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $autorService->editar();
            } catch (Exception $exception) {
                $logger->error('Erro ao atualizar o autor: ', [
                    'exception' => $exception,
                    'op' => 'edicao_autor',
                    'autor_id' => $autor->getId()
                ]);

                $this->addFlash(
                    'danger',
                    'Não foi possível atualizar o autor. Tente novamente mais tarde.'
                );

                return $this->redirectToRoute('app_autor_index', [], Response::HTTP_SEE_OTHER);
            }

            $this->addFlash('success', 'Autor atualizado com sucesso!');

            return $this->redirectToRoute('app_autor_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('autor/edit.html.twig', [
            'autor' => $autor,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_autor_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Autor $autor,
        AutorService $autorService,
        LivroRepository $livroRepository,
        LoggerInterface $logger
    ): Response {
        if (!$this->isCsrfTokenValid(
            'delete'.$autor->getId(),
            $request->getPayload()->getString('_token')
        )) {

            $this->addFlash('danger', 'Token de segurança inválido.');

            return $this->redirectToRoute('app_autor_index', [], Response::HTTP_SEE_OTHER);
        }

        $livrosAssossiados = $livroRepository->createQueryBuilder('l')
            ->innerJoin('l.autores', 'a')
            ->where('a.id = :autorId')
            ->setParameter('autorId', $autor->getId())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if ($livrosAssossiados !== null) {
            $this->addFlash(
                'danger',
                'Não é possível excluir este autor porque está associado a um ou mais livros.'
            );

            return $this->redirectToRoute('app_autor_index', [], Response::HTTP_SEE_OTHER);
        }

        try {
            $autorService->excluir($autor);
        } catch (ForeignKeyConstraintViolationException $exception) {
            $logger->warning('Tentativa de excluir autor com vínculos existentes.', [
                'exception' => $exception,
                'op' => 'exclusao_autor',
                'autor_id' => $autor->getId(),
            ]);

            $this->addFlash(
                'danger',
                'Não foi possível excluir o autor porque ele possui vínculos com outros registros.'
            );

            return $this->redirectToRoute('app_autor_index', [], Response::HTTP_SEE_OTHER);
        } catch (Exception $exception) {
            $logger->error('Erro ao excluir o autor.', [
                'exception' => $exception,
                'op' => 'exclusao_autor',
                'autor_id' => $autor->getId(),
            ]);

            $this->addFlash(
                'danger',
                'Não foi possível excluir o autor. Tente novamente mais tarde.'
            );

            return $this->redirectToRoute('app_autor_index', [], Response::HTTP_SEE_OTHER);
        }

        $this->addFlash('success', 'Autor removido com sucesso!');

        return $this->redirectToRoute('app_autor_index', [], Response::HTTP_SEE_OTHER);
    }
}
