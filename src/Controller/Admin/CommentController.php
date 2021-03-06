<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\TrickRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/comment", name="admin_")
 */
class CommentController extends AbstractController
{
    /**
     * @Route("/loadmore", name="comment_loadmore", methods={"POST"})
     */
    public function loadMoreComments(Request $request, CommentRepository $commentRepository, TrickRepository $trickRepository): JsonResponse
    {
        if ($request->isXmlHttpRequest()) {
            $data = [];
            $comments = $commentRepository->findLoadMoreComments($request->request->get('offset'), $commentRepository->count([]), $trickRepository->find($request->request->get('trick')));

            foreach ($comments as $comment) {
                $data[] = [
                    'id' => $comment->getId(),
                    'user' => $comment->getUser()->getUsername(),
                    'avatar' => $comment->getUser()->getAvatar(),
                    'created' => $comment->getCreatedAt()->format('d/m/Y H:i'),
                    'content' => $comment->getContent(),
                ];
            }

            return new JsonResponse($data);
        }
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/{id}", name="comment_show", methods={"GET"})
     */
    public function show(Comment $comment): Response
    {
        return $this->render('admin/comment/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/{id}/edit", name="comment_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Comment $comment): Response
    {
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "Les informations ont ??t?? mises ?? jour avec succ??s??!");

            return $this->redirectToRoute('admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/{id}", name="comment_delete", methods={"POST"})
     */
    public function delete(Request $request, Comment $comment): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comment);
            $entityManager->flush();

            $this->addFlash('success', "Le commentaire a ??t?? supprim??e avec succ??s??!");
        }

        return $this->redirectToRoute('admin', [], Response::HTTP_SEE_OTHER);
    }
}
