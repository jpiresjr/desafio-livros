<?php

namespace App\Controller;

use App\Entity\Livro;
use App\Form\LivroType;
use App\Repository\LivroRepository;
use App\Service\LivroService;
use Doctrine\DBAL\Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/livro')]
final class LivroController extends AbstractController
{
    #[Route(name: 'app_livro_index', methods: ['GET'])]
    public function index(LivroRepository $livroRepository): Response
    {
        return $this->render('livro/index.html.twig', [
            'livros' => $livroRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_livro_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        LivroService $livroService,
        LoggerInterface $logger
    ): Response {
        $livro = new Livro();
        $form = $this->createForm(LivroType::class, $livro);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $livroService->salvar($livro);
            } catch (Exception $exception) {
                $logger->error('Erro ao cadastrar livro. ', [
                    'exception' => $exception,
                    'op' => 'cadastro_livro'
                ]);

                $this->addFlash(
                    'danger',
                    'Não foi possível cadastrar o livro. Tente novamente mais tarde.'
                );
            }

            $this->addFlash('success', 'Livro cadastrado com sucesso!');

            return $this->redirectToRoute('app_livro_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('livro/new.html.twig', [
            'livro' => $livro,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_livro_show', methods: ['GET'])]
    public function show(Livro $livro): Response
    {
        return $this->render('livro/show.html.twig', [
            'livro' => $livro,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_livro_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Livro $livro,
        LivroService $livroService,
        LoggerInterface $logger
    ): Response {
        $form = $this->createForm(LivroType::class, $livro);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $livroService->editar();
            } catch (Exception $exception) {
                $logger->error('Erro ao editar o livro. ', [
                    'exception' => $exception,
                    'op' => 'edicao_livro',
                    'livro_id' => $livro->getId()
                ]);

                $this->addFlash('danger', 'O livro não pode ser editado, tente novamente mais tarde.');

                return $this->render('livro/edit.html.twig', [
                    'livro' => $livro,
                    'form' => $form,
                ]);
            }

            $this->addFlash('success', 'Livro editado com sucesso!');

            return $this->redirectToRoute('app_livro_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('livro/edit.html.twig', [
            'livro' => $livro,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_livro_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Livro $livro,
        LivroService $livroService,
        LoggerInterface $logger
    ): Response {

        if (!$this->isCsrfTokenValid(
            'delete'.$livro->getId(),
            $request->getPayload()->getString('_token'))
        ) {
            $this->addFlash('danger', 'Token de segurança inválido. Não foi possível excluir o livro.');

            return $this->redirectToRoute('app_livro_index', [], Response::HTTP_SEE_OTHER);
        }

        try {
            $livroService->excluir($livro);
        } catch (Exception $exception) {
                $logger->error('Falha ao excluir o livro: ', [
                   'exception' => $exception,
                   'op' => 'exclusao_livro',
                   'livro_id' => $livro->getId()
                ]);

                $this->addFlash('danger', 'Não foi possivel excluir o livro. Tente novamente mais tarde.');

                return $this->redirectToRoute('app_livro_index', [], Response::HTTP_SEE_OTHER);
        }

        $this->addFlash('success', 'Livro excluído com sucesso!');

        return $this->redirectToRoute('app_livro_index', [], Response::HTTP_SEE_OTHER);
    }
}
